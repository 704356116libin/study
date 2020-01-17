/**
 * {
 *    "url": 数据/函数
 * }
 */
const Mock = require('mockjs');
/**
 * 所有应用最新动态
 */
const appDynamicsInfo = [
  {
    app: '评审通',
    avatarUrl: 'https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png',
    userName: '小彤',
    type: '更新进度',
    time: '3小时前',
    date: '2018年11月15日 9:30',
    key: ['评审名称', '科长审批通过'],
    des: ['评审描述', '造价咨询费；符合条件通过；']
  },
  {
    app: '公告',
    avatarUrl: 'https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png',
    userName: '小彤',
    type: '发布公告',
    time: '2天前',
    date: '2018年10月28日 6:20',
    key: ['公告标题', '冬季时间调整'],
    des: ['所属栏目', '企业公告']
  }
];
/**
 * 评审通最新动态
 */
const reviewDynamicsInfo = [{
  app: '评审通',
  avatarUrl: 'https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png',
  userName: '小彤',
  type: '更新进度',
  time: '3小时前',
  date: '2018年11月15日 9:30',
  key: ['评审名称', '科长审批通过'],
  des: ['评审描述', '造价咨询费；符合条件通过；']
}];
/**
 * 公告最新动态
 */
const noticeDynamicsInfo = [{
  app: '公告',
  avatarUrl: 'https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png',
  userName: '小彤',
  type: '发布公告',
  time: '2天前',
  date: '2018年10月28日 6:20',
  key: ['公告标题', '冬季时间调整'],
  des: ['所属栏目', '企业公告']
}];
// 组织部门
const orgnizationDept = {
  "dept_tree": {
    "id": 35796314,
    "name": "探知科技",
    "c_id": 6989726,
    "users": [
      {
        "type": "user",
        "user_id": 4,
        "name": '004'
      }
    ],
    "children": [
      {
        "id": "Ly5fEh287828",
        "name": "财务部",
        "pid": "Xtz5f9278301",
        "number_people": 0,
        "users": [
          {
            "type": "user",
            "user_id": 1,
            "name": '001'
          }
        ],
        "children": [
          {
            "id": "qrxdXi306882",
            "name": "小财务部",
            "pid": "8xwgcX287828",
            "number_people": 0,
            "users": [
              {
                "type": "user",
                "user_id": 2,
                "name": '002'
              }
            ],
            "children": []
          }
        ]
      },
      {
        "id": "8CoSqT297355",
        "name": "人事部",
        "pid": "rZnYL1278301",
        "number_people": 0,
        "users": [
          {
            "type": "user",
            "user_id": 3,
            "name": '003'
          }
        ],
        "children": [
          {
            "id": "VJbKqK316409",
            "name": "小人事部",
            "pid": "NxSDzH297355",
            "number_people": 0,
            "children": [
              {
                "id": "ebhBuf325936",
                "name": "小小人事部",
                "pid": "B756nP316409",
                "number_people": 0,
                "users": [],
                "children": []
              }
            ]
          },
          {
            "id": "vkoGt5335463",
            "name": "人事部啊",
            "pid": "evyv8A297355",
            "number_people": 0,
            "users": [],
            "children": []
          }
        ]
      }
    ]
  },
  "version": 1503626052359
};

module.exports = {

  "/corpprateInfo": { // 用户在企业内的个人信息
    avatar: '',
    username: '小鹏弟弟',
    department: '网站UI',
    position: '大厨',
    companyname: '工作通科技有限公司'
  },
  "/userInfo": { // 用户的个人信息
    avatar: '',
    username: '小鹏弟弟',
    tel: '15736700059'
  },
  "/companyList": [ // 用户在企业内的个人信息
    {
      name: '工作通科技有限公司'
    },
    {
      name: '北京三里屯xxx餐饮有限公司'
    },
    {
      name: '神探科技有限公司'
    },
    {
      name: '我的工作通'
    }

  ],
  "/userApp": [// 用户的应用信息,有先后顺序    
    {
      name: 'review',
      title: '评审通',
    },
    {
      name: 'notice',
      title: '公告',
    },
    {
      name: 'doc',
      title: '文档',
    },
    {
      name: 'contact',
      title: '通讯录',
    }
  ],
  "/disabledApp": [ // 用户的没权限使用的应用信息   
    'systmgt'
  ],
  "/appDynamicsInfo": (req, res) => {
    switch (req.query.appType) {
      case '评审通':
        res.json(reviewDynamicsInfo);
        break;
      case '公告':
        res.json(noticeDynamicsInfo);
        break;
      case '全部动态':
        res.json(appDynamicsInfo);
        break;
    }
  },
  "/switchListInfo": [
    {
      name: '应用动态',
      show: true
    }
  ],
  "/deptTree": orgnizationDept

}