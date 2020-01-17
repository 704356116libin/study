import { get } from '../utils/request';
import { Model } from 'dva';

/** 公司和用户的一些基础信息 */
const Basis: Model = {
  namespace: 'Basis',
  state: {},
  effects: {
    /** 获取用户基础信息 */
    *queryUserInfo({ payload }, { call, put }) {
      const { cb } = payload || {} as any;
      const result = yield call(get, "/api/u_get_base_info");
      if (result.status === 'success') {
        yield put({ type: 'setUserInfo', payload: result.data });
        cb && cb(result.data);
      }
    },
    /** 获取首页要展示的公司基本信息 */
    *queryBasisInfo(__, { call, put }) {
      const result = yield call(get, "/api/management_enterprise_company_index");
      yield put({ type: 'setBasisInfo', payload: result });
    }
  },
  reducers: {
    setUserInfo(state, { payload }: any) {
      return {
        ...state,
        userInfo: payload
      }
    },
    setBasisInfo(state, { payload }: any) {
      return {
        ...state,
        basisInfo: payload
      }
    }
  }
}
export default Basis
