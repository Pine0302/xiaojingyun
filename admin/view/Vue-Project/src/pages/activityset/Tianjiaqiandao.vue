<template>
  <div class="setting">
    <div class="WSY_content">
      <div class="WSY_columnbox">
        <div class="WSY_remind_main">
          <div v-show="autoissu==0">
            <dl class="WSY_remind_dl02">
              <dt>活动名称：</dt>
              <dd>
                <input type="text" name="actname" placeholder="活动名称" maxlength="8" v-model="act_name">
              </dd>
            </dl>
            <dl class="WSY_remind_dl02">
              <dt>活动时间：</dt>
              <dd>
                <el-date-picker v-model="start_time" type="date" placeholder="选择日期时间">
                </el-date-picker>
                <span class="center-span">至</span>
                <el-date-picker v-model="end_time" type="date" placeholder="选择日期时间">
                </el-date-picker>
              </dd>
            </dl>
          </div>
          <div v-show="autoissu==1">
            <dl class="WSY_remind_dl02">
              <dt>活动名称：</dt>
              <dd>
                <input type="text" disabled="disabled" name="actname" placeholder="活动名称" maxlength="8" v-model="act_name">
              </dd>
            </dl>
            <dl class="WSY_remind_dl02">
              <dt>活动时间：</dt>
              <dd>
                <el-date-picker disabled="disabled" v-model="start_time" type="date" placeholder="选择日期时间">
                </el-date-picker>
                <span class="center-span">至</span>
                <el-date-picker disabled="disabled" v-model="end_time" type="date" placeholder="选择日期时间">
                </el-date-picker>
              </dd>
            </dl>
          </div>
          <dl class="WSY_remind_dl02">
            <dt>每天签到设置：</dt>
            <dd>
            </dd>
            <div class="qiandaosetbox">
              <div class="qiandaoline" v-for="(item,key,index) in time">
                <span class="demonstration">时间{{index+1}}、</span>
                <span>{{item.start}} &nbsp-&nbsp {{item.end}}</span>
                <span class="demonstration" style="margin-left:15px;">，可获得</span>
                <input v-model="item.integral" type="number" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^0-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <span class="demonstration" style="margin-left:15px;">积分</span>
              </div>
            </div>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>连续签到设置：</dt>
            <dd>
            </dd>
            <div class="qiandaosetbox">
              <div class="qiandaoline" v-for="(item,index) in continuity_sign">
                <span class="demonstration">连续签到</span>
                <span>{{item.times}}</span>
                <span class="demonstration" style="margin-left:15px;">次，之后每次可获得积分</span>
                <input type="number" maxlength="10" v-model="item.integral" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^0-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
              </div>
            </div>
          </dl>
          <el-form>
            <el-form-item label="是否自动发布：" style="margin-left:20px;">
              <label @click="check(auto_start)" style="margin-left:37px;">
                <input type="radio" name="auto_start" class="radio" value="0" v-model="auto_start">否</label>
              <label @click="check(auto_start)">
                <input type="radio" name="auto_start" class="radio" value="1" v-model="auto_start">是</label>
            </el-form-item>
          </el-form>
          <div class="submit_div">
            <Bigbutton btnval="保存"  @click.native.prevent="commit"></Bigbutton>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Bigbutton from '../../components/Bigbutton'
import util from '../../utils';

import '../../assets/js/jquery-1.12.1.min.js'


