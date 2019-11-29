<template>
    <div class="oplog">
        <div class="WSY_content">
            <div class="WSY_columnbox">
                <el-form ref="form" :model="form" label-width="80px" style="margin:20px;min-width:1000px;">
                    <el-form-item label="产品名称" class="top-inp">
                        <el-input v-model="form.search_pname" placeholder="请输入产品名称"></el-input>
                    </el-form-item>
                    <el-form-item label="产品编码" class="top-inp">
                        <el-input v-model="form.search_pid" placeholder="请输入产品编码"></el-input>
                    </el-form-item>
                    <!-- <el-form-item label="产品分类" class="top-inp timequantum">
                            <el-select v-model="form.search_ptype" placeholder="请选择">
                                <el-option v-for="item in optionscp" :key="item.value" :label="item.label" :value="item.value">
                                </el-option>
                            </el-select>
                        </el-form-item> -->
                    <div class="WSY_select_box">
                        <span>产品分类</span>
                        <select v-model="form.search_ptype" placeholder="--请选择--" class="WSY_elselect">
                            <option v-for="item in optionscp" :key="item.id" :id="item.id" :label="item.name" :value="item.id">
                            </option>
                        </select>
                    </div>
                    <br></br>
                    <el-form-item label="活动名称" class="top-inp">
                        <el-input v-model="form.search_actname" placeholder="请输入活动名称"></el-input>
                    </el-form-item>
                    <el-form-item label="活动编码" class="top-inp">
                        <el-input v-model="form.search_actid" placeholder="请输入活动编码"></el-input>
                    </el-form-item>
                    <el-form-item label="状态筛选" class="top-inp">
                        <el-select v-model="form.search_actstatus" placeholder="请选择">
                            <el-option v-for="item in optionszt" :key="item.value" :label="item.label" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="活动类型" class="top-inp">
                        <el-select v-model="form.search_acttype" placeholder="请选择">
                            <el-option v-for="item in optionshd" :key="item.value" :label="item.label" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item class="top-inp topbtn">
                        <el-button type="primary" class="WSY_bottonliss" @click.native="search">搜索</el-button>
                        <!--<el-button type="primary" class="WSY_bottonliss" @click.native="exportFun">导出</el-button>-->
                    </el-form-item>

                    <table width="97%" class="WSY_table">
                        <thead class="WSY_table_header" :class="skin">
                        <tr>
                            <th width="5%">活动编码</th>
                            <th width="8%">活动名称</th>
                            <th width="5%">活动类型</th>
                            <th width="8%">活动状态</th>
                            <th width="8%">产品图片</th>
                            <th width="5%">产品名称</th>
                            <th width="5%">产品编码</th>
                            <th width="5%">产品分类</th>
                            <th width="8%">现价</th>
                            <th width="8%">销量</th>
                            <th width="5%">订单积分总额</th>
                            <th width="5%">订单总额</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for='item in tableData'>
                                <td>{{item.act_id}}</td>
                                <td>{{item.act_name}}</td>
                                <td v-if="item.act_type == 0">购买产品送积分</td>
                                <td v-else-if="item.act_type == 1">签到送积分</td>
                                <td v-else-if="item.act_type == 2">兑换扣积分</td>
                                <td v-if="item.status == 0">未启用</td>
                                <td v-else-if="item.status == 1">启用</td>
                                <td v-else-if="item.status == 2">结束</td>
                                <td v-else-if="item.status == 3">手动结束</td>

                                <td><img width="50" height="50" :src="basehear+item.default_imgurl" alt="" style="margin-top: 8px;"></td>
                                <td>{{item.pname}}</td>
                                <td>{{item.pid}}</td>
                                <td>{{item.type_name}}</td>
                                <td>{{item.now_price}}</td>
                                <td>{{item.sales_volume}}</td>
                                <td>{{item.number}}</td>
                                <td>{{item.price}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <el-pagination @current-change="handleCurrentChange" :current-page.sync="form.page" :page-size="form.count" layout="prev, pager, next, jumper" :total="total" v-show="total>0">
                    </el-pagination>
                </el-form>
            </div>
        </div>
    </div>
</template>

<script>
import '../../assets/js/jquery-1.12.1.min.js'
import '../../assets/js/jquery.table2excel.min.js'

export default {
    mounted: function() {
        var self = this;
        //换肤
self.$http.post("mshop/admin/index.php?m=setting&a=get_shop_skin", {
})
.then(res => {
self.skin=res.data.skin
}).catch(err => {
console.log(err)
})

        self.$http.post('mshop/admin/index.php?m=activity&a=integral_activity_statistics&customer_id=' + self.customer_id, { system_code: '' }
        ).then(function(res) {
            for (var i = 0; i < res.data.data.total; i++) {
                self.tableData.push(res.data.data[i])
            }
            self.total = parseInt(res.data.data.total);
        })
            .catch(function(err) {
                console.log(err)
            })
        // 产品分类
        var getAllType = 'mshop/admin/index.php?m=activity&a=__get_all_type&customer_id=' + self.customer_id;
        self.$http.post(getAllType, {
            "system_code": ""
        })
            .then(res => {
                var newArr = [];
                newArr.push({ "id": '', "name": "全部分类" });
                if (res.data.first_type != "") {
                    $(res.data.first_type).each(function(i, data) {
                        newArr.push(data);
                        if (data.son != undefined) {
                            $(data.son).each(function(i, data1) {
                                newArr.push({ "id": data1.id, "name": "--" + data1.name })
                            });
                        }
                    });
                    self.optionscp = newArr;
                }
            }).catch(err => {
                console.log(err)
            })
    },
    data() {
        return {
            skin:'',
            value: '',
            total: 0,
            form: {
                "search_pid": '',//搜索参数：产品ID
                "search_pname": '',//搜索参数：产品名
                "search_ptype": '',//搜索参数：产品类型
                "search_actid": '',//搜索参数：活动ID
                "search_actname": '',//搜索参数：活动名称
                "search_actstatus": '',//搜索参数：活动状态
                "search_acttype": '',//搜索参数：活动类型
                "page": 1,//页数
                "count": 10//每页数量

            },
            optionscp: [],
            optionszt: [{
                value: '0',
                label: '未启用'
            }, {
                value: '1',
                label: '启用'
            }, {
                value: '2',
                label: '结束'
            }, {
                value: '3',
                label: '手动结束'
            }],
            optionshd: [{
                value: '0',
                label: '购买产品送积分'
            },  {
                value: '2',
                label: '兑换扣积分'
            }],
            tableData: []
        }
    },

    methods: {
        exportFun() {
            $(".table2excel").table2excel({
                exclude: ".noExl",
                name: "Excel Document Name",
                filename: "积分活动统计列表",
                exclude_img: true,
                exclude_links: true,
                exclude_inputs: true
            });
        },
        handleCurrentChange(val) {
            var self = this;
            if (val) {
                self.page = val;
            } else {
                self.page = 1;
            }
            self.$http.post("mshop/admin/index.php?m=activity&a=integral_activity_statistics&customer_id=" + self.customer_id, { 'system_code': '', data: self.form }
            ).then(function(res) {
                self.tableData = []
                for (var i = 0;i < res.data.data.total && i < res.data.data.list_num; i++) {
                    self.tableData.push(res.data.data[i])
                }
                self.total = parseInt(res.data.data.total);
            }).catch(function() {

            })
        },
        search: function() {
            var self = this;
            console.log(self.form)
            self.$http.post("mshop/admin/index.php?m=activity&a=integral_activity_statistics&customer_id=" + self.customer_id, { 'system_code': '', data: self.form }
            ).then(function(res) {
                self.handleCurrentChange();
            }).catch(function() {

            })
        }
    }
}

</script>
<style scoped>
.WSY_select_box select {
    height: 26px;
    width: 150px;
    margin-left: 18px;
}
</style>
<style >
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
}

.line {
    text-align: center;
}

.topbtn .el-form-item__content {
    margin-left: 0!important;
}
</style>
