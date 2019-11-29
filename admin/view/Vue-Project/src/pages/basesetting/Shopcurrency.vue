<template>
  <div class="setting">
    <div class="WSY_content">
        <div class="WSY_remind_main">
          <dl class="WSY_remind_dl02">
            <dt>{{shopintegralname}}转换{{customname}}开关：</dt>
            <dd>
              <el-switch on-text="关" off-text="开" on-color="#bfcbd9" off-color="#FF7170" on-value="0" off-value="1" v-model="turn_on_off"></el-switch>
            </dd>
          </dl>
      <div v-show="turn_on_off==1">
          <dl class="WSY_remind_dl02">
            <dt>转换条件：</dt>
            <dd>
              <span>最低转换{{shopintegralname}} </span><input type="number" name="trans_min" placeholder="" v-model="trans_min" @keyup="transMin(trans_min)"><span> (-1表示不限)</span>
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>转换系数：</dt>
            <dd>
              <div class="WSY_dd_div">
                <div class="WSY_dd_div_left">
                  <input type="radio" name="transRadio" v-model="trans_cient" value="-1">
                  <span>不限</span>
                </div>
                <div class="WSY_dd_div_left">
                  <input type="radio" name="transRadio" v-model="trans_cient" value="10">
                  <span>按整10</span>
                </div>
                <div class="WSY_dd_div_left">
                  <input type="radio" name="transRadio" v-model="trans_cient" value="100">
                  <span>按整100</span>
                </div>
                <div class="WSY_dd_div_left">
                  <input type="radio" name="transRadio" v-model="trans_cient" value="1000">
                  <span>按整1000</span>
                </div>
              </div>
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>转换规则：</dt>
            <dd>
              <span>{{customname}}： </span><span>{{shopintegralname}} 1 : </span><input type="number" name="trans_rule" placeholder="" v-model="trans_rule" @blur="regfun()" ref="trans_rule"><span> (可输入大于0的整数)</span>
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>转换说明：</dt>
            <dd>
              <textarea class="turntextarea" rows="10" cols="80" v-model="remark"></textarea>
            </dd>
          </dl>
      </div>
          <div class="submit_div">
            <Bigbutton btnval="保存" @mouseover="overblur" @click.native.prevent="commit"></Bigbutton>
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
  name: 'shopcurrency',
  data: function() {
    return {
      shopintegralname: '',
      storeintegralname: '',
      customname: '',
      "type": "1",
      "turn_on_off": "0",    //开关 0-关闭 1-开启
      "trans_min": "-1", //最低转换积分 -1不限
      "trans_cient": [],//转换系数 -1-不限 10-是的倍数 100-百的倍数 1000-千的倍数
      "trans_rule": "1", //转换规则
      "remark": ""  //转换说明
    }
  },
  components: {
    Bigbutton
  },
  watch: {},
  methods: {
    transMin(trans_min){
        var num = this.trans_min;
            var _reg = /^\-[1]?$/;
            var reg = /^\d*$/;
            if(!_reg.test(num) && !reg.test(num)){
                isNaN(parseInt(num)) ? this.trans_min ='' : this.trans_min=(num.substring(0,num.length-1));
            }
            if(this.trans_min.toString().length>11){
              this.trans_min=this.trans_min.substring(0,11);
            }else{
            }
    },
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
    overblur(){
      self.regfun();
    },
    regfun(){
      var str = this.$refs.trans_rule.value;
      var reg = /^\d+(?:\.0{1,2})?$/;
      var regzz = /^[1-9]\d*$/;
      if(this.$refs.trans_rule.value<=0){
        this.$refs.trans_rule.value="";
        this.trans_rule="";
      }else{
        if(str.slice(-3)!==".00"){
          if(reg.test(str)){
            this.$refs.trans_rule.value=str+'.00';
          }else{
            this.$refs.trans_rule.value = parseInt(str)+".00";
          }
        }else{

        }
      }

    },
    enptyComFun(obj, mes) {
      if (obj == "") {
        alert(mes);
        return false;
      }
    },
    commit() {
      var self = this;
      var datajson = {};
      if (
        self.enptyComFun(self.trans_min, '转换条件不能为空！') == false ||
        self.enptyComFun(self.trans_rule, '转换规则不能为空！') == false
        ){
        return;
      }
      if(self.turn_on_off==0){
        datajson = {
          "type": self.type,
          "turn_on_off": self.turn_on_off,    //开关 0-关闭 1-开启
          "trans_min": "-1", //最低转换积分 -1不限
          "trans_cient": "-1",//转换系数 -1-不限 10-是的倍数 100-百的倍数 1000-千的倍数
          "trans_rule": "1", //转换规则
          "remark": ""  //转换说明
        }
      }else{
        datajson = {
          "type": self.type,
          "turn_on_off": self.turn_on_off,    //开关 0-关闭 1-开启
          "trans_min": self.trans_min, //最低转换积分 -1不限
          "trans_cient": self.trans_cient,//转换系数 -1-不限 10-是的倍数 100-百的倍数 1000-千的倍数
          "trans_rule": self.trans_rule, //转换规则
          "remark": self.remark  //转换说明
        }
      }
      // if(self.enptyComFun(self.trans_min, '新用户首单推荐人积分不能为空！') == false ||
      //   errcle == true ||
      //   encleset == false
      // ) {
      //   return;
      // }

      var urlpost = "mshop/admin/index.php?m=activity&a=save_shopmall_integral_transform_setting&customer_id" + self.customer_id;
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
    }
  },
  created: function() {
    var self = this;
    // 共公接口—获取自定义名称（商城积分，门店积分，购物币）
    self.getDiyname();
    var urlget = 'mshop/admin/index.php?m=activity&a=shopmall_integral_transformation_setting&customer_id=' + self.customer_id
    self.$http.post(urlget, {
        "system_code": "",
        data: {}
      })
      .then(res => {
        // if(res.data.errcode==0){
        //  console.log(res.data.errmsg);
        // }
        var datajsonget = res.data.data_shop;
        if (datajsonget.turn_on_off) {
          self.turn_on_off = datajsonget.turn_on_off;
        } else {
          self.turn_on_off = "0";
        }
        if (datajsonget.trans_min) {
          self.trans_min = datajsonget.trans_min;
        }
        if (datajsonget.trans_cient) {
          self.trans_cient = datajsonget.trans_cient;
        }else{
          self.trans_cient = "-1";
        }
        if (datajsonget.trans_rule) {
          if(datajsonget.trans_rule.slice(-3)!==".00"){
            self.trans_rule = datajsonget.trans_rule+".00";
          }else{
            self.trans_rule = datajsonget.trans_rule;
          }
        }
        if (datajsonget.remark) {
          self.remark = datajsonget.remark;
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
.WSY_remind_dl02 dd .WSY_dd_div {
     margin-bottom: 0; 
}
.turntextarea{
  padding: 4px 5px;
    box-sizing: border-box;
    resize: none;
    border: 1px solid #dddddd;
}
</style>
