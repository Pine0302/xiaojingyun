<template>
  <div class="setting">
    <div class="WSY_content">
      <div class="WSY_columnbox">
        <div class="WSY_remind_main">
          <dl class="WSY_remind_dl02">
            <dt>签到积分开关：</dt>
            <dd>
              <el-switch on-text="关" off-text="开" on-color="#bfcbd9" off-color="#FF7170" on-value="0" off-value="1" v-model="sign_onoff"></el-switch>
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>悬浮开关：</dt>
            <dd>
              <el-switch on-text="关" off-text="开" on-color="#bfcbd9" off-color="#FF7170" on-value="0" off-value="1" v-model="suspend_onoff"></el-switch>
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>累加计算：</dt>
            <dd>
              <div class="WSY_dd_div" style="margin-bottom: 0;">
                <div class="WSY_dd_div_left">
                  <input type="radio" name="accumulationRadio" value="0" v-model="continuity">
                  <span>中断重新计算</span>
                </div>
                <div class="WSY_dd_div_left">
                  <input type="radio" name="accumulationRadio" value="1" v-model="continuity">
                  <span>中断也连续计算</span>
                </div>
              </div>
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>每天签到设置：</dt>
            <dd>
            </dd>
            <div class="qiandaosetbox">
              <div class="WSY_plus absolute1" @click="addTime()">+</div>
              <div class="qiandaoline" v-for="(item,index) in time">
                <span class="demonstration">时间{{index+1}}、</span>
                <el-time-picker v-model="item.start" placeholder="任意时间点" format="HH:mm" :picker-options="pickerOptions0">
                </el-time-picker>
                <span class="demonstration" style="margin-left:15px;">至</span>
                <el-time-picker v-model="item.end" placeholder="任意时间点" format="HH:mm" :picker-options="pickerOptions1">
                </el-time-picker>
                <span class="demonstration" style="margin-left:15px;">，可获得</span>
                <input v-model="item.integral" type="number" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^0-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <span class="demonstration" style="margin-left:15px;">积分</span>
                <div class="WSY_minus" @click="deleteTime(index)" v-show="time.length!=1">-</div>
              </div>
            </div>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>连续签到设置：</dt>
            <dd>
            </dd>
            <div class="qiandaosetbox">
              <div class="WSY_plus absolute2" @click="addTime1()">+</div>
              <div class="qiandaoline" v-for="(item,index) in continuity_sign">
                <span class="demonstration">连续签到</span>
                <input type="number" maxlength="10" v-model="item.times" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" @change="compareFun(index,$event.target.value)" class="tiqian">
                <span class="demonstration" style="margin-left:15px;">次，之后每次可获得积分</span>
                <input type="number" maxlength="10" v-model="item.integral" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^0-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <span class="demonstration" style="margin-left:15px;">积分</span>
                <div class="WSY_minus" @click="deleteTime1(index)" v-show="continuity_sign.length!=1">-</div>
              </div>
            </div>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>协议开关：</dt>
            <dd>
              <el-switch on-text="关" off-text="开" on-color="#bfcbd9" off-color="#FF7170" on-value="0" off-value="1" v-model="agreement_onoff"></el-switch>
            </dd>
          </dl>
          <dl class="WSY_remind_dl02">
            <dt>签到协议：</dt>
            <dd>
            </dd>
          </dl>
          <div class="richText">
            <quill-editor ref="myTextEditor" v-model="sign_agreement" :options="editorOption" style="height:500px;">
            </quill-editor>
          </div>
          <div class="submit_div">
            <Bigbutton  btnval="保存" @click.native.prevent="commit"></Bigbutton>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Bigbutton from '../../components/Bigbutton'
import { quillEditor } from 'vue-quill-editor'

import '../../assets/js/jquery-1.12.1.min.js'

