import req, { get, postForm } from '../utils/request';
import { message } from 'antd';
import { Model } from 'dva';

const Notice: Model = {
  namespace: 'Notice',
  state: {
    noticeList: [
      {
        text: 'loading...',
        id: 1
      },
      {
        text: 'loading...',
        id: 2
      },
    ],
  },
  effects: {
    /** 获取所有公告栏目信息 */
    *queryInitList({ payload }, { call, put }) {
      const result = yield call(get, '/api/c_notice_getAllColumn')
      yield put(
        {
          type: 'initList',
          payload: result
        }
      );
    },
    /** 展示所有的公告的信息 */
    *queryNoticeInfo({ payload }, { call, put }) {
      const result = yield call(get, '/api/c_notice_show', {
        params: {
          now_page: payload.now_page,
          page_size: payload.page_size,
        }
      })
      yield put({
        type: 'setNoticeInfo',
        payload: result
      });
    },
    /** 获取栏目对应信息 */
    *querycolumnDetailInfo({ payload }, { call, put }) {
      const result = yield call(get, '/api/c_notice_showByColumn', {
        params: {
          column_id: payload.column_id,
          now_page: payload.now_page,
          page_size: payload.page_size,
        }
      })
      yield put(
        {
          type: 'setNoticeInfo',
          payload: result
        }
      );
    },
    /** 通过id展示公告详请 */
    *queryNoticeContent({ payload }, { call, put }) {
      const result = yield call(get, '/api/c_notice_getNoticeById', {
        params: {
          notice_id: payload.noticeId
        }
      })
      yield put({
        type: 'setNoticeContent',
        payload: result
      });
    },
    /** 展示搜索的信息 */
    *querySearchInfo({ payload }, { call, put }) {
      const result = yield call(get, '/api/c_notice_searchNoticeByTitle', {
        params: {
          title: payload.title
        }
      })
      yield put({
        type: 'setNoticeInfo',
        payload: result
      });
    },
    /** 展示草稿箱中的信息 */
    *queryDraftInfo({ payload }, { call, put }) {
      const result = yield call(get, '/api/c_notice_getCancelNotice', {
        params: {
          now_page: payload.now_page,
          page_size: payload.page_size,
        }
      })
      yield put({
        type: 'setNoticeInfo',
        payload: result
      });
    },
    /** 用户关注的公告列表信息 */
    *queryMyFollowsList({ payload }, { call, put }) {
      const result = yield call(get, '/api/c_notice_user_follows', {
        params: {
          now_page: payload.now_page,
          page_size: payload.page_size,
        }
      })
      yield put({
        type: 'setNoticeInfo',
        payload: result
      });
    },
    /**  新建栏目分类信息 */
    *queryColumnClassify({ payload }, { call, put }) {
      const result = yield call(req, `/api/c_notice_addColumn`, {
        method: "post",
        body: { ...payload }
      })
      if (result.status === "success") {
        yield put({
          type: 'queryInitList'
        });
      } else if (result.status === "fail") {
        message.info(result.message);
      }
    },
    /** 删除公告栏目信息 */
    *removeNoticeColumn({ payload: { columnId, reload, showInfo } }, { call, put }) {
      const result = yield call(req, `/api/c_notice_removeColumn`, {
        method: "post",
        body: {
          column_id: columnId
        }
      })
      if (result.status === "success") {
        reload();
        message.success(result.message);
      } else if (result.status === "fail") {
        showInfo();
        message.success(result.message);
      }
    },
    /** 重命名公告栏目 */
    *queryColumnInfo({ payload }, { call, put }) {
      const result = yield call(req, `/api/c_notice_alterColumn`, {
        method: "post",
        body: {
          column_id: payload.column_id,
          name: payload.name
        }
      })
      if (result.status === "success") {
        yield put({
          type: 'queryInitList'
        });
      }
    },
    /** 置顶公告 */
    *isTopNoticeInfo({ payload: { value, reload } }, { call, put }) {
      const result = yield call(req, `/api/c_notice_top`, {
        method: "post",
        body: { notice_id: value }
      })
      if (result.status === "success") {
        reload();
      }
    },
    /** 取消置顶公告 */
    *noTopNoticeInfo({ payload: { value, reload } }, { call, put }) {
      const result = yield call(req, `/api/c_notice_topCancle`, {
        method: "post",
        body: { notice_id: value }
      })
      if (result.status === "success") {
        reload();
      }
    },
    /** 关注公告 */
    *followNoticeInfo({ payload: { value, reload } }, { call, put }) {
      const result = yield call(req, `/api/c_notice_followNotice`, {
        method: "post",
        body: { notice_id: value }
      })
      if (result.status === "success") {
        reload();
      }
    },
    /** 取消关注公告 */
    *enFollowNoticeInfo({ payload: { value, reload } }, { call, put }) {
      const result = yield call(req, `/api/c_notice_deFollowNotice`, {
        method: "post",
        body: { notice_id: value }
      })
      if (result.status === "success") {
        reload();
      }
    },
    /** 删除草稿箱的公告 */
    *removeNoticeInfo({ payload }, { call }) {
      const result = yield call(req, `/api/c_notice_remove`, {
        method: "post",
        body: { notice_id: payload.value }
      })
      // 执行子组件传递的方法
      if (result.status === "success") {
        payload.reload && payload.reload();
        payload.jumpPage && payload.jumpPage();
      }
    },
    /** 发布公告 */
    *handleFormInfo({ payload: { value, reload } }, { call, put }) {
      const result = yield call(postForm, `/api/c_notice_add`, {
        body: value
      })
      if (result.status === "success") {
        reload();
      }else {
        message.info(result.message)
      }
    },
    /**
     * 公告已读记录
     */
    *queryBrowseRecord({ payload: { notice_id, now_page } }, { call, put }) {
      const result = yield call(get, `/api/c_notice_browse_record`, {
        params: {
          notice_id, now_page
        }
      })
      yield put({
        type: 'setBrowseRecord',
        payload: result
      });
    },
    /** 公告未读记录 */
    *queryBrowseUnRecord({ payload: { notice_id, now_page } }, { call, put }) {
      const result = yield call(get, `/api/c_notice_unbrowse_record`, {
        params: {
          notice_id, now_page
        }
      })
      yield put({
        type: 'setBrowseUnRecord',
        payload: result
      });
    },
    /** 获取合作伙伴公告 */
    *queryPartnerNotices({ payload: { params } }, { call, put }) {
      const result = yield call(req, '/api/c_notice_getPartnerNotice', {
        method: 'POST',
        body: params
      });
      if (result.status === 'success') {
        yield put({
          type: 'setPartnerNotices',
          payload: result
        });
      } else {
        yield put({
          type: 'setPartnerNotices',
          payload: {
            page_count: 0,
            page_size: params.page_size,
            now_page: params.now_page,
            all_count: 0,
            data: []
          }
        })
      }
    },
  },
  reducers: {
    initList(state, { payload: noticeList }: any) {
      return {
        ...state,
        noticeList
      }
    },
    setNoticeInfo(state, { payload: publishedInfo }: any) {
      return {
        ...state,
        publishedInfo
      }
    },
    /** 展示公告详细信息 */
    setNoticeContent(state, { payload: noticeContent }: any) {
      return {
        ...state,
        noticeContent
      }
    },
    /** 展示栏目分类的信息 */
    setColumnClassifyInfo(state, { payload: setColumnClassifyInfo }: any) {
      return {
        ...state,
        noticeList: setColumnClassifyInfo
      }
    },
    /** 展示公告已读记录 */
    setBrowseRecord(state, { payload: noticeRecord }: any) {
      return {
        ...state,
        noticeRecord
      }
    },
    /** 展示公告未读记录 */
    setBrowseUnRecord(state, { payload: noticeUnRecord }: any) {
      return {
        ...state,
        noticeUnRecord
      }
    },
    setPartnerNotices(state, { payload: publishedInfo }: any) {
      return {
        ...state,
        publishedInfo
      }
    }
  }
}
export default Notice