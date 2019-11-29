<template>
  <div id="app">
    <!-- <router-link to="setting">基本设置</router-link>
    <!-- <router-link to="edit">编辑</router-link> -->
    <!-- <router-link to="productmanage">产品管理</router-link>
    <router-link to="sign">签到设置</router-link>
    <router-link to="oplog">操作日志</router-link>
    <hr/>
    <router-link to="Jifenhuodong">'积分活动'</router-link>
    <router-link to="Jifenhuodongtj">'积分活动统计'</router-link>
    <router-link to="Tianjiachanpin">'添加产品'</router-link>
    <router-link to="Tianjiahuodong">'添加活动'</router-link>
    <router-link to="Jifenmingxi">'积分明细'</router-link>
    <router-link to="Yonghujifentj">'用户积分统计'</router-link>
    <router-link to="Qiandaohuodong">'签到活动'</router-link>
    <router-link to="Tianjiaqiandao">'添加签到'</router-link>
    <router-link to="Duihuanhuodong">'兑换活动'</router-link>
    <router-link to="Tjduihuanhuodong">'添加兑换活动'</router-link>
    <router-link to="Tjduihuanhuodong">'统计兑换活动'</router-link>
    <router-link to="Qiandaomingxi">'签到明细'</router-link -->
    <router-view ></router-view> 

    

  </div>
</template>

<script>
import util from './utils'

export default {
  name: 'app',
  data: function() {
    return {
      skin:''
    }
  },
  components:{
  },
  beforeCreate: function () {
    var self = this;
    // 公共接口—获取自定义名称（商城积分，门店积分，购物币）
    util.getDiyName();
  },
  created: function() {
  
   
  }

}
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  margin-bottom: 40px;
}
.titinp{
  display: inline-block;
}
</style>
