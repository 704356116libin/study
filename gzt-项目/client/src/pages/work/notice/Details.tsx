import * as React from 'react';
import { Layout, Row, Col, Button, Popconfirm, Icon, message, Spin } from 'antd';
import req from '../../../utils/request';
import Contents from './Contents'
import { connect } from 'dva';
import { History } from 'history'
import BrowseModal from './BrowseModal';
import { Link } from 'react-router-dom';
import './contents.scss';

const NAMESPACE = 'Notice'; // dva model 命名空间
interface ContentProps {
  location: any,
  noticeContent: any,
  deleteNoticeInfo: any,
  followNoticeInfo: any,
  enFollowNoticeInfo: any,
  isTopNoticeInfo: any,
  showNoticeCancleTop: any,
  showNoticeContent: any,
  history: History,
  showBrowseRecord: any,
  showBrowseUnRecord: any,
  detailLoading: boolean,
  browseNoticeLoading: boolean,
  noticeRecord: any,
  noticeUnRecord: any,
  match?: any;
  columnCurrentInfo: Function;
  showMyFollowsList: Function;
  queryNoticeInfo: Function;
  queryDraftInfo: Function;
}

const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    detailLoading: state.loading.effects[`${NAMESPACE}/queryNoticeContent`],
    browseNoticeLoading: state.loading.effects[`${NAMESPACE}/queryBrowseRecord`],
  }
};
const mapDispatchToProps = (dispatch: any) => {
  return {
    /**
     *  展示对应公告的内容
     */
    showNoticeContent: (noticeId: any) => {
      dispatch({
        type: `${NAMESPACE}/queryNoticeContent`,
        payload: { noticeId }
      });
    },
    /**
     * 删除公告
     */
    deleteNoticeInfo: (value: any, jumpPage: any) => {
      dispatch({
        type: `${NAMESPACE}/removeNoticeInfo`,
        payload: {
          value,
          jumpPage
        }
      })
    },
    /**
     * 关注公告
     */
    followNoticeInfo: (value: any, reload: any) => {
      dispatch({
        type: `${NAMESPACE}/followNoticeInfo`,
        payload: {
          value,
          reload
        }
      })
    },
    /** 取消关注 */
    enFollowNoticeInfo: (value: any, reload: any) => {
      dispatch({
        type: `${NAMESPACE}/enFollowNoticeInfo`,
        payload: {
          value,
          reload
        }
      })
    },
    /** 置顶 */
    isTopNoticeInfo: (value: any, reload: any) => {
      dispatch({
        type: `${NAMESPACE}/isTopNoticeInfo`,
        payload: {
          value,
          reload
        }
      })
    },
    /**  取消公告置顶 */
    showNoticeCancleTop: (value: any, reload: any) => {
      dispatch({
        type: `${NAMESPACE}/noTopNoticeInfo`,
        payload: {
          value,
          reload
        }
      });
    },
    /** 已读浏览记录 */
    showBrowseRecord: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryBrowseRecord`,
        payload: params
      });
    },
    /** 未读浏览记录 */
    showBrowseUnRecord: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryBrowseUnRecord`,
        payload: params
      });
    },
    /** 展示具体栏目对应公告列表 用于刷*/
    columnCurrentInfo: (value: any) => {
      dispatch({
        type: `${NAMESPACE}/querycolumnDetailInfo`,
        payload: value
      })
    },
    /** 我的关注 用于刷新 */
    showMyFollowsList: (value: any) => {
      dispatch({
        type: `${NAMESPACE}/queryMyFollowsList`,
        payload: value
      });
    },
    /** 全部公告 用于刷新 */
    queryNoticeInfo: (payload: any) => {
      dispatch({
        type: `${NAMESPACE}/queryNoticeInfo`,
        payload
      });
    }
  }
}
@connect(mapStateToProps, mapDispatchToProps)
export default class Details extends React.Component<ContentProps, any>{
  state = ({
    // is_follow: this.props.noticeContent && this.props.noticeContent.notice && this.props.noticeContent.is_follow === 1 ? "取消关注" : "关注",
    // is_top: this.props.noticeContent && this.props.noticeContent.notice && this.props.noticeContent.notice.is_top === 1 ? "取消置顶" : "置顶",
    browseVisition: false,
    previewVisible: false,
    followLoading: false,
    topLoading: false,
    noticeId: this.props.match.params.noticeId
  })
  /**
   * 删除公告
   */
  deleteNotice = (value: any) => {
    this.props.deleteNoticeInfo(value, () => {
      this.refreshList();
      this.jumpPage();
    });
  }
  /**
   * 关注公告
   */
  followNotice = (isFollow: number) => {
    this.setState({
      followLoading: true
    })
    if (isFollow === 0) {

      this.props.followNoticeInfo(this.state.noticeId, () => {
        this.props.showNoticeContent(this.state.noticeId);
        this.showNoticeOperateInfo('关注成功');
        this.refreshList();
        this.setState({
          followLoading: false
        })
      });
    } else {

      this.props.enFollowNoticeInfo(this.state.noticeId, () => {
        this.props.showNoticeContent(this.state.noticeId);
        this.showNoticeOperateInfo('取消关注成功');
        this.refreshList();
        this.setState({
          followLoading: false
        })
        this.setState({
          followLoading: false
        })
      });
    }

  }
  showNoticeOperateInfo = (state: string) => {
    message.success(state);
  }

