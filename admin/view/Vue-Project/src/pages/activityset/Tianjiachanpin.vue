<template>
  <div class="oplog">
    <div class="WSY_content">
      <div class="WSY_columnbox">
        <el-form ref="form" label-width="80px" style="margin:20px;min-width:1000px;">
          <el-form-item label="产品名称" class="top-inp ">
            <el-input v-model="search_data.search_key.product_name" placeholder="请输入产品名称"></el-input>
          </el-form-item>
          <el-form-item label="产品编码" class="top-inp">
            <el-input v-model="search_data.search_key.product_id" placeholder="请输入产品编码"></el-input>
          </el-form-item>
          <!-- <el-form-item label="产品分类" class="top-inp timequantum">
                            <el-select v-model="search_data.search_key.type_id" placeholder="请选择">
                                <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value">
                                </el-option>
                            </el-select>
                        </el-form-item> -->
          <div class="WSY_select_box">
            <span>产品分类</span>
            <select v-model="value" placeholder="--请选择--" class="WSY_elselect">
              <option v-for="item in options" :key="item.id" :id="item.id" :label="item.name" :value="item.id">
              </option>
            </select>
          </div>
          <el-form-item class="top-inp timequantum">
            <el-button type="primary" class="WSY_bottonliss skin-btn" @click="search">搜索</el-button>
            <el-button type="primary" class="WSY_bottonliss skin-btn" @click="save">提交</el-button>
          </el-form-item>
          <div>
            <p>已选产品：</p>
            <ul v-if="items.length>0">
              <li class="selproduct" v-for="(item,index) in items" :index="item.index" :key="item.num">{{index+1}}、{{item.product_id}}、<span class="cpname">{{item.name}}；</span></li>
            </ul>
          </div>
          <table width="97%" class="WSY_table">
            <thead class="WSY_table_header" :class="skin">
              <th width="5%">
                <input type="checkbox" v-model="checkAll" @change="myclick"> </th>
              <th width="5%">序号</th>
              <th width="8%">产品图片</th>
              <th width="5%">产品编码</th>
              <th width="8%">产品名称</th>
              <th width="8%">产品分类</th>
              <th width="5%">产品原价</th>
              <th width="5%">产品现价</th>
              <th width="5%">产品库存</th>
            </thead>
            <tbody>
              <tr v-for='(item,index) in tableData'>
                <td>
                  <input type="checkbox" v-model="ischeck" :value='index'>
                </td>
                <td>{{index+1}}</td>
                <td><img :src="basehear + item.default_imgurl" width="40" height="40" style="margin-top: 10px;"></td>
                <td>{{item.product_id}}</td>
                <td>{{item.name}}</td>
                <td>{{item.type_name}}</td>
                <td>{{item.orgin_price}}</td>
                <td>{{item.now_price}}</td>
                <td>{{item.storenum}}</td>
              </tr>
            </tbody>
          </table>
          <el-pagination @current-change="handleCurrentChange" :current-page.sync="search_data.page" :page-size="search_data.page_size" layout="prev, pager, next, jumper" :total="total">
          </el-pagination>
        </el-form>
      </div>
    </div>
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

    self.$act_id = self.$route.query.act_id //获取路由传过来的参数act_id
    self.$integral_type=self.$route.query.integral_type //获取路由传过来的参数integral_type
    self.$act_type = self.$route.query.act_type //获取路由传过来的参数act_type
    console.log(self.$integral_type)
    self.search_data.act_id = self.$act_id
    if (self.$act_type == 0 && self.$act_id < 0) {
      //产品常量
      console.log('我是产品常量进来的')
      if(self.$integral_type==0){
           self.search_url = 'mshop/admin/index.php?m=common&a=get_products&customer_id=' + self.customer_id;
      }else{
           self.search_url = 'mshop/admin/index.php?m=activity&a=get_store_product_list&customer_id=' + self.customer_id;
       
      }
     
      self.save_url = 'mshop/admin/index.php?m=activity&a=add_integral_product&customer_id=' + self.customer_id;

    } else if (self.$act_type == 1 && self.$act_id > 0) {
      //积分产品
      console.log('我是积分产品进来的')
      self.search_url = 'mshop/admin/index.php?m=activity&a=get_integral_product_list&customer_id= ' + self.customer_id;
      self.save_url = 'mshop/admin/index.php?m=activity&a=add_activity_product&customer_id= ' + self.customer_id;

    } else if (self.$act_type == 2 && self.$act_id > 0) {
      //兑换产品
      console.log('我是兑换产品进来的')
      self.search_url = 'mshop/admin/index.php?m=activity&a=get_product_except_inte&customer_id=' + self.customer_id;
      //self.save_url = 'mshop/admin/index.php?m=activity&a=add_integral_exchange_product&customer_id=3243';
      self.save_url = 'mshop/admin/index.php?m=activity&a=add_activity_product&customer_id=' + self.customer_id;


    }
    self.$http.post(self.search_url, {
          "system_code": "",
          "data": self.search_data
        }

      ).then(function(res) {
        self.tableData = []
        for (var i = 0; i < res.data.datas.total && i < res.data.datas.list_num; i++) {
          self.tableData.push(res.data.datas.list[i])
        }
        self.total = parseInt(res.data.datas.total);
      })
      .catch(function(err) {
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
          self.options = newArr;
        }
      }).catch(err => {
        console.log(err)
      })

  },
  data() {
    return {
      skin:'',
      '$act_id': '',
      '$act_type': '',
      '$integral_type': '',
      search_url: '', //搜索接口url
      save_url: '', //保存可口url
      total: 0,
      count: 10,
      page: 1,
      search_data: {
        "page": 1, //页码
        "page_size": 10, //单页数据条数
        act_id: '',
        search_key: {
          "product_name": "", //搜索的产品名称，为空则不做筛选
          "product_id": "", //搜索的产品编码，为空则不做筛选
          "type_id": "" //搜索的产品分类id，-1为全部分类
        }

      }, //搜索接口需要传的参数
      save_data: {}, //保存接口需要传的参数
      zongshu: 200,
      currentPage4: 4,
      value: '',

      items: [],
      options: [],
      tableData: [],
      checkAll: '',
      ischeck: [],
      pids: '',
      pidsarr: [],
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
      self.$http.post(self.search_url, {
            "system_code": "",
            "data": self.search_data
          }

        ).then(function(res) {
          console.log(res.data.datas.list_num)
          self.tableData = []
           for (var i = 0; i < res.data.datas.total && i < res.data.datas.list_num; i++) {
            self.tableData.push(res.data.datas.list[i])
          }
          self.total = parseInt(res.data.datas.total);
          console.log(self.tableData);
        })
        .catch(function(err) {
          console.log(err)
        })

      if (self.$act_type == 0) {
        for (let i = 0; i < self.ischeck.length; i++) {
          self.pidsarr.push(self.tableData[self.ischeck[i]].id) //获取商品id链
        }
        self.pids = self.pidsarr.join('_');
        self.save_data = {
          "act_id":"1",
          "integral_type":self.$integral_type,//商城0  门店1
          "p_ids": self.pids, //商品ID链，pid_pid，如果只有一个产品，则只传一个pid即可
        };
      } else if (self.$act_type == 1) {
        if (self.ischeck.length != 0) {
          for (let i = 0; i < self.ischeck.length; i++) {
            self.pidsarr.push(self.tableData[self.ischeck[i]].product_id) //获取商品id链
          }
        } else {
          return;
        }

        self.pids = self.pidsarr.join('_');
        self.save_data = {
          "act_id": self.$act_id, //积分活动ID
          "act_type": '1',
          "p_ids": self.pids //形式：pid_可获积分_推荐购物积分   第一个产品  第二个产品 一直排下去

        };
      } else if (self.$act_type == 2) {
        if (self.ischeck.length != 0) {
          for (let i = 0; i < self.ischeck.length; i++) {
            self.pidsarr.push(self.tableData[self.ischeck[i]].product_id) //获取商品id链
          }
        } else {
          return;
        }

        self.pids = self.pidsarr.join('_');
        self.save_data = {
          "act_id": self.$act_id, //积分活动ID
          "act_type": '2',
          "p_ids": self.pids //形式：pid_可获积分_推荐购物积分   第一个产品  第二个产品 一直排下去
        };
      }

      if(this.items.length>0){
        self.$http.post(self.save_url, {
            "system_code": "",
            "data": self.save_data
          }

        ).then(function(res) {
          // location.reload();
        })
        .catch(function(err) {
          console.log(err)
        })
        this.ischeck = [];
      this.items = [];
      }
     
      

    },
    search: function() {
      var self = this;
      self.search_data.search_key.type_id = self.value
      self.$http.post(self.search_url, {
            "system_code": "",
            "data": self.search_data
          }

        ).then(function(res) {
          // self.handleCurrentChange();
          // console.log(self.tableData)
          self.tableData = []
          for (var i = 0; i < res.data.datas.total && i < res.data.datas.list_num; i++) {
            self.tableData.push(res.data.datas.list[i])
          }
          self.total = parseInt(res.data.datas.total);
        })
        .catch(function(err) {
          console.log(err)
        })
    },
    save: function() {
      var self = this;

      if (self.$act_type == 0) {
        for (let i = 0; i < self.ischeck.length; i++) {
          self.pidsarr.push(self.tableData[self.ischeck[i]].id) //获取商品id链
        }
        self.pids = self.pidsarr.join('_');
        self.save_data = {
          "act_id": "1",
          "integral_type":self.$integral_type,
          "p_ids": self.pids, //商品ID链，pid_pid，如果只有一个产品，则只传一个pid即可
        };
      } else if (self.$act_type == 1) {
        for (let i = 0; i < self.ischeck.length; i++) {
          self.pidsarr.push(self.tableData[self.ischeck[i]].product_id) //获取商品id链
        }
        self.pids = self.pidsarr.join('_');
        self.save_data = {
          "act_id": self.$act_id, //积分活动ID
          "act_type": '1',
          "p_ids": self.pids //形式：pid_可获积分_推荐购物积分   第一个产品  第二个产品 一直排下去

        };
      } else if (self.$act_type == 2) {
        for (let i = 0; i < self.ischeck.length; i++) {
          self.pidsarr.push(self.tableData[self.ischeck[i]].product_id) //获取商品id链
        }
        self.pids = self.pidsarr.join('_');
        self.save_data = {
          "act_id": self.$act_id, //积分活动ID
          "act_type": '2',
          "p_ids": self.pids //形式：pid_可获积分_推荐购物积分   第一个产品  第二个产品 一直排下去

        };
      }


      self.$http.post(self.save_url, {
            "system_code": "",
            "data": self.save_data
          }

        ).then(function(res) {
          if (self.$act_type == 0) {
            if(self.$integral_type==0){
              self.$router.push({ path: '/basesetting/productmanage' });
            }else{
              self.$router.push({ path: '/basesetting/Shopproductmanage' });
            }
            
          } else if (self.$act_type == 1) {
            self.$router.push({ path: '/activityset/Tianjiahuodong/Tianjiahuodongrelevance', query: { 'act_id': self.$act_id, 'act_type': 1 ,'active':2} });
          } else if (self.$act_type == 2) {
            self.$router.push({ path: '/activityset/Tjduihuanhuodong/Tjduihuanhuodongrelevance', query: { 'act_id': self.$act_id, 'act_type': 2,'active':2 } });
          }
        })
        .catch(function(err) {
          console.log(err)
        })
    },
    myclick: function() { //全选全不选
      console.log(this.checkAll)
      this.ischeck = [];
      this.items = []
      if (this.checkAll) {
        for (let i = 0; i < this.tableData.length; i++) {
          this.ischeck.push(i)
          this.items.push(this.tableData[i])
           console.log(this.tableData[i])
        }
      }
      this.items.pop()//解决最后一个重复的bug
    console.log(this.items)
    }

  },
  watch: {
    'ischeck': { //当全部选中时，类别的checkbox自动选中or取消
      handler: function(val, oldVal) {
         console.log(val.length)
         console.log(this.tableData.length)
        val.length == this.tableData.length ? this.checkAll = true : this.checkAll = false;
        //取两个数组的差集
        Array.prototype.diff = function(a) {
          return this.filter(function(i) { return a.indexOf(i) < 0; });
        }
        //删除数组的指定元素
        Array.prototype.remove = function(dx) {
          if (isNaN(dx) || dx > this.length) { return false; }
          for (var i = 0, n = 0; i < this.length; i++) {
            if (this[i] != this[dx]) {
              this[n++] = this[i]
            }
          }
          this.length -= 1
        }
        //展示以选中的产品

        if (val.length >= oldVal.length) {
          // if (val[val.length - 1] != "undefined") {
          this.items.push(this.tableData[val[val.length - 1]])

          // }
           console.log(this.items)
          console.log(val[val.length - 1] + "错了")
        } else {

          this.items.remove(oldVal.indexOf(oldVal.diff(val)[0]))
          // console.log(oldVal.indexOf(oldVal.diff(val)[0]))
        }
      }



    }
  }
}

</script>
<style scoped>
.WSY_select_box select {
  height: 30px;
  width: 150px;
  margin-left: 18px;
}

.WSY_select_box {
  display: inline-block;
  vertical-align: top;
  margin-top: 2px;
}

</style>
<style scoped>
.WSY_select_box span {
  display: inline-block;
  margin-right: 10px;
}

.WSY_elselect {
  height: 26px;
  width: 150px;
  margin-top: -1px;
  padding: 3px;
  border: 1px solid #ccc;
  border-radius: 2px;
}

.selproduct {
  float: left;
  margin-bottom: 15px;
}

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
}

.line {
  text-align: center;
}

.topbtn .el-form-item__content {
  margin-left: 0!important;
}
.cpname{
  display: inline-block;
  width: 83px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  vertical-align:sub;
  margin-right:5px;
}
</style>
