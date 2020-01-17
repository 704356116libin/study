import * as React from 'react'
import { Avatar, Layout, List, Skeleton, Badge, Icon, Spin } from 'antd'
import Hello from '../../components/hello'
import Workmessage from './workmessage/index'
import { connect } from 'dva';
import handleTime from '../../utils/handleTime';
import classNames from 'classnames';
import './dynamics.scss';

/** 工作动态概览 props */
interface OverviewProps {
  dynamicsInfo: any;
  helloInfo: string[];
  deleteLoading: boolean;
  initLoading: boolean;
  latestDynamic: any;
  showHello: () => void;
  showDynamicsInfo: () => void;
  showDynamicDetail: (params: any) => void;
  currentSelectKey: number;
  setCurrentSelectKey: (type: string, k: number) => void;
  deleteListNode: any;
  updateDynamicsInfo: (data: any) => void;
  unreadCount: number;
  updateDynamicsCount: (unreadCount: number) => void;
  /** 用户基础信息 */
  userInfo: any;
}

const { Sider } = Layout;
const NAMESPACE = 'Workdynamics'; // dva model 命名空间
const NOTIFICATION = 'Notification';
const USERINFO = 'UserInfo';
const mapStateToProps = (state: any) => {
  return {
    ...state[NOTIFICATION],
    ...state[NAMESPACE],
    userInfo: state[USERINFO].userInfo,
    deleteLoading: state.loading.effects[`${NAMESPACE}/deleteListNode`],
    initLoading: state.loading.effects[`${NAMESPACE}/queryDynamicsInfo`],
  }
};

const mapDispatchToProps = (dispatch: any) => {
  return {
    /** 展示欢迎语 */
    showHello: () => {
      dispatch({
        type: `${NAMESPACE}/setHello`,
      });
    },
    /**
     * 展示最新应用动态信息
     * @param appType 应用名称,比如'评审通' 或 '全部动态'
     */
    showDynamicsInfo: () => {
      dispatch({
        type: `${NAMESPACE}/queryDynamicsInfo`
      });
    },
    showDynamicDetail: (payload: any) => {
      dispatch({
        type: `${NAMESPACE}/queryDynamicDetail`,
        payload
      });
    },
    setCurrentSelectKey(type: string, currentSelectKey: number) {
      dispatch({
        type: `${NAMESPACE}/setCurrentSelectKey`,
        payload: { type, currentSelectKey }
      });
    },
    updateDynamicsInfo(payload: any) {
      dispatch({
        type: `${NAMESPACE}/updateDynamicsInfo`,
        payload
      });
    },
    updateDynamicsCount(unreadCount: any) {
      dispatch({
        type: `${NOTIFICATION}/setDynamicsCount`,
        payload: {
          unreadCount
        }
      });
    },
    deleteListNode(payload: any) {
      dispatch({
        type: `${NAMESPACE}/deleteListNode`,
        payload
      });
    }
  }
}

@connect(mapStateToProps, mapDispatchToProps)
export default class Overview extends React.Component<OverviewProps, any>{

  static defaultProps = {
    deleteLoading: false
  }
  state = {
    type: 'Hello',
    currentDeleteKey: -1,
    currentCompany: '',
    currentDynamicPosition: [],
    companyId: undefined,
    title: '',
    needRefresh: false
  }

  componentDidMount() {
    this.props.showDynamicsInfo();
    this.props.showHello();
  }

  componentDidUpdate(prevProps: any, prevState: any) {
    if (!prevState.isReload) {
      return
    }
    if (this.props.latestDynamic) {
      if (this.props.latestDynamic.type === prevState.type) {
        if (prevState.type === 'work_dynamic') {
          if (this.props.latestDynamic.data.company_id === prevState.currentDynamicPosition.company_id) {
            this.setState({
              isReload: false
            })
            this.props.showDynamicDetail({
              type: 'work_dynamic',
              now_page: 1,
              company_id: this.props.latestDynamic.data.company_id
            })
          }
        } else if (prevState.type === '社区福利') {
          // 。。。
        }
      }
    }
  }

