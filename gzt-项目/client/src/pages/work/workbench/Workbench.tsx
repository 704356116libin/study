/** 工作台 */
import * as React from 'react';
import { Layout, Icon } from 'antd';
import { connect } from 'dva';
import { NavLink } from 'react-router-dom';
import './workbench.scss';

const MyIcon = Icon.createFromIconfontCN({
  scriptUrl: '//at.alicdn.com/t/font_1022436_0d53ht9n6fcp.js', // 在 iconfont.cn 上生成
});

const NAMESPACE = 'Workbench';
const USERINFO = 'UserInfo';
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
        payload: { nav }
      });
    }
  }
}

@connect(mapStateToProps, mapDispatchToProps)
export default class Workbench extends React.Component<any, any> {

  render() {

    const { permission } = this.props;

    /** 企业后台管理员 */
    const PERMISSION_ADMIN = permission && permission.includes('c_super_manage_per');

    return (
      <Layout className="workbench">
        <div className="workbench-wrapper">
          <h3 className="workbench-title">全部应用</h3>
          <div className="workbench-item-wrapper">
            <NavLink className="workbench-item review" to="/work/review" onClick={() => this.props.showNavigation(['/work/review', '评审通'])}>
              <MyIcon className="logo" type="icon-pingshenfang" />
              <span className="title">评审通</span>
            </NavLink>
            <NavLink className="workbench-item notice" to="/work/notice" onClick={() => this.props.showNavigation(['/work/notice', '公告'])}>
              <MyIcon className="logo" type="icon-gonggao" />
              <span className="title">公告</span>
            </NavLink>
            <NavLink className="workbench-item approval" to="/work/approval" onClick={() => this.props.showNavigation(['/work/approval', '审批'])}>
              <MyIcon className="logo" type="icon-shenpi" />
              <span className="title">审批</span>
            </NavLink>
            <NavLink className="workbench-item assist" to="/work/assist" onClick={() => this.props.showNavigation(['/work/assist', '协助'])}>
              <MyIcon className="logo" type="icon-assist" />
              <span className="title">协助</span>
            </NavLink>
            {
              PERMISSION_ADMIN ? (
                <a className="workbench-item useradmins" target="_blank" href="/useradmins" >
                  <MyIcon className="logo" type="icon-xitongguanli" />
                  <span className="title">系统管理</span>
                </a>
              ) : null
            }
          </div>
        </div>
      </Layout>
    )
  }
}