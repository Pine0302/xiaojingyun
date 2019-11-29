<template>
    <div class="ordermanagement">
        <div class="WSY_content">
            <div class="WSY_columnbox">
                <div class="WSY_remind_main">
                    <div class="WSY_column_header">
                        <div class="tab_btn_box">
                            <div class="tab_btn" v-for="(item,index) in tabs" @click="tabFun(index)" v-bind:class="{tab_btn_bg : index==tabbg}">
                            {{item}}</div>
                        </div>
                    </div>
                    <div class="WSY_search_q">
                        <div class="WSY_search_div">
                            <div class="date_search">
                                <div class="com_date">
                                    <span class="demonstration">下单时间</span>
                                    <el-date-picker v-model="value1" type="date" placeholder="选择日期" :picker-options="pickerOptions0"></el-date-picker>
                                </div>
                                <div class="com_date">
                                    <span class="demonstration" style="margin-left:15px;">至</span>
                                    <el-date-picker v-model="value1" type="date" placeholder="选择日期" :picker-options="pickerOptions0"></el-date-picker>
                                </div>
                            </div>
                            <div class="WSY_iptbox">
                                <span>订单号</span>
                                <input type="text" name="opkey" placeholder="" maxlength="10" v-model="opkey"></div>
                            <div class="WSY_iptbox">
                                <span>产品名称</span>
                                <input type="text" name="opkey" placeholder="" maxlength="10" v-model="opkey"></div>
                            <div class="WSY_select_box">
                                <span>支付方式</span>
                                <el-select v-model="value" placeholder="--请选择--" class="WSY_elselect">
                                    <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"></el-option>
                                </el-select>
                            </div>
                            <div class="WSY_iptbox">
                                <span>收货人姓名</span>
                                <input type="text" name="opkey" placeholder="" maxlength="10" v-model="opkey"></div>
                            <div class="WSY_iptbox">
                                <span>手机号</span>
                                <input type="text" name="opkey" placeholder="" maxlength="10" v-model="opkey"></div>
                            <div class="WSY_small_btn" :class="skin" @click="searchFun">搜索</div>
                            <div class="WSY_small_btn" :class="skin" @click="exportFun">导出</div></div>
                    </div>

                    <!-- <el-table ref="multipleTable" :data="tableData3" border tooltip-effect="dark" style="width: 97%" @selection-change="handleSelectionChange" class="WSY_table">
                        <el-table-column type="selection" width="55"></el-table-column>
                        <el-table-column label="日期" width="120">
                            <template scope="scope">{{ scope.row.date }}</template></el-table-column>
                        <el-table-column prop="name" label="姓名" width="120"></el-table-column>
                        <el-table-column prop="address" label="地址" show-overflow-tooltip></el-table-column>
                    </el-table> -->

                    <table width="97%" class="WSY_table">
                        <thead class="WSY_table_header" :class="skin">
                            <th width="5%">全选</th>
                            <th width="10%">产品</th>
                            <th width="10%">支付凭证</th>
                            <th width="13%">收货人</th>
                            <th width="8%">订单金额</th>
                            <th width="5%">订单状态</th>
                            <th width="8%">邀请人</th>
                            <th width="8%">操作</th>
                        </thead>
                        <tbody>
                            <tr v-for="item in tableData">
                                <td>
                                    <input type="checkbox" v-model="item.checkboxGroup">
                                </td>
                                <td>
                                    <div class="tab_top">编号：{{item.num}}</div>
                                    <div class="tab_bottom">
                                        <img src="">
                                        <div class="tab_property">{{item.property}}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tab_top">{{item.time}}{{item.inout}}</div>
                                    <div class="tab_bottom">
                                        <img src="">
                                    </div>
                                </td>
                                <td>
                                    <div class="tab_top">支付：11111111（微信）  支付时间1207-10-02-22</div>
                                    <div class="tab_bottom">啊啊啊(刘嘉豪) （代理商）    13588888888</div>
                                </td>
                                <td>
                                    <div class="tab_top">{{item.price}}</div>
                                    <div class="tab_bottom">¥1.00元（免邮）</div>
                                </td>
                                <td>
                                    <div class="tab_top"></div>
                                    <div class="tab_bottom">{{item.status}}</div>
                                </td>
                                <td>
                                    <div class="tab_top"></div>
                                    <div class="tab_bottom">{{item.man}}</div>
                                </td>
                                <td class="WSY_t4">
                                    <div class="tab_top"></div>
                                    <div class="tab_bottom">
                                        <a title="查看">
                                        <img src="../../assets/images_V6.0/operating_icon/icon44.png"></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                </div>
            </div>
        </div>
    </div>
