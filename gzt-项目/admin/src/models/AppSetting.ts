
import req, { get } from '../utils/request';

export default {
  namespace: 'AppSetting',
  state: {
    apps: []
  },
  effects: {
    /**
     * 查找职务列表
     * @param ____  payload 用户传过来的参数
     * @param param1 sagaEffects方法
     */
    *queryApps({ payload: params }: any, { call, put }: any) {
      const result = yield call(get, '/api/management_company_funs');
      if (result.status === 'success') {
        yield put({
          type: 'setApps',
          payload: result.data
        });
      }
    },
    *toggleApp({ payload: { body, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/management_set_company_fun', {
        method: 'POST',
        body
      });

      if (result.status === 'success') {
        cb && cb('success');
      } else {
        cb && cb('fail');
      }
    },

  },
  reducers: {
    setApps(state: any, { payload: apps }: any) {
      return {
        ...state,
        apps
      }
    }
  }
}