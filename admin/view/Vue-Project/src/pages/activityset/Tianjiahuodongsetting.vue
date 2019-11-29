<template>
  <div class="oplog">
    <el-form ref="form" :model="form" label-width="130px" style="margin:20px;min-width:1000px;">
      <el-form-item label="活动名称" v-if="form.status==0 || !form.status ">
        <!-- <el-input v-model="form.act_name" placeholder="填写活动名称"></el-input> -->
        <input type="text" class="WSY_iptj" placeholder="填写活动名称" maxlength="8" v-model="form.act_name">
      </el-form-item>
      <el-form-item label="活动时间" v-if="form.status==0 || !form.status">
        <el-col :span="3">
          <el-date-picker type="date" placeholder="选择日期" v-model="form.start_time" style="width: 100%;"></el-date-picker>
        </el-col>
        <el-col class="line" :span="1">至</el-col>
        <el-col :span="3">
          <el-date-picker type="date" placeholder="选择日期" v-model="form.end_time" style="width: 100%;"></el-date-picker>
        </el-col>
      </el-form-item>
      <el-form-item label="是否自动发布" v-if="form.status==0 || !form.status">
        <label>
          <input type="radio" name="auto_start" class="radio" value="0" v-model="auto_start">否</label>
        <label>
          <input type="radio" name="auto_start" class="radio" value="1" v-model="auto_start">是</label>
      </el-form-item>
      <el-form-item label="积分模式" v-if="form.status==0 || !form.status">
        <label>
          <input type="radio" name="jifenmoshi" class="radio" value="1" v-model="jifenmoshi">按比例</label>
        <label>
          <input type="radio" name="jifenmoshi" class="radio" value="2" v-model="jifenmoshi">按固定值</label>
      </el-form-item>
      <el-form-item label="活动名称1" v-if="form.status>0">
        <!-- <el-input v-model="form.act_name" placeholder="填写活动名称"></el-input> -->
        <input disabled="disabled" type="text" class="WSY_iptj" placeholder="填写活动名称" maxlength="8" v-model="form.act_name">
      </el-form-item>
      <el-form-item label="活动时间" v-if="form.status>0">
        <el-col :span="3">
          <el-date-picker disabled="disabled" type="date" placeholder="选择日期" v-model="form.start_time" style="width: 100%;"></el-date-picker>
        </el-col>
        <el-col class="line" :span="1">至</el-col>
        <el-col :span="3">
          <el-date-picker disabled="disabled" type="date" placeholder="选择日期" v-model="form.end_time" style="width: 100%;"></el-date-picker>
        </el-col>
      </el-form-item>
      <el-form-item label="是否自动发布" v-if="form.status>0">
        <label>
          <input disabled="disabled" type="radio" name="auto_start" class="radio" value="0" v-model="auto_start">否</label>
        <label>
          <input disabled="disabled" type="radio" name="auto_start" class="radio" value="1" v-model="auto_start">是</label>
      </el-form-item>
      <el-form-item label="积分模式" v-if="form.status>0">
        <label>
          <input disabled="disabled" type="radio" name="jifenmoshi" class="radio" value="1" v-model="jifenmoshi">按比例</label>
        <label>
          <input disabled="disabled" type="radio" name="jifenmoshi" class="radio" value="2" v-model="jifenmoshi">按固定值</label>
      </el-form-item>
      <Bigbutton btnval="保存" @click.native="save"  style="text-align:center;"></Bigbutton>
    </el-form>
  </div>
</template>
<script>
import Bigbutton from '../../components/Bigbutton';

