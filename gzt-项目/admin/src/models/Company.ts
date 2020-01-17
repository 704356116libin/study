
import req, { get } from '../utils/request';
import { message } from 'antd';
export default {
  namespace: 'Company',
  state: {

  },
  effects: {
    // permission模块
    /**
     * 查找职务列表
     * @param ____  payload 用户传过来的参数
     * @param param1 sagaEffects方法
     */
    *queryPermissionList({ payload: params }: any, { call, put }: any) {
      const result = yield call(get, '/api/management_roles', {
        params
      })
      yield put({
        type: 'setPermissionList',
        payload: result
      });
    },
    /** 删除职务 */
    *removePermissionById({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/management_role_delete', {
        method: "delete",
        body: params
      })
      if (result.status === "success") {
        cb && cb();
        yield put({
          type: 'queryPermissionList',
        });
      } else {
        message.info('删除失败，请稍后再试~')
      }
    },
    /** 编辑职务 */
    *queryPositionInfo({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(get, '/api/management_role_edit', {
        params
      })
      cb && cb(result.per);
      yield put({
        type: 'setEditPermissionList',
        payload: result
      });
    },

    /** 查找公司基础权限 */
    * queryBasePermission({ payload }: any, { call, put }: any) {
      const result = yield call(get, '/api/management_c_per', { payload })
      yield put({
        type: 'setBasePermission',
        payload: result
      });
    },
    //sort 模块
    /** 部门列表 */
    * queryDepartmentList({ payload }: any, { call, put }: any) {
      const result = yield call(req, '/api/management_descendants', {
        method: "POST",
        body: payload
      })
      yield put({
        type: 'setDepartmentList',
        payload: result
      });
    },
    /** 获取企业当前认证信息 */
    *queryCertInfo({ payload }: any, { call, put }: any) {
      const result = yield call(get, '/api/management_get_enterprise_file');
      if (result.status === 'success') {
        yield put({
          type: 'setCertInfo',
          payload: result.enterpriseFile
        });
      }
    },
    // /** 获取公司基本信息 */
    // * queryCompanyInfo({ payload }: any, { call, put }: any) {
    //   const result = yield call(get, '/api/management_enterprise_company_data');
    //   if (result.status === 'success') {
    //     yield put({
    //       type: 'setCompanyInfo',
    //       payload: result.data
    //     });
    //   }

    // },
  },
  reducers: {
    setPermissionList(state: any, { payload: permissionList }: any) {
      return {
        ...state,
        permissionList
      }
    },

    setBasePermission(state: any, { payload: basePermissionList }: any) {
      return {
        ...state,
        basePermissionList
      }
    },
    setDepartmentList(state: any, { payload: departmentList }: any) {
      return {
        ...state,
        departmentList
      }
    },
    setEditPermissionList(state: any, { payload: editPermissionList }: any) {
      return {
        ...state,
        editPermissionList
      }
    },
    setCertInfo(state: any, { payload }: any) {
      return {
        ...state,
        certInfo: payload
      }
    },
    // /** 设置公司信息 */
    // setCompanyInfo(state: any, { payload }: any) {
    //   return {
    //     ...state,
    //     companyInfo: payload
    //   }
    // },
  }
}