<template>
  <div class="edit">
    <div class="WSY_content">
      <div class="WSY_columnbox">
        <div class="WSY_remind_main">
          <dl class="WSY_remind_dl02">
            <dt>产品图片：</dt>
            <dd>
              <img :src="basehear + tableDataM.default_imgurl" class="WSYimg">
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>产品名称：</dt>
            <dd>{{tableDataM.product_name}}</dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>产品分类：</dt>
            <dd>{{tableDataM.type_name}}</dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>积分模式：</dt>
            <dd>
            </dd>
          </dl>
          <div class="tab-one">
            <div class="tab-one-line">
              <span class="tab-one-title">积分模式</span>
              <div class="tab-one-radio">
                <input type="radio" @click="modeFun($event.target.value)" name="integralMode" v-model="tableDataM.mode" value="0">
                <span>默认全局</span></div>
              <div class="tab-one-radio">
                <input type="radio" @click="modeFun($event.target.value)" name="integralMode" v-model="tableDataM.mode" value="1">
                <span>自定义</span></div>
            </div>
            <div class="tab-one-line">
              <span class="tab-one-title">{{storeintegralname}}</span>
              <div class="tab-one-radio">
                <input v-bind:disabled="isReadOnly" type="radio" name="consume_typen" v-model="tableDataM.consume_type" value="2" @click="changeFun()">
                <span>固定积分</span></div>
              <div class="tab-one-radio">
                <input v-bind:disabled="isReadOnly" type="radio" name="consume_typen" v-model="tableDataM.consume_type" value="1" @click="changeFun()">
                <span>固定比例</span></div>
            </div>
          </div>
          <dl class="WSY_remind_dl02">
            <dt>产品参数：</dt>
            <dd>
              <div class="total_set_box">
                <p class="edit_p"><span @click="setAllFun()">全部设置</span></p>
                <div class="edit_set_tab procan">
                  <div class="edit_set_tab_line edit_set_tab_line1">
                    <div>原价￥<span>{{tableDataM.orgin_price}}</span></div>
                    <div>现价￥<span>{{tableDataM.now_price}}</span></div>
                    <div>成本￥<span>{{tableDataM.for_price}}</span></div>
                    <div>单位<span>{{tableDataM.unit}}</span></div>
                    <div>重量<span>{{tableDataM.weight}}</span></div>
                  </div>
                  <div class="edit_set_tab_line edit_set_tab_line3">
                    <div class="line3_box">
                      <span class="edit_set_tab_des">{{storeintegralname}}</span>
                      <div class="WSY_dd_div" v-show="tableDataM.consume_type==2">
                        <input v-bind:readonly="isReadOnly" class="WSY_small_ipt" type="number" name="consume_type1" v-model="tableDataM.consume_integral">
                        <span class="integralone">积分</span>
                        <span>换算结果</span>
                        <span>{{consumeintegralresfun1}}</span>
                      </div>
                      <div class="WSY_dd_div" v-show="tableDataM.consume_type==1">
                        <input v-bind:readonly="isReadOnly" class="WSY_small_ipt" type="number" name="consume_type2" v-model="tableDataM.consume_integral">
                        <span class="integralone">%</span>
                        <span>换算结果</span>
                        <span>{{consumeintegralresfun2}}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </dd>
          </dl>
          <dl class="WSY_remind_dl02" v-show="tableDataS">
            <dt>属性参数：</dt>
            <dd>
              <p class="edit_p">
                <span @click="toggleTbs=!toggleTbs">
                  <span v-if="toggleTbs">关闭</span>
                <span v-else="!toggleTbs">打开</span>
                </span>
              </p>
              <div v-show="toggleTbs" class="total_set_box total_set_boxtg" v-for="(itemi,index) in tableDataS">
                <div class="edit_set_tab">
                  <div class="edit_set_tab_left">
                    <p>
                      <span>{{itemi.pros_name}}</span>
                    </p>
                  </div>
                  <div class="edit_set_tab_right">
                    <div class="edit_set_tab_line edit_set_tab_line1">
                      <div>原价￥
                        <span>{{itemi.orgin_price}}</span>
                      </div>
                      <div>现价￥
                        <span>{{itemi.now_price}}</span>
                      </div>
                      <div>成本￥
                        <span>{{itemi.for_price}}</span>
                      </div>
                      <div>单位
                        <span>{{itemi.unit}}</span>
                      </div>
                      <div>重量
                        <span>{{itemi.weight}}</span>
                      </div>
                    </div>
                    <div class="edit_set_tab_line edit_set_tab_line3">
                      <div class="line3_box">
                        <span class="edit_set_tab_des">{{storeintegralname}}</span>
                        <div class="edit_set_tab_radio">
                          <span v-if="tableDataM.consume_type == 2">固定积分</span>
                          <span v-else-if="tableDataM.consume_type == 1">固定比例</span>
                          <input v-bind:readonly="isReadOnly" type="text" class="WSY_small_ipt" @input="countVal1($event.target.value,itemi.now_price,index)" v-model="itemi.consume_integral">
                          <span v-if="tableDataM.consume_type == 1">%</span>
                        </div>
                        <span class="edit_set_tab_des">换算结果</span>
                        <div class="edit_set_tab_radio">
                          <span class="changeres1">{{itemi.res1}}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </dd>
          </dl>
          <div class="submit_div">
            <Bigbutton class="btntwo" btnval="保存" @click.native.prevent="commit"></Bigbutton>
            <Bigbutton class="btntwo" btnval="返回" @click.native.prevent="$router.back(-1)"></Bigbutton>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Bigbutton from '../../components/Bigbutton'
