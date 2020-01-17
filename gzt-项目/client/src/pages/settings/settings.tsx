import React, { useEffect, useState } from 'react';
import { Layout, Menu } from 'antd';
import { Link } from 'react-router-dom';

import './setting.scss'

const { Sider } = Layout;

export default function Settings(props: any) {

  const [selectedKeys, setSelectKeys] = useState(['profile'])

  useEffect(() => {
    setSelectKeys([props.location.pathname.split('/')[2]])
  }, [props.location.pathname])

  return (
    <Layout style={{ height: 'calc(100vh - 61px)' }}>
      <div className="settings">
        <Sider theme="light" width="210" className="settings-sider">
          <Menu
            mode="inline"
            selectedKeys={selectedKeys}
            className="settings-menu"
          >
            <Menu.Item key="profile">
              <Link to="/settings/profile">
                <span className="nav-text">个人设置</span>
              </Link>
            </Menu.Item>
            <Menu.Item key="invitation">
              <Link to="/settings/invitation">
                <span className="nav-text">邀请信息</span>
              </Link>
            </Menu.Item>
          </Menu>
        </Sider>
        {props.children}
      </div>
    </Layout>
  )
}
