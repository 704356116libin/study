import update from 'immutability-helper';
import request, { get } from '../utils/request';
import { routerRedux, Model } from 'dva';
import { message } from 'antd';


const Urlmap = {
  "notice": ['/work/notice', '公告'],
  "review": ['/work/review', '评审通'],
  "approval": ['/work/approval', '审批'],
  "assist": ['/work/assist', '协助'],
  "communication": ['/work/communication', '沟通']
}



const Workbench: Model = {
  namespace: 'Workbench',
  state: {
    navigation: window.location.pathname.split('/')[1] === 'work'
      ? window.location.pathname.split('/')[2]
        ? [Urlmap[window.location.pathname.split('/')[2].split('-')[0]]]
        : []
      : [],
    currentTab: window.location.pathname.split('/')[1] === 'work'
      ? window.location.pathname.split('/')[2]
        ? Urlmap[window.location.pathname.split('/')[2].split('-')[0]][0]
        : '/work'
      : '/work',
    needJump: false
  },
  effects: {
    /**
     * 查找用户信息
     * @param ____  payload 用户传过来的参数
     * @param param1 sagaEffects方法
     */
    *handleNavigation({ payload: newCard }, { call, put }) {

      if (!newCard.nav) {
        // yield put(dispatch( routerRedux.push('/work'));
      }
      yield put({
        type: 'setNavigation',
        payload: newCard
      });
    },
    /** 获取用户当前加入的全部公司列表 */
    *queryCompanys({ payload }, { call, put, cancelled }) {
      const { cb } = payload || {} as any;
      const result = yield call(get, '/api/c_company_list');
      cb && cb(result);
      yield put({
        type: 'setCompanys',
        payload: result
      })
    },
    /** 切换当前所在公司 */
    *changeCompany({ payload: { id, cb } }, { call, put }) {
      const result = yield call(request, '/api/u_alter_company_id', {
        method: 'POST',
        body: {
          company_id: id === '0' ? Number(0) : id
        }
      })
      if (result.status === 'success') {
        message.success('公司切换成功');
        yield put({
          type: 'setCompanys',
          payload: result.data
        });
        yield put(routerRedux.push('/work'));  // 跳转到工作页面首页
        yield put({ type: 'resetWorkbench' });
        cb && cb();
      }
    }
  },
  reducers: {
    /**
     * 设置tab导航
     */
    setNavigation(state, { payload: { nav, k } }: any) {
      // 如果是当前已激活的标签
      if (k !== undefined) {
        let currentTab = state.currentTab;
        // 如果是当前选中的
        if (nav[0] === state.currentTab) {
          // 如果是最后一个
          if (k === state.navigation.length - 1) {
            // 如果是紧跟着工作台
            if (k === 0) {
              currentTab = '/work';
            } else {
              currentTab = state.navigation[k - 1][0];
            }
          } else {
            currentTab = state.navigation[k + 1][0];
          }
        } else {

          if (k === state.navigation.length - 1) {
            if (k === 0) {
              currentTab = '/work';
            }
          }
        }

        return {
          ...state,
          currentTab,
          navigation: update(state.navigation, {
            $splice: [[k, 1]]
          }),
          needJump: true
        }
      }
      let navigation = state.navigation;
      let havenot = true;
      for (const prevNav of state.navigation) {

        if (prevNav[0] === nav[0]) {
          havenot = false;
          break;
        }

      }
      if (havenot) {
        navigation = update(navigation, {
          $push: [nav]
        })

      }

      return {
        ...state,
        navigation,
        currentTab: nav[0],
        needJump: false
      }

    },
    setTabActive(state, { payload: newCard }: any) {
      return {
        ...state,
        navigation: state.navigation,
        currentTab: newCard,
        needJump: false
      }
    },
    setCompanys(state, { payload: companys }: any) {
      return {
        ...state,
        companys
      }
    },
    resetWorkbench(state) {
      return {
        ...state,
        navigation: [],
        currentTab: '/work',
        needJump: false
      }
    }
  }
}
export default Workbench