<template>
    <div class="oplog">
        <div class="WSY_content">
            <div class="WSY_columnbox">
                <el-form ref="form" :model="form" label-width="80px" style="margin:20px;min-width:1000px;">
                    <el-form-item label="活动名称" class="top-inp">
                        <el-input placeholder="输入活动名称" v-model="form.act_name"></el-input>
                    </el-form-item>
                    <el-form-item label="活动时间" class="top-inp timequantum">
                        <el-col :span="10">
                            <el-date-picker type="date" placeholder="选择日期" v-model="form.start_time" style="width: 100%;"></el-date-picker>
                        </el-col>
                        <el-col class="line" :span="3">至</el-col>
                        <el-col :span="10">
                            <el-date-picker type="date" placeholder="选择日期" v-model="form.end_time" style="width: 100%;"></el-date-picker>
                        </el-col>
                    </el-form-item>
                    <el-form-item label="创建时间" class="top-inp">
                        <el-date-picker type="date" placeholder="选择日期" v-model="form.add_time" style="width: 100%;"></el-date-picker>
                    </el-form-item>
                    <!-- <el-form-item label="状态筛选" class="top-inp">
                        <el-select v-model="value" placeholder="请选择">
                            <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item> -->
                      <div class="WSY_select_box">
                            <span>状态筛选</span>
                            <select v-model="value" placeholder="--请选择--" class="WSY_elselect">
                                <option v-for="item in options" :key="item.value" :id="item.value" :label="item.label" :value="item.value">
                                </option>
                            </select>
                        </div>

                    <el-form-item class="top-inp topbtn">
                        <el-button type="primary" class="WSY_bottonliss" :class="skin" @click="search">搜索</el-button>
                        <el-button type="primary" class="WSY_bottonliss" :class="skin" @click="add">添加</el-button>
                    </el-form-item>
                    <table width="97%" class="WSY_table">
                        <thead class="WSY_table_header" :class="skin">
                            <th width="5%">序号</th>
                            <th width="8%">活动名称</th>
                            <th width="10%">活动时间</th>
                            <th width="8%">创建时间</th>
                            <th width="5%">状态</th>
                            <th width="5%">是否自动发布</th>
                            <th width="5%">操作</th>
                        </thead>
                        <tbody>
                            <tr v-for='(item,index) in tableData'>
                                <td>{{item.act_id}}</td>
                                <td>{{item.act_name}}</td>
                                <td>{{item.start_time  | formatDate}}至{{item.end_time  | formatDate}}</td>
                                <td>{{item.add_time  | formatDate}}</td>
                                <td>{{item.status}}</td>
                                <td>
                                    <span v-if="item.auto_start==0">否</span>
                                    <span v-if="item.auto_start==1">自动发布</span>
                                    <span v-if="item.auto_start==2">手动发布</span>
                                </td>
                                <td class="WSY_t4">
                                    <a title="编辑" @click="edit(item.act_id)">
                                        <img src="../../assets/images_V6.0/operating_icon/icon05.png"></a>
                                    <a title="删除" v-if="item.status!='已启用'&&item.status!='进行中'" @click="delect(item.act_id)">
                                        <img src="../../assets/images_V6.0/operating_icon/icon04.png"></a>
                                    <a v-if="item.auto_start==0 && item.status=='未启动'" title="发布" @click="issue(item.act_id)">
                                        <img src="../../assets/images_V6.0/operating_icon/icon23.png"></a>
                                    <a v-if="item.status=='已启用'|| item.status=='进行中'" title="结束" @click="end(item.act_id)">
                                        <img src="../../assets/images_V6.0/operating_icon/icon08.png"></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <el-pagination @current-change="handleCurrentChange" :current-page.sync="page" :page-size="count" layout="prev, pager, next, jumper" :total="total">
                    </el-pagination>
                </el-form>
            </div>
        </div>
    </div>
</template>

<script>

import util from '../../utils';


