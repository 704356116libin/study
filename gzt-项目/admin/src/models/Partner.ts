import req, { get } from '../utils/request';
import { message } from 'antd';

export default {

  namespace: 'Partner',
  state: {

  },

  effects: {
    /**
     * 合作企业分组列表
     */
    *queryPartnerList({ payload: cb }: any, { call, put }: any) {
      // 如果是成员申请列表页面添加分组，需要再回调里设置state,其他页面则不需要
      const result = yield call(get, '/api/management_company_partner_types')

      if (cb && typeof cb === 'function') {
        cb(result)
      }
      yield put({
        type: 'setPartnerList',
        payload: result
      });
    },
    /**
     * 分组对应合作企业信息列表
     */
    *queryPartnerCorrespondList({ payload: { params } }: any, { call, put }: any) {
      const result = yield call(get, '/api/management_company_partner', {
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
    *operatingPartnerGroup({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/management_company_partner_types_operating', {
        method: 'POST',
        body: params
      })
      if (result.status === "success") {
        cb && cb(result.id);
        yield put({
          type: 'queryPartnerList',
        });
      }
    },
    /**
     * 合作企业申请列表
     */
    *queryApplicationList({ payload }: any, { call, put }: any) {
      const result = yield call(get, '/api/management_company_partner_apply')
      yield put({
        type: 'setApplicationList',
        payload: result
      });
    },
    /**
     * 搜索合作企业信息
     */
    *queryCompanyPartner({ payload: { params } }: any, { call, put }: any) {
      const result = yield call(req, '/api/management_search_company_partner', {
        method: 'POST',
        body: params
      })
      if (result.status === "success") {
        yield put({
          type: 'setCompanyPartner',
          payload: result.data
        });
      } else {
        message.warning(result.message);
      }
    },

    /**
     * 模糊搜索合作企业信息
     */
    *queryCompanyPartnerByName({ payload: { params } }: any, { call, put }: any) {
      const result = yield call(req, '/api/management_company_partner_by_name', {
        method: 'POST',
        body: params
      })
      if (result.status === "success") {
        yield put({
          type: 'setCorrespondList',
          payload: result
        });
      } else {
        message.warning(result.message);
      }
    },
    /**
     * 邀请合作企业
     */
    *invitePartner({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/c_send_company_partner', {
        method: 'POST',
        body: params
      })
      if (result.status === "success") {
        cb && cb();
      }
    },
    /**
     * 处理合作企业的申请
     */
    *dealPartnerApply({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/c_deal_company_partner', {
        method: 'POST',
        body: params
      })
      if (result.status === "success") {
        cb && cb();
        yield put({
          type: 'queryApplicationList'
        });
      } else if (result.status === "fail") {
        message.warning(result.message);
      }
    },
    /**
     * 解除合作企业的关系
     */
    *releaseRelationship({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/management_delete_company_partner', {
        method: 'DELETE',
        body: params
      })
      if (result.status === "success") {
        cb && cb();
      }
    },
  },
  reducers: {
    setPartnerList(state: any, { payload: partnerList }: any) {
      return {
        ...state,
        partnerList
      }
    },
    setCorrespondList(state: any, { payload: partnerCorrespondList }: any) {
      return {
        ...state,
        partnerCorrespondList
      }
    },
    setApplicationList(state: any, { payload: applicationList }: any) {
      return {
        ...state,
        applicationList
      }
    },
    setCompanyPartner(state: any, { payload: companyPartnerList }: any) {
      return {
        ...state,
        companyPartnerList
      }
    },
  }
}