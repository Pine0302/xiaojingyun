<template>
  <div>
    <el-dialog title="提示" :visible.sync="centerDialogVisible" :close-on-click-modal="false" top="25vh" :show-close="false" center>
      <span>请先开启{{shopintegralname}}或{{storeintegralname}}在进行设置！</span>
    </el-dialog>
    <div class="WSY_content" v-show="!centerDialogVisible">
      <div class="WSY_columnbox">
        <div class="WSY_remind_main">
          <div class="WSY_column_header">
            <div class="tab_btn_box">
<!--               <a class="tab_btn" v-show="typeshop==1" v-bind:class="active===1 ? 'white1' : ''">{{shopintegralname}}转{{customname}}</a>
              <a class="tab_btn" v-show="typestore==1" v-bind:class="active===2 ? 'white1' : ''">{{storeintegralname}}转{{customname}}</a> -->

              <a class="tab_btn" v-show="typeshop==1" v-bind:class="active===1 ? 'white1' : ''" @click="shopcurrency">{{shopintegralname}}转{{customname}}</a>
              <a class="tab_btn" v-show="typestore==1" v-bind:class="active===2 ? 'white1' : ''" @click="storecurrency">{{storeintegralname}}转{{customname}}</a>
            </div>
          </div>
          <router-view></router-view>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import util from '../../utils'

export default {
    data() {
        return {
          centerDialogVisible: false,
          active: 1,
          shopintegralname: '',
          storeintegralname: '',
          customname: '',
          typeshop:0,
          typestore:0
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
      shopcurrency: function() {
        var self = this;
        this.$router.push({ path: '/basesetting/turnsetting/shopcurrency' });
        self.active=1;
      },
      storecurrency: function() {
        var self = this;
        this.$router.push({ path: '/basesetting/turnsetting/storecurrency' });
        self.active=2;
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
        var datajsonget = res.data.turn_on_off;
        if(datajsonget.shop_onoff==1 && datajsonget.store_onoff==0){
          self.typeshop=1;
          self.typestore=0;
          self.shopcurrency();
        }else if(datajsonget.shop_onoff==0 && datajsonget.store_onoff==1){
          self.typeshop=0;
          self.typestore=1;
          self.storecurrency();
        }else if(datajsonget.shop_onoff==1 && datajsonget.store_onoff==1){
          self.typeshop=1;
          self.typestore=1;
          self.shopcurrency();
        }else{
          self.centerDialogVisible = true;
        }
        // console.log(self.typeshop+"~~~~"+self.typestore)
        // console.log(datajsonget);
      }).catch(err => {
        console.log(err)
      })


  }
}
</script>
<style scoped>
input.WSY_iptj {
  width: 200px;
  height: 24px;
  border: 1px solid #dddddd;
  border-radius: 2px;
  box-sizing: border-box;
  padding: 0 0;
  padding-left: 5px;
}

.el-input__inner {
  height: 26px;
  border: 1px solid #ccc;
  border-radius: 0;
  max-width: 400px;
}

td input {
  display: inline-block;
  margin: auto;
  width: 80%;
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

.integral {
  height: 20px;
  text-align: center;
  border: 1px solid #ccc;
}
.tab_btn {
  display: inline;
  float: left;
  line-height: 37px;
  padding-left: 15px;
  padding-right: 15px;
  font-size: 14px;
  color: #646464;
  cursor: pointer;
}
.tab_btn:hover {
  background-color: #fff;
  border-bottom: solid 2px #06a7e1;
}

</style>
