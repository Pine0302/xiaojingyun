<template>
  <div>
    <div class="WSY_content">
      <div class="WSY_columnbox">
        <div class="WSY_remind_main">
          <div class="WSY_column_header">
            <div class="tab_btn_box">
              <a class="tab_btn" v-bind:class="active===1 ? 'white1' : ''" @click="thdhhdsetting">活动设置</a>
              <a class="tab_btn" v-bind:class="active===2 ? 'white1' : ''" @click="thdhhdrelevance">关联产品</a>
            </div>
          </div>
          <router-view keep-alive></router-view>
        </div>
      </div>
    </div>
  </div>
</template>
<script>

export default {
    created: function() {
      var self = this;
    
          self.act_id = self.$route.query.act_id //获取路由传过来的参数act_id
      self.act_type = self.$route.query.act_type //获取路由传过来的参数act_type
       if(self.$route.query.active){
            self.active = self.$route.query.active
       }else{
         self.active=1
       }

       if(self.$route.query.active==2){
          self.$router.push({ path: '/activityset/Tjduihuanhuodong/Tjduihuanhuodongrelevance', query: { 'act_id': self.act_id, 'act_type': 2 } });
       }
    
    },
    data() {
        return {
          active:'1',
          act_id:"",
          act_type:2,
   
        }
    },
    methods: {
      thdhhdsetting: function() {
        var self = this;
        this.$router.push({ path: '/activityset/Tjduihuanhuodong/Tjduihuanhuodongsetting', query: { 'act_id': self.act_id, 'act_type': 2 } });
        self.active=1;
      },
      thdhhdrelevance: function() {
        var self = this;
        this.$router.push({ path: '/activityset/Tjduihuanhuodong/Tjduihuanhuodongrelevance', query: { 'act_id': self.act_id, 'act_type': 2 } });
        self.active=2;
      }
    }
}
</script>
<style>
.el-input__inner {
  height: 26px;
  border: 1px solid #ccc;
  border-radius: 0;
  max-width: 400px;
}

.line {
  text-align: center;
}

.inp {
  width: 140px;
  height: 30px;
  border: 1px solid #bfcbd9;
  box-sizing: border-box;
  color: #1f2d3d;
}

.inp:hover {
  border-color: #8391a5;
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
