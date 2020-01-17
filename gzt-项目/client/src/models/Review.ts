import req, { get } from "../utils/request";
import { message } from "antd";
import { Model } from "dva";

const Review: Model = {
  namespace: 'Review',
  state: {
  },
  effects: {
    /** --- 评审通模板 --- */

    /** 经典模板信息 */
    *queryClassicTemplates({ payload: cb }, { call, put }) {
      const result = yield get('/api/c_pst_get_classic_pst_template');
      if (result.status === 'success') {
        yield put({ type: 'setClassicTemplates', payload: result.data });
        cb && cb(result.data);
      } else {
        message.error('服务器异常，请稍后再试')
      }
    },
    /** 列表信息 */
    *queryTemplates({ payload: cb }, { call, put }) {
      const result = yield get('/api/c_pst_get_company_pst_template');
      if (result.status === 'success') {
        yield put({ type: 'setTemplates', payload: result.data });
        cb && cb(result.data.enable);
      } else {
        message.error('服务器异常，请稍后再试')
      }
    },
    /** 分组信息 */
    *queryTemplateGroup({ payload: cb }, { call, put }) {
      const result = yield get('/api/c_pst_get_pst_template_type');
      if (result.status === 'success') {
        yield put({ type: 'setTemplateGroup', payload: result.data });
        cb && cb(result.data);
      } else {
        message.error('服务器异常，请稍后再试')
      }
    },
    /** 指定模板详细信息 (by id) */
    *queryTemplateById({ payload: { id, cb } }, { call, put }) {
      const result = yield get('/api/c_pst_get_single_pst_template', {
        params: {
          id
        }
      });
      if (result.status === 'success') {
        yield put({ type: 'setTemplate', payload: result.data });
        cb && cb(result.data);
      } else {
        message.error('服务器异常，请稍后再试')
      }
    },

    /** --- 评审通流程 --- */
    /** 列表信息 */
    *queryProcesses({ payload }, { call, put }) {
      const result = yield get('/api/c_pst_get_company_process_template');
      if (result.status === 'success') {
        yield put({ type: 'setProcesses', payload: result.data });
      } else {
        message.error('服务器异常，请稍后再试')
      }
    },
    /** 分组信息 */
    *queryProcessGroup({ payload: cb }, { call, put }) {
      const result = yield get('/api/c_pst_get_process_template_type');
      if (result.status === 'success') {
        yield put({ type: 'setProcessGroup', payload: result.data });
        cb && cb(result.data);
      } else {
        message.error('服务器异常，请稍后再试')
      }
    },
    /** 指定流程详细信息 (by id) */
    *queryProcessById({ payload: { id, cb } }, { call, put }) {
      const result = yield get('/api/c_pst_get_single_process_template', {
        params: {
          id
        }
      });
      if (result.status === 'success') {
        yield put({ type: 'setProcess', payload: result.data });
        cb && cb(result.data);
      } else {
        message.error('服务器异常，请稍后再试')
      }
    },
    /** --- 评审通报告导出 --- */
    *queryReports({ payload }, { call, put }) {
      const result = yield get('/api/c_pst_exportTemplateList');
      if (result.status === 'success') {
        yield put({ type: 'setReports', payload: result.data });
      } else {
        message.error('服务器异常，请稍后再试')
      }
    },
    /** 分组信息 */
    *queryReportGroup({ payload: cb }, { call, put }) {
      const result = yield get('/api/c_pst_getExportTypeList');
      // if (result.status === 'success') {
      yield put({ type: 'setReportGroup', payload: result });
      cb && cb(result);
      // } else {
      //   message.error('服务器异常，请稍后再试')
      // }
    },
    /** 指定流程详细信息 (by id) */
    *queryReportById({ payload: { id, cb } }, { call, put }) {
      const result = yield req('/api/c_pst_exportTemplateEdit', {
        method: 'POST',
        body: {
          id
        }
      });
      if (result.status === 'success') {
        yield put({ type: 'setReport', payload: result.data });
        cb && cb(result.data[0]);
      } else {
        message.error('服务器异常，请稍后再试')
      }
    },
    /** 指定流程详细信息 (by id) */
    *queryExportpacks({ payload }, { call, put }) {
      const result = yield call(get, '/api/c_pst_exportPackageLike');
      yield put({ type: 'setExportpacks', payload: result });
    },
    /** --- 评审通其他 --- */
    /** 获取评审通基础表单数据 */
    *querySomeBaseData({ payload: { id, cb } }, { call, put }) {
      const result = yield get('/api/c_pst_get_basic_form_data');

      if (result.status === 'success') {
        yield put({ type: 'setSomeBaseData', payload: result.data });
      } else {
        message.error('服务器异常，请稍后再试')
      }
    },
    *queryCanLinkReviews({ payload }, { call, put }) {
      const result = yield get('/api/c_pst_can_related');

      if (result.status === 'success') {
        yield put({ type: 'setCanLinkReviews', payload: result.data });
      } else {
        message.error('服务器异常，请稍后再试')
      }
    },
    /** --- 评审通 --- */
    *queryReviewsByState({ payload: { params } }, { call, put }) {
      const result = yield get('/api/c_pst_search_by_state', {
        params
      });
      if (result.status === 'success') {
        yield put({ type: 'setReviews', payload: result.data });
      }
    },
    *queryReviewsByStateAndRole({ payload: { params } }, { call, put }) {
      const result = yield get('/api/c_pst_union_search_by_state', {
        params
      });
      if (result.status === 'success') {
        yield put({ type: 'setReviews', payload: result.data });
      }
    },
    /** 评审通单条详情 */
    *queryReviewById({ payload: { id, cb } }, { call, put }) {
      const result = yield get('/api/c_pst_get_single_detail', {
        params: {
          id
        }
      });
      if (result.status === 'success') {
        const { data, files } = result;
        yield put({ type: 'setReview', payload: { data, files } });
        cb && cb(data);
      }
    },
    /** 评审通 相关操作时间轴 */
    *queryTimeline({ payload: { params } }, { call, put }) {
      const result = yield get('/api/c_pst_operate_record', {
        params
      });
      if (result.status === 'success') {
        yield put({ type: 'setTimeline', payload: result.data });
      }
    },
    /** 处理评审详情的一些按钮操作 */
    *mutationsReview({ payload: { pathname, body, id, params, cb } }, { call, put }) {
      const result = yield req(pathname, {
        method: 'POST',
        body
      });

      if (result.status === 'success') {
        cb && cb();
        yield put({ type: 'queryReviewById', payload: { id } });
        yield put({ type: 'queryReviewsByStateAndRole', payload: { params } });
      }
    },
    /** 用户所有追加的表单数据 */
    *queryAllAddformData({ payload: { id, cb } }, { call, put }) {
      const result = yield get('/api/c_pst_get_merge_data', {
        params: { pst_id: id }
      });
      if (result.status === 'success') {
        yield put({ type: 'setAllAddformData', payload: result });
        cb && cb(result.form_data);
      }
    },
    /** 关联审批列表 */
    *queryLinkApprovals({ payload: { id } }, { call, put }) {
      const result = yield get('/api/c_pst_get_related_approval', {
        params: { pst_id: id }
      });
      if (result.status === 'success') {
        yield put({ type: 'setLinkApprovals', payload: result });

      }
    },
    /** 关联评审列表 */
    *queryLinkReviews({ payload: { id } }, { call, put }) {
      const result = yield get('/api/c_pst_get_self_related', {
        params: { pst_id: id }
      });
      if (result.status === 'success') {
        yield put({ type: 'setLinkReviews', payload: result.data });
      }
    }
  },
  reducers: {
    /** 设置评审通模板信息 to state */
    setTemplates(state, { payload }: any) {
      return {
        ...state,
        templates: payload
      }
    },
    setClassicTemplates(state, { payload }: any) {
      return {
        ...state,
        classicTemplates: payload
      }
    },
    setTemplateGroup(state, { payload }: any) {
      return {
        ...state,
        templateGroup: payload
      }
    },
    setProcesses(state, { payload }: any) {
      return {
        ...state,
        processes: payload
      }
    },
    setProcessGroup(state, { payload }: any) {
      return {
        ...state,
        processGroup: payload
      }
    },
    setTemplate(state, { payload }: any) {
      return {
        ...state,
        template: payload
      }
    },
    setProcess(state, { payload }: any) {
      return {
        ...state,
        process: payload
      }
    },
    setReportGroup(state, { payload }: any) {
      return {
        ...state,
        reportGroup: payload
      }
    },
    setReport(state, { payload }: any) {
      return {
        ...state,
        report: payload
      }
    },
    setReports(state, { payload }: any) {
      return {
        ...state,
        reports: payload
      }
    },
    setExportpacks(state, { payload }: any) {
      return {
        ...state,
        exportpacks: payload
      }
    },
    /** --- 评审通其他 --- */
    /** 获取评审通基础表单数据 */
    setSomeBaseData(state, { payload }: any) {
      return {
        ...state,
        someBaseData: payload
      }
    },
    setCanLinkReviews(state, { payload }: any) {
      return {
        ...state,
        canLinkReviews: payload
      }
    },
    /** --- 评审通 --- */
    /** 评审通列表信息 */
    setReviews(state, { payload }: any) {
      return {
        ...state,
        reviews: payload
      }
    },
    /** 某个评审通详情 */
    setReview(state, { payload: { data, files } }: any) {
      return {
        ...state,
        review: data,
        files
      }
    },
    /** 评审通 所有用户追加的表单 */
    setAllAddformData(state, { payload }: any) {
      return {
        ...state,
        allAddFormData: payload
      }
    },
    /** 某个评审通动态时间轴 */
    setTimeline(state, { payload }: any) {
      return {
        ...state,
        timeline: payload
      }
    },
    /** 关联审批列表 */
    setLinkApprovals(state, { payload }: any) {
      return {
        ...state,
        linkApprovals: payload
      }
    },
    /** 关联评审列表 */
    setLinkReviews(state, { payload }: any) {
      return {
        ...state,
        linkReviews: payload
      }
    },
  }
}
export default Review