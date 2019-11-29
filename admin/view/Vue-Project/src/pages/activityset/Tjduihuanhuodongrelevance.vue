<template>
  <div class="oplog">
    <el-form ref="form" :model="form" label-width="20px" style="margin:20px;min-width:1000px;">
      <el-form-item>
        <el-button type="primary" class="WSY_bottonliss" :class="skin" @click="addproducts" style="width:100px">添加产品</el-button>
        <el-button type="primary" class="WSY_bottonliss" :class="skin" @click.native="save" style="width:100px">保存</el-button>
      </el-form-item>
      <table width="97%" class="WSY_table">
        <thead class="WSY_table_header" :class="skin">
          <th width="5%">序号</th>
          <th width="8%">产品图片</th>
          <th width="5%">产品编码</th>
          <th width="8%">产品名称</th>
          <th width="8%">产品分类</th>
          <th width="5%">产品现价</th>
          <th width="5%" v-show="form.only_type==1 || form.only_type== -1">商城兑换积分</th>
          <th width="5%" v-show="form.only_type==2 || form.only_type== -1">门店兑换积分</th>
          <th width="5%">自付金额</th>
          <th width="5%">库存</th>
          <th width="5%">操作</th>
        </thead>
        <tbody>
          <tr v-for='(item,index) in tableData'>
            <td>{{index+1}}</td>
            <td><img :src="basehear + item.default_imgurl" width="40" height="40" style="margin-top: 10px;"></td>
            <td>{{item.product_id}}</td>
            <td>{{item.product_name}}</td>
            <td>{{item.type_name}}</td>
            <td>{{item.now_price}}</td>
            <td v-show="form.only_type==1 || form.only_type== -1">
              <input v-if="form.status==='0'" type="number" v-model="item.integral" class="integral" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">

               <input v-if="form.status>'0' && item.is_edit==='0'" type="number" v-model="item.integral" class="integral" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
              <input v-if="form.status>'0' && item.is_edit==='1'"  disabled="disabled" type="number" v-model="item.integral" class="integral" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
            </td>
            <td v-show="form.only_type==2 || form.only_type== -1">
              <input  v-if="form.status==='0'" type="number" v-model="item.store_integral" class="integral" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <input  v-if="form.status>'0' && item.is_edit==='0'"  type="number" v-model="item.store_integral" class="integral" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <input  v-if="form.status>'0' && item.is_edit==='1'"  disabled="disabled" type="number" v-model="item.store_integral" class="integral" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
            </td>
            <td>
              <input  v-if="form.status==='0'" type="text" v-model="item.money" class="integral" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <input  v-if="form.status>'0' && item.is_edit==='0'"   type="text" v-model="item.money" class="integral" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <input  v-if="form.status>'0' && item.is_edit==='1'"  disabled="disabled" type="text" v-model="item.money" class="integral" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
            </td>
            <td>
              <input  v-if="form.status==='0'" type="text" v-model="item.stock" class="integral" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
                <input  v-if="form.status>'0' && item.is_edit==='0'" type="text" v-model="item.stock" class="integral" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">
              <input  v-if="form.status>'0' && item.is_edit==='1'"  disabled="disabled" type="text" v-model="item.stock" class="integral" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}">

            </td>
            <td class="WSY_t4">
              <!-- <a v-if="from.status<'1'" title="编辑"  @click="edit(item.product_id)">
                                        <img src="../../assets/images_V6.0/operating_icon/icon05.png">
                                    </a> -->
              <a title="删除" @click="delect(item.act_id,item.product_id)">
                                        <img src="../../assets/images_V6.0/operating_icon/icon04.png">
                                    </a>
            </td>
          </tr>
        </tbody>
      </table>
      <el-pagination @current-change="handleCurrentChange" :current-page.sync="page" :page-size="count" layout="prev, pager, next, jumper" :total="total">
      </el-pagination>
    </el-form>
  </div>