import util from '../../utils'

export default {
  name: 'edit',
  data: function() {
    return {
      shopintegralname: '',
      storeintegralname: '',
      customname: '',
      toggleTbs: true,
      isReadOnly: true,
      tableDataM: [],
      tableDataS: [],
    }
  },
  computed: {
    consumeintegralresfun1: function() {
      return this.tableDataM.consume_integral;
    },
    consumeintegralresfun2: function() {
      return ((this.tableDataM.consume_integral * this.tableDataM.now_price) / 100).toFixed(2);
    },
    giftintegralresfun2: function() {
      return ((this.tableDataM.gift_set_value * this.tableDataM.now_price) / 100).toFixed(2);
    },
    giftintegralresfun1: function() {
      return this.tableDataM.gift_set_value;
    }
  },
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
    },
    changeFun() {
      this.initResFun();
    },
    initResFun() {
      if (this.tableDataM.consume_type == 2) {
        $.each(this.tableDataS, function(i, data) {
          data.res1 = data.consume_integral;
        });
      } else {
        $.each(this.tableDataS, function(i, data) {
          data.res1 = (data.now_price * data.consume_integral / 100).toFixed(2);
        });
      }
      if (this.tableDataM.recommend_type == 2) {
        $.each(this.tableDataS, function(i, data) {
          data.res2 = data.recommend_integral;
        });
      } else {
        $.each(this.tableDataS, function(i, data) {
          data.res2 = (data.now_price * data.recommend_integral / 100).toFixed(2);
        });
      }
    },
    modeFun(event) {
      if (event == 1) {
        this.isReadOnly = false;
      } else {
        this.isReadOnly = true;
        var giftSetValue = this.tableDataM.gift_set_value;
        //1按比例2按固定积分
        if (this.tableDataM.gift_set_type == 1) {
          this.tableDataM.consume_type = 1;
          this.tableDataM.recommend_type = 1;
        } else {
          this.tableDataM.consume_type = 2;
          this.tableDataM.recommend_type = 2;
          // $(".changeres1").text(giftSetValue);
        }
        this.tableDataM["consume_integral"] = giftSetValue;
        this.tableDataM["recommend_integral"] = giftSetValue;
        var tbmGiftsettype = this.tableDataM.gift_set_type;
        var centGiftSetValue = giftSetValue / 100;
        $.each(this.tableDataS, function(i, data) {
          data["consume_integral"] = giftSetValue;
          data["recommend_integral"] = giftSetValue;
          if (tbmGiftsettype == 0) {
            var centRes = (data.now_price * centGiftSetValue).toFixed(2);
            $(".changeres1").eq(i - 1).text(centRes);
            $(".changeres2").eq(i - 1).text(centRes);
            data["res1"] = centRes;
            data["res2"] = centRes;
          } else {
            $(".changeres1,.changeres2").text(giftSetValue);
            data["res1"] = giftSetValue;
            data["res2"] = giftSetValue;
          }
        });
      }
    },
    setAllFun() {
      var consumeIntegralVal = this.tableDataM.consume_integral;
      var recommendIntegralVal = this.tableDataM.recommend_integral;
      var consumeIntegraltype = this.tableDataM.consume_type;
      var recommendIntegraltype = this.tableDataM.recommend_type;
      $.each(this.tableDataS, function(i, data) {
        data["consume_integral"] = consumeIntegralVal;
        data["recommend_integral"] = recommendIntegralVal;
        if (consumeIntegraltype == 2) {
          $(".changeres1").eq(i - 1).text(consumeIntegralVal);
          data["res1"] = consumeIntegralVal;
        } else {
          var centRes1 = (data.now_price * consumeIntegralVal / 100).toFixed(2);
          $(".changeres1").eq(i - 1).text(centRes1);
          data["res1"] = centRes1;
        }
        if (recommendIntegraltype == 2) {
          $(".changeres2").eq(i - 1).text(recommendIntegralVal);
          data["res2"] = recommendIntegralVal;
        } else {
          var centRes2 = (data.now_price * recommendIntegralVal / 100).toFixed(2);
          $(".changeres2").eq(i - 1).text(centRes2);
          data["res2"] = centRes2;
        }

      });
      this.changeFun();
      console.log(this.tableDataS)
    },
    countVal1(event, now_price, index) {
      if (this.tableDataM.consume_type == 2) {
        $(".changeres1").eq(index - 1).text(parseFloat(event));
      } else {
        $(".changeres1").eq(index - 1).text(((parseFloat(now_price) * parseFloat(event)) / 100).toFixed(2));
      }
    },
    countVal2(event, now_price, index) {
      if (this.tableDataM.recommend_type == 2) {
        $(".changeres2").eq(index - 1).text(parseFloat(event));
      } else {
        $(".changeres2").eq(index - 1).text(((parseFloat(now_price) * parseFloat(event)) / 100).toFixed(2));
      }
    },
    commit() {
      var self = this;
      var newArr = [];
      newArr.push({
        "id": self.tableDataM.id,
        "mode": self.tableDataM.mode, //模式：0全局 1自定义
        "consume_integral": self.tableDataM.consume_integral,
        "recommend_integral": self.tableDataM.recommend_integral,
        "consume_type": self.tableDataM.consume_type, //赠送类型：1比例2固定值
        "recommend_type": self.tableDataM.recommend_type //推荐类型：1比例2固定值
      });
      $.each(self.tableDataS, function(i, data) {
        newArr.push({
          "id": data.id,
          "mode": self.tableDataM.mode, //模式：0全局 1自定义
          "consume_integral": data.consume_integral,
          "recommend_integral": data.recommend_integral,
          "consume_type": self.tableDataM.consume_type, //赠送类型：1比例2固定值
          "recommend_type": self.tableDataM.recommend_type //推荐类型：1比例2固定值
        });
      });
      var zanshi = [];
      var resobjPost = new Object();
      for (var i = 0; i < newArr.length; i++) {
        zanshi.push(i);
        resobjPost[zanshi[i]] = newArr[i];
      }
      var datajson = {
        "save_arr": resobjPost
      }
      //判空
      if (
        self.enptyComFun(self.tableDataM.mode, '积分模式不能为空！') == false ||
        self.enptyComFun(self.tableDataM.consume_type, '消费积分不能为空！') == false ||
        self.enptyComFun(self.tableDataM.recommend_type, '推荐积分不能为空！') == false ||
        self.enptyComFun(self.tableDataM.consume_integral, '消费积分不能为空！') == false ||
        self.enptyComFun(self.tableDataM.recommend_integral, '推荐积分不能为空！') == false
      ) {
        return;
      }
      var urlpost = "mshop/admin/index.php?m=activity&a=save_integral_setting_product&customer_id=" + self.customer_id;
      self.$http.post(urlpost, {
          "system_code": "",
          data: datajson
        })
        .then(res => {
          // this.$message({
          //   message: '保存成功！'
          // });
          alert("保存成功");
          this.$router.push({ path: '/basesetting/shopproductmanage'});
        }).catch(err => {
          console.log(err)
        })
    }
  },
  created: function() {
    var self = this;
    // 共公接口—获取自定义名称（商城积分，门店积分，购物币）
    self.getDiyname();

    var urlget = 'mshop/admin/index.php?m=activity&a=get_one_integral_setting_product&customer_id=' + self.customer_id
    self.$http.post(urlget, {
        "system_code": "",
        data: {
          "product_id": self.$route.query.product_id, //产品id
          "cust_id": "3243", //商家id
          "integral_type": "1" //0商城   1门店
        }
      })
      .then(res => {
        var datajsonget = res.data.data;
        if (res.data.data.main != "") {
          self.tableDataM = datajsonget.main;
          if (self.tableDataM.mode == 0) {
            self.isReadOnly = true;
          } else {
            self.isReadOnly = false;
          }
        }
        if (datajsonget.sub != "") {
          self.tableDataS = datajsonget.sub;
          self.initResFun();
        }
        var modeevent = res.data.data.main.mode;
        self.modeFun(modeevent);

      }).catch(err => {
        console.log(err)
      })
  },
  components: {
    Bigbutton
  }
}