var toolbarOptions = [
  ['bold', 'italic', 'underline', 'strike'], // toggled buttons
  ['blockquote', 'code-block'],

  [{ 'header': 1 }, { 'header': 2 }], // custom button values
  [{ 'list': 'ordered' }, { 'list': 'bullet' }],
  [{ 'script': 'sub' }, { 'script': 'super' }], // superscript/subscript
  [{ 'indent': '-1' }, { 'indent': '+1' }], // outdent/indent
  [{ 'direction': 'rtl' }], // text direction

  [{ 'size': ['small', false, 'large', 'huge'] }], // custom dropdown
  [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

  [{ 'color': [] }, { 'background': [] }], // dropdown with defaults from theme
  [{ 'font': [] }],
  [{ 'align': [] }],

  ['clean'] // remove formatting button
];


export default {
  name: 'setting',
  data: function() {
    return {
      pickerOptions0: {
        start: '00:00',
        step: '00:01',
        end: '23:59'
      },
      pickerOptions1: {
        start: '00:00',
        step: '00:01',
        end: '23:59'
      },
      sign_onoff: "0",
      suspend_onoff: "0",
      continuity: [],
      "time": [
        { "start": "", "end": "", "integral": "" }
      ],
      "continuity_sign": [
        { "times": "", "integral": "" }
      ],
      agreement_onoff: "0",
      sign_agreement: '',
      editorOption: {
        modules: {
          toolbar: toolbarOptions
        },
        placeholder: '请输入...',
      }
    }
  },
  components: {
    Bigbutton,
    quillEditor
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
    onEditorChange({ editor, html, text }) {
      console.log('editor change!', editor, html, text)
      his.sign_agreement = html
    },
    deleteTime(index) {
      this.time.splice(index, 1);
    },
    addTime() {
      this.time.push({
        start: "",
        end: "",
        integral: ""
      });
    },
    deleteTime1(index) {
      this.continuity_sign.splice(index, 1);
    },
    addTime1() {
      this.continuity_sign.push({
        "times": "",
        integral: ""
      });
    },
    compareFun(index, value) {
      if (index > 0) {
        var val = $(".tiqian").eq(index - 1).val();
        if (parseInt(value) <= parseInt(val)) {
          var alertText = "请输入比" + val + "大的天数";
          // this.$message({
          //   showClose: true,
          //   message: '请按递增次规则输入次数',
          //   type: 'error'
          // });
          alert(alertText);
          $(".tiqian").eq(index).val(parseFloat(val) + 1);
          this.continuity_sign[index].times = parseFloat(val) + 1
        }
      }
    },
    commit() {
      var self = this;
      var timearr = [];
      var signTimePost = self.time;
      var time = new Object();
      var hasEmpty3 = true;
      for (var i = 0; i < signTimePost.length; i++) {
        timearr.push(("time" + (i + 1)));
        var start = '';
        var end = '';
        var endold = "";
        start = self.formatDateTimeFil(signTimePost[i].start, "HH:mm");
        end = self.formatDateTimeFil(signTimePost[i].end, "HH:mm");
        console.log(end + "~~~")
        if (signTimePost.length > 0 && i > 0) {
          endold = self.formatDateTimeFil(signTimePost[i - 1].end, "HH:mm");
          if (start < endold) {
            alert("请按照递增规则选择时间！");
            hasEmpty3 = true;
            console.log(1)
          } else {
            if (start < end && start != "NaN:NaN" && end != "NaN:NaN" && signTimePost[i].integral != "") {
              time[timearr[i]] = { "start": start, "integral": signTimePost[i].integral, "end": end };
              hasEmpty3 = false;
              console.log(2)
            } else {
              alert("请按照递增规则选择时间！");
              hasEmpty3 = true;
              return;
              console.log(3)
            }
          }
        } else {
          if (start < end && start != "NaN:NaN" && end != "NaN:NaN" && signTimePost[i].integral != "") {
            time[timearr[i]] = { "start": start, "integral": signTimePost[i].integral, "end": end };
            hasEmpty3 = false;
          } else {
            alert("请按照递增规则选择时间！");
            hasEmpty3 = true;
          }
        }

      }
      var consignarr = [];
      var consignPost = self.continuity_sign;
      var continuity_sign = new Object();
      for (var i = 0; i < consignPost.length; i++) {
        consignarr.push(("continuity_sign" + (i + 1)));
        continuity_sign[consignarr[i]] = consignPost[i];
      }
      // 判空
      var hasEmpty1 = true;
      var hasEmpty2 = true;
      $.each(continuity_sign, function(i, n) {
        if (n.integral == "" || n.times == "") {
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
      $.each(time, function(i, n) {
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
      if (hasEmpty1 == true || hasEmpty2 == true || hasEmpty3 == true) {
        return;
      }
      if (
        self.enptyComFun(self.continuity, '累加计算方式不能为空！') == false
      ) {
        return;
      }
      var datajson = {
        "name": "",
        "sign_agreement": self.sign_agreement,
        "sign_onoff": self.sign_onoff,
        "agreement_onoff": self.agreement_onoff,
        "sign_json": {
          "continuity": self.continuity,
          "time": time,
          "continuity_sign": continuity_sign
        },
        "suspend_onoff": self.suspend_onoff
      }
      var urlpost = "mshop/admin/index.php?m=setting&a=sign_setting&customer_id=" + self.customer_id;
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
          alert('保存成功！');
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
    }
  },
  computed: {
    editor() {
      return this.$refs.myTextEditor.quillEditor
    }
  },
  mounted() {
    // console.log('this is my editor', this.editor)
  },
  created: function() {
    var self = this;
    var urlget = 'mshop/admin/index.php?m=setting&a=sign_read&customer_id=' + self.customer_id
    self.$http.post(urlget, {
        "system_code": "",
        data: ''
      })
      .then(res => {
        // if(res.data.errcode==0){
        // 	console.log(res.data.errmsg);
        // }
        var datajsonget = res.data.data;
        if (datajsonget.sign_onoff) {
          self.sign_onoff = datajsonget.sign_onoff;
        } else {
          self.sign_onoff = "0";
        }
        if (datajsonget.agreement_onoff) {
          self.agreement_onoff = datajsonget.agreement_onoff;
        } else {
          self.agreement_onoff = "0";
        }
        if (datajsonget.suspend_onoff) {
          self.suspend_onoff = datajsonget.suspend_onoff;
        } else {
          self.suspend_onoff = "0";
        }
        self.sign_agreement = datajsonget.sign_agreement;
        if (datajsonget.sign_json != null) {
          if (datajsonget.sign_json.continuity != null) {
            self.continuity = datajsonget.sign_json.continuity;
          }
          var signtime = [];
          if (datajsonget.sign_json.time != null) {
            $.each(datajsonget.sign_json.time, function(i, n) {
              var strtime = '2017-9-15 ';
              var temp = { "start": "", "integral": "", "end": "" };
              temp.start = strtime + n.start;
              temp.integral = n.integral;
              temp.end = strtime + n.end;
              signtime.push(temp);
            });
          }
          self.time = signtime;
          var continuity_signarr = [];
          if (datajsonget.sign_json.continuity_sign != null) {
            $.each(datajsonget.sign_json.continuity_sign, function(i, n) {
              continuity_signarr.push(n);
            });
            self.continuity_sign = continuity_signarr;
          }
        }
        // console.log(datajsonget);
      }).catch(err => {
        console.log(err)
      })
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
  margin-left: 145px;
  margin-top: 10px;
}

.qiandaoline .demonstration {
  margin-right: 10px;
}

</style>
