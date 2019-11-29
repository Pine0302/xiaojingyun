<template>
  <div class="oplog">
    <el-form ref="form" :model="form" label-width="20px" style="margin:20px;min-width:1000px;">
      <el-form-item>
        <el-button type="primary" class="WSY_bottonliss" @click="addproducts" style="width:100px">添加产品</el-button>
        <el-button type="primary" class="WSY_bottonliss" @click.native="save" style="width:100px">保存</el-button>
      </el-form-item>
      <table width="97%" class="WSY_table">
        <thead class="WSY_table_header" :class="skin">
          <th width="5%">序号</th>
          <th width="8%">产品图片</th>
          <th width="5%">产品编码</th>
          <th width="8%">产品名称</th>
          <th width="8%">产品分类</th>
          <th width="5%">产品现价</th>
          <th width="5%">可获得积分</th>
          <th width="5%">推荐购物积分</th>
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
            <td>
              <input v-if="form.status==='0'" type="number" v-model="item.consume_integral" @keyup="consume(index)" class="integral">
               <input v-if="form.status>'0'  && item.is_edit==='0'" type="number" v-model="item.consume_integral" @keyup="consume(index)" class="integral">
                <input v-if="form.status>'0'  && item.is_edit==='1'" type="number" disabled="disabled" v-model="item.consume_integral" @keyup="consume(index)" class="integral">
              <span v-if="jifenmoshi==1">%</span>
            </td>
            <td>
              <input v-if="form.status==='0'" type="number" v-model="item.recommend_integral" @keyup="recommend(index)" class="integral">
               <input v-if="form.status>'0'  && item.is_edit==='0'" type="number"  v-model="item.recommend_integral" @keyup="recommend(index)" class="integral">
                  <input v-if="form.status>'0'  && item.is_edit==='1'" type="number" disabled="disabled" v-model="item.recommend_integral" @keyup="recommend(index)" class="integral">
              <span v-if="jifenmoshi==1">%</span>
            </td>
            <td class="WSY_t4">
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

     self.act_id = self.$route.query.act_id //获取路由传过来的参数act_id
    self.type = self.$route.query.type //获取路由传过来的参数act_id
    self.act_type = self.$route.query.act_type //获取路由传过来的参数act_type
     self.act_name = self.$route.query.act_name //获取路由传过来的参数act_name
      self.start_time = self.$route.query.start_time //获取路由传过来的参数start_time
       self.end_time = self.$route.query.end_time //获取路由传过来的参数end_time

    if (self.act_id) {
      //获取活动信息
      self.$http.post('mshop/admin/index.php?m=activity&a=read&customer_id=' + self.customer_id, {
          'system_code': '',
          'data': {
            'act_id': self.act_id
          }
        }).then(function(res) {
          console.log(111)
          console.log(res.data.data)
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
      skin:'',
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
        act_name: '',
          start_time: '',
        end_time: '',
      tableData: [], //表格数据
      parameterchain: [],
      save_parameterchain: [] //参数链
    }
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
      that.form.ext_info = { "model": that.jifenmoshi };
      that.form.is_commission = that.fenyong;
      that.form.op = "conserve";
      that.form.auto_start = that.auto_start;
      that.form.act_type = "0";
      //  that.form.act_name = that.act_name;
      // that.form.start_time = that.formatDateTimeFil((that.start_time), 'yyyy-MM-dd') + " 00:00:00";
      // that.form.end_time = that.formatDateTimeFil((that.end_time), 'yyyy-MM-dd') + " 00:00:00";
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
         if(that.form.start_time==''){
         that.form.start_time=''
      }else{
          that.form.start_time = that.formatDateTimeFil((that.form.start_time), 'yyyy-MM-dd') + " 00:00:00"
      }

       if(that.form.end_time==''){
         that.form.end_time=''
      }else{
          that.form.end_time = that.formatDateTimeFil((that.form.end_time), 'yyyy-MM-dd') + " 23:59:59"
      }
    
      that.$http.post('mshop/admin/index.php?m=activity&a=save_activity&customer_id=' + that.customer_id, { system_code: '', 'data': that.form }).then(function(res) {

        })
        .catch(function(err) {
          console.log(err)
        })

      //保存关联产品信息

      for (let i = 0; i < that.tableData.length; i++) {
        that.parameterchain.push(that.tableData[i].product_id)
        that.parameterchain.push(that.tableData[i].consume_integral)
        that.parameterchain.push(that.tableData[i].recommend_integral)
        that.parameterchain.push(that.jifenmoshi)
        that.save_parameterchain.push(that.parameterchain.join('_'))
        that.parameterchain = []
      }
      that.$http.post('mshop/admin/index.php?m=activity&a=add_activity_integral_product&customer_id=' + that.customer_id, {
          system_code: '',
          'data': { "int_save_data": that.save_parameterchain, act_id: that.act_id } //形式：pid_可获积分_推荐购物积分_积分类型 //第一个产品  //第二个产品
        }).then(function(res) {
          that.$router.push({ path: '/activityset/Jifenhuodong' });
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