</script>
<style type="text/css">
/*@import '../../assets/css/comstylethree.css'*/

</style>
<style scoped>
.edit_p {
  /*width: 650px;*/
  text-align: right;
}

.edit_p span {
  cursor: pointer;
}

.total_set_box {
  margin-bottom: 10px;
}

.edit_set_tab {
  border: 1px solid #d8d8d8;
  border-top: none;
  width: fit-content;
  font-size: 0;
}

.edit_set_tab .edit_set_tab_line {
  border-top: 1px solid #d8d8d8;
  font-size: 0;
  display: flex;
  padding: 8px 15px;
  align-items: center;
}

.edit_set_tab .edit_set_tab_line1 div {
  display: inline-flex;
  flex: 1;
}

.edit_set_tab .edit_set_tab_line1 div span {
  padding-left: 5px;
}

.edit_set_tab .edit_set_tab_line .edit_set_tab_des {
  display: inline-block;
  width: 110px;
  vertical-align: middle;
}

.edit_set_tab .edit_set_tab_line2 .edit_set_tab_radio {
  display: inline-block;
  width: 180px;
  vertical-align: middle;
}

.edit_set_tab .edit_set_tab_line2 .edit_set_tab_radio input,
.edit_set_tab .edit_set_tab_line2 .edit_set_tab_radio span,
.edit_set_tab .edit_set_tab_line3 .line3_box .edit_set_tab_radio input,
.edit_set_tab .edit_set_tab_line3 .line3_box .edit_set_tab_radio span {
  vertical-align: middle;
}

