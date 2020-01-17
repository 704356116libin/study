/**
 * {
 *    "url": 数据/函数
 * }
 */
const Mock = require('mockjs');
const random_jokes = [
    {
        setup: 'What is the object oriented way to get wealthy ?',
        punchline: 'Inheritance',
        id: 1
    },
    {
        setup: 'To understand what recursion is...',
        punchline: "You must first understand what recursion is",
        id: 2
    },
    {
        setup: 'What do you call a factory that sells passable products?',
        punchline: 'A satisfactory',
        id: 3
    },
];
const noticeList = [
    {
        text: '全部公告',
        id: 1,
    },
    {
        text: '企业动态',
        id: 2
    },
    {
        text: '企业热点',
        id: 3
    },
    {
        text: '热点新闻',
        id: 5
    },
    {
        text: '我的关注',
        id: 4
    },
    {
        text: '员工公告',
        id: 6
    }
];
const columnInfo = [
    {
        text: '企业动态',
        id: 1
    },
    {
        text: '企业热点',
        id: 2
    },
    {
        text: '热点新闻',
        id: 3
    },
];
const publishedInfo = [// 已发布信息
    {
        app: '已发布',
        id:"1",
        type: '企业公告',
        title: '放假通知',
        desc: '根据季节变化，现调整工作时间，安排如下： 上午：8:00----12:00 下午：13:30---17:30 请公司团队成员遵照执行。',
        userName: '梦彧',
        time: '2018-11-12',
        is_top:0,
        is_attention:1
    },
    {
        app: '已发布',
        id:"2",
        type: '企业XX',
        title: 'xxx',
        desc: '安排安排安排安排安排安排安排安排安排安排安排安排安排安排安排安排安排安排安排安排安排安排安排安排安排安排',
        userName: '梦彧',
        time: '2018-11-13',
        is_top:1,
        is_attention:0
    }
]
const unpublishedInfo = [// 未发布信息
    {
        app: '未发布',
        type: '企业动态',
        title: '人员变更通知',
        desc: '根据公司业务拓展，现将招聘业务员10人,大家可以内推优秀人才，推荐有奖',
        userName: 'mz',
        time: '2018-11-22',
        is_top:1,
        is_attention:0,
        announcement_id:3
    },
    {
        app: '未发布',
        type: '企业新闻',
        title: '公司周年庆',
        desc: '公司成立15周年，于2019年1月1日晚上举办晚会，请公司员工按时参加活动',
        userName: 'mz',
        time: '2018-11-28',
        is_top:0,
        is_attention:1,
        announcement_id:4
    }
]
const noticeContent = [
    {
        id:'1',
        title:'企业15周年庆',
        desc:'公司成立15周年，历经风雨，才有今天的成就与辉煌，见证企业的发展...,于2019年1月1日晚上8点全体员工准时参加公司晚会活动',
        userName:'梦彧',
        time:'2018-11-26',
        browseCount:"52",
        receiver:"全体员工"
    }
];
const searchContent = [
    {
        id:'3',
        title:'企业15周年庆',
        desc:'公司成立15周年，历经风雨，才有今天的成就与辉煌，见证企业的发展...,于2019年1月1日晚上8点全体员工准时参加公司晚会活动',
        userName:'梦彧',
        time:'2018-12-26',
        browseCount:"52",
        receiver:"全体员工"
    }
];
const searchDraftInfo = [
    {
        id:'0010',
        title:'企业新闻',
        desc:'公司成立15周年，历经风雨，才有今天的成就与辉煌，见证企业的发展...,于2019年1月1日晚上8点全体员工准时参加公司晚会活动',
        userName:'ppbl',
        time:'2018-12-26',
        column:'企业信息'
    },
    {
        id:'0011',
        title:'企业公告',
        desc:'作息时间调整',
        userName:'province',
        time:'2018-12-26',
        column:'企业公告'
    },
    {
        id:'0012',
        title:'企业公告2',
        desc:'作息时间调整2',
        userName:'梦彧',
        time:'2018-12-22',
        column:'企业公告2'
    },
    {
        id:'0013',
        title:'企业公告3',
        desc:'作息时间调整3',
        userName:'梦彧2',
        time:'2018-12-23',
        column:'企业公告3'
    },
    {
        id:'0014',
        title:'企业公告4',
        desc:'作息时间调整4',
        userName:'梦彧4',
        time:'2018-12-24',
        column:'企业公告4'
    },
    {
        id:'0015',
        title:'企业公告5',
        desc:'作息时间调整5',
        userName:'梦彧5',
        time:'2018-12-25',
        column:'企业公告5'
    }
]
module.exports = {

    "/some/path": Mock.mock({
        'number1|1-100.1-10': 1,
        'number2|123.1-10': 1,
        'number3|123.3': 1,
        'number4|123.10': 1.123
    }),
    "/some/path2": [1, 2, 3],

    "/some/path3": (req, res) => {
        res.send('666');
        // res.end('OK');
    },
    "/random_joke": (req, res) => {
        setTimeout(() => {
            res.json(random_jokes);
        }, 3000);
    },

    "/noticeList": (req, res) => {
        setTimeout(() => {
            res.json(noticeList);
        }, 200);
    },
    "/columnInfo": (req, res) => {
        res.json(columnInfo);
    },
    "/noticeInfo": (req, res) => { //展示 已发布，未发布的信息
        switch (req.query.type) {
            case '已发布':
                res.json(publishedInfo);
                break;
            case '未发布':
                res.json(unpublishedInfo);
                break;
        }
    },
    "/noticeContent": (req, res) => { //展示 公告对应显示的内容
        console.log(req.query.id);
        res.json(noticeContent);
    },
    "/searchKey":(req,res)=>{ // 展示搜索显示的内容
        console.log(req.query.key);
        res.json(searchContent);
    },
    "/draftInfo":(req,res)=>{ // 展示草稿箱中的内容
        console.log(req.query.key);
        res.json(searchDraftInfo);
    },
    
}