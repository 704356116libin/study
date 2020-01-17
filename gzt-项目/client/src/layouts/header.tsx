import React from 'react';
import { Layout, Menu, Dropdown, Icon, Avatar, Badge, notification } from 'antd';
import { Link } from 'react-router-dom';
import { connect } from 'dva';
import Debounce from 'lodash-decorators/debounce';
import { get } from '../utils/request';
import PersonalCardModal from './personalCard';
import './header.scss'

const { Header } = Layout;

const NOTIFICATION = 'Notification';
const USERINFO = 'UserInfo';

const mapStateToProps = (state: any) => {
  return {
    ...state[NOTIFICATION],
    userInfo: state[USERINFO].userInfo
  }
};
const mapDispatchToProps = (dispatch: any) => {
  return {
    showDynamicsCount: () => {
      dispatch({
        type: `${NOTIFICATION}/queryDynamicsCount`
      })
    },
    setDynamicsInfo: (dynamicsInfo: string) => {
      dispatch({
        type: `${NOTIFICATION}/setDynamicsInfo`,
        payload: dynamicsInfo
      })
    },
    setDynamicsCount: (unreadCount: number, action: string) => {
      dispatch({
        type: `${NOTIFICATION}/setDynamicsCount`,
        payload: {
          unreadCount,
          action
        }
      })
    },
    /** 单独获取未读动态消息数量 */
    queryDynamicsCount: () => {
      dispatch({
        type: `${NOTIFICATION}/queryDynamicsCount`
      });
    },
    queryUserInfo: (cb: Function) => {
      dispatch({
        type: `${USERINFO}/queryUserInfo`,
        payload: { cb }
      })
    },
    queryUserPermission: (cb: Function) => {
      dispatch({
        type: `${USERINFO}/queryUserPermission`,
        payload: { cb }
      })
    }
  }
}
/** 退出登录 */
function logout() {
  window.localStorage.removeItem('access_token');
  window.localStorage.removeItem('refresh_token');
  window.location.href = '/login';
}

/** 设置菜单 */
const settings = (
  <Menu>
    <Menu.Item key="profile">
      <Link to="/settings/profile">个人设置</Link>
    </Menu.Item>
    {/* <Menu.Item key="1">
      <Link to="/systemSettings">系统设置</Link>
    </Menu.Item> */}
    <Menu.Item key="password">
      <a href="/reset" target="_blank">修改密码</a>
    </Menu.Item>
    <Menu.Divider />
    <Menu.Item key="companyRegister">
      <Link to="/companyRegister">创建企业</Link>
    </Menu.Item>
    <Menu.Divider />
    <Menu.Item key="website">
      <a href="https://www.pingshentong.com">评审通官网</a>
    </Menu.Item>
    <Menu.Item key="help">
      <Link to="/">帮助</Link>
    </Menu.Item>
    <Menu.Divider />
    <Menu.Item key="logout" onClick={logout}>
      <span>退出登录</span>
    </Menu.Item>
  </Menu>
);

@connect(mapStateToProps, mapDispatchToProps)
export default class AppHeader extends React.Component<any>{

  state = {
    url: window.location.pathname === '/' ? "dynamic" : window.location.pathname.split('/')[1],
    icon: 'down-circle',
    searchValue: '',
    isFocus: false,
    businessCardVisible: false,
    cardInfo: null
  }
  async componentDidMount() {
    // 查询当前未读消息数量
    this.props.queryDynamicsCount();

    // Create WebSocket connection.
    let socket: WebSocket | false;

    //获取用户基础信息
    this.props.queryUserInfo((data: any) => {
      if (process.env.NODE_ENV === 'development') {// local
        try {
          socket = new WebSocket(`ws://pst.gzt.test:9501/?user_id=${data.id}`);
        } catch (e) {
          socket = false;
          alert('请配置本地swoole环境');
        }
      } else {// online
        socket = new WebSocket(`wss://pst.pingshentong.com:9501/wss?user_id=${data.id}`);
      }

      if (socket) {// 如果websocket连接建立成功
        // Connection opened
        socket.addEventListener('open', (event) => {

          socket && socket.send('Hello Server!');
        });

        // Listen for messages
        socket.addEventListener('message', (event) => {

          const notificationInfo = JSON.parse(event.data);

          console.log('推送消息：', notificationInfo);

          if (notificationInfo.type === 'dynamic_single') {// 动态
            notification.open({
              message: notificationInfo.data.data.title,
              description: notificationInfo.data.data.content
            });
            this.props.setDynamicsInfo({ data: notificationInfo.data, action: 'news' });
          } else if (notificationInfo.type === 'dynamic_refresh') {
            // 更新当前未读消息数量
            this.props.queryDynamicsCount();
          }
        });
      }
    })
    // 获取当前用户所拥有的权限    
    this.props.queryUserPermission();
  }

