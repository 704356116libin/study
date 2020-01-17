import * as React from 'react';
import { Layout, Menu, Icon } from 'antd';
import { Link } from 'react-router-dom';
import './approval.scss';
import { connect } from 'dva';
const { Sider } = Layout;

const USERINFO = 'UserInfo';

const mapStateToProps = (state: any) => {
  return {
    permission: state[USERINFO].permission,
  }
};
@connect(mapStateToProps)
export default class MyApproval extends React.Component<any, any> {
  state = {
    /** 根据当前页面url设置左侧导航默认选中状态 */
    url: window.location.pathname.split('/')[3],
    leavePage: this.props.location.pathname,
    leaveState: this.props.location.state
  }

  constructor(props: any, ...args: any) {
    super(props, ...args);
    props.cacheLifecycles.didCache(this.componentDidCache.bind(this));
    props.cacheLifecycles.didRecover(this.componentDidRecover.bind(this));
  }
  // 保存上次离开时的页面 和 状态
  componentDidCache() {
    this.setState({
      leavePage: this.props.location.pathname,
      leaveState: this.props.location.state
    })
  }
  // 恢复上次离开时的页面 和 状态
  componentDidRecover() {
    this.props.history.replace({
      pathname: this.state.leavePage,
      state: this.state.leaveState
    });
  }
  componentDidUpdate(prevProps: any) {
    // 更新左侧导航激活状态
    if (prevProps.location.pathname !== this.props.location.pathname) {
      this.setState({
        url: this.props.location.pathname.split('/')[3]
      })
    }
  }
  render() {

    const { permission } = this.props;
    /** 审批管理权 */
    const PERMISSION_APPROVAL_MANAGEMENT = permission && permission.includes('c_approval_manage_per');

    return (
      <Layout id="work-approval" style={{ height: 'calc(100vh - 97px)' }}>
        <Sider width="210" className="box" style={{ background: '#fff', overflowY: 'auto', overflowX: 'hidden', borderRight: '1px solid #e8e8e8', display: (this.props.location.pathname === '/work/approval/template' || this.props.location.pathname === '/work/approval/newCreate') ? 'none' : 'block' }}>
          <div className="text-center">
            <Link to='/work/approval/create' className="creatApproval">新建审批</Link>
          </div>
          <Menu
            mode="inline"
            selectedKeys={[this.state.url]}
            style={{ border: 'none' }}
          >
            <Menu.Item key="pending" className="">
              <Link to="/work/approval/pending">
                <Icon type="folder-open" />
                <span>待我审批</span>
              </Link>
            </Menu.Item>
            <Menu.Item key="approved" className="">
              <Link to="/work/approval/approved">
                <Icon type="folder-open" />
                <span>我已审批</span>
              </Link>
            </Menu.Item>
            <Menu.Item key="initiate" className="">
              <Link to='/work/approval/initiate'>
                <Icon type="folder-open" />
                <span>我发起的</span>
              </Link>
            </Menu.Item>
            <Menu.Item key="ccApproval" className="">
              <Link to='/work/approval/ccApproval'>
                <Icon type="folder-open" />
                <span>抄送给我的</span>
              </Link>
            </Menu.Item>
            <Menu.Item key="archive" className="">
              <Link to='/work/approval/archive'>
                <Icon type="folder-open" />
                <span>已归档</span>
              </Link>
            </Menu.Item>
            {
              PERMISSION_APPROVAL_MANAGEMENT ? (
                <Menu.Item key="management" className="">
                  <Link to='/work/approval/management' >
                    <Icon type="setting" />
                    <span>审批管理</span>
                  </Link>
                </Menu.Item>
              ) : null
            }
          </Menu>
        </Sider>
        {this.props.children}
      </Layout>
    )
  }
}