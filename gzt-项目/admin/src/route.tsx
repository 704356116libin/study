import * as React from 'react';
const { lazy } = React;
/**
 * 路由配置文件 默认按需加载
 */
export const routes = [
  {
    path: '/',
    component: lazy(() => import('./pages/index')),
    title: '工作通-后台',
  },
  {
    path: '/information',
    component: lazy(() => import('./pages/company/Information')),
    title: '企业信息',
  },
  {
    path: '/license',
    component: lazy(() => import('./pages/company/License')),
    title: '执照上传',
  },
  {
    path: '/structure',
    component: lazy(() => import('./pages/structure/Index')),
    title: '组织结构',
  },
  {
    path: '/inviteStaff',
    component: lazy(() => import('./pages/structure/InviteStaff')),
    title: '邀请员工',
  },
  {
    path: '/partner',
    component: lazy(() => import('./pages/partner/Index')),
    title: '合作伙伴',
  },
  {
    path: '/addPartner',
    component: lazy(() => import('./pages/partner/AddPartner')),
    title: '添加合作伙伴',
  },
  {
    path: '/applicationList',
    component: lazy(() => import('./pages/partner/ApplicationList')),
    title: '合作伙伴列表',
  },
  {
    path: '/externalContact',
    component: lazy(() => import('./pages/externalContact/Index')),
    title: '外部联系人',
  },
  {
    path: '/addContact',
    component: lazy(() => import('./pages/externalContact/AddContact')),
    title: '添加外部联系人',
  },
  {
    path: '/permission',
    component: lazy(() => import('./pages/permission/Index')),
    title: '职务权限',
  },
  {
    path: '/newPosition',
    component: lazy(() => import('./pages/permission/NewPosition')),
    title: '职务权限'
  },
  {
    path: '/sort',
    component: lazy(() => import('./pages/sort/Index')),
    title: '组织排序',
  },
  {
    path: '/setting',
    component: lazy(() => import('./pages/company/AppSetting')),
    title: '应用设置',
  },
  {
    path: '/log',
    component: lazy(() => import('./pages/company/Log')),
    title: '操作日志',
  },
  {
    path: '/buy',
    component: lazy(() => import('./pages/buy')),
    title: '职务权限',
    routes: [
      {
        path: '/sms',
        cache: true,
        component: lazy(() => import('./pages/buy/sms')),
      },
      {
        path: '/people',
        component: lazy(() => import('./pages/buy/people')),
        title: '职务权限',
      },
      {
        path: '/netdisc',
        component: lazy(() => import('./pages/buy/netdisc')),
        title: '职务权限',
      },
      {
        path: '/order',
        component: lazy(() => import('./pages/buy/order')),
        title: '职务权限',
      },
      {
        path: '/invoice',
        component: lazy(() => import('./pages/buy/invoice')),
        title: '职务权限',
      }
    ]
  },


]