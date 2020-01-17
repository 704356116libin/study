import * as React from 'react';
const { lazy } = React;
/**
 * 路由配置文件 默认按需加载
 */
export const routes = [
  {
    path: '/',
    redirect: 'dynamic'
  },
  {
    path: '/dynamic',
    component: lazy(() => import('./pages/dynamic/Dynamics')),
    title: '工作通-动态',
  },
  {
    path: '/work',
    cache: true,
    component: lazy(() => import('./pages/work/Work')),
    title: '工作通-工作',
    routes: [
      {
        path: '/',
        component: lazy(() => import('./pages/work/workbench/Workbench')),
      },
      {
        path: '/review',
        cache: true,
        component: lazy(() => import('./pages/work/review/ReviewSider')),
        routes: [
          {
            path: '/templates',
            cache: true,
            component: lazy(() => import('./pages/work/review/Templates')),
          },
          {
            path: '/initiate',
            cache: true,
            component: lazy(() => import('./pages/work/review/Initiate')),
          },
          {
            path: '/detail/:reviewId',
            cache: true,
            component: lazy(() => import('./pages/work/review/ReviewDetail')),
          },
          {
            path: '/export',
            component: lazy(() => import('./pages/work/review/exportPreview')),
          },
          {
            path: '/dataanalysis',
            cache: true,
            component: lazy(() => import('./pages/work/review/Dataanalysis')),
          },
          {
            path: '/templatemgt',
            cache: true,
            component: lazy(() => import('./pages/work/review/templatemgt/Templatemgt')),
          },
          {
            path: '/createTemplate',
            component: lazy(() => import('./pages/work/review/templatemgt/CreateTemplate')),
          },
          {
            path: '/tempGroupSort',
            component: lazy(() => import('./pages/work/review/templatemgt/GroupSort')),
          },
          {
            path: '/processmgt',
            cache: true,
            component: lazy(() => import('./pages/work/review/processmgt/Processmgt')),
          },
          {
            path: '/processGroupSort',
            component: lazy(() => import('./pages/work/review/processmgt/GroupSort')),
          },
          {
            path: '/createProcess',
            component: lazy(() => import('./pages/work/review/processmgt/CreateProcess')),
          },
          {
            path: '/exportmgt',
            component: lazy(() => import('./pages/work/review/exportmgt/Exportmgt')),
          },
          {
            path: '/createReport',
            component: lazy(() => import('./pages/work/review/exportmgt/CreateReport')),
          },
          {
            path: '/reportGroupSort',
            component: lazy(() => import('./pages/work/review/exportmgt/GroupSort')),
          },
          {
            path: '/othersmgt',
            component: lazy(() => import('./pages/work/review/othersmgt/Othersmgt')),
          },
          {
            path: '/customizesmgt',
            component: lazy(() => import('./pages/work/review/customizesmgt/customizesmgt')),
          },
          {
            path: '/labelsmgt',
            component: lazy(() => import('./pages/work/review/labelsmgt/labelsmgt')),
          },
          {
            path: '/:reviewType(all|reviewing|reviewed|pendingApproval|pendingReceive|initiated|copytome|archived|pendingAssign|recyclebin|cancled)',
            cache: true,
            component: lazy(() => import('./pages/work/review/Reviews')),
          }
        ]
      },
      {
        path: '/notice',
        cache: true,
        component: lazy(() => import('./pages/work/notice/Notices')),
        title: '工作通-公告',
        routes: [
          {
            path: '/',
            cache: true,
            component: lazy(() => import('./pages/work/notice/NoticeList')),
          },
          {
            path: '/create',
            component: lazy(() => import('./pages/work/notice/createNotice/Create')),
          },
          {
            path: '/details/:noticeId',
            component: lazy(() => import('./pages/work/notice/Details')),
          }
        ]
      },
      /**审批 */
      {
        path: '/approval',
        cache: true,
        component: lazy(() => import('./pages/work/approval/MyApprovalSider')),
        routes: [
          {
            path: '/',
            redirect: '/pending',
          },
          {
            path: '/:approvalType(pending|approved|initiate|ccApproval|archive)', // 待审批/我已审批/我发起的/抄送给我的/归档
            cache: true,
            component: lazy(() => import('./pages/work/approval/Approval')),
          },
          {
            path: '/management', // 审批管理
            cache: true,
            component: lazy(() => import('./pages/work/approval/management/Index')),
          },
          {
            path: '/groupSort', // 分组排序
            component: lazy(() => import('./pages/work/approval/management/Group')),
          },
          {
            path: '/create', // 新建审批
            cache: true,
            component: lazy(() => import('./pages/work/approval/CreateApproval')),
          },
          {
            path: '/template',
            cache: true,
            component: lazy(() => import('./pages/work/approval/Template')),
          },
          {
            path: '/newCreate', // 审批管理-新建审批
            cache: true,
            component: lazy(() => import('./pages/work/approval/management/Create')),
          }
        ]
      },
      {
        path: '/assist',
        cache: true,
        component: lazy(() => import('./pages/work/assist/Assist')),
        routes: [
          {
            path: '/',
            cache: true,
            component: lazy(() => import('./pages/work/assist/MyAssist')),
          },
          {
            path: '/template',
            component: lazy(() => import('./pages/work/assist/Template')),
          }
        ]
      },
    ]
  },
  {
    path: '/doc',
    component: lazy(() => import('./pages/doc/doc')),
    title: '工作通-文档',
    routes: [
      {
        path: '/',
        redirect: '/dynamic'
      },
      {
        path: '/dynamic',
        component: lazy(() => import('./pages/doc/dynamic')),
        title: '工作通-文档',
      },
      {
        path: '/recently',
        component: lazy(() => import('./pages/doc/recently')),
        title: '工作通-文档',
      },
      {
        path: '/personal',
        component: lazy(() => import('./pages/doc/personal')),
        title: '工作通-文档',
      },
      {
        path: '/company/:companyId',
        component: lazy(() => import('./pages/doc/company')),
        title: '工作通-文档',
      }
    ]
  },
  {
    path: '/work/systmgt',
    component: lazy(() => (() => <div>系统管理</div>) as any),
    title: '工作通-系统管理'
  },
  {
    path: '/contact',
    component: lazy(() => import('./pages/contact/contact')),
    title: '工作通-通讯录',
    routes: [
      {
        path: '/',
        redirect: '/company'
      },
      {
        path: '/company/:companyId',
        component: lazy(() => import('./pages/contact/company')),
      }
    ]
  },
  {
    path: '/settings',
    component: lazy(() => import('./pages/settings/settings')),
    routes: [
      {
        path: '/profile',
        component: lazy(() => import('./pages/settings/profile')),
      },
      {
        path: '/invitation',
        component: lazy(() => import('./pages/settings/invitation')),
      }
    ]
  },
  {
    path: '/companyRegister',
    component: lazy(() => import('./pages/companyRegister')),
  }
]