import moment from 'moment';
import req, { get } from '../utils/request';
import decryptId from '../utils/decryptId'
import { message } from 'antd';
import { Model } from 'dva';

const helloTime = new Map([ // 各个时段欢迎语
  [[3, 4, 5], ['凌晨了', '注意休息']],
  [[6, 7], ['早上好', '该起床上班了']],
  [[8, 9, 10, 11], ['上午好', '新的一天，新的起点，新的动力，从工作通开始~']],
  [[12, 13], ['中午了', '该吃饭了']],
  [[14, 15, 16, 17], ['下午好', '喝点什么吧，然后打起精神']],
  [[18, 19], ['傍晚好', '注意休息']],
  [[20, 21, 22], ['晚上好', '注意休息']],
  [[23, 0, 1, 2], ['深夜了', '注意休息']]
])
const Workdynamics: Model = {
  namespace: 'Workdynamics',
  state: {
    selectedKeys: [window.location.pathname.split('/')[2] ? window.location.pathname.split('/')[2] : 'dynamics'],
    // openKeys: [contact.includes(location.pathname.split('/')[2]) ? 'contact' : ''],
    userInfo: {},
    companyList: [],
    userChange: true,
    type: 'Hello',
    switchListInfo: [{ name: '' }],
    currentSelectKey: -1,
    dynamicsInfo: {
      unread_count: 0,
      data: [
        {
          type: '工作通小秘书',
          unread_count: 0,
          data: {
            title: '我是小秘书',
            content: '有什么问题可以找我'
          }
        },
        {
          type: '工作通大秘书',
          unread_count: 0,
          data: {
            title: '我是大秘书',
            content: '有什么问题也可以找我'
          }
        }
      ]
    },
    workmessage: {
      currentPage: 1,
      data: [],
      noMore: false
    }
  },
  effects: {
    /**
     * 查找用户信息
     * @param ____  payload 用户传过来的参数
     * @param param1 sagaEffects方法
     */
    *queryUserInfo(____, { call, put }) {
      yield put({ type: 'setUserInfo', payload: yield call(req, "/corpprateInfo") });
      yield put({ type: 'setCompanyList', payload: yield call(req, "/companyList") });
    },
    /** 查找最新应用动态信息 */
    *queryDynamicsInfo(__, { call, put }) {
      const dynamicInfo = yield call(req, `/api/dynamic_get_list_info`);
      yield put({
        type: 'setDynamicsInfo',
        payload: dynamicInfo
      });
      yield put({
        type: 'Notification/setDynamicsCount',
        payload: {
          unreadCount: dynamicInfo.unread_count
        }
      });
    },
    /**
     * 删除某条动态信息
     * @param payload 当前需要删除的动态的type, 唯一id(比如工作通知是company_id)
     */
    *deleteListNode({ payload }, { call, put }, ) {
      // 唯一标识映射
      const uniqueIDMap = {
        work_dynamic: 'company_id',
        群组聊天: '群组_id',
        单人聊天: 'user_id'
      }
      // 动态传id
      const deleteInfo = {
        type: payload.type,
        [uniqueIDMap[payload.type]]: payload.data[uniqueIDMap[payload.type]]
      };
      // 请求服务端删除
      const result = yield call(req, `/api/dynamic_delete_list_node`, {
        method: 'POST',
        body: deleteInfo
      });
      // 如果成功, 刷新数据
      if (result.status === 'success') {
        yield put({
          type: 'queryDynamicsInfo'
        })
      }
    },
    /** 显示某一类动态详情信息 */
    *queryDynamicDetail({ payload: params }, { call, put }) {

      const result = yield call(get, `/api/dynamic_get_list_detail`, {
        params
      });
      if (result.status === 'success') {
        yield put({
          type: 'setDynamicDetail',
          payload: {
            type: '',
            currentPage: params.now_page,
            data: result.data
          }
        })
      } else if (result.status === 'fail') {
        message.info('没有更多消息了');
        yield put({
          type: 'setDynamicDetail',
          payload: {
            type: 'noMore'
          }
        })
      }
    },
    /** 查找工作台显示的模块列表 */
    *querySwitchListInfo(____, { call, put }) {
      yield put({
        type: 'setSwitchListInfo',
        payload: yield call(req, `/switchListInfo`)
      });
    },
  },
  reducers: {
    /** 设置欢迎信息 */
    setHello(state) {
      const nowTimeSection = parseInt(moment().format('HH'), 10);
      let helloInfo: string[] = [];
      for (const [time, text] of helloTime) {
        if (time.includes(nowTimeSection)) {
          helloInfo = text;
          break;
        }
      }
      return {
        ...state,
        helloInfo
      }
    },
    /**
     * 设置用户信息到state
     * @param state 上面的state
     * @param param1 payload 用户传过来的参数
     */
    setUserInfo(state, { payload: newCard }: any) {
      state.userChange = false;
      return {
        ...state,
        userInfo: newCard
      }
    },
    /** 设置用户当前拥有的公司列表信息 */
    setCompanyList(state, { payload: newCard }: any) {

      state.userChange = false;
      return {
        ...state,
        companyList: newCard
      }
    },
    /**
     * 设置最新应用动态信息
     * @param payload 用户传过来的参数,应用名称,比如'评审通' 或 '全部动态'
     */
    setDynamicsInfo(state, { payload: newCard }: any) {
      return {
        ...state,
        dynamicsInfo: newCard
      }
    },
    updateDynamicsInfo(state, { payload: { data, action } }: any) {

      const newDynamicsInfo = state.dynamicsInfo.data;
      let currentSelectKey = state.currentSelectKey; // 动态列表当前选中key
      let newDynamic = true;
      let prevUnreadCount: number = 0; // 初始化当前通知的未读数量
      newDynamicsInfo.forEach((item: any, k: number) => {
        if (item.type === data.type) {
          if (item.type === 'work_dynamic') {
            if (decryptId(item.data.company_id) === decryptId(data.data.company_id)) {
              if (action === 'read') {
                data.unread_count = 0;
              } else {
                data.unread_count = prevUnreadCount = item.unread_count + 1;
              }
              newDynamicsInfo[k] = data;
              newDynamic = false;
            }
          }  else if (item.type === 'web_notice') {
            if (decryptId(item.data.company_id) === decryptId(data.data.company_id)) {
              if (action === 'read') {
                data.unread_count = 0;
              } else {
                data.unread_count = prevUnreadCount = item.unread_count + 1;
              }
              newDynamicsInfo[k] = data;
              newDynamic = false;
            }
            newDynamicsInfo[k] = data;
            newDynamic = false;
          }else if (item.type === '群组聊天') {
            if (item.data.群组_id === data.data.群组_id) {
              if (action === 'read') {
                data.unread_count = 0;
              } else {
                data.unread_count = prevUnreadCount = item.unread_count + 1;
              }
              newDynamicsInfo[k] = data;
              newDynamic = false;
            }
            newDynamicsInfo[k] = data;
            newDynamic = false;
          } else if (item.type === '单人聊天') {
            if (item.data.user_id === data.data.user_id) {
              if (action === 'read') {
                data.unread_count = 0;
              } else {
                data.unread_count = prevUnreadCount = item.unread_count + 1;
              }
              newDynamicsInfo[k] = data;
              newDynamic = false;
            }
          }
        }
      });
      if (newDynamic) {
        newDynamicsInfo.unshift(data);
        if (state.type !== 'Hello') {
          currentSelectKey = state.currentSelectKey + 1
        }
      }
      return {
        ...state,
        dynamicsInfo: {
          unread_count: action === 'read' ?
            state.dynamicsInfo.unread_count - prevUnreadCount :
            action === 'news' ?
              state.dynamicsInfo.unread_count + 1 :
              state.dynamicsInfo.unread_count,
          data: [...newDynamicsInfo]
        },
        latestDynamic: data,
        currentSelectKey
      }
    },
    /** 设置当前动态左侧选中项,以及类型 */
    setCurrentSelectKey(state, { payload }: any) {
      const { type, currentSelectKey } = payload
      return {
        ...state,
        type,
        currentSelectKey
      }
    },
    /**
     * 设置某一类动态详细信息
     * @param payload 用户传过来的参数,应用名称,比如'评审通' 或 '全部动态'
     */
    setDynamicDetail(state, { payload: newCard }: any) {
      if (newCard.type === 'noMore') { // 没有更多
        return {
          ...state,
          workmessage: {
            noMore: true,
            currentPage: state.workmessage.currentPage,
            data: state.workmessage.data
          }
        }
      } else if (state.workmessage.currentPage === newCard.currentPage) { // 刷新
        return {
          ...state,
          workmessage: {
            noMore: true,
            currentPage: state.workmessage.currentPage,
            data: [...newCard.data]
          }
        }
      } else { // 更新数据
        return {
          ...state,
          workmessage: {
            currentPage: state.workmessage.currentPage + 1,
            data: [...state.workmessage.data, ...newCard]
          }
        }
      }

    },
    /** 设置工作台显示的模块列表信息 */
    setSwitchListInfo(state, { payload: newCard }: any) {
      return {
        ...state,
        switchListInfo: newCard
      }
    },
    changeUser(state, { payload: newCard }: any) {
      state.userChange = true;
      return state
    },
    setTimer(state, { payload: newCard }: any) {
      state.timers = newCard;
      return state
    }
  }
}
export default Workdynamics