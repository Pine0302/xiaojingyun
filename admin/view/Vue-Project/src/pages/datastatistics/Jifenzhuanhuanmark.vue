<template>
    <div class="oplog">
        <div class="WSY_content">
            <div class="WSY_columnbox">
                <el-form ref="form" :model="form" label-width="80px" style="margin:20px;min-width:1000px;" class="table2excel">
                    <el-form-item label="用户名" class="top-inp">
                        <el-input v-model="form.search_user_name"></el-input>
                    </el-form-item>
                    <el-form-item label="用户ID" class="top-inp">
                        <el-input v-model="form.search_user_id"></el-input>
                    </el-form-item>
                    <el-form-item label="时间" class="top-inp timequantum">
                        <el-col :span="10">
                            <el-date-picker type="date" placeholder="选择日期" v-model="form.search_start_time" style="width: 100%;"></el-date-picker>
                        </el-col>
                        <el-col class="line" :span="3">至</el-col>
                        <el-col :span="10">
                            <el-date-picker type="date" placeholder="选择日期" v-model="form.search_end_time" style="width: 100%;"></el-date-picker>
                        </el-col>
                    </el-form-item>
                    <el-form-item label="积分类型" class="top-inp">
                        <el-select v-model="form.type" placeholder="请选择">
                            <el-option v-for="item in optionshd" :key="item.value" :label="item.label" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-button type="primary" class="WSY_bottonliss" :class="skin" @click="search">搜索</el-button>
                    <!-- <el-button type="primary" class="WSY_bottonliss export_btn" @click="flag && oFunc(0)">导出</el-button> -->

                    <table width="97%" class="WSY_table">
                        <thead class="WSY_table_header" :class="skin">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="5%">用户ID</th>
                                <th width="5%">用户名</th>
                                <th width="8%">已绑定手机号</th>
                                <th width="5%">变动前积分</th>
                                <th width="5%">变动积分</th>
                                <th width="5%">变动后积分</th>
                                <th width="8%">订单号/交易号</th>
                                <th width="8%">下单时间</th>
                                <th width="5%">积分类型</th>
                                <th width="5%">转换对象</th>
                                <th width="5%">已转换数量</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for='(item,index) in tableData'>
                                <td>{{item.id}}</td>
                                <td>{{item.user_id}}</td>
                                <td>{{item.user_name}}</td>
                                <td>{{item.phone}}</td>
                                <td>{{item.before_num}}</td>
                                <td>{{item.change_num}}</td>
                                <td>{{item.after_num}}</td>
                                <td>{{item.batchcode}}</td>
                                <td>{{item.createtime}}</td>
                                <td>
                                    <span v-if="item.type==1">{{diyname.shop_integral_name}}</span>
                                    <span v-else>{{diyname.store_integral_name}}</span>
                                </td>
                                <td>
                                    <span v-if="item.change_object==1">{{diyname.custom_name}}</span>
                                </td>
                                <td>{{item.object_num}}</td>
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
        
        self.$http.post('mshop/admin/index.php?m=activity&a=integral_transformation_log&customer_id=' + self.customer_id, {
            system_code: '',
            data: {
                "page": self.page, //页数
                "count": self.count, //每页数量
                "search_user_name": "", //搜索参数：用户名称
                "search_start_time": "", //搜索参数：开始时间
                "search_end_time": "", //搜索参数：结束时间
                "search_user_id": "", //搜索参数：用户id
                "type": '',//积分类型

            }
        }).then(function(res) {

            for (var i = 0; i < res.data.datas.total && i < res.data.datas.list_num; i++) {
                self.tableData.push(res.data.datas[i])
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
            self.optionshd[1].label=res.data.data.shop_integral_name
               self.optionshd[2].label=res.data.data.store_integral_name

        }).catch(function(err) {
            console.log(err)
        })

    },
    data() {
        return {
            skin:'',
            "page": 1, //页数
            "count": 10,
            total: 0,
            diyname: {
                "shop_integral_name": "",
                "store_integral_name": "",
                "custom_name": ""
            },
            form: {
                search_user_name: '',
                search_user_id: '',
                search_start_time: '',
                search_end_time: '',
                type: '',
                page: ''
            },
            optionshd: [{
                value: '-1',
                label: '全部'
            }, {
                value: '1',
                label: '商城积分'
            }
                , {
                value: '2',
                label: '门店积分'
            }],
            tableData: []
        }
    },
    methods: {
        handleCurrentChange(val) {
            var self = this;
            if (val) {
                self.page = val;
            } else {
                self.page = 1;
            }
            self.form.page = self.page;
            self.form.count = self.count;
            self.$http.post('mshop/admin/index.php?m=activity&a=integral_transformation_log&customer_id=' + self.customer_id, {
                system_code: '',
                data: self.form
            }).then(function(res) {
                self.tableData = [];
                for (var i = 0; i < res.data.datas.total && i < res.data.datas.list_num; i++) {
                    self.tableData.push(res.data.datas[i])
                }
                self.total = parseInt(res.data.datas.total);
            }).catch(function(err) {
                console.log(err)
            })
        },
        search: function() {
            var self = this;
            self.form.page = '1';
            self.page = '1';
            self.form.count = self.count;
            if (self.form.search_start_time) {
                self.form.search_start_time = util.formatDateTimeFil(self.form.search_start_time, "yyyy-MM-dd");
            }
            if (self.form.search_end_time) {
                self.form.search_end_time = util.formatDateTimeFil(self.form.search_end_time, "yyyy-MM-dd");
            }
            self.$http.post('mshop/admin/index.php?m=activity&a=integral_transformation_log&customer_id=' + self.customer_id, {
                system_code: '',
                data: self.form
            }).then(function(res) {
                self.tableData = [];
                for (var i = 0; i < res.data.datas.total && i < res.data.datas.list_num; i++) {
                    self.tableData.push(res.data.datas[i])
                }
                self.total = parseInt(res.data.datas.total);
            }).catch(function(err) {
                console.log(err)
            })
        }
    }
}
</script>



<style>
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
</style>
