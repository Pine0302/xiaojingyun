<template>
    <div class="lineitem">
        <div class="WSY_content">
            <div class="WSY_columnbox">
                <div class="WSY_remind_main">
                    <div class="lineitem_top">
                        <div class="lineitem_topbox">
                            <div class="lineitem_title">订单信息</div>
                            <ul>
                                <li class="lineitem_li">订单号：{{orderid}}({{status}})</li>
                                <li class="lineitem_li">下单时间：{{ordertime}}</li>
                                <li class="lineitem_li">支付方式：{{type}}</li>
                                <li class="lineitem_li">支付时间：{{paytime}}</li>
                                <li class="lineitem_li">订单金额（含运费）：{{ordermoney}}</li>
                                <li class="lineitem_li">实付款：{{paymoney}}</li>
                                <li class="lineitem_li">订单备注：{{orderdes}}</li>
                                <li class="lineitem_li">使用商城积分：{{score}}</li>
                            </ul>
                        </div>
                        <div class="lineitem_topbox">
                            <div class="lineitem_title">收货人<span class="WSY_small_btn" @click="showAlertV1=true">修改收货地址</span></div>
                            <div class="alert_box" v-show="showAlertV1">
                                <div class="alert_title_line">
                                    <span>修改收件地址</span>
                                    <div class="WSY_cancle" @click="showAlertV1=false">×</div>
                                </div>
                                <ul class="alert_ul">
                                    <li class="alert_li">
                                        <div class="alert_li_left">收件人姓名</div>
                                        <div class="alert_li_right">
                                            <input type="text" name="">
                                        </div>
                                    </li>
                                    <li class="alert_li">
                                        <div class="alert_li_left">收件人手机</div>
                                        <div class="alert_li_right">
                                            <input type="text" name="">
                                        </div>
                                    </li>
                                    <li class="alert_li">
                                        <div class="alert_li_left">省级</div>
                                        <div class="alert_li_right">
                                            <select v-model="prov">
                                                <option v-for="option in arr" :value="option.name">
                                                    {{ option.name }}
                                                </option>
                                            </select>
                                        </div>
                                    </li>
                                    <li class="alert_li">
                                        <div class="alert_li_left">市级</div>
                                        <div class="alert_li_right">
                                            <select v-model="city">
                                                <option v-for="option in cityArr" :value="option.name">
                                                    {{ option.name }}
                                                </option>
                                            </select>
                                        </div>
                                    </li>
                                    <li class="alert_li">
                                        <div class="alert_li_left">区/县/镇</div>
                                        <div class="alert_li_right">
                                            <select v-model="district" v-if="district">
                                                <option v-for="option in districtArr" :value="option.name">
                                                    {{ option.name }}
                                                </option>
                                            </select>
                                        </div>
                                    </li>
                                    <li class="alert_li">
                                        <div class="alert_li_left">详细地址</div>
                                        <div class="alert_li_right">
                                            <input type="text" name="">
                                        </div>
                                    </li>
                                    <li class="alert_li alert_btn_line">
                                        <span class="WSY_small_btn" :class="skin">确定</span>
                                        <span class="WSY_small_btn" :class="skin">取消</span>
                                    </li>
                                    <!-- <select v-model="prov">
                                        <option v-for="option in arr" :value="option.name">
                                            {{ option.name }}
                                        </option>
                                    </select>
                                    <select v-model="city">
                                        <option v-for="option in cityArr" :value="option.name">
                                            {{ option.name }}
                                        </option>
                                    </select>
                                    <select v-model="district" v-if="district">
                                        <option v-for="option in districtArr" :value="option.name">
                                            {{ option.name }}
                                        </option>
                                    </select> -->
                                </ul>
                            </div>
                            <ul>
                                <li class="lineitem_li">姓名：{{name}}</li>
                                <li class="lineitem_li">手机号码：{{phone}}</li>
                                <li class="lineitem_li">收货地址：{{address}}</li>
                                <li class="lineitem_li">微信号：{{ictrip}}</li>
                            </ul>
                        </div>
                        <div class="lineitem_topbox">
                            <div class="lineitem_title">物流信息<span class="WSY_small_btn" @click="showAlertV2=true">修改快递信息</span></div>
                            <div class="alert_box alert_box2" v-show="showAlertV2">
                                <div class="alert_title_line">
                                    <span></span>
                                    <div class="WSY_cancle" @click="showAlertV2=false">×</div>
                                </div>
                                <ul class="alert_ul">
                                    <li class="alert_li">
                                            <select v-model="prov">
                                                <option v-for="option in arr" :value="option.name">
                                                    {{ option.name }}
                                                </option>
                                            </select>
                                    </li>
                                    <li class="alert_li">
                                        <input type="text" placeholder="快递单号" name="">
                                    </li>
                                    <li class="alert_li">
                                        <input type="text" placeholder="物流备注" name="">
                                    </li>
                                    <li class="alert_li alert_btn_line">
                                        <span class="WSY_small_btn" :class="skin">确定</span>
                                        <span class="WSY_small_btn" :class="skin">取消</span>
                                    </li>
                                </ul>
                            </div>
                            <ul>
                                <li class="lineitem_li">物流公司：{{logisticsCompany}}</li>
                                <li class="lineitem_li">快递单号：{{trackingNumber}}</li>
                                <li class="lineitem_li">发货时间：{{deliveryTime}}</li>
                                <li class="lineitem_li">物流备注：{{logisticsNote}}</li>
                            </ul>
                        </div>
                    </div>
                    <hr/>
                    <p class="tab_title">产品信息</p>
                    <table width="97%" class="WSY_table">
                        <thead class="WSY_table_header" :class="skin">
                            <th width="5%">产品编码</th>
                            <th width="10%">产品名称</th>
                            <th width="10%">产品分类</th>
                            <th width="10%">属性</th>
                            <th width="10%">现价</th>
                            <th width="18%">数量</th>
                            <th width="18%">合计</th>
                        </thead>
                        <tbody>
                            <tr v-for="item in tableData">
                                <td>
                                    <p>ID：{{item.ordernumber}}</p>
                                </td>
                                <td>{{item.name}}</td>
                                <td>{{item.type}}</td>
                                <td>{{item.property}}</td>
                                <td>{{item.price}}</td>
                                <td>{{item.num}}</td>
                                <td>{{item.total}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="tab_title">操作日志</p>
                    <table width="97%" class="WSY_table">
                        <thead class="WSY_table_header" :class="skin">
                            <th width="5%">序号</th>
                            <th width="10%">时间</th>
                            <th width="10%">操作</th>
                            <th width="10%">操作人</th>
                            <th width="18%">操作描述</th>
                        </thead>
                        <tbody>
                            <tr v-for="item in tableData2">
                                <td>{{item.ordernumber}}</td>
                                <td>{{item.time}}</td>
                                <td>{{item.operat}}</td>
                                <td>{{item.operatman}}</td>
                                <td>{{item.operatdes}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
// import '../../assets/js/addressData.js'
import addressData from "../../assets/js/addressData.js";

export default {
  name: "lineitem",
  data: function() {
    return {
      skin: "",
      showAlertV1: false,
      showAlertV2: false,
      orderid: 11,
      status: "代发货",
      ordertime: "2016-12-12  23:23：23",
      type: "支付宝",
      paytime: "2016-12-12  23:23：23",
      ordermoney: "￥8790",
      paymoney: "￥8790",
      orderdes: "",
      score: 144,
      name: "ji",
      phone: 12255666,
      address: "",
      ictrip: "",
      logisticsCompany: "",
      trackingNumber: 122,
      deliveryTime: "",
      logisticsNote: "",

      arr: addressData,
      prov: "北京",
      city: "北京",
      district: "东城区",
      cityArr: [],
      districtArr: [],
      tableData: [
        {
          ordernumber: "20",
          name: "会",
          type: "服装",
          property: "白色XL纯棉",
          price: 100,
          num: 10,
          total: 100
        },
        {
          ordernumber: "20",
          name: "会",
          type: "服装",
          property: "白色XL纯棉",
          price: 100,
          num: 10,
          total: 100
        },
        {
          ordernumber: "20",
          name: "会",
          type: "服装",
          property: "白色XL纯棉",
          price: 100,
          num: 10,
          total: 100
        }
      ],
      tableData2: [
        {
          ordernumber: "20",
          time: "2016-12-26  16:51:23",
          operat: "兑换比例",
          operatman: "aa",
          operatdes: "兑换比例：100:1"
        },
        {
          ordernumber: "20",
          time: "2016-12-26  16:51:23",
          operat: "兑换比例",
          operatman: "aa",
          operatdes: "兑换比例：100:1"
        },
        {
          ordernumber: "20",
          time: "2016-12-26  16:51:23",
          operat: "兑换比例",
          operatman: "aa",
          operatdes: "兑换比例：100:1"
        }
      ]
    };
  },
  created: function() {
    var self = this;
    //换肤
    self.$http
      .post("mshop/admin/index.php?m=setting&a=get_shop_skin", {})
      .then(res => {
        self.skin = res.data.skin;
      })
      .catch(err => {
        console.log(err);
      });
  },
  methods: {
    updateCity: function() {
      for (var i in this.arr) {
        var obj = this.arr[i];
        if (obj.name == this.prov) {
          this.cityArr = obj.sub;
          break;
        }
      }
      this.city = this.cityArr[1].name;
    },
    updateDistrict: function() {
      for (var i in this.cityArr) {
        var obj = this.cityArr[i];
        if (obj.name == this.city) {
          this.districtArr = obj.sub;
          break;
        }
      }
      if (
        this.districtArr &&
        this.districtArr.length > 0 &&
        this.districtArr[1].name
      ) {
        this.district = this.districtArr[1].name;
      } else {
        this.district = "";
      }
    }
  },
  beforeMount: function() {
    this.updateCity();
    this.updateDistrict();
  },
  watch: {
    prov: function() {
      this.updateCity();
      this.updateDistrict();
    },
    city: function() {
      this.updateDistrict();
    }
  }
};
</script>
<style type="text/css">
/*@import '../../assets/css/comstyleone.css'*/
</style>
<style scoped>
.WSY_remind_main {
  min-height: 500px;
}
.lineitem_top {
  display: flex;
  font-size: 0;
  justify-content: space-around;
  margin-left: 30px;
  margin-top: 20px;
}
.WSY_remind_main .lineitem_topbox {
  line-height: 30px;
  width: 33.33%;
  position: relative;
}
.WSY_remind_main .lineitem_topbox ul li {
  font-size: 14px;
}
.WSY_remind_main .lineitem_topbox .lineitem_title {
  font-size: 16px;
  font-weight: 600;
  display: flex;
  justify-content: space-between;
}
.WSY_remind_main .lineitem_topbox .lineitem_title .WSY_small_btn {
  height: 26px;
  display: inline-block;
  vertical-align: middle;
  background-color: #06a7e1;
  border: #06a7e1 1px solid;
  border-radius: 2px;
  line-height: 26px;
  padding: 0 15px;
  color: #fff;
  cursor: pointer;
  margin-right: 30px;
}
.tab_title {
  font-size: 16px;
  font-weight: 600;
  margin-left: 20px;
  margin-top: 20px;
}

.alert_box {
  font-size: 0;
  width: 300px;
  padding: 15px 15px;
  background-color: #ffffff;
  box-shadow: 0px 3px 6px 0px rgb(193, 193, 193);
  border: 1px solid #999;
  position: absolute;
  top: 32px;
  right: 10px;
}
.alert_box2 {
  width: 210px;
}
.alert_box2 .WSY_small_btn {
  margin-left: 0;
}
.alert_box .alert_title_line {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.alert_box .alert_title_line span {
  font-size: 16px;
  font-weight: 600;
}
.alert_box .alert_title_line .WSY_cancle {
  font-size: 25px;
  width: 20px;
  height: 20px;
  background: #06a7e1;
  text-align: center;
  line-height: 20px;
  color: #fff;
  border-radius: 4px;
  cursor: pointer;
}
.alert_box .alert_ul .alert_li {
  font-size: 0;
  display: flex;
  margin-top: 10px;
}
.alert_box .alert_ul .alert_li .alert_li_left {
  font-size: 14px;
  width: 90px;
}
.alert_box .alert_ul .alert_li .alert_li_right {
  font-size: 14px;
  flex: 1;
}
.alert_box .alert_ul .alert_li input,
.alert_box .alert_ul .alert_li select,
.alert_box .alert_ul .alert_li .alert_li_right input,
.alert_box .alert_ul .alert_li .alert_li_right select {
  width: 200px;
  height: 24px;
  border: 1px solid #dddddd;
  border-radius: 2px;
  box-sizing: border-box;
  padding: 0 0;
  padding-left: 5px;
}
.alert_box .alert_ul .alert_li .WSY_small_btn {
  height: 26px;
  display: inline-block;
  vertical-align: middle;
  background-color: #06a7e1;
  border: #06a7e1 1px solid;
  border-radius: 2px;
  line-height: 26px;
  padding: 0 15px;
  color: #fff;
  cursor: pointer;
  margin-right: 30px;
}
.alert_box .alert_ul .alert_li.alert_btn_line {
  text-align: right;
  display: flex;
  justify-content: flex-end;
  margin-right: -20px;
}
.alert_box2 .alert_ul .alert_li {
  margin-top: 17px;
}
</style>