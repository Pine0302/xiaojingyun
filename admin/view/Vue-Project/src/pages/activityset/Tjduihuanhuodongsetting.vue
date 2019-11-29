<template>
  <div class="oplog">
    <el-form ref="form" :model="form" label-width="120px" style="margin:20px;min-width:1000px;">
      <div v-if="form.status==0 || !form.status">
        <el-form-item label="活动名称">
          <el-input v-model="form.act_name" placeholder="填写活动名称"></el-input>
        </el-form-item>
        <el-form-item label="活动时间" style="width:476px;">
          <el-col :span="10">
            <el-date-picker type="date" placeholder="选择日期" v-model="form.start_time" style="width: 100%;"></el-date-picker>
          </el-col>
          <el-col class="line" :span="3">至</el-col>
          <el-col :span="10">
            <el-date-picker type="date" placeholder="选择日期" v-model="form.end_time" style="width: 100%;"></el-date-picker>
          </el-col>
        </el-form-item>
        <el-form-item label="积分类型" v-show="shop_onoff!=0 || store_onoff!=0">
          <label v-show="shop_onoff==1">
            <input type="checkbox" class="checkbox" value="1" v-model="mallpoint">仅{{diyname.shop_integral_name}}</label>
          <label v-show="store_onoff==1">
            <input type="checkbox" class="checkbox" value="2" v-model="storepoint">仅{{diyname.store_integral_name}}</label>
        </el-form-item>
        <el-form-item label="是否自动发布">
          <label>
            <input type="radio" name="auto_start" class="radio" value="0" v-model="auto_start">否</label>
          <label>
            <input type="radio" name="auto_start" class="radio" value="1" v-model="auto_start">是</label>
        </el-form-item>
      </div>
      <div v-if="form.status>0">
        <el-form-item label="活动名称">
          <el-input disabled="disabled" v-model="form.act_name" placeholder="填写活动名称"></el-input>
        </el-form-item>
        <el-form-item label="活动时间" style="width:476px;">
          <el-col :span="10">
            <el-date-picker disabled="disabled" type="date" placeholder="选择日期" v-model="form.start_time" style="width: 100%;"></el-date-picker>
          </el-col>
          <el-col class="line" :span="3">至</el-col>
          <el-col :span="10">
            <el-date-picker disabled="disabled" type="date" placeholder="选择日期" v-model="form.end_time" style="width: 100%;"></el-date-picker>
          </el-col>
        </el-form-item>
        <el-form-item label="积分类型" v-show="shop_onoff!=0 || store_onoff!=0">
          <label v-show="shop_onoff==1">
            <input type="checkbox" disabled="disabled" class="checkbox" value="1" v-model="mallpoint">仅{{diyname.shop_integral_name}}</label>
          <label v-show="store_onoff==1">
            <input type="checkbox" disabled="disabled" class="checkbox" value="2" v-model="storepoint">仅{{diyname.store_integral_name}}</label>
        </el-form-item>
        <el-form-item label="是否自动发布">
          <label>
            <input disabled="disabled" type="radio" name="auto_start" class="radio" value="0" v-model="auto_start">否</label>
          <label>
            <input disabled="disabled" type="radio" name="auto_start" class="radio" value="1" v-model="auto_start">是</label>
        </el-form-item>
      </div>
      <Bigbutton btnval="保存" @click.native="save" style="text-align:center;"></Bigbutton>
    </el-form>
  </div>
</template>
<script>
import Bigbutton from '../../components/Bigbutton'



