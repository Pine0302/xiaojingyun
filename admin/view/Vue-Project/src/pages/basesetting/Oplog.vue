<template>
    <div class="oplog">
        <div class="WSY_content">
            <div class="WSY_columnbox">
                <div class="WSY_search_q">
                    <div class="WSY_search_div">
                        <div class="WSY_iptbox">
                            <span>操作关键字</span>
                            <input type="text" name="keyword" placeholder="" maxlength="10" v-model="keyword"></div>
                        <div class="date_search">
                            <div class="com_date">
                                <span class="demonstration">时间搜索</span>
                                <el-date-picker v-model="start_time" type="date" placeholder="选择日期"></el-date-picker>
                            </div>
                            <div class="com_date">
                                <span class="demonstration" style="margin-left:15px;">至</span>
                                <el-date-picker v-model="end_time" type="date" placeholder="选择日期"></el-date-picker>
                            </div>
                        </div>
                        <div class="WSY_small_btn" :class="skin" @click="search">搜索</div>
                        <!-- <div class="WSY_small_btn" @click="exportFun">导出</div> -->
                    </div>
                </div>
                <table width="97%" class="WSY_table table2excel">
                    <thead class="WSY_table_header" :class="skin">
                    <tr >
                        <td width="5%">序号</td>
                        <td width="10%">时间</td>
                        <td width="10%">操作人</td>
                        <td width="18%">操作描述</td>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for='(item,index) in tableData'>
                            <td>{{item.log_id}}</td>
                            <td>{{item.add_time}}</td>
                            <td>{{item.admin_name}}</td>
                            <td>{{item.remark}}</td>
                        </tr>
                    </tbody>
                </table>
                <el-pagination
                  @current-change="handleCurrentChange"
                  :current-page.sync="page"
                  :page-size="count"
                  layout="prev, pager, next, jumper"
                  :total="total" v-show="total>0">
                </el-pagination>
            </div>
        </div>
    </div>
</template>
<script>
import '../../assets/js/jquery.table2excel.min.js'

export default {
    name: 'oplog',
    data() {
        return {
            skin:'',
            keyword: '',
            start_time: '',
            end_time: '',
            total: 0,
            count: 10,
            page: 1,
            tableData: []
        }
    },
    methods: {
        search: function() {
            var self = this;
            var urlget = 'mshop/admin/index.php?m=activity&a=read_admin_log&customer_id=' + self.customer_id;
            var transferData = {
                "keyword": self.keyword,//查询关键词
                "page": self.page,//页数
                "count": self.count,//每页显示数量
                "start_time": self.start_time,//查询时间（起始时间）
                "end_time": self.end_time//查询时间（结束时间）
            };
            self.$http.post(urlget, {
                "system_code": "",
                "data": transferData
            })
                .then(function(res) {
                    self.handleCurrentChange();
                })
                .catch(function(err) {
                    console.log(err)
                })
        },
        exportFun() {
            $(".table2excel").table2excel({
                exclude: ".noExl",
                name: "Excel Document Name",
                filename: "操作日志",
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
            var urlget = 'mshop/admin/index.php?m=activity&a=read_admin_log&customer_id=' + self.customer_id;
            var transferData = {
                "keyword": self.keyword,//查询关键词
                "page": self.page,//页数
                "count": self.count,//每页显示数量
                "start_time": self.start_time,//查询时间（起始时间）
                "end_time": self.end_time//查询时间（结束时间）
            };
            self.$http.post(urlget, {
                "system_code": "",
                "data": transferData
            })
                .then(function(res) {
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
                })
                .catch(function(err) {
                    console.log(err)
                })
        }
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
        var urlget = 'mshop/admin/index.php?m=activity&a=read_admin_log&customer_id=' + self.customer_id;
        var transferData = {
            "keyword": self.keyword,//查询关键词
            "page": self.page,//页数
            "count": self.count,//每页显示数量
            "start_time": self.start_time,//查询时间（起始时间）
            "end_time": self.end_time//查询时间（结束时间）
        };
    
                self.$http.post(urlget, {
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
.WSY_table_header{
    color: #fff;
}
</style>