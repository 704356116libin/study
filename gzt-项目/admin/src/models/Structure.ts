import req, { get } from '../utils/request';
import { message } from 'antd';
export default {
  namespace: 'Structure',
  state: {

  },
  effects: {
    /** 公司信息 */
    *queryCompanyInfo({ payload: { cb } }: any, { call, put }: any) {
      const result = yield call(get, '/api/c_department_getAllTree')
      cb && cb(result.data.id, result.data.name);
      yield put({
        type: 'setCompanyInfo',
        payload: result
      });
    },
    /** 部门下人员信息 */
    *queryDepartmentInfo({ payload: { params } }: any, { call, put }: any) {
      const result = yield call(get, '/api/c_department_departmentDetail', {
        params: {
          ...params,
          activation: 2
        }
      })
      if (result.status === "success") {
        yield put({
          type: 'setCompanyDepStaffInfo',
          payload: result
        });
      }
    },
    /** 修改部门名称 */
    *queryDepartmentName({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/c_department_editDepartment', {
        method: "POST",
        body: params
      })
      if (result.status === "success") {
        cb && cb();
        yield put({
          type: 'queryCompanyInfo',
          payload: result
        });
      }
    },
    /** 邀请码 */
    // * queryCompanyInviteInfo(payload: any, { call, put }: any) {
    //   const result = yield call(get, '/api/management_generate_invitation_code')
    //   yield put({
    //     type: 'setCompanyInviteInfo',
    //     payload: result
    //   });
    // },
    /** 邀请员工 */

    *inviteStaffByTel({ payload: { body, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/c_department_addStallByTel', {
        method: 'POST',
        body
      })

      if (result.status === "success") {
        cb && cb();

        yield put({
          type: 'setInviteStaffByTel',
          payload: result
        });

      } else if (result.status === "fail") {
        message.error(result.message)
      }
    },

    /** 通过手机号查找用户信息*/

    *queryStaffListByTel({ payload: { params } }: any, { call, put }: any) {

      const result = yield call(get, '/api/c_department_searchTel', {
        params
      })
      yield put({
        type: 'set123',
        payload: result
      });
    },
    /**禁用员工 */
    *disableStaff({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/c_department_batchFreeze', {
        method: 'POST',
        body: params.data
      })
      if (result.status === "success") {
        cb && cb();
      } else {
        message.info(result.message);
      }
    },
    /**启用员工 */

    *enableStaff({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/c_department_thaw', {
        method: 'POST',
        body: params.data
      })
      if (result.status === "success") {
        cb && cb();
      }
    },
    /**选择职务 */
    *queryManagementRoles({ payload }: any, { call, put }: any) {
      const result = yield call(get, '/api/management_roles')
      yield put({
        type: 'setManagementRoles',
        payload: result
      });
    },
    /**批量修改职务 */
    *editStaffPosition({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/c_department_batchEditRoles', {
        method: 'POST',
        body: params
      })
      if (result.status === "success") {
        cb && cb();
      } else {
        message.info(result.message);
      }
    },
    /**新增部门 */
    *addDepartment({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/c_department_appendNode', {
        method: 'POST',
        body: params
      })
      if (result.status === "success") {
        cb && cb();
      } else {
        message.info(result.message);
      }
    },
    /**批量修改部门 */
    *batchEditDepartment({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/c_department_batchEditDepartments', {
        method: 'POST',
        body: params
      })
      if (result.status === "success") {
        cb && cb();
      } else {
        message.info(result.message);
      }
    },
    /**新增员工 */
    *addStaff({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/c_department_saveUserDate', {
        method: 'POST',
        body: params
      })
      if (result.status === "success") {
        cb && cb();
      } else {
        message.info(result.message);
      }
    },
    /**查找员工详细的信息 */
    *queryStaffDetailInfo({ payload: { params } }: any, { call, put }: any) {
      const result = yield call(req, '/api/c_department_userDetail', {
        method: 'POST',
        body: params
      })
      if (result.status === "success") {
        yield put({
          type: 'setStaffInfo',
          payload: result.data
        });
      } else {
        message.info(result.message);
      }
    },
    /**编辑员工信息 */
    *editStaffInfo({ payload: { params, cb } }: any, { call, put }: any) {
      const result = yield call(req, '/api/c_department_editUserDetail', {
        method: 'POST',
        body: params
      })
      if (result.status === "success") {
        cb && cb();
      } else {
        message.info(result.message);
      }
    },
    /**员工邀请链接 */
    *queryInvitationUrl({ payload: { cb } }: any, { call, put }: any) {
      const result = yield call(get, '/api/management_invitation_url')
      cb && cb(result.url);
      yield put({
        type: 'setInvitationUrl',
        payload: result
      });
    },
    /**邀请链接是否过期 */
    *queryUrlInvalid({ payload: { params, cb } }: any, { call, put }: any) {

      const result = yield call(req, '/api/management_redeem_invitation_code', {
        method: 'POST',
        body: params
      })
      if (result.status === "success") {
        console.log(6265);
      }
    },
  },
  reducers: {
    /**公司树信息 */
    setCompanyInfo(state: any, { payload: companyInfo }: any) {
      return {
        ...state,
        companyInfo
      }
    },
    /**公司部门对应员工信息 */
    setCompanyDepStaffInfo(state: any, { payload: companyDepStaffInfo }: any) {
      return {
        ...state,
        companyDepStaffInfo
      }
    },
    // setCompanyInviteInfo(state: any, { payload: companyInviteInfo }: any) {
    //   console.log(companyInviteInfo);
    //   return {
    //     ...state,
    //     companyInviteInfo
    //   }
    // },
    setManagementRoles(state: any, { payload: managementRolesInfo }: any) {
      return {
        ...state,
        managementRolesInfo
      }
    },
    setStaffInfo(state: any, { payload: staffInfo }: any) {
      return {
        ...state,
        staffInfo
      }
    },
    setInvitationUrl(state: any, { payload: invitationUrl }: any) {
      return {
        ...state,
        invitationUrl
      }
    },
    setStaffDetailInfo(state: any, { payload: staffDetail }: any) {

      return {
        ...state,
        staffDetail
      }
    }

  }
}