export default {
  data() {
    return {
      shopintegralname: '',
      storeintegralname: '',
      customname: '',
      shop_onoff:0,
      store_onoff:0,
      only_type: '',
      mallpoint: '',
      storepoint: '',
      auto_start: '0',
      total: 0,
      count: 10,
      page: 1,
      act_id: '',
         diyname: {
                "shop_integral_name": "",
                "store_integral_name": "",
                "custom_name": ""
            },
      form: {

        op: 'conserve',
        act_name: '',
        start_time: '',
        end_time: '',
        act_id: '',
        act_type: '2',
        is_commission: '1',
        ext_info: {
          is_refund: '0',
          is_return: '0',
          is_exchange: '0'
        }


      },
      tableData: [],
      parameterchain: [],
      save_parameterchain: [] //参数链


    }
  },
  created: function() {
    var self = this;

    self.form.act_id = self.$route.query.act_id //获取路由传过来的参数act_id
    self.form.act_type = self.$route.query.act_type //获取路由传过来的参数act_type

    if (self.form.act_id && self.form.act_id != '-1') {
      //获取活动信息
      self.$http.post('mshop/admin/index.php?m=activity&a=get_exchange_status&customer_id=' + self.customer_id, {
        'system_code': '',
        'data': {
          'activity_id': self.form.act_id
        }
      }).then(function(res) {

        self.form = res.data.data
        self.only_type = res.data.data.only_type
        self.fenyong = res.data.data.is_commission
      console.log( self.only_type)
        if (self.only_type == '-1') {
          self.mallpoint = true
          self.storepoint = true
           console.log( '-1')
        } else if (self.only_type == '1') {
          self.mallpoint = true
          self.storepoint = false
           console.log( '1')
        } else if (self.only_type == '2') {
          self.mallpoint = false
          self.storepoint = true
           console.log('2')
        }

      })
        .catch(function(err) {
          console.log(err)
        })
    }else{
           //获取商城和问点积分开关
        self.$http.post('mshop/admin/index.php?m=activity&a=get_shopmall_integral_onoff_setting&customer_id=' + self.customer_id, {
            system_code: '',
            data: {
            }
        }).then(function(res) {
          if(res.data.data.shop_onoff==='1'){
                self.mallpoint = true
          }else{
             self.mallpoint = false
          }
             if(res.data.data.store_onoff==='1'){
                self.storepoint = true
          }else{
             self.storepoint = false
          }

        }).catch(function(err) {
            console.log(err)
        })
    }
     //获取自定义名称
        self.$http.post('mshop/admin/index.php?m=activity&a=get_diyname&customer_id=' + self.customer_id, {
            system_code: '',
            data: {
            }
        }).then(function(res) {
            self.diyname=res.data.data;

        }).catch(function(err) {
            console.log(err)
        })
        self.getDiyname();
        console.log()
        self.$http.post('/mshop/admin/index.php?m=activity&a=get_shopmall_integral_onoff_setting&customer_id=' + self.customer_id, {
            system_code: '',
            data: {
            }
        }).then(function(res) {
            self.shop_onoff=res.data.data.data.shop_onoff;
            self.store_onoff=res.data.data.data.store_onoff;
            if(self.shop_onoff==0 && self.store_onoff==0){
              if(self.shopintegralname || self.storeintegralname){
                alert("请先开启"+self.shopintegralname+"或"+self.storeintegralname+"在进行设置！");
              }
            }
        }).catch(function(err) {
            console.log(err)
        })
        

  },
  components: {
    Bigbutton
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
          'act_id': self.form.act_id, //活动id   
          page: self.page, //当前页数   
          page_size: self.count, //每页数量

        }
      }).then(function(res) {
        self.tableData = [];
        for (var i = 0; i < res.data.datas.total && i < res.data.datas.list_num; i++) {
          self.tableData.push(res.data.datas.list[i])
        }
        console.log(self.tableData)
        self.total = parseInt(res.data.datas.total);
      })
        .catch(function(err) {
          console.log(err)
        })
    },
    // addproducts: function() {
    //   var that = this;
    //   that.form.op = 'conserve'
    //   that.form.auto_start = that.auto_start
    //   that.form.start_time = that.formatDateTimeFil((that.form.start_time), 'yyyy-MM-dd') + " 00:00:00"
    //   that.form.end_time = that.formatDateTimeFil((that.form.end_time), 'yyyy-MM-dd') + " 23:59:59"
    //   if (that.form.status > 0) {
    //     that.$router.push({ path: '/activityset/Tianjiachanpin', query: { 'act_id': that.form.act_id, 'act_type': 2 } });
    //   } else {
    //     that.$http.post('mshop/admin/index.php?m=activity&a=save_activity&customer_id=' + that.customer_id, { 'system_code': '', 'data': that.form }).then(function(res) {
    //         if (res.data.errcode == 0) {
    //           that.act_id = res.data.data.act_id
    //           console.log(that.act_id)
    //           that.$router.push({ path: '/activityset/Tianjiachanpin', query: { 'act_id': that.act_id, 'act_type': 2 } });
    //         } else {
    //           alert("活动名称和活动时间不能为空")
    //         }
    //       })
    //       .catch(function(err) {
    //         console.log(err)
    //       })
    //   }


    // },
    save: function() {
      var that = this;
      if(!that.mallpoint && !that.storepoint){
          alert("必须选择积分类型");
          return
              
      }
      //保存活动信息
      that.form.op = 'conserve'
      that.form.auto_start = that.auto_start
      that.form.is_commission='1'
      that.form.ext_info = { "model": that.jifenmoshi }
  
      if (that.form.start_time == '') {
        that.form.start_time = ''
      } else {
        that.form.start_time = that.formatDateTimeFil((that.form.start_time), 'yyyy-MM-dd') + " 00:00:00"
      }

      if (that.form.end_time == '') {
        that.form.end_time = ''
      } else {
        that.form.end_time = that.formatDateTimeFil((that.form.end_time), 'yyyy-MM-dd') + " 23:59:59"
      }
     if(that.mallpoint==true && that.storepoint==true ){
        that.form.only_type=-1
        
      }else{
         if(that.mallpoint==true){
            that.form.only_type=1
          }else if(that.storepoint==true){
            that.form.only_type=2
          }
      }


      that.$http.post('mshop/admin/index.php?m=activity&a=save_activity&customer_id=' + that.customer_id, { 'system_code': '', 'data': that.form }).then(function(res) {
        if (res.data.errcode == 0) {
          if (res.data.data.operation === 'insert') {
            that.$router.push({ path: '/activityset/Tjduihuanhuodong/Tjduihuanhuodongrelevance', query: { 'act_id': res.data.data.act_id, 'act_type': 2, 'active': 2 } });
            $(".tab_btn_box").find(".tab_btn").eq(1).addClass("white1").end().eq(0).removeClass("white1");
          } else {
            that.$router.push({ path: '/activityset/Duihuanhuodong' });
          }
        } else {
          alert(res.data.errmsg)
        }



      })
        .catch(function(err) {
          console.log(err)
        })

      //保存活动关联产品

      // for (let i = 0; i < that.tableData.length; i++) {
      //   that.parameterchain.push(that.tableData[i].product_id)
      //   that.parameterchain.push(that.tableData[i].integral)
      //   that.parameterchain.push(that.tableData[i].store_integral)
      //   that.parameterchain.push(that.tableData[i].money)
      //   that.parameterchain.push(that.tableData[i].stock)
      //   that.save_parameterchain.push(that.parameterchain.join('_'))
      //   that.parameterchain = []
      // }
      // that.$http.post('mshop/admin/index.php?m=activity&a=add_integral_exchange_product&customer_id=' + that.customer_id, {
      //     system_code: '',
      //     'data': { "exc_save_data": that.save_parameterchain, act_id: that.form.act_id } //形式：pid_可获商城积分_可获得门店积分_推荐购物积分_积分类型 //第一个产品     //第二个产品
      //   }).then(function(res) {
      //     console.log(1111111)
      //     console.log(res)
      //     console.log(1111111)
      //     that.$router.push({ path: '/activityset/Duihuanhuodong' });
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
</style>
