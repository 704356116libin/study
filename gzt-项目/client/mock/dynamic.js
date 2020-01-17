/**
 * 所有应用最新动态
 */



module.exports = {

  "/dynamicInfo": {
    unread_count: 6,
    data: [
      {
        type: '工作通知',
        unread_count: 3,
        time: '14:22',
        data: {
          company_id: 11111,
          company_name: '北京三里屯xxx有限公司',
          title: '工作通知: 北京三里屯xxx有限公司',
          latest_news: '[公告]: 最新2019年节假日安排已出炉'
        }
      },
      {
        type: '工作通知',
        unread_count: 1,
        time: '昨天',
        data: {
          company_id: 22222,
          company_name: '北京神探科技有限公司',
          title: '工作通知: 北京神探科技有限公司',
          latest_news: '[审批]: 小彭有一个请假审批待你处理'
        }
      },
      {
        type: '社区福利',
        unread_count: 1,
        time: '1-1',
        data: {
          title: '社区福利',
          latest_news: '新用户注册即送5G空间和邮....'
        }
      },
      {
        type: '个人消息',
        unread_count: 1,
        time: '12-26',
        data: {
          title: '大锤',
          latest_news: '今天下班去健身房吗？'
        }
      }
    ]
  },
  "/workmessage": [ // 
    {
      type: '评审通'
    },
    {
      type: '审批'
    },
    {
      type: '协助',
      data: {
        created_at: "2019-01-02 15:59:07",
        description: "<p>dasdsadsadsa</p>",
        id: 24,
        is_cancel: 0,
        limit_time: null,
        status: "进行中",
        title: "dsadsadsa"
      }
    },
    {
      type: '公告',
      data: {
        id: "1",
        title: "放假通知",
        company_id: "eidLIJ11545",
        organiser: "用户_13164377353",
        content: "此项目需要签署保密协议、如有违约或者泄密需要承担一定的法律责任,请有关部门遵守规定、此公告立即生效......",
        order: 10,
        is_show: 1,
        is_top: 0,
        is_follow: 0,
        updated_at: "2018-12-21 16:02:15"
      }
    },
  ]

}