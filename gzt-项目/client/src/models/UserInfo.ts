import req, { get } from '../utils/request';
import { routerRedux, Model } from 'dva';

const UserInfo: Model = {
  namespace: 'UserInfo',
  state: {
    userApp: []
  },
  effects: {
    *queryUserApp(____, { call, put }) { // sagaEffects
      let disabledApp; // 存储用户当前没权限或禁用的app信息
      if (localStorage.getItem('disabledApp')) {
        disabledApp = JSON.parse(localStorage.getItem('disabledApp')!);
      } else {
        disabledApp = yield call(req, "/disabledApp");
        localStorage.setItem('disabledApp', JSON.stringify(disabledApp));
      }
      const appName = window.location.pathname.split('/')[1]; // 获取当前应用

      if (disabledApp.includes(appName)) { // 如果被禁用
        yield put(routerRedux.push('/'))  // 跳转到根目录为'/'的页面
      } else { // 否则获取数据,进行下一步（渲染顶部导航）
        yield put({ type: 'setUserAppInfo', payload: yield call(req, "/userApp") });
      }
    },
    /** 获取用户基础信息 */
    *queryUserInfo({ payload }, { call, put }) {
      const { cb } = payload || {} as any;
      const result = yield call(get, "/api/u_get_base_info");
      if (result.status === 'success') {
        yield put({ type: 'setUserInfo', payload: result.data });
        cb && cb(result.data);
      }
    },
    /** 获取用户权限信息 */
    *queryUserPermission(__, { call, put }) {
      const result = yield call(get, "/api/u_get_permissions");
      // if (result.status === 'success') {
      yield put({ type: 'setUserPermission', payload: result });
      // }
    },
    /** 更新用户基础信息 */
    *updateUserInfo({ payload: { body, cb } }, { call, put }) {
      const result = yield call(req, "/api/u_eidtPersonalData", {
        method: 'POST',
        body
      });
      if (result.status === 'success') {
        cb && cb();
        yield put({ type: 'queryUserInfo' });
      }
    }
  },
  reducers: {
    setUserAppInfo(state, { payload: newCard }: any) {
      return {
        ...state,
        userApp: newCard
      }
    },
    setUserInfo(state, { payload }: any) {
      return {
        ...state,
        userInfo: payload
      }
    },
    setUserPermission(state, { payload: permission }: any) {
      return {
        ...state,
        permission
      }
    }
  }
}
export default UserInfo