  emitEmpty = () => {
    this.setState({ searchValue: '' });
    this.setState({ isFocus: false });
  }
  onFocus = () => {
    this.setState({ isFocus: true });
  }
  onChangeSearchValue = (e: any) => {
    this.setState({ searchValue: e.target.value });
    this.debounceSearchValue();

  }
  showBusinessCard = async () => {
    this.setState({
      businessCardVisible: true
    })
    const result = await get('/api/u_get_card_info');
    if (result.status === 'success') {
      this.setState({
        cardInfo: result.data
      })
    }

  }
  hiddenBusinessCard = () => {
    this.setState({
      businessCardVisible: false
    })
  }
  @Debounce(500, {
    leading: true,
    trailing: false,
  })
  debounceSearchValue() {
    console.log(111);
    // 像后台发送search请求
  }

  render() {
    const { userInfo } = this.props;
    const { isFocus, cardInfo } = this.state;
    // const suffix = isFocus ? <Icon type="close-circle" theme="filled" onClick={this.emitEmpty} /> : null;
    // const prefix = isFocus ? null : <Icon type="search" />;
    const display = isFocus ? 'none' : 'inline-block';
    // const displayBlock = isFocus ? 'block' : 'none';
    // const width = isFocus ? '260px' : '100px';

    return (
      <Header className="header" >
        <div style={{ zIndex: 199, position: 'relative', float: 'left', padding: '0 10px', width: '280px' }}>
          <div style={{ marginRight: '15px', display }}>
            <span style={{ padding: '0 10px', color: '#333', cursor: 'pointer' }} onClick={this.showBusinessCard}>
              <Avatar size={42} src={userInfo && userInfo.avatar.status === 'success' && userInfo.avatar.avatar.oss_path} style={{ color: '#f56a00', backgroundColor: '#fde3cf' }}>
                {userInfo && userInfo.name}
              </Avatar>
            </span>
          </div>
          {/* <div className={isFocus ? 'focus' : ''} style={{ display: 'inline-block', width }}>
            <Input
              placeholder=""
              prefix={prefix}
              suffix={suffix}
              value={searchValue}
              onChange={this.onChangeSearchValue}
              onFocus={this.onFocus}
              size="default"
            />
          </div>
          <Tabs tabPosition="top" className="searchTab" defaultActiveKey="1" style={{ display: displayBlock }}>
            <TabPane tab="全部" key="1">搜索动态消息、功能、联系人</TabPane>
            <TabPane tab="动态" key="2">搜索动态消息</TabPane>
            <TabPane tab="功能" key="3">搜索功能</TabPane>
            <TabPane tab="联系人" key="4">搜索联系人</TabPane>
          </Tabs> */}
        </div>
        <Menu
          mode="horizontal"
          defaultSelectedKeys={[this.state.url]}
          style={{ float: 'left', marginLeft: 'calc(50% - 387px)', lineHeight: '60px', color: '#333', textAlign: 'center' }}
          className="header-nav"
        >
          <Menu.Item key="dynamic" >
            <Link style={{ display: 'block' }} to="/dynamic">
              <Badge count={this.props.unreadCount}>
                <span style={{ fontSize: '17px' }}>动态</span>
              </Badge>
            </Link>
          </Menu.Item>
          <Menu.Item key="work">
            <Link to="/work">工作</Link>
          </Menu.Item>
          <Menu.Item key="doc">
            <Link to="/doc">文档</Link>
          </Menu.Item>
          <Menu.Item key="contact">
            <Link to="/contact">联系人</Link>
          </Menu.Item>
        </Menu>
        <div style={{ float: 'right', paddingRight: '16px', textAlign: 'right' }}>
          <Dropdown
            overlay={settings}
            overlayStyle={{ padding: '0 10px' }}
            trigger={['click']}
          >
            <span style={{ padding: '0 10px', color: '#fff', cursor: 'pointer' }}>
              <Icon type="appstore" theme="filled" style={{ fontSize: '16px' }} />
            </span>
          </Dropdown>
        </div>
        <PersonalCardModal
          visible={this.state.businessCardVisible}
          onCancel={this.hiddenBusinessCard}
          dataSource={cardInfo}
        />
      </Header>
    );
  }
}