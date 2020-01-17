import * as React from 'react';
import { Layout, Dropdown, Menu, Icon } from 'antd';
import { connect } from 'dva';
import { Link } from 'react-router-dom';
import classnames from 'classnames';
import { dropByCacheKey, getCachingKeys } from 'react-router-cache-route';
import { History, Location } from 'history';
import './work.scss';

// url 映射
const Urlmap = {
  "notice": ['/work/notice', '公告'],
  "review": ['/work/review', '评审通'],
  "approval": ['/work/approval', '审批'],
  "assist": ['/work/assist', '协助'],
  "communication": ['/work/communication', '沟通']
}

const NAMESPACE = 'Workbench'; // dva model 命名空间
const USERINFO = 'UserInfo'; // dva model 命名空间
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    permission: state[USERINFO].permission
  }
};
const mapDispatchToProps = (dispatch: any) => {
  return {
    showNavigation: (nav: any, k: number) => {
      dispatch({
        type: `${NAMESPACE}/setNavigation`,
        payload: { nav, k }
      });
    },
    showTabActive: (currentTab: string) => {
      dispatch({
        type: `${NAMESPACE}/setTabActive`,
        payload: currentTab
      });
    },
    queryCompanys: () => {
      dispatch({
        type: `${NAMESPACE}/queryCompanys`
      });
    },
    changeCompany: (id: string | number, cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/changeCompany`,
        payload: { id, cb }
      });
    },
    queryUserPermission: () => {
      dispatch({
        type: `${USERINFO}/queryUserPermission`
      });
    }
  }
}

interface WorkProps {
  location: Location;
  history: History;
  /** 获取用户当前加入的所有公司列表 */
  queryCompanys: Function;
  companys: any;
  /** 当前处于激活状态的 tab 标签 */
  showTabActive: Function;
  currentTab: any;
  /** 是否需要跳转 */
  needJump: boolean;
  /** 当前导航 */
  showNavigation: Function;
  navigation: any[];
  /** 切换公司 */
  changeCompany: Function;
  /** 重新获取用户权限 */
  queryUserPermission: Function;
}

@connect(mapStateToProps, mapDispatchToProps)
export default class Work extends React.Component<WorkProps, any>{

  state = {
    canJump: false,
    prevTab: '',
    needClear: false,
    icon: 'caret-down'
  }

  constructor(props: any) {
    super(props);
    // props.cacheLifecycles.didCache(this.componentDidCache.bind(this));
    props.cacheLifecycles.didRecover(this.componentDidRecover.bind(this));
  }

  componentDidMount() {
    const pathname = this.props.location.pathname;
    this.props.queryCompanys();


    // 如果是从其他页面跳转到工作模块的内部某个页面
    if (this.props.location.state) {
      const navigation = Urlmap[pathname.split('/')[2].split('-')[0]];
      const currentTab = Urlmap[pathname.split('/')[2].split('-')[0]][0];
      this.props.showTabActive(currentTab);
      this.props.showNavigation(navigation);
    } else {

      // 如果直接点击顶部导航 `工作` 进入，跳转到之前打开的标签页，其他情况不用处理
      if (pathname === '/work' && this.props.currentTab !== '/work') {
        this.props.history.replace(this.props.currentTab);
      }
    }
  }

  componentDidUpdate(prevProps: any) {

    if (this.props.needJump) {
      if (prevProps.currentTab !== this.props.currentTab) {
        this.props.history.replace(this.props.currentTab);
      }
      // 判断上次是工作模块下的其他页面,这次是工作台然后再跳转
      else if (prevProps.location.pathname !== '/work' && this.props.location.pathname === '/work') {
        if (prevProps.currentTab !== this.props.currentTab) {
          this.props.history.replace(this.props.currentTab);
        }
      }
    }
    if (this.state.needClear) {
      for (const cacheKey of getCachingKeys()) {

        cacheKey.includes(this.state.prevTab) && dropByCacheKey(cacheKey)
      }
      this.setState({
        needClear: false
      })
    }

  }
  componentDidRecover() {
    this.props.history.replace(this.props.currentTab)
  }
  /**
   * 设置当前处于激活状态的tab
   * @param currentTab 
   */
  showTabActive(currentTab: string) {
    this.props.showTabActive(currentTab);
  }
  /**
   * 设置 tab 导航
   * @param nav 当前 tab 信息 ['path', 'name']
   * @param k 当前点击的 tab 索引
   */
  showNavigation(nav: string[], k: number) {

    this.props.showNavigation(nav, k);
    this.setState({
      prevTab: nav[0],
      needClear: true
    })
  }
  /** 切换公司 */
  companyChange = ({ key }: any) => {
    this.props.changeCompany(key, () => {
      // 清除所有缓存页面
      for (const cacheKey of getCachingKeys()) {
        dropByCacheKey(cacheKey)
      }
      this.props.queryUserPermission();
    })
  }
  /** 更换图标 */
  changeIcon(visible: boolean) {
    const nextIcon = visible ? 'caret-up' : 'caret-down';
    this.setState({
      icon: nextIcon
    })
  }

  render() {

    const { navigation, currentTab, companys } = this.props;
    const { icon } = this.state;

    // 公司列表
    const enterpriseList = (
      <Menu onClick={this.companyChange}>
        {
          companys && companys.relate_companys.map(({ id, name }: any) => (
            <Menu.Item key={id}>{name}</Menu.Item>
          ))
        }
        <Menu.Divider />
        <Menu.Item key={0}>私人工作空间</Menu.Item>
      </Menu>
    );
    return (
      <Layout>
        <div className="workbench-header clearfix">
          <div className={classnames({ active: '/work' === currentTab })}><Link to="/work" onClick={() => this.showTabActive('/work')} style={{ display: 'inline-block', padding: '0 20px' }}>工作台</Link></div>
          {
            navigation.map((tab: string[], k: number) => (
              <div key={tab[0]} className={classnames({ active: tab[0] === currentTab })}>
                <Link onClick={() => this.showTabActive(tab[0])} to={tab[0]} style={{ display: 'inline-block', padding: '0 30px' }}>
                  {tab[1]}
                </Link>
                <Icon onClick={() => this.showNavigation(tab, k)} type="close" style={{ padding: '0 6px', cursor: 'pointer' }} />
              </div>
            ))
          }
          <Dropdown
            overlay={enterpriseList}
            trigger={['click']}
            className="company"
            placement="bottomRight"
            onVisibleChange={(visible: boolean) => this.changeIcon(visible)}
          >
            <span className="ant-dropdown-link" style={{ padding: '0 20px', cursor: 'pointer' }}>
              <span style={{ paddingRight: '8px' }}>{companys && companys.current_company.name ? companys.current_company.name : '私人工作空间'}</span>
              <Icon type={icon} />
            </span>
          </Dropdown>
        </div>
        {this.props.children}
      </Layout>
    )
  }
}
