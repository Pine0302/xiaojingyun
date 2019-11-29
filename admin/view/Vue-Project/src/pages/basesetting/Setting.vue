<template>
  <div class="setting">
    <div class="WSY_content">
      <div class="WSY_columnbox">
        <div class="WSY_remind_main">
          <dl class="WSY_remind_dl02">
            <dt>{{shopintegralname}}开关：</dt>
            <dd>
              <el-switch on-text="关" off-text="开" on-color="#bfcbd9" off-color="#FF7170" on-value="0" off-value="1" v-model="shop_onoff"></el-switch>
              <el-tooltip placement="right">
                <div slot="content">提示：默认适用范围线上商城商品管理的商品，结算后才派发；
                  <br/>参与拼团、众筹、砍价等营销活动的产品不能参与积分活动</div>
                <el-button class="hint_img"></el-button>
              </el-tooltip>
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>自定义积分命名：</dt>
            <dd>
              <input type="text" name="naming" placeholder="商城积分" maxlength="6" v-model="integral_name">
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>赠送设置：</dt>
            <dd>
              <div class="WSY_dd_div">
                <div class="WSY_dd_div_left">
                  <input type="radio" name="donateRadio" v-model="gift_set_type" value="0">
                  <span>按比例</span>
                </div>
                <div class="WSY_dd_div_left">
                  <input type="radio" name="donateRadio" v-model="gift_set_type" value="1">
                  <span>按固定积分</span>
                </div>
              </div>
              <div class="WSY_dd_div">
                <input class="WSY_small_ipt" type="number" name="percentage" maxlength="10" v-model="gift_set_value1" v-show="gift_set_type==0" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^0-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <span v-show="gift_set_type==0">%</span>
                <input class="WSY_small_ipt" type="number" name="fixed" maxlength="10" v-model="gift_set_value2" v-show="gift_set_type==1" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^0-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <span v-show="gift_set_type==1">积分</span>
              </div>
            </dd>
          </dl>
          <!-- <dl class="WSY_remind_dl02">
            <dt>积分兑换比例：</dt>
            <dd>
              <input class="WSY_small_ipt" type="number" maxlength="10" v-model="conversion_ratio_integral">
              <span class="margind">：</span>
              <input class="WSY_small_ipt" type="number" maxlength="10" v-model="conversion_ratio_price">
              <span class="WSY_des_span">1000积分=1元</span>
            </dd>
          </dl> -->
          <!-- <dl class="WSY_remind_dl02">
                        <dt>是否参与分佣：</dt>
                        <dd>
                            <div class="WSY_dd_div">
                                <div class="WSY_dd_div_left">
                                    <input type="radio" name="centRadio" v-model="is_commission" value="0">
                                    <span>是</span></div>
                                <div class="WSY_dd_div_left">
                                    <input type="radio" name="centRadio" v-model="is_commission" value="1">
                                    <span>否</span></div>
                            </div>
                        </dd>
                    </dl> -->
          <dl class="WSY_remind_dl02">
            <dt>清除积分时间：</dt>
            <dd>
              <!-- <el-date-picker v-model="clear_integral_time" type="datetime" placeholder="选择日期时间" class="WSY_elipt" format="MM-dd HH:mm"></el-date-picker> -->
              <el-date-picker v-model="clear_integral_time" type="datetime" placeholder="选择日期时间" class="WSY_elipt" format="MM-dd HH:mm" :picker-options="pickerBeginDateBefore"></el-date-picker>
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>清除积分通知：</dt>
            <dd class="cleartime">
              <div class="qiandaosetbox">
                <div class="WSY_plus absolute2" @click="addTime1()">+</div>
                <div class="absolute3">
                  <el-tooltip placement="right">
                    <div slot="content">提示：通知时间要比清除时间提前至少一天,系统默认提前10天通知一次，12点会自动通知</div>
                    <el-button class="hint_img"></el-button>
                  </el-tooltip>
                </div>
                <div class="qiandaoline" v-for="(item,index) in clearTime">
                  <span class="demonstration" style="margin-left:15px;">提前</span>
                  <input type="number" v-model="item.ahead_days" @change="compareFun(index,$event.target.value)" class="clearti" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                  <span class="demonstration" style="margin-left:15px;">天</span>
                  <span class="demonstration" style="margin-left:15px;">时间</span>
                  <el-time-picker v-model="item.notice_time" placeholder="任意时间点" format="HH:mm" class="cleartisel">
                  </el-time-picker>
                  <div class="WSY_minus" @click="deleteTime1(index)" v-show="clearTime.length!=1">-</div>
                </div>
              </div>
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>订单售后/维权开关：</dt>
            <dd>
              <el-switch on-text="关" off-text="开" on-color="#bfcbd9" off-color="#FF7170" on-value="0" off-value="1" v-model="aftersale_onoff"></el-switch>
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>奖励积分开关：</dt>
            <dd>
              <el-switch on-text="关" off-text="开" on-color="#bfcbd9" off-color="#FF7170" on-value="0" off-value="1" v-model="reward_onoff"></el-switch>
              <el-tooltip placement="right">
                <div slot="content">提示：一次性奖励，即时到积分账单上</div>
                <el-button class="hint_img"></el-button>
              </el-tooltip>
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>用户关注公众号，奖励：</dt>
            <dd>
              <div class="WSY_dd_div">
                <input type="number" placeholder="" maxlength="10" v-model="focus_reward" value="" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^0-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <span>积分</span>
                <span style="margin-left: 20px;">推荐人</span>
                <input type="number" placeholder="" maxlength="10" v-model="referrer_focus_reward" value="" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^0-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <span>积分</span>
              </div>
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>用户绑定手机，奖励：</dt>
            <dd>
              <div class="WSY_dd_div">
                <input type="number" placeholder="" maxlength="10" v-model="bind_phone_reward" value="" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^0-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <span>积分</span>
                <span style="margin-left: 20px;">推荐人</span>
                <input type="number" placeholder="" maxlength="10" v-model="referrer_bind_phone_reward" value="" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^0-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <span>积分</span>
              </div>
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>用户首单，奖励：</dt>
            <dd>
              <div class="WSY_dd_div">
                <div class="WSY_dd_div_left">
                  <input type="radio" name="firstOrderRadio" value="1" v-model="first_order_reward_type">
                  <span>按比例</span>
                </div>
                <div class="WSY_dd_div_left">
                  <input type="radio" name="firstOrderRadio" value="2" v-model="first_order_reward_type">
                  <span>按固定</span>
                </div>
              </div>
              <div class="WSY_dd_div">
                <input type="number" placeholder="" maxlength="10" v-model="first_order_reward" value="" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^0-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <span v-show="first_order_reward_type==2">积分</span>
                <span v-show="first_order_reward_type==1">%</span>
                <span style="margin-left: 20px;">推荐人</span>
                <input type="number" placeholder="" maxlength="10" v-model="referrer_first_order_reward" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^0-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <span v-show="first_order_reward_type==2">积分</span>
                <span v-show="first_order_reward_type==1">%</span>
              </div>
            </dd>
          </dl>
          <div class="submit_div">
            <Bigbutton btnval="保存" @click.native.prevent="commit"></Bigbutton>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Bigbutton from '../../components/Bigbutton'
