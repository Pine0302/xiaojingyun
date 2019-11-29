<template>
    <div class="oplog">
        <div class="WSY_content">
            <div class="WSY_columnbox">
                <el-form ref="form" :model="form" label-width="80px" style="margin:20px;min-width:1000px;" class="table2excel">
                    <el-form-item label="积分类型" class="top-inp">
                        <el-select v-model="form.integral_type" placeholder="请选择">
                            <el-option v-for="item in integraloptions" :key="item.value" :label="item.label" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="账单类型" class="top-inp">
                        <el-select v-model="form.type" placeholder="请选择">
                            <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="时间" class="top-inp timequantum">
                        <el-col :span="9">
                            <el-date-picker type="date" placeholder="选择日期" v-model="form.start_time" style="width: 100%;"></el-date-picker>
                        </el-col>
                        <el-col class="line" :span="3">至</el-col>
                        <el-col :span="9">
                            <el-date-picker type="date" placeholder="选择日期" v-model="form.end_time" style="width: 100%;"></el-date-picker>
                        </el-col>
                    </el-form-item>
                    <el-form-item class="top-inp topbtn">
                        <el-button type="primary" class="WSY_bottonliss" :class="skin" @click="search">搜索</el-button>
                        <!--<el-button type="primary" @click="exportFun" class="WSY_bottonliss">导出</el-button>-->
                    </el-form-item>

                    <table width="97%" class="WSY_table">
                        <thead class="WSY_table_header" :class="skin">
                            <th width="5%">序号</th>
                            <th width="8%">订单号</th>
                            <th width="5%">订单金额</th>
                            <th width="8%">积分类型</th>
                            <th width="8%">类型</th>
                            <th width="8%">收入/出账</th>
                            <th width="5%">变动前积分</th>
                            <th width="5%">变动后积分</th>
                            <th width="5%">创建时间</th>
                            <th width="8%">备注</th>
                        </thead>
                        <tbody>
                            <tr v-for='item in tableData'>
                                <td>{{item.log_id}}</td>
                                <td>{{item.order_id}}</td>
                                <td>{{item.price}}</td>
                                <td>
                                    <span v-if="item.type!=9 && item.type!=10 && item.type!=1">{{diyname.shop_integral_name}}</span>
                                    <span v-if="item.type==9 || item.type==10 ">{{diyname.store_integral_name}}</span>
                                </td>
                                <td>
                                    <span v-if="item.type!=1 && item.number>0">收入</span>
                                    <span v-if="item.type!=1 && item.number<0">支出</span>
                                    <span v-if="item.type==1">签到收入</span>
                                </td>
                                <td>{{item.number}}</td>
                                <td>{{item.before}}</td>
                                <td>{{item.after}}</td>
                                <td>{{item.add_time}}</td>
                                <td>{{item.remark}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <el-pagination @current-change="handleCurrentChange" :current-page.sync="page" :page-size="page_size" layout="prev, pager, next, jumper" :total="total">
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
    created: function() {
        var self = this

        //换肤
            self.$http.post("mshop/admin/index.php?m=setting&a=get_shop_skin", {
            })
            .then(res => {
            self.skin=res.data.skin
            }).catch(err => {
            console.log(err)
            })
        self.user_id = self.$route.query.user_id
        if(self.$route.query.integral_type){
             self.form.integral_type = self.$route.query.integral_type
        }
        

        self.$http.post('mshop/admin/index.php?m=activity&a=user_integral_log&customer_id=' + self.customer_id, {
            system_code: '', data: {
                "user_id": self.user_id,        //用户id   194515这个账号有数据
                "integral_type": self.form.integral_type,      //积分类型：-1为全部 0为商城积分  1为门店积分
                "type": "-1",               //类型：-1为全部 1为收入  2为支出  3签到收入
                "start_time": "",       //搜索参数：开始时间
                "end_time": "",     //搜索参数：结束时间
                "page": self.page - 1,          //页数
                "page_size": self.page_size     //每页数量
                
            }
        }).then(function(res) {
            for (var i = 0; i < res.data.datas.total && i < res.data.datas.list_num; i++) {
                self.tableData.push(res.data.datas.list[i])
            }
            self.total = parseInt(res.data.datas.total);
        }).catch(function(err) {
            console.log(err)
        })
           //获取自定义名称
        self.$http.post('mshop/admin/index.php?m=activity&a=get_diyname&customer_id=' + self.customer_id, {
            system_code: '',
            data: {
            }
        }).then(function(res) {
            console.log(res.data.data)
            self.diyname=res.data.data
            self.integraloptions[1].label=res.data.data.shop_integral_name
               self.integraloptions[2].label=res.data.data.store_integral_name

        }).catch(function(err) {
            console.log(err)
        })


    },
    data() {
        return {
            skin:'',
            user_id: '',
            value: '',
            total: 0,
            "page": 1,          //页数
            "page_size": 10,
             diyname: {
                "shop_integral_name": "",
                "store_integral_name": "",
                "custom_name": ""
            },
            form: {
                "user_id": "",      //用户id   194515这个账号有数据
                "integral_type": "-1",
                "type": "-1",
                "start_time": "",       //搜索参数：开始时间
                "end_time": "",     //搜索参数：结束时间
                // "page": 0,           //页数
                // "page_size": 10      //每页数量

            },
            integraloptions: [{
                value: '-1',
                label: '全部'
            }, {
                value: '0',
                label: '商城积分'
            }, {
                value: '1',
                label: '门店积分'
            }],
            options: [{
                value: '-1',
                label: '全部'
            }, {
                value: '1',
                label: '收入'
            }, {
                value: '2',
                label: '支出'
            }, {
                value: '3',
                label: '签到收入'
            }],
            tableData: []
        }
    },

    methods: {
        exportFun() {
            $(".table2excel").table2excel({
                exclude: ".noExl",
                name: "Excel Document Name",
                filename: "积分明细列表",
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
            this.$http.post('mshop/admin/index.php?m=activity&a=user_integral_log&customer_id=' + self.customer_id, {
                system_code: '', data: {
                    "user_id": self.user_id,        //用户id   194515这个账号有数据
                    "integral_type": self.form.integral_type,      //积分类型：-1为全部 0为商城积分 1为门店积分
                    "type": self.form.type,               //类型：-1为全部 1为收入  2为支出  3签到收入
                    "start_time": self.form.start_time,       //搜索参数：开始时间
                    "end_time": self.form.end_time,     //搜索参数：结束时间
                    "page": self.page - 1,            //页数
                    "page_size": self.page_size     //每页数量
                }

            }).then(function(res) {
                console.log(res)
                self.tableData = [];
                for (var i = 0; i < res.data.datas.total && i < res.data.datas.list_num; i++) {
                    self.tableData.push(res.data.datas.list[i])
                }
                self.total = parseInt(res.data.datas.total);
            }).catch(function(err) {
                console.log(err)
            })
        },
        search: function() {
            var self = this
            console.log(self.form)
            self.form.user_id=self.user_id
            self.form.page=self.page
            self.form.page_size=self.page_size
            this.$http.post('mshop/admin/index.php?m=activity&a=user_integral_log&customer_id=' + self.customer_id, {
                system_code: '', data: self.form

            }).then(function(res) {
                self.handleCurrentChange();
                // self.tableData = []
                // for (var i = 0; i < res.data.datas.total && i < res.data.datas.list_num; i++) {
                //     self.tableData.push(res.data.datas.list[i])
                // }
                // console.log(res.data.datas.total)
            }).catch(function(err) {
                console.log(err)
            })
        }
    }
}

</script>
<style >
.top-inp {
    display: inline-block;
    margin-right: 10px;
}

.el-input__inner {
    height: 26px;
    border: 1px solid #ccc;
    border-radius: 0;

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


.WSY_columnbox .el-form .el-input__inner {
    height: 26px!important;
    border: 1px solid #ccc!important;
    width: 100%; 
    border-radius: 0;
}
</style>