  /** 置顶公告 */
  isTopNotice = (isTop: number) => {
    this.setState({
      topLoading: true
    })
    if (isTop === 0) {

      this.props.isTopNoticeInfo(this.state.noticeId, () => {
        this.props.showNoticeContent(this.state.noticeId);
        this.showNoticeOperateInfo('置顶成功');
        this.setState({
          followLoading: false
        })
        this.setState({
          topLoading: false
        })
        this.refreshList();

      });
    } else {

      this.props.showNoticeCancleTop(this.state.noticeId, () => {
        this.props.showNoticeContent(this.state.noticeId);
        this.showNoticeOperateInfo('取消置顶成功');
        this.setState({
          followLoading: false
        })
        this.setState({
          topLoading: false
        })
        this.refreshList();

      });
    }

  }
  /**  刷新对应的列表信息 */
  refreshList = () => {
    const locationState = this.props.location.state;
    if (locationState) {
      if (locationState.type === 'publishNotice') {
        if (locationState.currentActiveColumn === 'column') {
          this.props.columnCurrentInfo(locationState.currentColumnInfo)
        } else if (locationState.currentActiveColumn === 'follow') {
          this.props.showMyFollowsList(locationState.currentColumnInfo)
        } else {
          // 全部公告
          this.props.queryNoticeInfo(locationState.currentColumnInfo);
        }
      } else { // 草稿箱
        this.props.queryDraftInfo({
          now_page: 1,
          page_size: 10,
        })
      }
    }
  }
  /** 撤销公告 */
  cancelNotice = (id: number) => {
    (async () => {
      const result = await req('/api/c_notice_cancelNotice', {
        method: 'POST',
        body: { notice_id: id }
      })
      if (result.status === 'success') {
        message.success('撤销成功');
        this.refreshList();
        this.jumpPage();
      }
    })()
  }
  componentDidMount() {
    this.props.showNoticeContent(this.state.noticeId);
  }
  /**
   * 返回notice初始页面
   */
  jumpPage = () => {
    this.props.history.push('/work/notice');
  }
  /**
   * 浏览人数
   */
  browseHistory = () => {
    this.setState({
      browseVisition: true,
    })
    this.browseRecord();
    this.browseUnRecord()
  }
  /**
   * 已读记录
   */
  browseRecord = () => {
    this.props.showBrowseRecord({
      'notice_id': this.state.noticeId,
      'now_page': 1
    })
  }
  /**
   * 未读记录
   */
  browseUnRecord = () => {
    this.props.showBrowseUnRecord({
      'notice_id': this.state.noticeId,
      'now_page': 1
    })
  }
  browseCancel = () => {
    this.setState({
      browseVisition: false,
    })
  }

  render() {

    const { noticeContent, detailLoading, noticeRecord, noticeUnRecord, browseNoticeLoading } = this.props;
    const { browseVisition, followLoading, topLoading } = this.state;
    const locationType = this.props.location.state ? this.props.location.state.type : 'publishNotice';
    const browseModalProps = {
      visible: browseVisition,
      onCancel: this.browseCancel,
      loading: browseNoticeLoading,
      browseRecord: noticeRecord,
      browseUnRecord: noticeUnRecord,
    }
    return (
      <Layout style={{ height: 'calc(100vh - 97px)', overflowY: 'auto', background: '#fff' }}>
        <Spin spinning={detailLoading} delay={300}>
          {
            (() => {
              if (noticeContent) {
                const { notice } = noticeContent;
                const { is_follow, is_top } = notice;
                return (
                  <div className='white notice-content'>
                    <Row type="flex" style={{ padding: '16px 0', borderBottom: '1px solid #D0D0D0' }}>
                      <div style={{ margin: 'auto', width: '1000px' }}>
                        <Col span={6}>
                          <Link to="/work/notice"><span style={{ lineHeight: '32px' }} className="goback"> <Icon type="arrow-left" />返回</span></Link>
                        </Col>
                        <Col span={18} style={{ textAlign: 'right' }}>
                          {
                            locationType !== 'draftNotice' ? (// 草稿箱进入的编辑显示的按钮
                              <span>
                                <Button loading={followLoading} className="btnOperate" onClick={() => this.followNotice(is_follow)}>{is_follow === 1 ? "取消关注" : "关注"}</Button>
                                <Button loading={topLoading} className="btnOperate" onClick={() => this.isTopNotice(is_top)}>{is_top === 1 ? "取消置顶" : "置顶"}</Button>
                              </span>
                            ) : ''
                          }
                          <Link to={
                            {
                              pathname: "/work/notice/create",
                              state: { detailInfo: noticeContent, type: locationType, id: this.state.noticeId }
                              // 区分是否是通过草稿箱进入
                            }
                          }>
                            <Button className="btnOperate">编辑</Button>
                          </Link>
                          <Popconfirm placement="bottom" title="确定要删除此公告？" onConfirm={() => this.deleteNotice(this.state.noticeId)}>
                            <Button className="btnOperate">删除</Button>
                          </Popconfirm>
                          {/* <Popconfirm placement="bottom" title="确定要撤销此公告？" onConfirm={() => this.cancelNotice(this.state.noticeId)}>
                            <Button className="btnOperate">撤销</Button>
                          </Popconfirm> */}
                        </Col>
                      </div>
                    </Row>
                    <Row >
                      <Col>
                        <Contents detailInfo={noticeContent} browseHistory={this.browseHistory} style={{ margin: 'auto', width: '1000px' }} />
                      </Col>
                    </Row>
                  </div>
                )
              } else {
                return null;
              }
            })()
          }
        </Spin>
        <BrowseModal {...browseModalProps} />
      </Layout>
    );
  }
}
