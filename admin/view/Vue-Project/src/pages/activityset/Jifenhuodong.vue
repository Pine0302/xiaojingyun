<template>
    <div class="oplog">
        <div class="WSY_content">
            <div class="WSY_columnbox">
                <div class="WSY_search_q">
                    <div class="WSY_search_div">
                        <div class="WSY_iptbox">
                            <span>活动名称</span>
                            <input type="text" name="act_name1" placeholder="" maxlength="10" v-model="act_name1"></div>
                        <div class="date_search">
                            <div class="com_date">
                                <span class="demonstration">活动时间</span>
                                <el-date-picker v-model="start_time" type="date" placeholder="选择日期"></el-date-picker>

                            </div>
                            <div class="com_date">
                                <span class="demonstration" style="margin-left:15px;">至</span>
                                <el-date-picker v-model="end_time" type="date" placeholder="选择日期"></el-date-picker>

                            </div>
                        </div>
                        <div class="WSY_iptbox">
                            <span>创建时间</span>
                            <el-date-picker v-model="add_time" type="date" placeholder="选择日期"></el-date-picker>
                        </div>
                        <div class="WSY_select_box">
                            <span>状态筛选</span>
                            <select v-model="value" placeholder="--请选择--" class="WSY_elselect">
                                <option v-for="item in options" :key="item.value" :id="item.value" :label="item.label" :value="item.value">
                                </option>
                            </select>
                        </div>
                        <div class="WSY_small_btn"  :class="skin" @click="search">搜索</div>
                        <div class="WSY_small_btn"  :class="skin"  @click="add">添加</div>
                    </div>
                </div>
                <table width="97%" class="WSY_table">
                    <thead class="WSY_table_header" :class="skin">
                        <th width="5%">序号</th>
                        <th width="10%">活动名称</th>
                        <th width="10%">活动时间</th>
                        <th width="10%">创建时间</th>
                        <th width="8%">状态</th>
                        <th width="8%">是否自动发布</th>
                        <th width="18%">操作</th>
                    </thead>
                    <tbody>
                        <tr v-for='(item,index) in tableData'>
                            <td>{{item.act_id}}</td>
                            <td>{{item.act_name}}</td>
                            <td>{{item.start_time | formatDate}}至{{item.end_time | formatDate}}</td>
                            <td>{{item.add_time | formatDate}}</td>
                            <td>{{item.status}}
                                <!-- <span v-if="item.status==0">待发布</span>
                                <span v-if="item.status==1"><span>已发布</span></span>
                                <span v-if="item.status==2">已结束</span>
                                <span v-if="item.status==3">手动结束</span> -->
                            </td>
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
            </div>
        </div>
    </div>
