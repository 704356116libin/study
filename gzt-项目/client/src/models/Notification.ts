import { get } from '../utils/request';
import { Model } from 'dva';

const Notification: Model = {
  namespace: 'Notification',
  state: {
    unreadCount: 0
  },
  effects: {
    *setDynamicsInfo({ payload: newCard }, { call, put }) {
      yield put({ type: 'Workdynamics/updateDynamicsInfo', payload: newCard });
      yield put({ type: 'setDynamicsCount', payload: { unreadCount: newCard.data.unread_count, action: newCard.action } });
    },
    *queryDynamicsCount(__, { call, put }) {

      const result = yield call(get, "/api/dynamic_get_list_unreadCount");
      if (result.status === 'success') { // special  专门 专门获取数量
        yield put({ type: 'setDynamicsCount', payload: { unreadCount: result.unread_count, action: 'special' } });
      }
    },
  },
  reducers: {
    /**
     * 设置最新应用动态信息
     * @param payload 用户传过来的参数
     */
    setDynamicsCount(state, { payload: { unreadCount, action } }: any) {
      if (action && action === 'news') {
        unreadCount = state.unreadCount + 1
      }
      return {
        unreadCount
      }
    },

  }
}
export default Notification