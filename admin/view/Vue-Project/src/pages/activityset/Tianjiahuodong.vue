<template>
  <div>
    <div class="WSY_content">
      <div class="WSY_columnbox">
        <div class="WSY_remind_main">
          <div class="WSY_column_header">
            <div class="tab_btn_box">
              <a class="tab_btn" v-bind:class="active===1 ? 'white1' : ''" @click="thhdsetting">活动设置</a>
              <a class="tab_btn" v-bind:class="active===2 ? 'white1' : ''" @click="thhdrelevance">关联产品</a>
            </div>
          </div>
          <router-view></router-view>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
    created: function() {
      var self = this;
      
       self.active = self.$route.query.active //获取路由传过来的参数active
       if(self.active){
            self.active = self.$route.query.active
       }else{
         self.active=1
       }
      self.act_id = self.$route.query.act_id //获取路由传过来的参数act_id
      self.act_type = self.$route.query.act_type //获取路由传过来的参数act_type
    },
    data() {
        return {
          active: 1,
          act_id:"",
          act_type:0
        }
    },
    methods: {
      thhdsetting: function() {
        var self = this;
        this.$router.push({ path: '/activityset/Tianjiahuodong/Tianjiahuodongsetting', query: { 'act_id': self.act_id, 'act_type': 0 } });
        self.active=1;
      },
      thhdrelevance: function() {
        var self = this;
        this.$router.push({ path: '/activityset/Tianjiahuodong/Tianjiahuodongrelevance', query: { 'act_id': self.act_id, 'act_type': 0 } });
        self.active=2;
      }
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