</template>
<script>
import util from '../../utils';
import global_ from '../../components/Global'
export default {
    name: 'oplog',
    components: {
        // Datepicker
    },
    data() {
        return {
            
            now: util.formatDateTimeFil(new Date(), "yyyy-MM-dd"),
            act_name1: '',
            add_time: '',
            start_time: '',
            end_time: '',
            total: 0,
            count: 10,
            page: 1,
            tableData: [],
            value: '',
            skin:'',
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
                "data": { 'op': 'release', 'act_id': act_id,"act_type": 0 }
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
        search: function() {
            var self = this;
            console.log(self.customer_id)
            if (self.start_time) {
                self.start_time = util.formatDateTimeFil(self.start_time, "yyyy-MM-dd");
            }
            if (self.end_time) {
                self.end_time = util.formatDateTimeFil(self.end_time, "yyyy-MM-dd");
            }
            if (self.add_time) {
                self.add_time = util.formatDateTimeFil(self.add_time, "yyyy-MM-dd");
            }


            var transferData = {
                "act_name": self.act_name1,
                "add_time": self.add_time,
                "status": self.value,
                "page": self.page - 1,//页数
                "count": self.count,//每页显示数量
                "start_time": self.start_time,//查询时间（起始时间）
                "end_time": self.end_time,//查询时间（结束时间）
                act_type: 0
            };
            self.$http.post("mshop/admin/index.php?m=activity&a=activity_index&customer_id=" + self.customer_id, {
                "system_code": "",
                "data": transferData
            })
                .then(res => {
                    self.handleCurrentChange();
                    // self.act_name1 = ''
                    // self.add_time = ''
                    // self.start_time = ''
                    // self.end_time = ''
                    // self.value = '-1'
                }).catch(err => {
                    console.log(err)
                })
        },

        exportFun() {

        },
        add: function() {
            this.$router.push({ path: '/activityset/Tianjiahuodong', query: { 'act_type': 0 } });
        },
        delect: function(act_id) {
            var that = this;
            if (confirm("确认是否删除？")) {
                that.$http.post("mshop/admin/index.php?m=activity&a=save_activity&customer_id=" + that.customer_id, { system_code: '', data: { 'op': 'del', 'act_id': act_id, 'act_type': 0 } }
                ).then(function(res) {
                    window.location.reload()
                })
                    .catch(function(err) {
                        console.log(err)
                    })
            }

        },
        edit: function(obj) {
            this.$router.push({ path: '/activityset/Tianjiahuodong', query: { 'act_id': obj, 'act_type': 0 } });
        },
        handleCurrentChange(val) {
            var self = this;
            if (val) {
                self.page = val;
            } else {
                self.page = 1;
            }
            if (self.start_time) {
                self.start_time = util.formatDateTimeFil(self.start_time, "yyyy-MM-dd");
            }
            if (self.end_time) {
                self.end_time = util.formatDateTimeFil(self.end_time, "yyyy-MM-dd");
            }
            if (self.add_time) {
                self.add_time = util.formatDateTimeFil(self.add_time, "yyyy-MM-dd");
            }

            var transferData = {
                "act_name": self.act_name1,
                "add_time": self.add_time,
                "status": self.value,
                "page": self.page - 1,//页数
                "count": self.count,//每页显示数量
                "start_time": self.start_time,//查询时间（起始时间）
                "end_time": self.end_time,//查询时间（结束时间）
                act_type: 0
            };
            self.$http.post("mshop/admin/index.php?m=activity&a=activity_index&customer_id=" + self.customer_id, {
                "system_code": "",
                "data": transferData
            })
                .then(res => {
                    var resArr = [];
                    var lastpage = Math.ceil(res.data.data.total / 10);
                    var lastcount = res.data.data.total % 10;
                    var lastprepage = Math.floor(res.data.data.total / 10);
                    if (self.page > lastprepage) {
                        for (var i = 0; i < lastcount; i++) {
                            resArr.push(res.data.data[i]);
                        }
                    } else {
                        for (var i = 0; i < self.count; i++) {
                            resArr.push(res.data.data[i]);
                        }
                    }
                    self.tableData = resArr;
                    self.total = parseInt(res.data.data.total);
                }).catch(err => {
                    console.log(err)
                })
        },

    },
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
        var transferData = {
            "act_name": self.act_name1,
            "add_time": self.add_time,
            "status": self.value,
            "page": self.page - 1,//页数
            "count": self.count,//每页显示数量
            "start_time": self.start_time,//查询时间（起始时间）
            "end_time": self.end_time,//查询时间（结束时间）
            act_type: 0
        };


        self.$http.post("mshop/admin/index.php?m=activity&a=activity_index&customer_id=" + self.customer_id, {
            "system_code": "",
            "data": transferData
        })
            .then(res => {
                var resArr = [];
                var lastpage = Math.ceil(res.data.data.total / 10);
                var lastcount = res.data.data.total % 10;
                var lastprepage = Math.floor(res.data.data.total / 10);
                if (self.page > lastprepage) {
                    for (var i = 0; i < lastcount; i++) {
                        resArr.push(res.data.data[i]);
                    }
                } else {
                    for (var i = 0; i < self.count; i++) {
                        resArr.push(res.data.data[i]);
                    }
                }
                self.tableData = resArr;
                self.total = parseInt(res.data.data.total);
            }).catch(err => {
                console.log(err)
            })


    }
}
</script>

<style scoped>
/*@import '../../assets/css/comstyleone.css'*/

.el-date-editor.el-input {
    width: 150px;
}
</style>