import '../../assets/js/jquery-1.12.1.min.js'
import util from '../../utils'

export default {
  name: 'setting',
  data: function() {
    return {
      shopintegralname: '',
      storeintegralname: '',
      customname: '',
      shop_onoff: "0",
      reward_onoff: "0",
      aftersale_onoff: "0",
      "integral_name": "", //积分命名
      "gift_set_type": [], //赠送设置（类型：1、按比例，2按固定积分）
      "gift_set_value1": "",
      "gift_set_value2": "",
      // "gift_set_value": [],//赠送设置（值：类型1为比例，类型2为积分）
      "conversion_ratio_integral": "", //积分兑换比例（积分）
      "conversion_ratio_price": "", //积分兑换比例（金额）
      // "is_commission": [],//是否参与分佣
      "clear_integral_time": '', //清除积分时间
      pickerBeginDateBefore: {
        disabledDate(time) {
          var startDataTime = 2 + "-" + 1 + " " + 0 + ":" + 0 + ":" + 0;
          var endDataTime = 12 + "-" + 31 + " " + 23 + ":" + 59 + ":" + 59;
          var sd = new Date(startDataTime);
          var ed = new Date(endDataTime);
          return time.getTime() < sd;
        }
      },
      "clearTime": [{
        "ahead_days": "",
        "notice_time": ""
      }],
      "focus_reward": "", //新用户关注公众号奖励
      "referrer_focus_reward": "", //新用户关注公众号 推荐人奖励
      "bind_phone_reward": "", //新用户绑定手机奖励
      "referrer_bind_phone_reward": "", //新用户绑定手机 推荐人奖励
      "first_order_reward_type": [], //新用户首单奖励类型：1,、比例；2、固定积分
      "first_order_reward": "", //新用户首单奖励（1、比例，2、积分）
      "referrer_first_order_reward": "" //新用户首单 推荐人奖励（1、比例，2、积分）
    }
  },
  components: {
    Bigbutton
  },
  watch: {},
  methods: {
    getDiyname(){
      var self = this;
      var urlget = 'mshop/admin/index.php?m=activity&a=get_diyname&customer_id=' + self.customer_id
      self.$http.post(urlget, {
        "system_code": "",
        data: ''
      })
      .then(res => {
        // if(res.data.errcode==0){
        //  console.log(res.data.errmsg);
        // }
        var datajsonget = res.data.data;
        // console.log(datajsonget)
        if (datajsonget.shop_integral_name) {
          self.shopintegralname = datajsonget.shop_integral_name;
        }
        if (datajsonget.store_integral_name) {
          self.storeintegralname = datajsonget.store_integral_name;
        }
        if (datajsonget.custom_name) {
          self.customname = datajsonget.custom_name;
        }
      }).catch(err => {
        console.log(err)
      })
    },
    commit() {
      var self = this;
      var gift_set_value = "";
      if (self.gift_set_type == 0) {
        gift_set_value = self.gift_set_value1;
      } else {
        gift_set_value = self.gift_set_value2;
      }
      var cleaheaddaysAry = new Array();
      var clenoticetimeAry = new Array();
      $.each(self.clearTime, function(i, n) {
        cleaheaddaysAry.push(n.ahead_days);
        clenoticetimeAry.push(self.formatDateTime3(n.notice_time, "HH:mm"));
      });
      var aryy = cleaheaddaysAry;
      var naryy = aryy.sort();
      var errcle = false;
      for (var i = 0; i < aryy.length; i++) {
        if (naryy[i] == naryy[i + 1]) {
          checkdate(clenoticetimeAry[i], clenoticetimeAry[i + 1]);
        }
      }

      function checkdate(t1, t2) {
        var t11 = t1.split(":");
        var t21 = t2.split(":");
        var sj1 = parseInt(t11[0]) * 12 + t11[1];
        var sj2 = parseInt(t21[0]) * 12 + t21[1]
        if (sj1 >= sj2) {
          alert("清除积分通知时间设置有误，请按照递增规则填写！");
          errcle = true;
          return false;
        }
        return true;
      }
      var time = [];
      var selfarrclearTime = [];
      var encleset = false;
      $.each(self.clearTime, function(i, n) {
        if (n.notice_time == "NaN-NaN-NaN NaN:NaN:NaN" || n.ahead_days == "") {
          encleset = false;
          alert("清除积分通知时间设置不能为空！");
          return;
        } else {
          encleset = true;
        }
        selfarrclearTime.push({ "ahead_days": n.ahead_days, "notice_time": util.formatDateTimeFil(n.notice_time, "yyyy-MM-dd HH:mm:ss") });
      });
      var clearTimePost = selfarrclearTime;
      var clear_integral_notice = new Object();
      for (var i = 0; i < clearTimePost.length; i++) {
        time.push(("time" + (i + 1)));
        clear_integral_notice[time[i]] = clearTimePost[i];
      }
      var monClearTime = "";
      if (self.clear_integral_time != "") {
        monClearTime = new Date(self.clear_integral_time).getMonth() + 1;
        if (monClearTime <= 1) {
          alert('清除积分请选择在2月到12月的时段内！');
          return;
        } else {
          // console.log('时间合理')
        }
      }
      //判空
      if (
        self.enptyComFun(self.integral_name, '积分命名不能为空！') == false ||
        self.enptyComFun(gift_set_value, '赠送设置不能为空！') == false ||
        self.enptyComFun(self.gift_set_type, '赠送设置不能为空！') == false ||
        self.enptyComFun(self.clear_integral_time, '清除积分时间不能为空！') == false ||
        self.enptyComFun(self.focus_reward, '新用户关注公众号奖励积分不能为空！') == false ||
        self.enptyComFun(self.referrer_focus_reward, '新用户关注公众号推荐人积分不能为空！') == false ||
        self.enptyComFun(self.bind_phone_reward, '新用户绑定手机奖励积分不能为空！') == false ||
        self.enptyComFun(self.referrer_bind_phone_reward, '新用户绑定手机推荐人积分不能为空！') == false ||
        self.enptyComFun(self.first_order_reward, '新用户首单奖励积分不能为空！') == false ||
        self.enptyComFun(self.first_order_reward_type, '新用户首单奖励积分不能为空！') == false ||
        self.enptyComFun(self.referrer_first_order_reward, '新用户首单推荐人积分不能为空！') == false ||
        errcle == true ||
        encleset == false
      ) {
        return;
      }
      var datajson = {
        "shop_onoff": self.shop_onoff, //购物积分开关
        "reward_onoff": self.reward_onoff, //奖励积分开关
        aftersale_onoff: self.aftersale_onoff,
        "basic_json": {
          "integral_name": self.integral_name, //积分命名
          "gift_set_type": self.gift_set_type, //赠送设置（类型：1、按比例，2按固定积分）
          "gift_set_value": gift_set_value, //赠送设置（值：类型1为比例，类型2为积分）
          "clear_integral_time": util.formatDateTimeFil(self.clear_integral_time, "yyyy-MM-dd HH:mm:ss"), //清除积分时间
          "clear_integral_notice": clear_integral_notice,
          "focus_reward": self.focus_reward, //新用户关注公众号奖励
          "referrer_focus_reward": self.referrer_focus_reward, //新用户关注公众号 推荐人奖励
          "bind_phone_reward": self.bind_phone_reward, //新用户绑定手机奖励
          "referrer_bind_phone_reward": self.referrer_bind_phone_reward, //新用户绑定手机 推荐人奖励
          "first_order_reward_type": self.first_order_reward_type, //新用户首单奖励类型：1,、比例；2、固定积分
          "first_order_reward": self.first_order_reward, //新用户首单奖励（1、比例，2、积分）
          "referrer_first_order_reward": self.referrer_first_order_reward //新用户首单 推荐人奖励（1、比例，2、积分）
        }
      }
      var urlpost = "mshop/admin/index.php?m=activity&a=integral_setting&customer_id=" + self.customer_id;
      self.$http.post(urlpost, {
          "system_code": "",
          data: datajson
        })
        .then(res => {
          // this.$message({
          //   message: '保存成功！'
          //   // ,
          //   // type: 'success'
          // });
          alert("保存成功！");
        }).catch(err => {
          console.log(err)
        })
    },
    deleteTime1(index) {
      this.clearTime.splice(index, 1);
    },
    addTime1() {
      this.clearTime.push({
        "ahead_days": "", //提前天数
        "notice_time": "" //通知时间
      });
    },
    formatDateTime3(time, format) {
      var t = new Date(time);
      var tf = function(i) { return (i < 10 ? '0' : '') + i };
      return format.replace(/yyyy|MM|dd|HH|mm|ss/g, function(a) {
        switch (a) {
          case 'yyyy':
            return tf(t.getFullYear());
            break;
          case 'MM':
            return tf(t.getMonth() + 1);
            break;
          case 'mm':
            return tf(t.getMinutes());
            break;
          case 'dd':
            return tf(t.getDate());
            break;
          case 'HH':
            return tf(t.getHours());
            break;
          case 'ss':
            return tf(t.getSeconds());
            break;
        }
      })
    },
    compareFun(index, value) {
      var cleartimeerr = false;
      var self = this;
      if (index > 0) {
        var val = $(".clearti").eq(index - 1).val();
        if (value < val) {
          var alertText = "请输入大于等于" + val + "的天数";
          var thiscleartime = 1;
          self.$message({
            showClose: true,
            message: alertText,
            type: 'error',
            onClose: function() {
              $(".clearti").eq(index).val(parseFloat(val) + 1);
              thiscleartime = parseFloat(val) + 1;
              self.clearTime[index].ahead_days = thiscleartime;
            }
          });
        }
        // else if (value = val) {
        //  var tisel = $(".cleartisel").find(".el-input__inner").eq(index - 1).val();
        //  var alertText = "您填写的天数与上一条相同，时间请您选择小于" + tisel;
        //  this.$message({
        //    showClose: true,
        //    message: alertText,
        //    type: 'warning'
        //  });
        // }
      }
    },
    enptyComFun(obj, mes) {
      if (obj == "") {
        // this.$message({
        //   showClose: true,
        //   message: mes,
        //   type: 'error'
        // });
        alert(mes);
        return false;
      }
    }
  },
  created: function() {
    var self = this;
    // 共公接口—获取自定义名称（商城积分，门店积分，购物币）
    self.getDiyname();
    var urlget = 'mshop/admin/index.php?m=activity&a=integral_setting_details&customer_id=' + self.customer_id
    self.$http.post(urlget, {
        "system_code": "",
        data: ''
      })
      .then(res => {
        // if(res.data.errcode==0){
        //  console.log(res.data.errmsg);
        // }
        var datajsonget = res.data.data;
        if (datajsonget.shop_onoff) {
          self.shop_onoff = datajsonget.shop_onoff;
        } else {
          self.shop_onoff = "0";
        }
        if (datajsonget.reward_onoff) {
          self.reward_onoff = datajsonget.reward_onoff;
        } else {
          self.reward_onoff = "0";
        }
        if (datajsonget.aftersale_onoff) {
          self.aftersale_onoff = datajsonget.aftersale_onoff;
        } else {
          self.aftersale_onoff = "0";
        }
        if (datajsonget.basic_json != null) {
          if (datajsonget.basic_json.integral_name != null) {
            self.integral_name = datajsonget.basic_json.integral_name;
          }
          if (datajsonget.basic_json.gift_set_type != null) {
            self.gift_set_type = datajsonget.basic_json.gift_set_type;
          }
          if (datajsonget.basic_json.gift_set_type == 0 && datajsonget.basic_json.gift_set_value != null) {
            self.gift_set_value1 = datajsonget.basic_json.gift_set_value;
          } else if (datajsonget.basic_json.gift_set_type == 1 && datajsonget.basic_json.gift_set_value != null) {
            self.gift_set_value2 = datajsonget.basic_json.gift_set_value;
          }
          if (datajsonget.basic_json.conversion_ratio_integral != null) {
            self.conversion_ratio_integral = datajsonget.basic_json.conversion_ratio_integral;
          }
          if (datajsonget.basic_json.conversion_ratio_price != null) {
            self.conversion_ratio_price = datajsonget.basic_json.conversion_ratio_price;
          }
          // if(datajsonget.basic_json.is_commission!=null){
          //  self.is_commission=datajsonget.basic_json.is_commission;
          // }
          if (datajsonget.basic_json.clear_integral_time != null) {
            if (datajsonget.basic_json.clear_integral_time == "NaN-NaN-NaN NaN:NaN:NaN") {
              self.clear_integral_time = "";
            }else{
              self.clear_integral_time = datajsonget.basic_json.clear_integral_time;
            }
          }
          var clearTime = [];
          
          if (datajsonget.basic_json.clear_integral_notice != null) {
            $.each(datajsonget.basic_json.clear_integral_notice, function(i, n) {
              if (n.notice_time == "NaN-NaN-NaN NaN:NaN:NaN") {
                n.notice_time = "";
              }else if(/[a-zA-Z]+/.test(n.notice_time)){
                n.notice_time = n.notice_time.replace(/[a-zA-Z]|[:]|\s+|\s+/g,'');
              }
              clearTime.push(n);
            });
            self.clearTime = clearTime;
          }
          if (datajsonget.basic_json.focus_reward != null) {
            self.focus_reward = datajsonget.basic_json.focus_reward;
          }
          if (datajsonget.basic_json.referrer_focus_reward != null) {
            self.referrer_focus_reward = datajsonget.basic_json.referrer_focus_reward;
          }
          if (datajsonget.basic_json.bind_phone_reward != null) {
            self.bind_phone_reward = datajsonget.basic_json.bind_phone_reward;
          }
          if (datajsonget.basic_json.referrer_bind_phone_reward != null) {
            self.referrer_bind_phone_reward = datajsonget.basic_json.referrer_bind_phone_reward;
          }
          if (datajsonget.basic_json.first_order_reward_type != null) {
            self.first_order_reward_type = datajsonget.basic_json.first_order_reward_type;
          }
          if (datajsonget.basic_json.first_order_reward != null) {
            self.first_order_reward = datajsonget.basic_json.first_order_reward;
          }
          if (datajsonget.basic_json.referrer_first_order_reward != null) {
            self.referrer_first_order_reward = datajsonget.basic_json.referrer_first_order_reward;
          }
        }
        // console.log(datajsonget);
      }).catch(err => {
        console.log(err)
      })


  }
}

</script>
<style scoped>
/*@import '../../assets/css/comstylethree.css'*/

.WSY_remind_dl02 {
  overflow: visible;
}

.qiandaosetbox {
  clear: both;
  position: relative;
}

.qiandaoline {
  display: block;
  text-align: left;
  margin-bottom: 10px;
}

.qiandaoline .demonstration {
  margin-right: 10px;
}

.WSY_minus,
.WSY_plus {
  display: inline-block;
  width: 24px;
  height: 24px;
  line-height: 22px;
  text-align: center;
  font-weight: 100;
  cursor: pointer;
  font-size: 26px;
  border: 1px solid #dddddd;
  border-radius: 2px;
  box-sizing: border-box;
  vertical-align: middle;
  margin-left: 10px;
  color: #2c3e50;
  box-sizing: border-box;
}

.absolute2 {
  position: absolute;
  bottom: 0;
  left: 565px;
}
.absolute3 {
  position: absolute;
  bottom: 0;
  left: 640px;
}
.WSY_minus {
  font-size: 30px;
  line-height: 18px;
  margin-left: 66px;
}

</style>