</template>
<script>
export default {
  created: function() {
    var self = this;

    //换肤
    self.$http.post("mshop/admin/index.php?m=setting&a=get_shop_skin", {
    })
    .then(res => {
    self.skin=res.data.skin
    }).catch(err => {
    console.log(err)
    })
    self.getDiyname();
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
          
          self.status=res.data.data.status
          self.form.status=res.data.data.status
          self.form.only_type=res.data.data.only_type
          console.log(self.form.only_type)
        })
        .catch(function(err) {
          console.log(err)
        })

      //获取活动关联产品
      self.$http.post('mshop/admin/index.php?m=activity&a=__get_activity_product&customer_id=' + self.customer_id, {
          'system_code': '',
          'data': {
            'act_id': self.form.act_id, //活动id   
            page: self.page, //当前页数   
            page_size: self.count, //每页数量

          }
        }).then(function(res) {
          for (var i = 0; i < res.data.datas.total && i < res.data.datas.list_num; i++) {
            self.tableData.push(res.data.datas.list[i])
          }
          console.log(self.tableData)
          self.total = parseInt(res.data.datas.total);
        })
        .catch(function(err) {
          console.log(err)
        })
    }
          console.log(self.form.only_type)
  },
  data() {
    return {
      skin:'',
      shopintegralname: '',
      storeintegralname: '',
      customname: '',
      status:'',
      auto_start: '0',
      total: 0,
      count: 10,
      page: 1,
      act_id: '',
      form: {
         status:'',
         only_type:-1,
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
    addproducts: function() {
      var that = this;

      // that.form.op = 'conserve'
      // that.form.auto_start = that.auto_start
      // that.form.start_time = that.formatDateTimeFil((that.form.start_time), 'yyyy-MM-dd') + " 00:00:00"
      // that.form.end_time = that.formatDateTimeFil((that.form.end_time), 'yyyy-MM-dd') + " 23:59:59"
      // console.log( that.status)
      if (that.status<=1) {
        that.$router.push({ path: '/activityset/Tianjiachanpin', query: { 'act_id': that.form.act_id, 'act_type': 2 } });
      } else if(that.status==2){
            alert("不能添加产品");
      }else {
        alert('手动终止和结束的活动不能添加产品')
        // that.$http.post('mshop/admin/index.php?m=activity&a=save_activity&customer_id=' + that.customer_id, { 'system_code': '', 'data': that.form }).then(function(res) {
        //     if (res.data.errcode == 0) {
        //       that.act_id = res.data.data.act_id
        //       console.log(that.act_id)
        //       that.$router.push({ path: '/activityset/Tianjiachanpin', query: { 'act_id': that.act_id, 'act_type': 2 } });
        //     } else {
        //       alert("活动名称和活动时间不能为空")
        //     }
        //   })
        //   .catch(function(err) {
        //     console.log(err)
        //   })
      }


    },
    save: function() {
      var that = this;
      //保存活动信息
      that.form.op = 'conserve'
      that.form.auto_start = that.auto_start
      that.form.start_time = that.formatDateTimeFil((that.form.start_time), 'yyyy-MM-dd') + " 00:00:00"
      that.form.end_time = that.formatDateTimeFil((that.form.end_time), 'yyyy-MM-dd') + " 23:59:59"
      that.$http.post('mshop/admin/index.php?m=activity&a=save_activity&customer_id=' + that.customer_id, { 'system_code': '', 'data': that.form }).then(function(res) {

        })
        .catch(function(err) {
          console.log(err)
        })

      //保存活动关联产品

      for (let i = 0; i < that.tableData.length; i++) {
        that.parameterchain.push(that.tableData[i].product_id)
        that.parameterchain.push(that.tableData[i].integral)
        that.parameterchain.push(that.tableData[i].store_integral)
        that.parameterchain.push(that.tableData[i].money)
        that.parameterchain.push(that.tableData[i].stock)
        that.save_parameterchain.push(that.parameterchain.join('_'))
        that.parameterchain = []
      }
      that.$http.post('mshop/admin/index.php?m=activity&a=add_integral_exchange_product&customer_id=' + that.customer_id, {
          system_code: '',
          'data': { "exc_save_data": that.save_parameterchain, act_id: that.form.act_id } //形式：pid_可获商城积分_可获得门店积分_推荐购物积分_积分类型 //第一个产品     //第二个产品
        }).then(function(res) {
          that.$router.push({ path: '/activityset/Duihuanhuodong' });
        })
        .catch(function(err) {
          console.log(err)
        })

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