</template>
<script>

    export default {
        name: 'ordermanagement',
        data: function() {
            return {
                skin:'',
                tabbg: 0,
                tabs:["所有订单","待签收","待付款","待发货","已发货","待完成","交易完成","已关闭"],
                tabbg : true,
                tableData: [{
                    checkboxGroup: [],
                    num: 122,
                    property: '',
                    time: '',
                    inout: '',
                    price: '',
                    status: '',
                    man: ''
                }, {
                    checkboxGroup: [],
                    num: 122,
                    property: '',
                    time: '',
                    inout: '',
                    price: '',
                    status: '',
                    man: ''
                },{
                    checkboxGroup: [],
                    num: 122,
                    property: '',
                    time: '',
                    inout: '',
                    price: '',
                    status: '',
                    man: ''
                }],
                multipleSelection: [],
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
                    time: '2016-12-26  16:51:23',
                    operat: '兑换比例',
                    operatman: 'aa',
                    operatdes: '兑换比例：100:1'
                },
                {
                    ordernumber: '20',
                    time: '2016-12-26  16:51:23',
                    operat: '兑换比例',
                    operatman: 'aa',
                    operatdes: '兑换比例：100:1'
                },
                {
                    ordernumber: '20',
                    time: '2016-12-26  16:51:23',
                    operat: '兑换比例',
                    operatman: 'aa',
                    operatdes: '兑换比例：100:1'
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
        methods: {
            totalOrder() {
                
            },
            toSign() {
                
            },
            tabFun(index) {
                console.log(index);
                this.tabbg=index;
            },
            searchFun() {
                
            },
            exportFun() {
                
            },
            toggleSelection(rows) {
                if (rows) {
                  rows.forEach(row => {
                    this.$refs.multipleTable.toggleRowSelection(row);
                  });
                } else {
                  this.$refs.multipleTable.clearSelection();
                }
              },
              handleSelectionChange(val) {
                this.multipleSelection = val;
              }
        },
        created: function() {
            var self=this
            self.tabbg=0;
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
<style type="text/css">
/*@import '../../assets/css/comstyleone.css'*/
/*.ordermanagement .WSY_table td{
    padding: 0;
    text-align: center;
}
.ordermanagement .el-table__body{
    border-collapse:collapse; 
    border-spacing:0; 
}
.el-table_1_column_1{
    border-collapse:collapse; 
    border-spacing:0; 
}*/
</style>
<style scoped>
.tab_btn_box{
    display: block;
    font-size: 0;
    line-height: 39px;
}
.tab_btn_box .tab_btn{
    display: inline-block;
    line-height: 37px;
    padding-left: 15px;
    padding-right: 15px;
    font-size: 14px;
    color: #646464;
    cursor: pointer;
}
.tab_btn_box .tab_btn:hover,
.tab_btn_bg{
    background-color: #fff;
    border-bottom: solid 2px #06a7e1;
}
.WSY_search_div{
    margin-bottom: 20px;
}
.WSY_search_div .date_search,
.WSY_search_div .WSY_iptbox,
.WSY_search_div .WSY_select_box,
.WSY_search_div .WSY_small_btn
{
    margin-top: 10px;
}
</style>