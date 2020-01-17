import req, { get } from '../utils/request';
import { message } from 'antd';

export default {

  namespace: 'Contact',
  state: {

  },

  effects: {
    /**
     * 合作企业分组列表
     */
    *queryContactList({ payload }: any, { call, put }: any) {
      const result = yield call(get, '/api/management_external_contact_types')
      yield put({
        type: 'setContactList',
        payload: result
      });
    },
    /**
     * 分组对应外部联系人信息列表
     */
    *queryContactCorrespondList({ payload: { params } }: any, { call, put }: any) {
      const result = yield call(get, '/api/management_external_users', {
        params
      })

      yield put({
        type: 'setCorrespondList',
        payload: result
      });
    },
    /**
     * 分组名的增,删,改
     */
    *operatingContactGroup({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/management_external_contact_types_operating', {
        method: 'POST',
        body: params
      })
      if (result.status === "success") {
        cb && cb();
        yield put({
          type: 'queryContactList',
        });
      }
    },
    /**
     * 外部联系人申请列表
     */
    *queryApplicationList({ payload }: any, { call, put }: any) {
      const result = yield call(get, '/api/management_external_users')
      yield put({
        type: 'setApplicationList',
        payload: result
      });
    },
    /**
     * 搜索外部联系人信息
     */
    *queryCompanyContact({ payload: { params } }: any, { call, put }: any) {
      const result = yield call(req, '/api/management_search_external_users', {
        method: 'POST',
        body: params
      })
      if (result.status === 'success') {
        yield put({
          type: 'setCompanyContact',
          payload: result.data
        });
      } else {
        message.info(result.message)
      }

    },
    /**
     * 模糊搜索外部联系人信息
     */
    *queryCompanyContactByname({ payload: { params } }: any, { call, put }: any) {
      const result = yield call(req, '/api/management_external_user_by_name', {
        method: 'POST',
        body: params
      })
      yield put({
        type: 'setCorrespondList',
        payload: result
      });
    },
    /** 邀请外部联系人 */
    *inviteContact({ payload: { body, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/management_invite_external_users', {
        method: 'POST',
        body
      })
      if (result.status === "success") {
        cb && cb()
      } else {
        message.error(result.message)
      }
    },
    /**
     * 处理合作企业的申请
     */
    *dealContactApply({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/c_deal_company_contact', {
        method: 'POST',
        body: params
      })
      if (result.status === "success") {
        cb && cb();
        yield put({
          type: 'queryApplicationList'
        });
      }
    },
    /**
     * 解除联系人的关系
     */
    *releaseRelationship({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/management_delete_external_user', {
        method: 'DELETE',
        body: params
      })
      if (result.status === "success") {
        cb && cb();
      }
    },
  },
  reducers: {
    setContactList(state: any, { payload: contactList }: any) {
      return {
        ...state,
        contactList
      }
    },
    setCorrespondList(state: any, { payload: contactCorrespondList }: any) {
      return {
        ...state,
        contactCorrespondList
      }
    },
    setApplicationList(state: any, { payload: partnerList }: any) {
      return {
        ...state,
        partnerList
      }
    },
    setCompanyContact(state: any, { payload: companyContactList }: any) {
      return {
        ...state,
        companyContactList
      }
    },
  }
}