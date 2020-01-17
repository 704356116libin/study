import React, { useState } from 'react';
import { LocaleProvider, Layout } from 'antd';
import SiderMenu from './layouts/SiderMenu';
import Header from './layouts/Header';
import moment from 'moment';
import zh_CN from 'antd/lib/locale-provider/zh_CN';
import MyProvider from './layouts/MyProvider';
moment.locale('zh-cn');

export default function App(props: any) {

  const [collapsed, setCollapsed] = useState(false);

  return (
    <LocaleProvider locale={zh_CN}>
      <MyProvider
        collapsed={collapsed}
        toggleCollapsed={() => setCollapsed(!collapsed)}
      >
        <Layout>
          <SiderMenu />
          <Layout className="ee-main" style={{ paddingLeft: collapsed ? 80 : 245 }}>
            <Header />
            {props.children}
          </Layout>
        </Layout>
      </MyProvider>
    </LocaleProvider>
  )
}
