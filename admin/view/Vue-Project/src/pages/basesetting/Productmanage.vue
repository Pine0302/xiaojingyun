<template>
  <div class="productmanage">
    <div class="WSY_content">
      <div class="WSY_columnbox">
        <div class="WSY_search_q">
          <div class="WSY_search_div">
            <div class="WSY_iptbox">
              <span>产品名称</span>
              <input type="text" name="naming" placeholder="" maxlength="10" v-model="naming">
            </div>
            <div class="WSY_iptbox">
              <span>产品编码</span>
              <input type="number" name="type" placeholder="" maxlength="10" v-model="product_id">
            </div>
            <div class="WSY_select_box">
              <span>产品分类</span>
              <select v-model="value" placeholder="--请选择--" class="WSY_elselect">
                <option v-for="item in options" :key="item.id" :id="item.id" :label="item.name" :value="item.id">
                </option>
              </select>
            </div>
            <div class="WSY_small_btn" :class="skin" @click="searchFun">搜索</div>
            <!-- <div class="WSY_small_btn" @click="">导出</div> -->
            <div class="WSY_small_btn" :class="skin" @click="addproducts">添加产品</div>
          </div>
        </div>
        <table width="97%" class="WSY_table table2excel">
          <thead class="WSY_table_header" :class="skin">
            <tr>
              <th width="5%">序号</th>
              <th width="5%">产品图片</th>
              <th width="5%">产品编码</th>
              <th width="8%">产品名称</th>
              <th width="8%">产品分类</th>
              <th width="5%">产品原价</th>
              <th width="5%">产品现价</th>
              <th width="5%">可获{{shopintegralname}}</th>
              <th width="5%">推荐积分</th>
              <th width="5%">操作</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in tableData">
              <td>{{item.id}}</td>
              <td>
                <img style="margin-top: 8px;margin-bottom: 8px;" :src="basehear+item.default_imgurl" width="50" height="50"></td>
              <td>{{item.product_id}}</td>
              <td>{{item.name}}</td>
              <td>{{item.type_name}}</td>
              <td>{{item.orgin_price}}</td>
              <td>{{item.now_price}}</td>
              <td>{{item.true_consume_integral}}</td>
              <td>{{item.true_recommend_integral}}</td>
              <td class="WSY_t4">
                <a title="编辑">
                                    <img src="../../assets/images_V6.0/operating_icon/icon05.png" @click="edit(item.product_id)"></a>
                <a title="删除">
                                    <img src="../../assets/images_V6.0/operating_icon/icon04.png" @click="delect(item.product_id)"></a>
              </td>
            </tr>
          </tbody>
        </table>
        <el-pagination @current-change="handleCurrentChange" :current-page.sync="page" :page-size="page_size" layout="prev, pager, next, jumper" :total="total">
        </el-pagination>
      </div>
    </div>
  </div>
</template>
<script>
import '../../assets/js/jquery.table2excel.min.js'
import util from '../../utils'

