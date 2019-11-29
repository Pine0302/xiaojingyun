import Vue from 'vue'
import Router from 'vue-router'
import Jifenmingxi from '../pages/datastatistics/Jifenmingxi'
import Yonghujifentj from '../pages/datastatistics/Yonghujifentj'
import Qiandaomingxi from '../pages/datastatistics/Qiandaomingxi'
import Jifenhuodongtj from '../pages/datastatistics/Jifenhuodongtj'
import Jifenzhuanhuanmark from '../pages/datastatistics/Jifenzhuanhuanmark'
import Jifenhuodong from '../pages/activityset/Jifenhuodong'
import Tianjiahuodong from '../pages/activityset/Tianjiahuodong'
import Tianjiahuodongsetting from '../pages/activityset/Tianjiahuodongsetting'
import Tianjiahuodongrelevance from '../pages/activityset/Tianjiahuodongrelevance'
import Tianjiachanpin from '../pages/activityset/Tianjiachanpin'
import Qiandaohuodong from '../pages/activityset/Qiandaohuodong'
import Tianjiaqiandao from '../pages/activityset/Tianjiaqiandao'
import Duihuanhuodong from '../pages/activityset/Duihuanhuodong'
import Tjduihuanhuodong from '../pages/activityset/Tjduihuanhuodong'
import Tjduihuanhuodongsetting from '../pages/activityset/Tjduihuanhuodongsetting'
import Tjduihuanhuodongrelevance from '../pages/activityset/Tjduihuanhuodongrelevance'

import '../assets/css/content.css'
import '../assets/css/contentblue.css'

import Setting from '../pages/basesetting/Setting'
import Shopsetting from '../pages/basesetting/Shopsetting'
import Edit from '../pages/basesetting/Edit'
import Shopedit from '../pages/basesetting/Shopedit'
import Productmanage from '../pages/basesetting/Productmanage'
import Shopproductmanage from '../pages/basesetting/Shopproductmanage'
import Sign from '../pages/basesetting/Sign'
import Oplog from '../pages/basesetting/Oplog'
import Buypoints from '../pages/basesetting/Buypoints'
import Topupintegral from '../pages/basesetting/Topupintegral'
import Rechargelog from '../pages/basesetting/Rechargelog'
import Lineitem from '../pages/ordermanagement/Lineitem'
import Ordermanagement from '../pages/ordermanagement/Ordermanagement'
import Turnsetting from '../pages/basesetting/Turnsetting'
import Shopcurrency from '../pages/basesetting/Shopcurrency'
import Storecurrency from '../pages/basesetting/Storecurrency'



Vue.use(Router)
export default new Router({
  mode: 'hash',
  //   scorllBehavior: () => ({
  // y: 0
  // }),
  routes: [
    { path: '/', redirect: 'activityset/Jifenhuodong' }, //重定向
    {
      path: '/activityset/Jifenhuodong',
      name: '积分活动',
      component: Jifenhuodong,
    },
    {
      path: '/datastatistics/Jifenhuodongtj',
      name: '积分活动统计',
      component: Jifenhuodongtj,
    },
    {
      path: '/activityset/Tianjiachanpin',
      name: '添加产品',
      component: Tianjiachanpin,
    },
    {
      path: '/activityset/Tianjiahuodong',
      name: '添加活动',
      component: Tianjiahuodong,
       redirect: 'activityset/Tianjiahuodong/Tianjiahuodongsetting',
      children: [{
        path: 'Tianjiahuodongsetting',
        name: 'Tianjiahuodongsetting',
        component: Tianjiahuodongsetting
      }, {
        path: 'Tianjiahuodongrelevance',
        name: 'Tianjiahuodongrelevance',
        component: Tianjiahuodongrelevance
      }]
    },

    {
      path: '/datastatistics/Jifenmingxi',
      name: '积分明细',
      component: Jifenmingxi,
    },
    {
      path: '/datastatistics/Yonghujifentj',
      name: '用户积分统计',
      component: Yonghujifentj,
    },
     {
      path: '/datastatistics/Jifenzhuanhuanmark',
      name: '积分转换记录',
      component: Jifenzhuanhuanmark,
    },
    {
      path: '/activityset/Qiandaohuodong',
      name: '签到活动',
      component: Qiandaohuodong,
    },

    {
      path: '/activityset/Tianjiaqiandao',
      name: '添加签到',
      component: Tianjiaqiandao,
    },
    {
      path: '/activityset/Duihuanhuodong',
      name: '兑换活动',
      component: Duihuanhuodong,
    },
    {
      path: '/activityset/Tjduihuanhuodong',
      name: '添加兑换活动',
      component: Tjduihuanhuodong,
      redirect: 'activityset/Tjduihuanhuodong/Tjduihuanhuodongsetting',
      children: [{
        path: 'Tjduihuanhuodongsetting',
        name: 'Tjduihuanhuodongsetting',
        component: Tjduihuanhuodongsetting
      }, {
        path: 'Tjduihuanhuodongrelevance',
        name: 'Tjduihuanhuodongrelevance',
        component: Tjduihuanhuodongrelevance
      }]
    },

    {
      path: '/datastatistics/Qiandaomingxi',
      name: 'Qiandaomingxi',
      component: Qiandaomingxi,
    },
    {
      path: '/basesetting/shopsetting',
      name: '门店积分设置',
      component: Shopsetting
    },
    {
      path: '/basesetting/setting',
      name: '商城积分设置',
      component: Setting
    },
    {
      path: '/basesetting/edit',
      name: '商城编辑产品积分',
      component: Edit
    },
    {
      path: '/basesetting/shopedit',
      name: '门店编辑产品积分',
      component: Shopedit
    },
    {
      path: '/basesetting/productmanage',
      name: '商城积分管理',
      component: Productmanage
    },
    {
      path: '/basesetting/shopproductmanage',
      name: '门店积分管理',
      component: Shopproductmanage
    },
    {
      path: '/basesetting/sign',
      name: '签到设置',
      component: Sign
    },
    {
      path: '/basesetting/oplog',
      name: '操作日志',
      component: Oplog
    },
    {
      path: '/basesetting/buypoints',
      component: Buypoints
    },
    {
      path: '/basesetting/topupintegral',
      component: Topupintegral
    },
    {
      path: '/basesetting/rechargelog',
      component: Rechargelog
    },
    {
      path: '/ordermanagement/lineitem',
      component: Lineitem
    },
    {
      path: '/ordermanagement/ordermanagement',
      component: Ordermanagement
    },
    {
      path: '/tianjiaqiandao',
      component: Tianjiaqiandao
    },
    {
      path: '/basesetting/turnsetting',
      name: '积分转换设置',
      component: Turnsetting,
      children: [{
        path: 'Shopcurrency',
        name: 'Shopcurrency',
        component: Shopcurrency
      }, {
        path: 'Storecurrency',
        name: 'Storecurrency',
        component: Storecurrency
      }]
    }
  ]
})
