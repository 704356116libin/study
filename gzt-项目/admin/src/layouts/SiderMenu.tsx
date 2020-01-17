import React, { useEffect, useContext, useState } from 'react';
import { Layout, Menu, Icon } from 'antd';
import { Link } from 'react-router-dom';
import { connect } from 'dva';
import { SelectParam } from 'antd/lib/menu';
import { Dispatch } from 'redux';
import { myContext } from '../layouts/MyProvider';
import { get } from '../utils/request';
import './index.scss';

const { Sider } = Layout;
const SubMenu = Menu.SubMenu;
const NAMESPACE = 'Basis';

const mapDispatchToProps = (dispatch: Dispatch<any>) => {
  return {
    queryUserInfo() {
      dispatch({
        type: `${NAMESPACE}/queryUserInfo`
      })
    }
  }
}

function SiderMenu(props: any) {
  const { collapsed } = useContext(myContext);
  const [currentUrl, setCurrentUrl] = useState(window.location.pathname.split('/')[2]);
  const [companyName, setCompanyName] = useState('');

  useEffect(() => {
    props.queryUserInfo();
    (async () => {
      const result = await get('/api/management_enterprise_company_data');
      if (result.status === 'success') {
        setCompanyName(result.data.name)
      }
    })()
  }, []);

  /** 处理左侧导航选中状态 */
  function handleSiderSelect({ key }: SelectParam) {
    setCurrentUrl(key)
  }

  return (
    <Sider
      className='sider'
      trigger={null}
      collapsible
      collapsed={collapsed}
      width="245px"
    >
      <Link className="company-name overflow-ellipsis" to="/">
        {companyName}
      </Link>
      <Menu
        theme="dark"
        mode="inline"
        defaultSelectedKeys={['information']}
        selectedKeys={[currentUrl]}
        defaultOpenKeys={['sub1']}
        onSelect={handleSiderSelect}
      >
        <SubMenu key="sub1" title={<span><Icon type="team" /><span>企业认证</span></span>}>
          <Menu.Item key="information">
            <Link to="/information">
              <Icon type="team" />
              <span>企业信息</span>
            </Link>
          </Menu.Item>
          <Menu.Item key="license">
            <Link to="/license">
              <Icon type="cloud-upload" />
              <span>执照上传</span>
            </Link>
          </Menu.Item>
        </SubMenu>
        <Menu.Item key="structure">
          <Link to="/structure">
            <Icon type="deployment-unit" />
            <span className="nav-text">组织结构</span></Link>
        </Menu.Item>
        <Menu.Item key="partner">
          <Link to="/partner">
            <Icon type="deployment-unit" />
            <span className="nav-text">合作企业</span></Link>
        </Menu.Item>
        <Menu.Item key="externalContact">
          <Link to="/externalContact">
            <Icon type="deployment-unit" />
            <span className="nav-text">外部联系人</span></Link>
        </Menu.Item>
        <Menu.Item key="permission">
          <Link to="/permission">
            <Icon type="lock" />
            <span className="nav-text">职务权限</span>
          </Link>
        </Menu.Item>
        <Menu.Item key="sort">
          <Link to="/sort">
            <Icon type="sort-ascending" />
            <span className="nav-text">组织排序</span>
          </Link>
        </Menu.Item>
        <Menu.Item key="buy">
          <Link to="/buy/people">
            <Icon type="sort-ascending" />
            <span className="nav-text">购买</span>
          </Link>
        </Menu.Item>
        {/* <Menu.Item key="setting">
            <Link to="/setting">
              <Icon type="setting" />
              <span className="nav-text">应用设置</span>
            </Link>
          </Menu.Item>
          <Menu.Item key="log">
            <Link to="/log">
              <Icon type="book" />
              <span className="nav-text">操作日志</span>
            </Link>
          </Menu.Item> */}
      </Menu>
    </Sider>
  )
}
export default connect(undefined, mapDispatchToProps)(SiderMenu)