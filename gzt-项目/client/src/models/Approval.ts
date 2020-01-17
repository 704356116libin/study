import req, { get } from '../utils/request';
import { message } from 'antd';
import { Model } from 'dva';

const Approval: Model = {
  namespace: 'Approval',
  state: {
    assistList: [],
    assistDetails: {},
    currentStatus: 'all'
  },
  effects: {
    /**
     * 查找审批列表
     * @param ____  payload 用户传过来的参数
     * @param param1 sagaEffects方法
     */
    *queryApprovalList({ payload: params }, { call, put }) {
      const result = yield call(get, '/api/c_approval_list', {
        params
      })
      yield put({
        type: 'setApprovalList',
        payload: result
      });
    },
    /**
     * 审批-可用模板
     */
    *queryApplyTemplateList({ payload: params }, { call, put }) {
      const result = yield call(get, '/api/c_approval_templates_able', {
        params
      })
      yield put({
        type: 'setApplyTemplateList',
        payload: result
      });
    },
    /**
     * 审批管理-默认模板列表
     */
    *queryDefaultTemList({ payload }, { call, put }) {
      const result = yield call(get, '/api/c_approval_templates_classic', {
        payload
      })
      yield put({
        type: 'setDefaultTemplateList',
        payload: result
      });
    },
    /**
     * 新建审批-重新申请
     */

    *queryAgainApplyInfo({ payload: params }, { call, put }) {
      const result = yield call(get, '/api/c_approval_again_apply', {
        params
      })
      yield put({
        type: 'correspondTemplateInfo',
        payload: result
      });
    },

    /**
     * 审批管理-已有模板列表
     */
    *queryExistTemList({ payload }, { call, put }) {
      const result = yield call(get, '/api/c_approval_templates_existing', {
        payload
      })
      yield put({
        type: 'setExistTemplateList',
        payload: result
      });
    },
    /**
     * 新建审批
     */
    *queryApprovalFormInfo({ payload }, { call, put }) {
      const result = yield call(req, `/c_approval_create`, {
        method: "post",
        body: { ...payload }
      })
      yield put({
        type: 'setApprovalForm',
        payload: result
      });
    },
    /**
     * 详情
     */
    *queryApprovalDetail({ payload: { params, cb } }, { call, put }) {
      const result = yield call(get, `/api/c_approval_detail`, {
        params
      })

      yield put({
        type: 'setApprovalDetail',
        payload: result
      });

      cb && cb(result)
    },
    /**
     * 审批-模板选择列表
     */

    *queryTemplateSelectList({ payload: cb }, { call, put }) {

      const result = yield call(get, `/api/c_approval_types`);
      // 如果是创建审批模板页面，需要再回调里设置state,其他页面则不需要
      if (cb && typeof cb === 'function') {
        cb(result)
      } else {
        yield put({
          type: 'setTemplateSelectList',
          payload: result
        });
      }

    },
    /**
     * 创建审批-对应模板展示
     */

    *queryCorrespondTemplateInfo({ payload: params }, { call, put }) {
      const result = yield call(get, `/api/c_approval_template_select`, {
        params
      })
      yield put({
        type: 'correspondTemplateInfo',
        payload: result
      });
    },
    /**
     * 审批管理-模板列表
     */
    *queryManagementTemList({ payload }, { call, put }) {
      const result = yield call(get, `/api/c_approval_templates_all`, {
        payload
      })
      yield put({
        type: 'setManagementTemList',
        payload: result
      });
    },
    /**
     * 审批管理-是否启用模板
     */
    *queryTemWhetherToEnable({ payload: params }, { call, put }) {
      const result = yield call(get, `/api/c_approval_template_enable`, {
        params
      })
      if (result.status === "success") {
        message.info(result.message);
        yield put({
          type: 'queryManagementTemList',
        });
      } else {
        message.info(result.message);
      }
    },

    /**
     * 审批管理-排序列表
     */
    *queryTemSortList({ payload: params }, { call, put }) {
      const result = yield call(get, `/api/c_approval_types`, {
        params
      })
      yield put({
        type: 'setTemSortList',
        payload: result
      });
    },
    /**
     * 审批管理-修改名称
     */
    *queryCreateTemName({ payload: { params, cb } }, { call, put }) {
      const result = yield call(req, `/api/c_approval_type_edit`, {
        method: "post",
        body: params
      })
      if (result.status === "success") {
        cb && cb();
        yield put({
          type: 'queryManagementTemList',
        });
      }
    },
    /**
     * 删除模板 
     */
    *removeTemplate({ payload: { params, cb } }, { call, put }) {
      const result = yield call(get, `/api/c_approval_template_delete`, {
        params
      })
      if (result.status === "success") {
        cb && cb();
        yield put({
          type: 'queryManagementTemList',
        });
      }
    },
    /**
     * 删除类型 
     */
    *removeTemplateType({ payload: { params } }, { call, put }) {
      const result = yield call(get, `/api/c_approval_type_delete`, {
        params: { id: params.id }
      })
      if (result.status === "success") {
        message.info(result.message)
        yield put({
          type: 'queryManagementTemList',
        });
      } else if (result.status === "fail") {
        message.warning(result.message);
      }
    },

    /**
     * 审批管理-添加分组
     */
    *queryApprovalGroupType({ payload: { params, cb } }, { call, put }) { //{ params, cb}
      const result = yield call(req, `/api/c_approval_type_add`, {
        method: "post",
        body: params
      })

      if (result.status === "success") {
        cb && cb();
        yield put({
          type: 'queryManagementTemList',
        });
      }
    },
    /**
     * 审批管理-创建审批-selsec组件中新建分组
     */
    *queryApprovalGroupTypeInner({ payload: { newType, cb } }, { call, put }) {

      const result = yield call(req, `/api/c_approval_type_add`, {
        method: "post",
        body: {
          name: newType
        }
      })
      if (result.status === "success") {
        cb && cb(result.data.type_id)
      } else if (result.status === "reachLimit") {
        message.info(result.message)
      }
    },
    /**
     * 审批管理-展示编辑跳转后的详细内容
     */
    *queryManagementTemplateInfo({ payload: params }, { call, put }) {
      const result = yield call(get, `/api/c_approval_template_edit`, {
        params
      })

      yield put({
        type: 'setManagementDetailInfo',
        payload: result
      });
    }
  },
  reducers: {
    /** 设置approvalList */
    setApprovalList(state, { payload: approvalList }: any) {
      return {
        ...state,
        approvalList
      }
    },
    setApplyTemplateList(state, { payload: applyTemplateList }: any) {
      return {
        ...state,
        applyTemplateList
      }
    },
    setDefaultTemplateList(state, { payload: defaultTemplateList }: any) {
      return {
        ...state,
        defaultTemplateList
      }
    },
    setExistTemplateList(state, { payload: existTemplateList }: any) {
      return {
        ...state,
        existTemplateList
      }
    },
    setApprovalDetail(state, { payload: approvalDetails }: any) {
      return {
        ...state,
        approvalDetails
      }
    },
    setManagementTemList(state, { payload: managementTemList }: any) {
      return {
        ...state,
        managementTemList
      }
    },
    setTemSortList(state, { payload: temSortList }: any) {
      return {
        ...state,
        temSortList
      }
    },
    setTemplateSelectList(state, { payload: templateSelectInfo }: any) {
      return {
        ...state,
        templateSelectInfo
      }
    },
    correspondTemplateInfo(state, { payload: correspondTemplate }: any) {
      return {
        ...state,
        correspondTemplate
      }
    },
    setManagementDetailInfo(state, { payload: managementDetailInfo }: any) {
      return {
        ...state,
        managementDetailInfo
      }
    }

  }
}
export default Approval