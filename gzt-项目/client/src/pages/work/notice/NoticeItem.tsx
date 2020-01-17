import * as React from 'react';
import { History } from 'history';
import { Col, List, Divider, Icon, Popconfirm, Spin, message } from 'antd';
import { Link } from 'react-router-dom';
import { connect } from 'dva';
import req from '../../../utils/request';
const MyIcon = Icon.createFromIconfontCN({
  scriptUrl: '//at.alicdn.com/t/font_1164981_2pxhfc8fm2x.js'
})
const NAMESPACE = 'Notice'; // dva model 命名空间
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    removeLoading: state.loading.effects[`${NAMESPACE}/removeNoticeInfo`],
  }
}
interface NoticeItemProps {
  className: string;
  dataSource: any,
  operating: any,
  reloadDraft: any,
  isTop: any,
  isFollow: any,
  followNoticeInfo?: any,
  enFollowNoticeInfo?: any,
  showNoticeTop?: any,
  showNoticeCancleTop?: any,
  removerDraftNoticeInfo?: any,
  history: History,
  draftList?: any,
  currentActiveColumn?: any
  currentColumnInfo?: any

}
const mapDispatchToProps = (dispatch: any) => {
  return {
    /**
     *  公告置顶
     */
    showNoticeTop: (value: any, reload: any) => {
      dispatch({
        type: `${NAMESPACE}/isTopNoticeInfo`,
        payload: {
          value,
          reload
        }
      });
    },
    /**
     *  取消公告置顶
     */
    showNoticeCancleTop: (value: any, reload: any) => {
      dispatch({
        type: `${NAMESPACE}/noTopNoticeInfo`,
        payload: {
          value,
          reload
        }
      });
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
    /**
     * 取消关注
     */
    enFollowNoticeInfo: (value: any, reload: any) => {
      dispatch({
        type: `${NAMESPACE}/enFollowNoticeInfo`,
        payload: {
          value,
          reload
        }
      })
    },
    /**
     *  删除草稿箱的公告
     */
    removerDraftNoticeInfo: (value: any, reload: any) => {
      dispatch({
        type: `${NAMESPACE}/removeNoticeInfo`,
        payload: {
          value,
          reload
        }
      });
    }
  }
}

@connect(mapStateToProps, mapDispatchToProps)
export default class NoticeItem extends React.Component<NoticeItemProps>{
  state = {
    is_follow: this.props.dataSource.is_follow === 1 ? "取消关注" : "关注",
    topLoading: false
  }

  toggleAttention = (e: any, isFollow: number) => {
    e.preventDefault();
    e.stopPropagation();
    this.setState({
      topLoading: true
    })
    if (isFollow === 0) {
      this.props.followNoticeInfo(this.props.dataSource.id, () => {
        this.props.isFollow('关注成功', this.props.currentActiveColumn);
        this.setState({
          topLoading: false
        })
      });
    } else {
      this.props.enFollowNoticeInfo(this.props.dataSource.id, () => {
        this.props.isFollow('取消关注成功', this.props.currentActiveColumn);
        this.setState({
          topLoading: false
        })
      });
    }
  }
  /**
   * 公告置顶
   */
  toggleTop = (e: any, isTop: any) => {
    e.preventDefault();
    e.stopPropagation();
    this.setState({
      topLoading: true
    })
    if (isTop === 0) {
      (async () => {
        await this.props.showNoticeTop(this.props.dataSource.id, () => {
          this.props.isTop('置顶成功', this.props.currentActiveColumn);
          this.setState({
            topLoading: false
          })
        });
      })()
    } else {
      this.props.showNoticeCancleTop(this.props.dataSource.id, () => {
        this.props.isTop('取消置顶成功', this.props.currentActiveColumn);
        this.setState({
          topLoading: false
        })
      });
    }
  }
  removeDraftNotice = (e: any, value: any) => {
    e.preventDefault();
    e.stopPropagation();
    this.props.removerDraftNoticeInfo(value, this.props.reloadDraft);
    // 接收父组件传递的方法 ,传递给modal
  };
  /**
   * 发布公告
   */
  editNotice = (e: any) => {
    e.preventDefault();
    e.stopPropagation();
    const { id, } = this.props.dataSource;
    (async () => {
      const result = await req('/api/c_notice_publish', {
        method: 'POST',
        body: {
          notice_id: id,
        }
      })
      if (result.status === 'success') {
        this.props.draftList()
        message.success('发布成功');
      } else {
        message.info('服务器失败，请稍后再试');
      }
    })()
  }
  render() {
    const { className } = this.props;
    const item = this.props.dataSource;
    const operating = this.props.operating;
    const { topLoading } = this.state;

    const hasfollow = item.is_follow === 1;
    const hasTop = item.is_top === 1
    return (
      (() => {// 区分是通过其他栏目或草稿箱进入详情
        if (operating === 0) { // 通过其他栏目
          return (
            <List.Item className="clearfix">
              <Link
                className={className}
                to={
                  {
                    pathname: `/work/notice/details/${item.id}`,
                    state: {
                      type: 'publishNotice',
                      currentActiveColumn: this.props.currentActiveColumn,
                      currentColumnInfo: this.props.currentColumnInfo
                    }
                  }
                }
                style={{ display: 'flex', alignItems: "center" }}
              >
                <Col span={5} style={{ padding: '0 12px' }}>
                  <span >{item.title}</span>
                  {hasfollow && (
                    <MyIcon
                      type="icon-shoucang1"
                      style={{ marginLeft: 10, fontSize: 16 }}
                    />
                  )}
                  {hasTop && (
                    <MyIcon type="icon-zhiding"
                      style={{ marginLeft: 10, fontSize: 16 }}
                    />
                  )}
                  {/* {
                    (() => {
                      if (item.is_follow === 1 || item.is_top === 1) {
                        return (
                          <List.Item>
                            <List.Item.Meta
                              avatar={
                                item.is_follow === 1 ?
                                  <MyIcon
                                    type="icon-shoucang1"
                                    style={{ height: '20px', lineHeight: '20px', marginTop: '6px', fontSize: '18px' }}
                                  />
                                  : null
                              }
                            />
                            <List.Item.Meta
                              avatar={
                                item.is_top === 1 ?
                                  <MyIcon type="icon-zhiding"
                                    style={{ height: '20px', lineHeight: '20px', marginTop: '6px', fontSize: '18px' }}
                                  />
                                  : null
                              }
                            />
                          </List.Item>
                        )
                      } else {
                        return null;
                      }
                    })()
                  } */}
                </Col>
                <Col span={5}>
                  <span>{item.type}</span>
                </Col>
                <Col span={5}>
                  <span>{item.organiser}</span>
                </Col>
                <Col span={5}>
                  <span>{item.created_at}</span>
                </Col>
                <Col span={4}>
                  <Spin spinning={topLoading}>
                    <span className="defaultColor"
                      style={{ display: 'inclineBlock', padding: '10px 10px 10px 0' }}
                      onClick={(e: any) => {
                        this.toggleAttention(e, item.is_follow);
                      }}
                    >
                      {item.is_follow === 1 ? "取消关注" : "关注"}
                    </span>
                    <Divider type="vertical" />

                    <span className="defaultColor"
                      style={{ display: 'inclineBlock', padding: '10px 10px 10px 0' }}
                      onClick={(e: any) => {
                        this.toggleTop(e, item.is_top);
                      }}
                    >
                      {item.is_top === 1 ? "取消置顶" : "置顶"}
                    </span>
                  </Spin>
                </Col>
              </Link>
            </List.Item>
          )
        } else { // 草稿
          return (

            <List.Item className="clearfix">
              <Link
                className={className}
                to={
                  {
                    pathname: `/work/notice/details/${item.id}`,
                    state: { type: 'draftNotice' }
                  }
                }
                style={{ display: 'flex', alignItems: "center" }}
              >
                <Col span={5} style={{ padding: '0 12px' }}>
                  <span >{item.title}</span>
                  {hasfollow && (
                    <MyIcon
                      type="icon-shoucang1"
                      style={{ marginLeft: 10, fontSize: 16 }}
                    />
                  )}
                  {hasTop && (
                    <MyIcon type="icon-zhiding"
                      style={{ marginLeft: 10, fontSize: 16 }}
                    />
                  )}
                  {/* {
                    (() => {
                      if (item.is_follow === 1 || item.is_top === 1) {
                        return (
                          <List.Item>
                            <List.Item.Meta
                              avatar={
                                item.is_follow === 1 ?
                                  <MyIcon
                                    type="icon-shoucang1"
                                    style={{ height: '20px', lineHeight: '20px', marginTop: '6px', fontSize: '18px' }}
                                  />
                                  : null
                              }
                            />
                            <List.Item.Meta
                              avatar={
                                item.is_top === 1 ?
                                  <MyIcon type="icon-zhiding"
                                    style={{ height: '20px', lineHeight: '20px', marginTop: '6px', fontSize: '18px' }}
                                  />
                                  : null
                              }
                            />
                          </List.Item>
                        )
                      } else {
                        return null;
                      }
                    })()
                  } */}
                </Col>
                <Col span={5}>
                  <span>{item.type}</span>
                </Col>
                <Col span={5}>
                  <span>{item.organiser}</span>
                </Col>
                <Col span={5}>
                  <span>{item.created_at}</span>
                </Col>
                <Col span={4}>
                  <div>
                    <span className="defaultColor"
                      onClick={e => {
                        this.editNotice(e);
                      }}
                    >
                      发布
                  </span>
                    <Divider type="vertical" />
                    <Popconfirm title="是否要删除此行？" onConfirm={e => this.removeDraftNotice(e, this.props.dataSource.id)}>
                      <span className="defaultColor">删除</span>
                    </Popconfirm>
                  </div>
                </Col>
              </Link>
            </List.Item >
          )
        }
      })()
    )
  }
}