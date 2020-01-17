const Mock = require('mockjs');

const peddingList = {
  page_size: 1,
  now_page: 10,
  all_count: 3,
  data: [
    {
      id: 1,
      type: "事假",
      content: "ho ho ho ha ha ha hei hei hei ya ya ya ",
      time: "2018-12-24 16:55:45",
      applicationTime: "2",
      sponsor: "Dan",
      approver: "Eason",
      currentState: "审批中"
    },
    {
      id: 2,
      type: "事假2",
      content: "ho ha hei  ya ya ya ",
      time: "2018-12-24 16:55:45",
      applicationTime: "2",
      sponsor: "Dan",
      approver: "Eason",
      currentState: "审批中"
    },
    {
      id: 3,
      type: "事假3",
      content: "ho ha hei ya a a a",
      time: "2018-12-24 16:55:45",
      applicationTime: "2",
      sponsor: "Dan",
      approver: "Eason",
      currentState: "审批中"
    },
    {
      id: 4,
      type: "事假4",
      content: "js",
      time: "2018-12-24 16:55:45",
      applicationTime: "2",
      sponsor: "Dan",
      approver: "Eason",
      currentState: "审批中"
    },
    {
      id: 5,
      type: "事假5",
      content: "jquery",
      time: "2018-12-24 16:55:45",
      applicationTime: "2",
      sponsor: "Dan",
      approver: "Eason",
      currentState: "审批中"
    },
    {
      id: 6,
      type: "事假6",
      content: "ho ha hei ya a a a",
      time: "2018-12-24 16:55:45",
      applicationTime: "2",
      sponsor: "Dan",
      approver: "Eason",
      currentState: "审批中"
    },
    {
      id: 7,
      type: "事假7",
      content: "ho ha hei ya a a a",
      time: "2018-12-24 16:55:45",
      applicationTime: "2",
      sponsor: "Dan",
      approver: "Eason",
      currentState: "审批中"
    },
    {
      id: 8,
      type: "事假8",
      content: "ho ha hei ya a a a",
      time: "2018-12-24 16:55:45",
      applicationTime: "2",
      sponsor: "Dan",
      approver: "Eason",
      currentState: "审批中"
    },
    {
      id: 9,
      type: "事假9",
      content: "ho ha hei ya a a a",
      time: "2018-12-24 16:55:45",
      applicationTime: "2",
      sponsor: "Dan",
      approver: "Eason",
      currentState: "审批中"
    },
    {
      id: 10,
      type: "事假1审批中",
      content: "ho ha hei ya a a a",
      time: "2018-12-24 16:55:45",
      applicationTime: "2",
      sponsor: "Dan",
      approver: "Eason",
      currentState: "审批中"
    },
    {
      id: 11,
      type: "事假11",
      content: "ho ha hei ya a a a",
      time: "2018-12-24 16:55:45",
      applicationTime: "2",
      sponsor: "Dan",
      approver: "Eason",
      currentState: "审批中"
    }
  ]
}
const approvedList = {
  page_size: 1,
  now_page: 10,
  all_count: 3,
  data: [
    {
      id: 1,
      type: "事假",
      content: "啦啦啦啦啦啦啦啦啦啦啦啦",
      time: "2018-12-24 16:55:45",
      applicationTime: "5",
      sponsor: "Dan",
      approver: "Eason",
      currentState: "审批通过"
    }, {
      id: 2,
      type: "事假2",
      content: "哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈",
      time: "2018-12-24 17:55:45",
      applicationTime: "2",
      sponsor: "Dan",
      approver: "Eason",
      currentState: "审批中"
    },
    {
      id: 3,
      type: "事假3",
      content: "吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼",
      time: "2018-12-24 12:55:45",
      applicationTime: "2",
      sponsor: "Dan",
      approver: "Eason",
      currentState: "审批不通过"
    },
    {
      id: 4,
      type: "事假4",
      content: "没得说，请假是不可能的，没理由",
      time: "2018-11-24 1审批中:审批中审批中:审批中审批中",
      applicationTime: "12",
      sponsor: "Dan",
      approver: "Eason",
      currentState: "审批不通过"
    }
  ]
}
const initiated = {
  page_size: 1,
  now_page: 10,
  all_count: 3,
  data: [{
    id: 1,
    type: "事假",
    content: "啦啦啦啦啦啦啦啦啦啦啦啦",
    time: "2018-12-24 16:55:45",
    applicationTime: "5",
    sponsor: "Dan",
    approver: "Eason",
    currentState: "审批通过"
  },
  {
    id: 2,
    type: "事假2",
    content: "哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈",
    time: "2018-12-24 17:55:45",
    applicationTime: "2",
    sponsor: "Tom",
    approver: "Eason",
    currentState: "审批中"
  },
  {
    id: 3,
    type: "事假3",
    content: "吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼",
    time: "2018-12-24 12:55:45",
    applicationTime: "2",
    sponsor: "Tom",
    approver: "Eason",
    currentState: "审批不通过"
  },
  {
    id: 4,
    type: "事假4",
    content: "没得说，请假是不可能的，没理由",
    time: "2018-11-24 1审批中:审批中审批中:审批中审批中",
    applicationTime: "12",
    sponsor: "Tom",
    approver: "Eason",
    currentState: "审批不通过"
  },
  {
    id: 5,
    type: "事假3",
    content: "吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼",
    time: "2018-12-24 12:55:45",
    applicationTime: "2",
    sponsor: "Tom",
    approver: "Eason",
    currentState: "审批通过"
  }
  ]
}
const ccApproval = {
  page_size: 1,
  now_page: 10,
  all_count: 3,
  data: [{
    id: 1,
    type: "事假",
    content: "啦啦啦啦啦啦啦啦啦啦啦啦",
    time: "2018-12-24 16:55:45",
    applicationTime: "5",
    sponsor: "Dan",
    approver: "Eason",
    currentState: "审批通过"
  }, {
    id: 2,
    type: "事假2",
    content: "哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈",
    time: "2018-12-24 17:55:45",
    applicationTime: "2",
    sponsor: "Tom",
    approver: "Eason",
    currentState: "审批中"
  },
  {
    id: 3,
    type: "事假3",
    content: "吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼",
    time: "2018-12-24 12:55:45",
    applicationTime: "2",
    sponsor: "Tom",
    approver: "Eason",
    currentState: "审批不通过"
  },
  {
    id: 4,
    type: "事假4",
    content: "没得说，请假是不可能的，没理由",
    time: "2018-11-24 1审批中:审批中审批中:审批中审批中",
    applicationTime: "12",
    sponsor: "Tom",
    approver: "Eason",
    currentState: "审批不通过"
  },
  {
    id: 5,
    type: "事假3",
    content: "吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼",
    time: "2018-12-24 12:55:45",
    applicationTime: "2",
    sponsor: "Tom",
    approver: "Eason",
    currentState: "审批不通过"
  },
  ]
}
const archive = {
  page_size: 1,
  now_page: 10,
  all_count: 3,
  data: [{
    id: 1,
    type: "事假",
    content: "啦啦啦啦啦啦啦啦啦啦啦啦",
    time: "2018-12-24 16:55:45",
    sponsor: "Dan",
    approver: "Eason",
    currentState: "已归档"
  }, {
    id: 2,
    type: "事假2",
    content: "哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈",
    time: "2018-12-24 17:55:45",
    sponsor: "Tom",
    approver: "Eason",
    currentState: "已归档"
  },
  {
    id: 3,
    type: "事假3",
    content: "吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼",
    time: "2018-12-24 12:55:45",
    sponsor: "Tom",
    approver: "Eason",
    currentState: "已归档"
  },
  {
    id: 4,
    type: "事假4",
    content: "没得说，请假是不可能的，没理由",
    time: "2018-11-24 10:00:00",
    sponsor: "Tom",
    approver: "Eason",
    currentState: "已归档"
  },
  {
    id: 5,
    type: "事假3",
    content: "吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼吼",
    time: "2018-12-24 12:55:45",
    sponsor: "Tom",
    approver: "Eason",
    currentState: "已归档"
  },
  ]
}
const templateList = [
  {
    type: '请假',
    typeId: '0001',
    data: [
      {
        name: '请假',
        id: '00001',
        desc: "用于请假流程"
      },
      {
        name: '加班',
        id: '0002',
        desc: "用于加班流程"
      },
      {
        name: '转正',
        id: '00002',
        desc: "用于员工转正流程"
      }
    ]
  },
  {
    type: '人事',
    typeId: '0002',
    data: [
      {
        name: '转正',
        id: '00001',
        desc: "用于员工转正流程"
      },
      {
        name: '招聘',
        id: '00002',
        desc: "用于企业招聘流程"
      },
    ]
  }
]
const approvalDetailInfo = {
  id: 1,
  approvalNumber: 'sj20181215173301',
  type: '事假',
  department: '前端开发',
  updated_at: '2019-01-04 13:44:31至2019-01-20 13:44:31',
  days: 2,
  leave: '个人私事',
  data: {
    url: ''
  }
}
const managementTemplateList = [
  {
    id: 0001,
    type: '出勤休假',
    num: '2',
    data: [
      {
        id: 1,
        title: '请假',
        desc: '适用于有事外出',
        updated_at: '2019-01-04 13:44:31',
        processType: '固定流程',
        permission: "全体员工",
        operatType: 0
      },
      {
        id: 2,
        title: '加班',
        desc: '适用于加班',
        updated_at: '2018-03-04 13:44:31',
        processType: '固定流程',
        permission: "全体员工",
        operatType: 1
      },
    ]
  },
  {
    id: 0002,
    type: '人事',
    num: '2',
    data: [
      {
        id: 1,
        title: '转正',
        desc: '用于试用期内员工的员工转正申请',
        updated_at: '2018-11-04 13:44:31',
        processType: '固定流程',
        permission: "全体员工",
        operatType: 1
      },
      {
        id: 2,
        title: '招聘',
        desc: '用于用人部门需求申请',
        updated_at: '2018-12-04 13:44:31',
        processType: '固定流程',
        permission: "全体员工",
        operatType: 0
      },
    ]
  }
]
const sortTem = [
  {
    id: 0001,
    type: '出勤休假',
    num: '2',
  },
  {
    id: 0002,
    type: '人事',
    num: '2',
  }, {
    id: 0003,
    type: '财务',
    num: '6',
  }
]
const approvalTypeList = [
  {
    type_name: "福利待遇",
    data: [
      {
        desc: "lalalala",
        id: 6,
        name: "模板",
      },
      {
        desc: "hahahaha",
        id: 1,
        name: "模板2",
      },
    ]
  },
]
module.exports = {
  "/c_approval_taskList": (req, res) => {
    switch (req.query.type) {
      case 'pending':
        res.json(peddingList);// 待审批
        break;
      case 'approved': // 我已评审
        res.json(approvedList); // 我已审批
        break;
      case 'myInitiated':
        res.json(initiated); // 我发起的
        break;
      case 'ccApproval':
        res.json(ccApproval); // 抄送给我的
        break;
      case 'archive':
        res.json(archive); // 已归档
        break;
    }
  },
  "/c_approval_getTemplate": (req, res) => {
    res.json(templateList);
  },
  "/c_approval_Detail": (req, res) => {
    res.json(approvalDetailInfo);
  },
  "/c_management_template": (req, res) => {
    res.json(managementTemplateList);
  },
  "/c_template_sort": (req, res) => {
    res.json(sortTem);
  },
  "/c_approval_approvalTypeList": (req, res) => {
    res.json(approvalTypeList);
  }

}