export default {


  created: function() {
    var self = this;

    self.act_id = self.$route.query.act_id //获取路由传过来的参数act_id
    self.act_type = self.$route.query.act_type //获取路由传过来的参数act_type


    if (self.act_id) {
      //获取活动信息
      self.$http.post('mshop/admin/index.php?m=activity&a=read&customer_id=' + self.customer_id, {
        'system_code': '',
        'data': {
          'act_id': self.act_id
        }
      }).then(function(res) {

        self.form = res.data.data
        self.fenyong = res.data.data.is_commission
        self.jifenmoshi = res.data.data.ext_info.model
      })
        .catch(function(err) {
          console.log(err)
        })

      //获取活动关联产品
      self.$http.post('mshop/admin/index.php?m=activity&a=__get_activity_product&customer_id=' + self.customer_id, {
        'system_code': '',
        'data': {
          'act_id': self.act_id, //活动id   
          page: self.page, //当前页数   
          page_size: self.count, //每页数量

        }
      }).then(function(res) {
        for (var i = 0; i < res.data.datas.total && i < res.data.datas.list_num; i++) {
          self.tableData.push(res.data.datas.list[i])
        }
        self.total = parseInt(res.data.datas.total);
        console.log(self.total + "~~~")
      })
        .catch(function(err) {
          console.log(err)
        })
    }

  },

  data() {
    return {

      act_id: '',
      act_type: '',
      leijijifen: '1000000',
      qiandaonum: '20',
      total: 0,
      count: 10,
      page: 1,
      value: '',
      jifenmoshi: '1',
      auto_start: 0,
      fenyong: '1',
      form: {
        act_name: '',
        start_time: '',
        end_time: '',


      },

      tableData: [], //表格数据
      parameterchain: [],
      save_parameterchain: [] //参数链
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
    consume: function(index) {
      var self = this
      if (self.tableData[index].consume_integral < 0) {
        self.tableData[index].consume_integral = ''
      }
    },
    recommend: function(index) {
      var self = this
      if (self.tableData[index].recommend_integral < 0) {
        self.tableData[index].recommend_integral = ''
      }
    },
    handleCurrentChange(val) {
      var self = this;
      if (val) {
        self.page = val;
      } else {
        self.page = 1;
      }
      //获取活动关联产品
      self.$http.post('mshop/admin/index.php?m=activity&a=__get_activity_product&customer_id=' + self.customer_id, {
        'system_code': '',
        'data': {
          'act_id': self.act_id, //活动id   
          page: self.page, //当前页数   
          page_size: self.count, //每页数量

        }
      }).then(function(res) {
        self.tableData = [];
        for (var i = 0; i < res.data.datas.total && i < res.data.datas.list_num; i++) {
          self.tableData.push(res.data.datas.list[i])
        }
        self.total = parseInt(res.data.datas.total);
      })
        .catch(function(err) {
          console.log(err)
        })
    },
    addproducts: function() {
      var that = this;
      that.form.ext_info = { "model": that.jifenmoshi }
      that.form.is_commission = that.fenyong
      that.form.op = "conserve"
      that.form.auto_start = that.auto_start
      that.form.act_type = "0"
      that.form.start_time = that.formatDateTimeFil((that.form.start_time), 'yyyy-MM-dd') + " 00:00:00"
      that.form.end_time = that.formatDateTimeFil((that.form.end_time), 'yyyy-MM-dd') + " 00:00:00"
      if (that.form.status > 0) {
        that.$router.push({ path: '/activityset/Tianjiachanpin', query: { 'act_id': that.act_id, 'act_type': 1 } });
      } else {
        console.log(that.form.status)
        that.$http.post('mshop/admin/index.php?m=activity&a=save_activity&customer_id=' + that.customer_id, { system_code: '', 'data': that.form }).then(function(res) {
          if (res.data.errcode == 0) {
            that.act_id = res.data.data.act_id
            console.log(that.act_id)
            that.$router.push({ path: '/activityset/Tianjiachanpin', query: { 'act_id': that.act_id, 'act_type': 1 } });
          } else {
            alert("活动名称和活动时间不能为空")
          }

        })
          .catch(function(err) {
            console.log(err)
          })
      }


    },
    save: function() {

      var that = this;
      //保存活动信息

      that.form.ext_info = { "model": that.jifenmoshi }
      that.form.is_commission = that.fenyong
      that.form.op = "conserve"
      that.form.auto_start = that.auto_start
      that.form.act_type = "0"
      that.form.start_time = that.formatDateTimeFil((that.form.start_time), 'yyyy-MM-dd') + " 00:00:00"
      that.form.end_time = that.formatDateTimeFil((that.form.end_time), 'yyyy-MM-dd') + " 00:00:00"
      that.$http.post('mshop/admin/index.php?m=activity&a=save_activity&customer_id=' + that.customer_id, { system_code: '', 'data': that.form }).then(function(res) {

        if (res.data.errcode == 0) {
          if (res.data.data.operation === 'insert') {
            that.$router.push({ path: '/activityset/Tianjiahuodong/Tianjiahuodongrelevance', query: {'act_name':that.form.act_name,'start_time':that.form.start_time,'end_time':that.form.end_time,'act_id': res.data.data.act_id, 'act_type': 2, 'active': 2 } });
            $(".tab_btn_box").find(".tab_btn").eq(1).addClass("white1").end().eq(0).removeClass("white1");
          } else {
            that.$router.push({ path: '/activityset/Jifenhuodong' });
          }
        } else {
          alert(res.data.errmsg)
        }

      })
        .catch(function(err) {
          console.log(err)
        })

      //保存关联产品信息

      // for (let i = 0; i < that.tableData.length; i++) {
      //   that.parameterchain.push(that.tableData[i].product_id)
      //   that.parameterchain.push(that.tableData[i].consume_integral)
      //   that.parameterchain.push(that.tableData[i].recommend_integral)
      //   that.parameterchain.push(that.jifenmoshi)
      //   that.save_parameterchain.push(that.parameterchain.join('_'))
      //   that.parameterchain = []
      // }
      // that.$http.post('mshop/admin/index.php?m=activity&a=add_activity_integral_product&customer_id=' + that.customer_id, {
      //     system_code: '',
      //     'data': { "int_save_data": that.save_parameterchain, act_id: that.act_id } //形式：pid_可获积分_推荐购物积分_积分类型 //第一个产品  //第二个产品
      //   }).then(function(res) {
      //     that.$router.push({ path: '/activityset/Jifenhuodong' });
      //   })
      //   .catch(function(err) {
      //     console.log(err)
      //   })
    },
    delect: function(act_id, p_id) {
      var that = this
      if (confirm("确认是否删除？")) {
        that.$http.post('mshop/admin/index.php?m=activity&a=del_activity_product&customer_id=' + that.customer_id, {
          " system_code": "",
          "data": { "p_id": p_id, "act_id": act_id, }
        }).then(function(res) {
          window.location.reload()
        }).catch(function(err) {

        })
      }
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
</style>
