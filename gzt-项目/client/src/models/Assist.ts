import req, { get } from '../utils/request';
import { Model } from 'dva';

const Assist: Model = {
  namespace: 'Assist',
  state: {
    assistList: [],
    assistDetails: {},
    currentStatus: 'all'
  },
  effects: {
    /**
     * 查找协助列表
     * @param ____  payload 用户传过来的参数
     * @param param1 sagaEffects方法
     */
    *queryAssistList({ payload: params }, { call, put }) {
      const result = yield call(get, '/api/c_assist_taskList', {
        params
      })
      yield put({
        type: 'setAssistList',
        payload: { status: params.status, result }
      });

    },
    *queryAssistDetails({ payload: id, cb }, { call, put }) {

      const result = yield call(req, `/api/c_assist_taskDetail?id=${id}`)

      yield put({
        type: 'setAssistDetails',
        payload: result
      });

      cb && cb(result);
    },
    *searchAssistList({ payload: params }, { call, put }) {
      const result = yield call(get, '/api/c_assist_search', {
        params
      })

      yield put({
        type: 'setSearchList',
        payload: result
      });
    }
  },
  reducers: {
    /** 设置assistList */
    setAssistList(state, { payload: assistList }: any) {
      return {
        ...state,
        currentStatus: assistList.status,
        assistList: assistList.result
      }
    },
    setAssistDetails(state, { payload: assistDetails }: any) {
      return {
        ...state,
        assistDetails
      }
    },
    setSearchList(state, { payload: assistList }: any) {
      return {
        ...state,
        currentStatus: 'all',
        assistList
      }
    }
  }
}
export default Assist