export default {
  name: 'productmanage',
  data() {
    return {
      skin:'',
      shopintegralname: '',
      storeintegralname: '',
      customname: '',
      total: 0,
      page_size: 10,
      page: 1,
      naming: '',
      product_id: '',
      options: [],
      value: '',
      tableData: []     
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
    edit: function(obj) {
      this.$router.push({ path: '/basesetting/edit', query: { 'product_id': obj ,"integral_type": "0" } });
    },
    delect: function(product_id) {
      var self = this;
      var urlget = 'mshop/admin/index.php?m=activity&a=del_integral_product&customer_id=' + self.customer_id;
      var transferData = {
        "p_id": product_id,    //产品id
        "integral_type": "0" ,     //0商城   1门店
      };
      if (confirm("确认是否删除？")) {
        self.$http.post(urlget, {
            "system_code": "",
            "data": transferData
          })
          .then(res => {
            window.location.reload();
          }).catch(err => {
            console.log(err)
          })
      }
    },
    search: function() {
      var self = this;
      var urlget = 'mshop/admin/index.php?m=activity&a=read_admin_log&customer_id=' + self.customer_id;
      var transferData = {
        "keyword": self.keyword, //查询关键词
        "page": self.page, //页数
        "page_size": self.page_size, //每页显示数量
        "start_time": self.start_time, //查询时间（起始时间）
        "end_time": self.end_time //查询时间（结束时间）
      };
      self.$http.post(urlget, {
          "system_code": "",
          "data": transferData
        })
        .then(function(res) {
          self.handleCurrentChange();
        })
        .catch(function(err) {
          console.log(err)
        })
    },
    searchFun() {
      var self = this;
      var urlget = 'mshop/admin/index.php?m=activity&a=__get_integral_product&customer_id=' + self.customer_id;
      var transferData = {
        "search_key": {
          "product_name": self.naming,
          "product_id": self.product_id,
          "type_id": self.value
        },
        "page": self.page, //页数
        "page_size": self.page_size,
        "integral_type": "0" //0商城   1门店
      };
      self.$http.post(urlget, {
          "system_code": "",
          "data": transferData
        })
        .then(res => {
          self.handleCurrentChange();
        }).catch(err => {
          console.log(err)
        })
    },
    handleCurrentChange(val) {
      var self = this;
      if (val) {
        self.page = val;
      } else {
        self.page = 1;
      }
      var urlget = 'mshop/admin/index.php?m=activity&a=__get_integral_product&customer_id=' + self.customer_id;
      var transferData = {
        "search_key": {
          "product_name": self.naming,
          "product_id": self.product_id,
          "type_id": self.value
        },
        "page": self.page, //页数
        "page_size": self.page_size,
      "integral_type": "0" //0商城   1门店
      };
      self.$http.post(urlget, {
          "system_code": "",
          "data": transferData
        })
        .then(function(res) {
          console.log(res)
          var datajsonget = res.data.datas;
          self.total = parseInt(datajsonget.total);
          self.tableData = datajsonget.list;
        })
        .catch(function(err) {
          console.log(err)
        })
    },
    exportFun() {
      $(".table2excel").table2excel({
        exclude: ".noExl",
        name: "Excel Document Name",
        filename: "产品管理",
        exclude_img: true,
        exclude_links: false,
        exclude_inputs: true
      });
    },
    addproducts() {
      this.$router.push({ path: '/activityset/Tianjiachanpin', query: { 'act_id': '-1', 'act_type': 0 ,"integral_type":"0"} });
    }
  },
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
    // 共公接口—获取自定义名称（商城积分，门店积分，购物币）
    self.getDiyname();
    var urlget = 'mshop/admin/index.php?m=activity&a=__get_integral_product&customer_id=' + self.customer_id;
    var transferData = {
      "search_key": {
        "product_name": self.naming,
        "product_id": self.product_id,
        "type_id": self.value
      },
      "page": self.page, //页数
      "page_size": self.page_size,
      "integral_type": "0" //0商城   1门店
    };

    self.$http.post(urlget, {
        "system_code": "",
        "data": transferData
      })
      .then(res => {
        var datajsonget = res.data.datas;
        self.total = parseInt(datajsonget.total);
        self.tableData = datajsonget.list;
      }).catch(err => {
        console.log(err)
      })

    var getAllType = 'mshop/admin/index.php?m=activity&a=__get_all_type&customer_id=' + self.customer_id;
    self.$http.post(getAllType, {
        "system_code": ""
      })
      .then(res => {
        var newArr = [];
        newArr.push({ "id": '', "name": "全部分类" });
        if (res.data.first_type != "") {
          $(res.data.first_type).each(function(i, data) {
            newArr.push(data);
            if (data.son != undefined) {
              $(data.son).each(function(i, data1) {
                newArr.push({ "id": data1.id, "name": "--" + data1.name })
              });
            }
          });
          console.log(newArr)
          self.options = newArr;
        }
      }).catch(err => {
        console.log(err)
      })
  }
}

</script>
<style scoped>
/*@import '../../assets/css/comstyleone.css'*/

.WSY_elselect {
  height: 26px;
}

</style>
