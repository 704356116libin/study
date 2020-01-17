import { get } from "../utils/request";
import { Model } from "dva";

const Contact: Model = {
  namespace: 'Contact',
  state: {
  },
  effects: {
    *queryDepartments({ payload: { params } }, { call, put }) {
      const result = yield call(get, '/api/c_department_departmentDetail', {
        params: {
          ...params,
          activation: 1
        }
      });
      if (result.status === 'success') {
        yield put({
          type: 'setDepartments',
          payload: result.data
        })
      }
    },
    *queryPartners({ payload: { params } }, { call, put }) {
      const result = yield call(get, '/api/management_company_partner', { params });
      yield put({
        type: 'setPartners',
        payload: result.data
      })
    },
    *queryExternalContacts({ payload: { params } }, { call, put }) {
      const result = yield call(get, '/api/management_external_users', { params });
      yield put({
        type: 'setExternalContacts',
        payload: result.data
      })
    }
  },
  reducers: {
    setDepartments(state, { payload: departments }: any) {
      return {
        ...state,
        departments
      }
    },
    setPartners(state, { payload: partners }: any) {
      return {
        ...state,
        partners
      }
    },
    setExternalContacts(state, { payload: externalContacts }: any) {
      return {
        ...state,
        externalContacts
      }
    }
  }
}
export default Contact
