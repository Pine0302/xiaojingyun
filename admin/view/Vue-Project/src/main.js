// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import ElementUI from 'element-ui'
import VueQuillEditor from 'vue-quill-editor'
import Axios from 'axios'

const baseURL = window.location.protocol + '//' + window.location.hostname
// const baseURL = 'https://admin.weisanyun.cn'
Axios.defaults.baseURL = baseURL
Vue.prototype.$http = Axios

import util from './utils'
import 'element-ui/lib/theme-default/index.css'
import './assets/css/common.css'
import './assets/css/comstyleone.css'
import './assets/css/comstylethree.css'



import * as filters from './filters'
import global_ from './components/Global'//引用文件


Vue.use(ElementUI)


Vue.use(VueQuillEditor)
//定义全局时间处理过滤器
Object.keys(filters).forEach(key => {
  Vue.filter(key, filters[key])
})

Vue.config.productionTip = false

/* eslint-disable no-new */
Vue.prototype.basehear = baseURL


Vue.prototype.$http.post('mshop/admin/index.php?m=backinit&a=back_init', {

}).then(function(res) {
  if (res.data.errcode == 0) {
    Vue.prototype.customer_id = res.data.customer_id
    new Vue({
      el: '#app',
      router,
      template: '<App/>',
      components: { App },

    })
    console.log("已登陆")
  } else {
    // Vue.prototype.customer_id = '3243'
    // new Vue({
    //   el: '#app',
    //   router,
    //   template: '<App/>',
    //   components: { App }
    // })

    alert(res.data.errmsg)
    throw '登录已经超时，请重新登录！'
  }
}).catch(function(err) {
  console.log(err)
})
