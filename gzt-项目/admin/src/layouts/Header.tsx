import React, { useContext } from 'react';
import './index.scss';
import { Layout, Icon } from 'antd';
import { Link } from 'react-router-dom';
import { myContext } from '../layouts/MyProvider';

const { Header } = Layout;

export default function Headers(props: any) {

  const { collapsed, toggleCollapsed } = useContext(myContext);

  return (
    <Header className="white text-right ee-header" style={{ width: `calc(100% - ${collapsed ? 80 : 245}px)` }}>
      <div style={{ float: 'left' }
      } >
        <Icon
          className="trigger"
          type={collapsed ? 'menu-unfold' : 'menu-fold'}
          onClick={toggleCollapsed}
        />
      </ div>
      <Link to="/help" style={{ padding: ' 0 12px', height: '100%' }}><Icon type="question-circle" style={{ padding: ' 0 12px', fontSize: '18px' }} /><span>帮助</span></Link>
      <Link to="/exit" style={{ padding: ' 0 12px', height: '100%' }}><Icon type="logout" style={{ padding: ' 0 12px', fontSize: '18px' }} /><span >安全退出</span></Link>
    </Header>
  )
}