export default {
    created: function() {
        var self = this;
        //换肤
            self.$http.post("mshop/admin/index.php?m=setting&a=get_shop_skin", {
            })
            .then(res => {
            self.skin=res.data.skin
            }).catch(err => {
            console.log(err)
            })

        self.$http.post('mshop/admin/index.php?m=activity&a=activity_index&customer_id=' + self.customer_id, { 'system_code': '', 'data': { "act_type": 1, "count": self.count, "page": self.page - 1 } }
        ).then(function(res) {
            for (var i = 0; i < res.data.data.total && i < res.data.data.list_num; i++) {
                self.tableData.push(res.data.data[i])
            }
            console.log(self.tableData)
            self.total = parseInt(res.data.data.total);
        })
            .catch(function(err) {
                console.log(err)
            })

    },
    data() {
        return {
            skin:'',
            tableData: [],
            leijijifen: '1000000',
            qiandaonum: '20',
            zongshu: 200,
            currentPage4: 4,
            value: '-1',
            total: 0,
            count: 10,
            page: 1,
            form: {
                act_name: '',
                start_time: '',
                end_time: '',
                add_time: '',
                act_type: '1',
                "count": 10,
                "page": 0
            },
            options: [
                {
                value: '',
                label: '全部'
            },{
                value: '0',
                label: '未启动'
            }, {
                value: '1',
                label: '已启用'
            }, {
                value: '2',
                label: '结束'
            }, {
                value: '3',
                label: '手动终止'
            }],

        }
    },

    methods: {
        end: function(act_id) {
            var self = this
            if (confirm("确认是否结束此活动？")) {
                self.$http.post("mshop/admin/index.php?m=activity&a=save_activity&customer_id=" + self.customer_id, {
                    "system_code": "",
                    "data": { 'op': 'end', 'act_id': act_id,"act_type": 1 }
                })
                    .then(res => {
                        window.location.reload()
                    }).catch(err => {
                        console.log(err)
                    })
            }
        },
        issue: function(act_id) {
            var self = this
            self.$http.post("mshop/admin/index.php?m=activity&a=save_activity&customer_id=" + self.customer_id, {
                "system_code": "",
                "data": { 'op': 'release', 'act_id': act_id,"act_type": 1 }
            })
                .then(res => {
                    if (res.data.errcode == 0) {
                        alert("发布成功")
                        window.location.reload()
                    } else {
                        alert(res.data.errmsg)

                    }
                }).catch(err => {
                    console.log(err)
                })
            
        },
        handleCurrentChange(val) {
            var self = this;
            if (val) {
                self.page = val;
            } else {
                self.page = 1;
            }
            self.$http.post('mshop/admin/index.php?m=activity&a=activity_index&customer_id=' + self.customer_id, { 'system_code': '', 'data': { "act_type": 1, "count": self.count, "page": self.page - 1 } }
        ).then(function(res) {
            self.tableData=[];
            for (var i = 0; i < res.data.data.total && i < res.data.data.list_num; i++) {
                self.tableData.push(res.data.data[i])
            }
            console.log(self.tableData)
            self.total = parseInt(res.data.data.total);
        })
            .catch(function(err) {
                console.log(err)
            })

        },
        search: function() {
            var that = this
            that.tableData = []
            that.form.status = that.value
             if (that.form.start_time) {
                that.form.start_time = util.formatDateTimeFil(that.form.start_time, "yyyy-MM-dd");
            }
            if (that.form.end_time) {
                that.form.end_time = util.formatDateTimeFil(that.form.end_time, "yyyy-MM-dd");
            }
            if (that.form.add_time) {
                that.form.add_time = util.formatDateTimeFil(that.form.add_time, "yyyy-MM-dd");
            }
            that.$http.post('mshop/admin/index.php?m=activity&a=activity_index&customer_id=' + that.customer_id, { 'data': that.form }
            ).then(function(res) {
                // that.value = '-1'
                //  that.form.act_name = ''
                //     that.form.add_time = ''
                //     that.form.start_time = ''
                //    that.form.end_time = ''
                   
                for (var i = 0; i < res.data.data.total&& i < res.data.data.list_num; i++) {
                    that.tableData.push(res.data.data[i])
                }
            })
                .catch(function(err) {
                    console.log(err)
                })
        },
        add: function() {
            this.$router.push({ path: '/activityset/Tianjiaqiandao' });
        },
        edit: function(obj) {
            this.$router.push({ path: '/activityset/Tianjiaqiandao', query: { 'act_id': obj } });
        },
        delect: function(obj) {
            var that = this;
             if (confirm("确认是否删除？")) {
            that.$http.post('mshop/admin/index.php?m=activity&a=save_activity&customer_id=' + that.customer_id, { system_code: '', data: { 'op': 'del', 'act_id': obj, 'act_type': 1 } }
            ).then(function(res) {
                if(res.data.errcode == 600){
                    alert(res.data.errmsg+"，请先结束该活动！");
                }else{
                   
                        window.location.reload()
                    
                }
            })
                .catch(function(err) {
                    console.log(err)
                })
                }
        }
    }
}

</script>
<style scoped>
@import '../../assets/css/comstyleone.css';
.top-inp {
    display: inline-block;
    margin-right: 10px;
}

.el-input__inner {
    height: 26px;
    border: 1px solid #ccc;
    border-radius: 0;
    max-width: 150px;
}

.timequantum {
    vertical-align: bottom;
    width: 430px;
}

.line {
    text-align: center;
}

.topbtn .el-form-item__content {
    margin-left: 0!important;
}
.WSY_select_box select {
    width: 150px;
    margin-top: 1px;
    padding: 3px;
    border: solid 1px #ccc;
    border-radius: 2px;
}
</style>