  onCurrentDynamic(item: any, k: number) {

    this.setState({
      type: item.type,
      title: item.data.title,
      companyId: item.data.company_id,
      currentDynamicPosition: item.data,
      isReload: true
    })

    this.props.setCurrentSelectKey(item.type, k);
    // 如果未读数为 0 ，说明没有新消息，不用更新数据
    if (item.unread_count === 0) {
      this.setState({
        needRefresh: false
      })
    } else {
      this.props.updateDynamicsCount(this.props.unreadCount - item.unread_count);
      this.props.updateDynamicsInfo({ data: item, action: 'read' });
      this.setState({
        needRefresh: true
      })
    }
  }

  /**
   * 关闭当前动态
   */
  onCloseDynamic = (e: React.MouseEvent, item: any, k: any) => {
    e.stopPropagation();
    this.setState({
      currentDeleteKey: k
    })
    this.props.deleteListNode(item);
  }

  render() {

    const { helloInfo = [], currentSelectKey, deleteLoading, initLoading, userInfo } = this.props;
    const { title, type, companyId, currentDeleteKey, needRefresh } = this.state;

    return (
      <Layout>
        <Sider width="240" theme="light" className="dynamic-sider beautiful-scroll-bar">
          <List
            dataSource={this.props.dynamicsInfo.data}
            size="small"
            renderItem={
              (item: any, k: number) => (
                <Spin spinning={currentDeleteKey === k ? deleteLoading : false} indicator={<Icon type="loading" style={{ fontSize: 14, color: 'red' }} spin />}>
                  <div onClick={() => this.onCurrentDynamic(item, k)} >
                    <List.Item className={classNames("dynamic-list", { active: currentSelectKey === k })} >
                      <Skeleton avatar title={false} paragraph={{ rows: 2 }} loading={initLoading} active>
                        <List.Item.Meta
                          style={{ width: '100%' }}
                          avatar={<Avatar size={42} src="https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png" />}
                          title={
                            <div className="clearfix">
                              <span title={item.data.title} className="pull-left overflow-ellipsis" style={{ width: 'calc( 100% - 42px )', color: '#222' }}>{item.data.title}</span>
                              <span className="pull-right" style={{ paddingLeft: '6px', width: '42px', textAlign: 'right', color: 'rgba(0, 0, 0, 0.45)', fontSize: '12px' }}>{handleTime(item.data.time)}</span>
                            </div>
                          }
                          description={
                            <div className="overflow-ellipsis" title={item.data.content}>
                              {item.data.content}
                            </div>
                          }
                        />
                        <Badge
                          count={currentSelectKey === k ? 0 : item.unread_count}
                          className="my-badge"
                          style={{
                            position: 'absolute',
                            right: '0px',
                            bottom: '10px',
                            height: '16px',
                            minWidth: '16px',
                            lineHeight: '16px'
                          }}
                        />
                        <div className="dynamic-close" onClick={(e) => this.onCloseDynamic(e, item, k)}>
                          <Icon type="close-circle" theme="filled" />
                        </div>
                      </Skeleton>
                    </List.Item>
                  </div>
                </Spin>
              )
            }
          />
        </Sider>
        <Layout className="dynamic-content">
          {
            (() => {
              switch (type) {
                case 'work_dynamic':
                  return <Workmessage needRefresh={needRefresh} title={title} type={type} companyId={companyId} companyname={this.state.currentCompany} />
                case 'web_notice':
                  return <Workmessage needRefresh={needRefresh} title={title} type={type} companyId={companyId} companyname={this.state.currentCompany} />
                case '个人消息':
                  return '这里是个人消息'
                default:
                  return <Hello name={userInfo && userInfo.name} helloInfo={helloInfo} />
              }
            })()
          }
        </Layout>
      </Layout>
    )
  }
}