<template>
  <div class="oplog">
    <div class="WSY_content">
      <div class="WSY_columnbox">
        <el-form ref="form" :model="form" label-width="80px" style="margin:20px;min-width:1000px;">
          <!-- <el-form-item label="时间" class="top-inp timequantum">
                        <el-col :span="10">
                            <el-date-picker type="date" placeholder="选择日期" v-model="form.starttime" ></el-date-picker>
                        </el-col>
                        <el-col class="line" :span="1">至</el-col>
                        <el-col :span="10">
                            <el-date-picker type="date" placeholder="选择日期" v-model="form.endtime" ></el-date-picker>
                        </el-col>
                    </el-form-item> -->
          <div class="date_search">
            <div class="com_date">
              <span class="demonstration">时间</span>
              <el-date-picker v-model="form.starttime" type="date" placeholder="选择日期"></el-date-picker>
            </div>
            <div class="com_date">
              <span class="demonstration" style="margin-left:15px;">至</span>
              <el-date-picker v-model="form.endtime" type="date" placeholder="选择日期"></el-date-picker>
            </div>
          </div>
          <el-form-item class="top-inp topbtn">
            <el-button type="primary" class="WSY_bottonliss" @click.native="search">搜索</el-button>
            <!-- <el-button type="primary" class="WSY_bottonliss" @click="exportFun">导出</el-button> -->
          </el-form-item>
          <div class="qiandao">累计签到积分：{{head.sign_numbers}}；签到次数：{{head.sign_count}}</div>
          <table width="97%" class="WSY_table table2excel">
            <thead class="WSY_table_header" :class="skin">
              <th width="5%">序号</th>
              <th width="8%">时间</th>
              <th width="5%">签到获得积分</th>
            </thead>
            <tbody>
              <tr v-for='item in tableData'>
                <td>{{item.log_id}}</td>
                <td>{{item.add_time}}</td>
                <td>{{item.number}}</td>
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

    self.user_id = self.$route.query.user_id
    self.$http.post('mshop/admin/index.php?m=activity&a=integral_sign_log&customer_id=' + self.customer_id, {
        'system_code': '',
        'data': {
          "user_id": self.user_id,
          "type": 1,
          "page": self.page, //页数
          "count": self.count,
          "start_time": "",
          "end_time": ""
        }
      }).then(function(res) {
        self.head = res.data.data_count
        for (var i = 0; i < res.data.data_list.total && i < res.data.data_list.list_num; i++) {
          self.tableData.push(res.data.data_list.data[i])
        }
        self.total = parseInt(res.data.data_list.total);
      })
      .catch(function(err) {
        console.log(err)
      })



  },

  data() {
    return {
      skin:'',
      head: {
        sign_numbers: '',
        sign_count: '',
      },
      total: 0,
      "page": 1, //页数
      "count": 10,
      form: {
        name: '',
        code: '',
        starttime: '',
        endtime: '',

      },
      tableData: []
    }
  },
  methods: {
    exportFun() {
      $(".table2excel").table2excel({
        exclude: ".noExl",
        name: "Excel Document Name",
        filename: "签到明细",
        exclude_img: true,
        exclude_links: true,
        exclude_inputs: true
      });
    },
    handleCurrentChange(val) {
      var self = this;
      if (val) {
        self.page = val;
      } else {
        self.page = 1;
      }
      self.$http.post('mshop/admin/index.php?m=activity&a=integral_sign_log&customer_id=' + self.customer_id, {
          'system_code': '',
          'data': {
            "user_id": self.user_id,
            "type": 1,
            "page": self.page, //页数
            "count": self.count,
            "start_time": self.form.starttime,
            "end_time": self.form.endtime
          }
        }).then(function(res) {
          self.head = res.data.data_count
          self.tableData = []
          for (var i = 0; i < res.data.data_list.total && i < res.data.data_list.list_num; i++) {
            self.tableData.push(res.data.data_list.data[i])
          }
          self.total = parseInt(res.data.data_list.total);

        })
        .catch(function(err) {
          console.log(err)
        })
    },
    search: function() {
      var self = this;

      self.$http.post('mshop/admin/index.php?m=activity&a=integral_sign_log&customer_id=' + self.customer_id, {
          'system_code': '',
          'data': {
            "user_id": self.user_id,
            "type": 1,
            "page": self.page - 1, //页数
            "count": self.count,
            "start_time": self.form.starttime,
            "end_time": self.form.endtime
          }
        }).then(function(res) {
          self.handleCurrentChange();
          //                 self.head = res.data.data_count
          //                 self.tableData = []
          //                 for (var i = 0; i < res.data.data_list.total; i++) {
          //                     self.tableData.push(res.data.data_list[i])
          //                 }
          // self.total=parseInt(res.data.data_list.total);

        })
        .catch(function(err) {
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

.qiandao {
  margin-bottom: 10px;
}

.el-input__inner {
  height: 26px;
  border: 1px solid #ccc;
  border-radius: 0;
}
.el-date-editor.el-input {
  width: 150px;
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

</style>
