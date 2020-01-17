import { get } from '../utils/request';
import { Model, routerRedux } from 'dva';
import { message } from 'antd';

const Doc: Model = {
  namespace: 'Doc',
  state: {
  },
  effects: {
    /** 获取文件夹 */
    *queryCompanyFolders({ payload: { params, cb } }, { call, put }) {
      const result = yield call(get, '/api/c_oss_get_directory_to_info', {
        params
      })
      if (result.status === 'success') {
        cb && cb(result.data);
        yield put({
          type: 'setCompanyFolders',
          payload: result.data
        })
      } else {
        message.error(result.message)
        yield put(routerRedux.push('/doc/dynamic'))
      }
    },
    *queryPersonalFolders({ payload: { params, cb } }, { call, put }) {
      const result = yield call(get, '/api/u_oss_get_directory_to_info', {
        params
      })
      if (result.status === 'success') {
        cb && cb(result.data);
        yield put({
          type: 'setPersonalFolders',
          payload: result.data
        })
      }
    }
  },
  reducers: {
    setPersonalFolders(state, { payload: personalFolders }: any) {
      return {
        ...state,
        personalFolders
      }
    },
    setCompanyFolders(state, { payload: companyFolders }: any) {
      return {
        ...state,
        companyFolders
      }
    },
  }
}
export default Doc