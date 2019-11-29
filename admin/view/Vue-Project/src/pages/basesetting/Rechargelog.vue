<template>
    <div class="rechargelog">
        <div class="WSY_content">
            <div class="WSY_columnbox">
                <div class="WSY_search_q">
                    <div class="WSY_search_div">
                        <div class="WSY_iptbox">
                            <span>操作关键字</span>
                            <input type="text" name="opkey" placeholder="" maxlength="10" v-model="opkey"></div>
                        <div class="date_search">
                            <div class="com_date">
                                <span class="demonstration">时间搜索</span>
                                <el-date-picker v-model="value1" type="date" placeholder="选择日期" :picker-options="pickerOptions0"></el-date-picker>
                            </div>
                            <div class="com_date">
                                <span class="demonstration" style="margin-left:15px;">至</span>
                                <el-date-picker v-model="value1" type="date" placeholder="选择日期" :picker-options="pickerOptions0"></el-date-picker>
                            </div>
                        </div>
                        <div class="WSY_select_box">
                            <span>产品分类</span>
                            <el-select v-model="value" placeholder="--请选择--" class="WSY_elselect">
                                <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"></el-option>
                            </el-select>
                        </div>
                        <div class="WSY_small_btn skin-btn" @click="searchFun">搜索</div>
                        <div class="WSY_small_btn skin-btn" @click="exportFun">导出</div></div>
                </div>
                <table width="97%" class="WSY_table">
                    <thead class="WSY_table_header" :class="skin">
                        <th width="5%">序号</th>
                        <th width="12%">订单号</th>
                        <th width="10%">充值时间</th>
                        <th width="10%">充值金额</th>
                        <th width="10%">充值前金额</th>
                        <th width="10%">充值后金额</th></thead>
                    <tbody>
                        <tr v-for="item in tableData">
                            <td>{{item.ordernumber}}</td>
                            <td>{{item.orderid}}</td>
                            <td>{{item.time}}</td>
                            <td>{{item.rechargemoney}}</td>
                            <td>{{item.beformoney}}</td>
                            <td>{{item.aftermoney}}</td></tr>
                    </tbody>
                </table>
                <el-pagination
                  @size-change="handleSizeChange"
                  @current-change="handleCurrentChange"
                  :current-page="currentPage4"
                  :page-sizes="[100, 200, 300, 400]"
                  :page-size="100"
                  layout="total, sizes, prev, pager, next, jumper"
                  :total="400">
                </el-pagination>
            </div>
        </div>
    </div>
</template>
<script>

    export default {
        name: 'rechargelog',
        data() {
            return {
                skin:'',
                currentPage4: 4,
                opkey: '',
                pickerOptions0: {
                    disabledDate(time) {
                        return time.getTime() < Date.now() - 8.64e7;
                    }
                },
                value1: '',
                tableData: [{
                    ordernumber: '20',
                    orderid: '123058',
                    time: '2016-12-26',
                    rechargemoney: 100,
                    beformoney: 100,
                    aftermoney: 10000
                },
                {
                    ordernumber: '20',
                    orderid: '123058',
                    time: '2016-12-26',
                    rechargemoney: 100,
                    beformoney: 100,
                    aftermoney: 10000
                },
                {
                    ordernumber: '20',
                    orderid: '123058',
                    time: '2016-12-26',
                    rechargemoney: 100,
                    beformoney: 100,
                    aftermoney: 10000
                }],
                options: [{
                    value: 'Option1',
                    label: 'Option1'
                },
                {
                    value: 'Option2',
                    label: 'Option2'
                },
                {
                    value: 'Option3',
                    label: 'Option3'
                },
                {
                    value: 'Option4',
                    label: 'Option4'
                },
                {
                    value: 'Option5',
                    label: 'Option5'
                }],
                value: ''
            }
        },
        components: {
            
        },
        methods: {
            handleSizeChange(val) {
                console.log(`每页 ${val} 条`);
            },
            handleCurrentChange(val) {
                console.log(`当前页: ${val}`);
            },
            searchFun() {

            },
            exportFun() {

            }
        },
        created:function(){
            var self=this
            //换肤
            self.$http.post("mshop/admin/index.php?m=setting&a=get_shop_skin", {
            })
            .then(res => {
            self.skin=res.data.skin
            }).catch(err => {
            console.log(err)
            })

        }
    }
</script>

<style scoped>
/*@import '../../assets/css/comstyleone.css'*/
</style>