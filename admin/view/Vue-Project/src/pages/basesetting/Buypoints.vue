<template>
    <div class="buypoints">
        <div class="WSY_content">
            <div class="WSY_columnbox">
                <div class="WSY_search_q">
                    <div class="WSY_search_div">
                        <div class="WSY_iptbox">
                            <span>用户名称</span>
                            <input type="text" name="naming" placeholder="" maxlength="10" v-model="naming"></div>
                        <div class="WSY_iptbox">
                            <span>用户编码</span>
                            <input type="text" name="code" placeholder="" maxlength="10" v-model="code"></div>
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
                        <div class="WSY_small_btn" :class="skin" @click="searchFun">搜索</div>
                        <div class="WSY_small_btn" :class="skin" @click="exportFun">导出</div></div>
                </div>
                <table width="97%" class="WSY_table">
                    <thead class="WSY_table_header" :class="skin">
                        <th width="5%">序号</th>
                        <th width="8%">用户头像</th>
                        <th width="5%">用户编码</th>
                        <th width="8%">用户名称</th>
                        <th width="8%">积分余额</th>
                        <th width="5%">累计充值积分</th>
                        <th width="5%">操作</th></thead>
                    <tbody>
                        <tr v-for="item in tableData">
                            <td>{{item.ordernumber}}</td>
                            <td>
                                <img src=""></td>
                            <td>{{item.encoding}}</td>
                            <td>{{item.name}}</td>
                            <td>{{item.balance}}</td>
                            <td>{{item.totalrecharge}}</td>
                            <td class="WSY_t4">
                                <a title="查看">
                                    <img src="../../assets/images_V6.0/operating_icon/icon44.png"></a>
                                <a title="充值">
                                    <img src="../../assets/images_V6.0/operating_icon/icon51.png"></a>
                            </td>
                        </tr>
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
    name: 'buypoints',
    data() {
        return {
            skin:'',
            currentPage4: 4,
            naming: '',
            code: '',
            pickerOptions0: {
                disabledDate(time) {
                    return time.getTime() < Date.now() - 8.64e7;
                }
            },
            value1: '',
            tableData: [
                {
                    ordernumber: '20',
                    img: 'Tom',
                    encoding: '235',
                    name: '名称',
                    balance: '100',
                    totalrecharge: '233'
                },
                {
                    ordernumber: '20',
                    img: 'Tom',
                    encoding: '235',
                    name: '名称',
                    balance: '100',
                    totalrecharge: '233'
                },
                {
                    ordernumber: '20',
                    img: 'Tom',
                    encoding: '235',
                    name: '名称',
                    balance: '100',
                    totalrecharge: '233'
                }
            ],
            options: [
                {
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
                }
            ],
            value: ''
        }
    },
    components:{

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