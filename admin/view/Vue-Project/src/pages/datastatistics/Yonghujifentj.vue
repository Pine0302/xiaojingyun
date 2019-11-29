<template>
  <div class="oplog">
    <div class="WSY_content">
      <div class="WSY_columnbox">
        <el-form ref="form" :model="form" label-width="80px" style="margin:20px;min-width:1000px;" class="table2excel">
          <el-form-item label="用户名称" class="top-inp">
            <el-input v-model="form.search_user_name"></el-input>
          </el-form-item>
          <el-form-item label="用户编码" class="top-inp">
            <el-input v-model="form.search_user_id"></el-input>
          </el-form-item>
          <el-form-item label="创建时间" class="top-inp timequantum">
            <el-col :span="10">
              <el-date-picker type="date" placeholder="选择日期" v-model="form.start_time" style="width: 100%;"></el-date-picker>
            </el-col>
            <el-col class="line" :span="3">至</el-col>
            <el-col :span="10">
              <el-date-picker type="date" placeholder="选择日期" v-model="form.end_time" style="width: 100%;"></el-date-picker>
            </el-col>
          </el-form-item>
          <el-button type="primary" class="WSY_bottonliss" @click="search">搜索</el-button>
          <!-- <el-button type="primary" class="WSY_bottonliss export_btn" @click="flag && oFunc(0)">导出</el-button> -->
          <table width="97%" class="WSY_table">
            <thead class="WSY_table_header" :class="skin">
              <tr>
                <th width="5%">序号</th>
                <th width="8%">用户头像</th>
                <th width="5%">用户编码</th>
                <th width="8%">用户名称</th>
                <th width="8%">积分余额</th>
                <th width="5%">累计收入积分</th>
                <th width="5%">累计签到积分</th>
                <th width="5%">累计出账积分</th>
                <th width="5%">清除次数</th>
                <th width="5%">清除总额</th>
                <th width="5%">操作</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for='(item,index) in tableData'>
                <td>{{item.id}}</td>
                <td><img style="margin-top: 14px;" :src="item.weixin_headimgurl" width="50" height="50"></td>
                <td>{{item.user_id}}</td>
                <td>{{item.weixin_name}}</td>
                <td>{{Number(item.balance) + Number(item.store_balance)}}</td>
                <td>{{Number(item.input)+Number(item.store_input)}}</td>
                <td>
                  <router-link :to="{name: 'Qiandaomingxi', query: {'user_id': item.user_id}}">{{item.sign_score}}</router-link>
                </td>
                <td>{{Number(item.output)+Number(item.store_output)}}</td>
                <td>{{item.clear_num}}</td>
                <td>{{item.clear_sum}}</td>
                <td class="WSY_t4">
                  <a title="查看" @click="check(item.user_id)">
                                        <img src="../../assets/images_V6.0/operating_icon/icon1.png">
                                    </a>
                </td>
              </tr>
            </tbody>
          </table>
          <el-pagination @current-change="handleCurrentChange" :current-page.sync="page" :page-size="count" layout="prev, pager, next, jumper" :total="total">
          </el-pagination>
        </el-form>
      </div>
    </div>
  </div>
</template>
<script>
import '../../assets/js/jquery-1.12.1.min.js'
import '../../assets/js/jquery.table2excel.min.js'
import util from '../../utils';


export default {
  created: function() {
    var self = this
    //换肤
self.$http.post("mshop/admin/index.php?m=setting&a=get_shop_skin", {
})
.then(res => {
self.skin=res.data.skin
}).catch(err => {
console.log(err)
})

    self.$http.post('mshop/admin/index.php?m=activity&a=integral_stat_user_list&customer_id=' + self.customer_id, {
      system_code: '',
      data: {
        "search_user_name": "", //搜索参数：用户名称
        "start_time": "", //搜索参数：开始时间
        "end_time": "", //搜索参数：结束时间
        "search_user_id": "", //搜索参数：用户id
        "page": self.page - 1, //页数
        "count": self.count //每页数量
      }
    }).then(function(res) {
      for (var i = 0; i < res.data.data.total && i < res.data.data.list_num; i++) {
        self.tableData.push(res.data.data[i])
      }
      self.total = parseInt(res.data.data.total);
    }).catch(function(err) {
      console.log(err)
    })
  },
  data() {
    return {
      skin:'',
      flag: true,
      exportcpage: 0,
      total: 0,
      "page": 1, //页数
      "count": 10,
      form: {
        search_user_name: '',
        search_user_id: '',
        start_time: '',
        end_time: ''
      },
      tableData: []
    }
  },

  methods: {
    handleCurrentChange(val) {
      var self = this;
      if (val) {
        self.page = val;
      } else {
        self.page = 1;
      }
      self.form.page = self.page - 1;
      self.form.count = self.count;
      this.$http.post('mshop/admin/index.php?m=activity&a=integral_stat_user_list&customer_id=' + self.customer_id, {
        system_code: '',
        data: self.form
      }).then(function(res) {
        self.tableData = [];
        for (var i = 0; i < res.data.data.total && i < res.data.data.list_num; i++) {
          self.tableData.push(res.data.data[i])
        }
        self.total = parseInt(res.data.data.total);
      }).catch(function(err) {
        console.log(err)
      })
    },
    search: function() {
      var self = this
      self.form.page = self.page - 1;
      self.form.count = self.count;
      if (self.form.start_time) {
                self.form.start_time = util.formatDateTimeFil(self.form.start_time, "yyyy-MM-dd");
            }
            if (self.form.end_time) {
                self.form.end_time = util.formatDateTimeFil(self.form.end_time, "yyyy-MM-dd");
            }
      this.$http.post('mshop/admin/index.php?m=activity&a=integral_stat_user_list&customer_id=' + self.customer_id, {
        system_code: '',
        data: self.form
      }).then(function(res) {
        self.handleCurrentChange();
      }).catch(function(err) {
        console.log(err)
      })
    },
    check: function(user_id) {
      this.$router.push({ path: '/datastatistics/Jifenmingxi', query: { 'user_id': user_id } });
    },
    oFunc: function(expage) {
      var self = this
      var exportcpage = 0;
      var url = '/mshop/admin/index.php?m=activity&a=integral_stat_user_list&customer_id=' + self.customer_id
      self.$http.post(url, {
        data: { "page": expage, "export": 1 }
      }).then(function(res) {
        var exporttotal = res.data.sum1;
        var exportpages = Math.ceil(res.data.sum1 / 20);
        if (expage < exportpages - 1) {
          expage = res.data.page1;
          console.log(expage + "expage")
          self.oFunc(expage);
          self.flag = false;
        } else {
          self.flag = true;
          window.location.href = '/mshop/admin/index.php?m=activity&a=integral_stat_user_list&output=1&customer_id=' + self.customer_id;
          return;
        }
      }).catch(function(err) {
        console.log(err)
      })

    }
  }
}

</script>
<style scoped>
.top-inp {
  display: inline-block;
  margin-right: 10px;
}

.el-input__inner {
  height: 26px;
  border: 1px solid #ccc;
  border-radius: 0;
  max-width: 150px;
}

.timequantum {
  vertical-align: bottom;
  width: 430px;
}

.line {
  text-align: center;
}

</style>