export default {
  name: 'setting',
  data: function() {
    return {
      autoissu: '0',
      auto_start: '0',
      act_name: '',
      start_time: '',
      end_time: '',
      "time": [
        { "start": "", "end": "", "integral": "" }
      ],
      "continuity_sign": [
        { "times": "", "integral": "" }
      ]

    }
  },
  components: {
    Bigbutton
  },
  methods: {
    formatDateTimeFil(time, format) {
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
      });
    },
    enptyComFun(obj, mes) {
      if (obj == "") {
        this.$message({
          showClose: true,
          message: mes,
          type: 'error'
        });
        return false;
      }
    },
    check(issu) {
      var self = this

      if (issu == 1) {

        self.$http.post('mshop/admin/index.php?m=activity&a=checktime_auto&customer_id=' + self.customer_id, {
            "system_code": "",
            "data": {
              "start_time": self.formatDateTimeFil(self.start_time, "yyyy-MM-dd"),
              "end_time": self.formatDateTimeFil(self.end_time, "yyyy-MM-dd"),
              "act_type": "1"

            }
          })
          .then(res => {

            if (res.data.errcode == 0) {
              self.autoissu = 1

            } else {
              self.auto_start = 0
              alert(res.data.errmsg)

            }

          }).catch(err => {
            console.log(err)
          })
      } else {
        self.autoissu = 0
      }

    },
    commit() {
      var self = this;
      // 判空
      var hasEmpty1 = true;
      var hasEmpty2 = true;
      $.each(self.continuity_sign, function(i, n) {
        if (n.integral == "") {
          self.$message({
            showClose: true,
            message: '连续签到设置不能为空！',
            type: 'error'
          });
          hasEmpty1 = true;
          return false;
        } else {
          hasEmpty1 = false;
        }
      });
      $.each(self.time, function(i, n) {
        if (n.integral == "") {
          self.$message({
            showClose: true,
            message: '每天签到设置不能为空！',
            type: 'error'
          });
          hasEmpty2 = true;
          return false;
        } else {
          hasEmpty2 = false;
        }
      });
      if (hasEmpty1 == true || hasEmpty2 == true) {
        return;
      }
      if (
        self.enptyComFun(self.act_name, '活动名称不能为空！') == false ||
        self.enptyComFun(self.start_time, '活动时间不能为空！') == false ||
        self.enptyComFun(self.end_time, '活动时间不能为空！') == false
      ) {
        return;
      }

      function compareDate(d1, d2) {
        return ((new Date(d1.replace(/-/g, "\/"))) > (new Date(d2.replace(/-/g, "\/"))));
      }
      if (compareDate(util.formatDateTimeFil(self.start_time, "yyyy-MM-dd HH:mm:ss"), util.formatDateTimeFil(self.end_time, "yyyy-MM-dd HH:mm:ss"))) {
        self.$message({
          showClose: true,
          message: '活动的结束时间应大于开始时间！',
          type: 'error'
        });
        return;
      }
      var datajson = {
        'auto_start': self.auto_start,
        "act_name": self.act_name,
        "start_time": self.formatDateTimeFil(self.start_time, "yyyy-MM-dd"),
        "end_time": self.formatDateTimeFil(self.end_time, "yyyy-MM-dd"),
        "op": "conserve",
        "act_id": self.act_id,
        "act_type": '1',
        "ext_info": {
          "time": self.time,
          "continuity_sign": self.continuity_sign
        }
      }
      var urlpost = "mshop/admin/index.php?m=activity&a=save_activity&customer_id=" + self.customer_id;
      self.$http.post(urlpost, {
          "system_code": "",
          data: datajson
        })
        .then(res => {
          if (res.data.errcode != 0) {
            alert(res.data.errmsg);
            self.$router.push({ path: '/activityset/Qiandaohuodong' });
          } else {
            self.$router.push({ path: '/activityset/Qiandaohuodong' });
          }

        }).catch(err => {
          console.log(err)
        })
    }
  },
  created: function() {
    var self = this;
    self.act_id = self.$route.query.act_id //获取路由传过来的参数act_id
    if (self.act_id) {
      self.$http.post('mshop/admin/index.php?m=activity&a=read', { system_code: '', data: { act_id: self.act_id } }).then(function(res) {
          self.auto_start = res.data.data.auto_start
          self.autoissu = 1
          if (res.data.data.act_name != "") {
            self.act_name = res.data.data.act_name;
          }
          if (res.data.data.start_time != "") {
            self.start_time = res.data.data.start_time;
          }
          if (res.data.data.end_time != "") {
            self.end_time = res.data.data.end_time;
          }
          if (res.data.data.ext_info != "") {
            self.time = res.data.data.ext_info.time;
            self.continuity_sign = res.data.data.ext_info.continuity_sign;
          }
        })
        .catch(function(err) {
          console.log(err)
        })
    } else {
      self.$http.post('mshop/admin/index.php?m=activity&a=sign_add&customer_id=' + self.customer_id, { system_code: '' }).then(function(res) {
          if (res.data.data.ext_info != "") {
            self.time = res.data.data.ext_info.time;
            self.continuity_sign = res.data.data.ext_info.continuity_sign;
          }
        })
        .catch(function(err) {
          console.log(err)
        })
    }
  }
}

</script>
<style type="text/css">
/*@import '../../assets/css/comstylethree.css'*/

</style>
<style scoped>
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

.absolute1 {
  position: absolute;
  bottom: 0;
  left: 965px;
}

.absolute2 {
  position: absolute;
  bottom: 0;
  left: 825px;
}

.WSY_minus {
  font-size: 30px;
  line-height: 18px;
  margin-left: 40px;
}

.richText {
  width: 65%;
  margin-left: 240px;
  height: 600px;
}

.ql-container {
  height: 500px;
  overflow-y: auto;
}

.qiandaosetbox {
  overflow: hidden;
  clear: both;
  margin-top: 22px;
  margin-bottom: 3px;
  position: relative;
}

.qiandaoline {
  display: block;
  text-align: left;
  margin-left: 150px;
  margin-top: 10px;
}

.qiandaoline .demonstration {
  margin-right: 10px;
}

.center-span {
  width: 50px;
  display: inline-block;
  text-align: center;
  margin-left: 5px;
}

.WSY_remind_dl02 dt{
  width:130px;
  text-align:left;
}
.el-form .el-form-item__label{
  width:130px;
  margin-left:20px;
}
</style>