.edit_set_tab .edit_set_tab_line3 {
  display: block;
}

.edit_set_tab .edit_set_tab_line3 .line3_box {
  font-size: 0;
}

.edit_set_tab .edit_set_tab_line3 .line3_box:first-child {
  margin-bottom: 8px;
}

.edit_set_tab .edit_set_tab_line3 .line3_box .edit_set_tab_radio {
  display: inline-block;
  width: 180px;
  vertical-align: middle;
}

.edit_set_tab .edit_set_tab_line3 .line3_box .edit_set_tab_radio span {
  margin-right: 15px;
}









/*.total_set_boxtg{display: none;}*/

.edit_set_tab_left {
  display: inline-flex;
  width: 150px;
  height: 116px;
  overflow: hidden;
  border-top: 1px solid #d8d8d8;
  border-right: 1px solid #d8d8d8;
  vertical-align: top;
  text-align: center;
}

.edit_set_tab_left p {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.edit_set_tab_left p span {
  background-color: red;
  display: block;
  width: 90%;
  color: #ffffff;
}

.edit_set_tab_right {
  display: inline-block;
}

.WSYimg {
  width: 100px;
  height: 100px;
}

.product-base-info {
  width: 180px;
  display: inline-block;
  vertical-align: middle;
}
.product-base-info span {
  margin-left: 6px;
}
.integralone {
  display: inline-block;
  vertical-align: middle;
  width: 50px;
}

.tab-one {
  margin-left: 240px;
  margin-top: -24px;
  border: 1px solid #d8d8d8;
  display: inline-block;
  vertical-align: top;
  padding: 8px 15px;
}
.tab-one .tab-one-line {
  width: 100%;
}
.tab-one .tab-one-line .tab-one-title {
  display: inline-block;
  vertical-align: middle;
  width: 140px;
}
.tab-one .tab-one-line .tab-one-radio {
  display: inline-block;
  vertical-align: middle;
  width: 220px;
}
.tab-one .tab-one-line .tab-one-radio input,
.tab-one .tab-one-line .tab-one-radio span {
  display: inline-block;
  vertical-align: middle;
  line-height: 30px;
}
.line3_box .WSY_dd_div {
  display: inline-block;
  vertical-align: middle;
  margin-bottom: 0;
}
.procan {
  width: 615px;
}
.procan .edit_set_tab_line .edit_set_tab_des {
  width: 140px;
}

.procan .edit_set_tab_line2 .edit_set_tab_radio {
  width: 160px;
}
.btntwo {
  display: inline-block;
  vertical-align: middle;
  margin-right: 40px;
}

</style>
