import * as React from 'react';
import { Layout, Menu, Icon } from 'antd';
import { Link } from 'react-router-dom';
import './review.scss';
import { SelectParam } from 'antd/lib/menu';
import { connect } from 'dva';

const { Sider } = Layout;
const SubMenu = Menu.SubMenu;
const USERINFO = 'UserInfo';

const mapStateToProps = (state: any) => {
  return {
    permission: state[USERINFO].permission,
  }
};
@connect(mapStateToProps)
export default class ReviewSider extends React.Component<any>{

  state = {
    currentUrl: this.props.location.pathname.split('/')[3] || 'templates',
    leavePage: this.props.location.pathname,
    leaveState: this.props.location.state
  }

  constructor(props: any, ...args: any) {
    super(props, ...args);
    props.cacheLifecycles.didCache(this.componentDidCache.bind(this));
    props.cacheLifecycles.didRecover(this.componentDidRecover.bind(this));
  }

  componentDidMount() {
    if (this.props.location.pathname === '/work/review') {
      this.props.history.replace('/work/review/templates');
    }
  }

  componentDidUpdate(prevProps: any, prevState: any) {
    if (prevState.currentUrl !== this.props.location.pathname.split('/')[3]) {
      this.setState({
        currentUrl: this.props.location.pathname.split('/')[3],
      })
    }
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

  handleSiderSelect = ({ key }: SelectParam) => {
    this.setState({
      currentUrl: key
    })
  }

  render() {

    const { currentUrl } = this.state;
    const { permission } = this.props;
    const display = this.props.location.pathname === '/work/review/initiate' ||
      this.props.location.pathname === '/work/review/createTemplate' ||
      this.props.location.pathname === '/work/review/createProcess' ||
      this.props.location.pathname === '/work/review/createReport' ||
      /^\/work\/review\/detail\/\w+/.test(this.props.location.pathname)
      ? 'none' : 'block'

    /** 评审管理权 */
    const PERMISSION_REVIEW_MANAGEMENT = permission && permission.includes('c_pst_manage_per');

    return (
      <Layout className="work-review">
        <Sider width="210" className="review-sider" style={{ display }}>
          <Menu
            mode="inline"
            selectedKeys={[currentUrl]}
            defaultOpenKeys={['templatemgt', 'processmgt', 'exportmgt', 'othersmgt', 'customizesmgt', 'labelsmgt'].includes(currentUrl) ? ['reviewmgt'] : []}
            style={{ borderRightColor: 'transparent' }}
            theme="light"
            onSelect={this.handleSiderSelect}
          >
            <Menu.Item key="templates">
              <Link to="/work/review/templates">
                <Icon type="edit" />
                <span className="nav-text">发起项目</span>
              </Link>
            </Menu.Item>
            <Menu.Item key="all">
              <Link to="/work/review/all">
                <Icon type="bars" />
                <span className="nav-text">全部评审</span>
              </Link>
            </Menu.Item>
            <Menu.Item key="reviewing">
              <Link to="/work/review/reviewing">
                <Icon type="hourglass" />
                <span className="nav-text">评审中的</span>
              </Link>
            </Menu.Item>
            <Menu.Item key="pendingReceive">
              <Link to="/work/review/pendingReceive">
                <Icon type="hourglass" />
                <span className="nav-text">待接收的</span>
              </Link>
            </Menu.Item>
            <Menu.Item key="pendingAssign">
              <Link to="/work/review/pendingAssign">
                <Icon type="sync" />
                <span className="nav-text">待指派的</span>
              </Link>
            </Menu.Item>
            <Menu.Item key="pendingApproval">
              <Link to="/work/review/pendingApproval">
                <Icon type="inbox" />
                <span className="nav-text">待审核的</span>
              </Link>
            </Menu.Item>
            <Menu.Item key="reviewed">
              <Link to="/work/review/reviewed">
                <Icon type="coffee" />
                <span className="nav-text">评审完成的</span>
              </Link>
            </Menu.Item>
            <Menu.Item key="archived">
              <Link to="/work/review/archived">
                <Icon type="copy" />
                <span className="nav-text">已归档</span>
              </Link>
            </Menu.Item>
            <Menu.Item key="cancled">
              <Link to="/work/review/cancled">
                <Icon type="copy" />
                <span className="nav-text">已作废</span>
              </Link>
            </Menu.Item>

            {/* <Menu.Item key="recyclebin">
              <Link to="/work/review/recyclebin">
                <Icon type="delete" />
                <span className="nav-text">回收站</span>
              </Link>
            </Menu.Item> */}
            <Menu.Item key="dataanalysis">
              <Link to="/work/review/dataanalysis">
                <Icon type="pie-chart" />
                <span className="nav-text">数据展示</span>
              </Link>
            </Menu.Item>
            {
              PERMISSION_REVIEW_MANAGEMENT ? (
                <SubMenu
                  key="reviewmgt"
                  title={
                    <span>
                      <Icon type="setting" />
                      <span className="nav-text">评审管理</span>
                    </span>
                  }
                >
                  <Menu.Item key="templatemgt">
                    <Link to="/work/review/templatemgt">
                      <Icon type="form" />
                      <span className="nav-text">模板管理</span>
                    </Link>
                  </Menu.Item>
                  <Menu.Item key="processmgt">
                    <Link to="/work/review/processmgt">
                      <Icon type="retweet" />
                      <span className="nav-text">流程设置</span>
                    </Link>
                  </Menu.Item>
                  <Menu.Item key="exportmgt">
                    <Link to="/work/review/exportmgt">
                      <Icon type="export" />
                      <span className="nav-text">导出设置</span>
                    </Link>
                  </Menu.Item>
                  {/* <Menu.Item key="othersmgt">
                    <Link to="/work/review/othersmgt">
                      <Icon type="build" />
                      <span className="nav-text">其他设置</span>
                    </Link>
                  </Menu.Item> */}
                  <Menu.Item key="customizesmgt">
                    <Link to="/work/review/customizesmgt">
                      <Icon type="build" />
                      <span className="nav-text">业务科室</span>
                    </Link>
                  </Menu.Item>
                  <Menu.Item key="labelsmgt">
                    <Link to="/work/review/labelsmgt">
                      <Icon type="build" />
                      <span className="nav-text">标签管理</span>
                    </Link>
                  </Menu.Item>
                </SubMenu>
              ) : null
            }
          </Menu>
        </Sider>
        {this.props.children}
      </Layout>
    )
  }
}
