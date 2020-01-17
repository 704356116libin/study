import React, { useEffect, useState } from 'react';
import { Layout, Menu, Icon } from 'antd';
import { Link } from 'react-router-dom'; //  Switch, Route, 
import { connect } from 'dva';
import { Dispatch } from 'redux';
import './doc.scss'
import { Location, History } from 'history';
import { SelectParam } from 'antd/lib/menu';
import decryptId from '../../utils/decryptId';

const SubMenu = Menu.SubMenu;
const { Sider } = Layout;

// const NAMESPACE = 'Doc';
const WORKBENCH = 'Workbench';
interface StateToDocProps {
  companys: any;
}
interface DispatchToDocProps {
  /** 获取加入的公司列表信息 */
  queryCompanys: () => void;
}

interface DocProps extends StateToDocProps, DispatchToDocProps {
  children?: React.ReactNode;
  location: Location;
  history: History;
}


const mapStateToProps: (state: any) => StateToDocProps = (state) => ({
  companys: state[WORKBENCH].companys
});

const mapDispatchToProps: (dispatch: Dispatch) => DispatchToDocProps = (dispatch) => ({
  queryCompanys() {
    dispatch({
      type: `${WORKBENCH}/queryCompanys`,
    })
  },
  cancelQueryCompanys() {
    dispatch({
      type: `${WORKBENCH}/cancelQueryCompanys`
    })
  }
});

function Doc(props: DocProps) {

  const { companys } = props;
  const [currentUrl, setCurrentUrl] = useState(['dynamic'])

  useEffect(() => {
    props.queryCompanys();

    if (props.location.pathname.split('/')[2] === 'company' && (
      props.location.pathname.split('/')[3] === undefined ||
      props.location.pathname.split('/')[3] === '')
    ) {
      props.history.replace('/doc/dynamic');
      setCurrentUrl(['dynamic']);
      return
    }

    setCurrentUrl([
      props.location.pathname.split('/')[2] === 'company'
        ? decryptId(props.location.pathname.split('/')[3])
        : props.location.pathname.split('/')[2]
    ])
  }, [])

  useEffect(() => {
    if (props.location.pathname.split('/')[2] === 'dynamic' && currentUrl[0] !== 'dynamic') {
      setCurrentUrl(['dynamic'])
    }
  }, [props.location.pathname])

  function handleSelect(param: SelectParam) {
    setCurrentUrl(param.selectedKeys)
  }

  return (
    <Layout className="doc">
      <Sider width="210" theme="light" className="big-sider">
        <Menu
          selectedKeys={currentUrl}
          defaultOpenKeys={['company']}
          onSelect={handleSelect}
          mode="inline"
        >
          <Menu.Item key="dynamic">
            <Link to="/doc/dynamic">
              <Icon type="smile" />
              <span className="nav-text">文件动态</span>
            </Link>
          </Menu.Item>
          <Menu.Item key="recently">
            <Link to="/doc/recently">
              <Icon type="smile" />
              <span className="nav-text">最近使用</span>
            </Link>
          </Menu.Item>
          <Menu.Item key="personal">
            <Link to="/doc/personal">
              <Icon type="user" />
              <span className="nav-text">我的文件</span>
            </Link>
          </Menu.Item>
          <SubMenu key="company" title={<span><Icon type="file-text" /><span>企业文件</span></span>}>
            {
              companys && companys.relate_companys.map(({ id, name }: any) => (
                <Menu.Item key={decryptId(id)}>
                  <Link to={`/doc/company/${id}`}>
                    <Icon type="smile" />
                    <span className="nav-text">{name}</span>
                  </Link>
                </Menu.Item>
              ))
            }
          </SubMenu>
        </Menu>
      </Sider>

      {props.children}

    </Layout>
  )
}
export default connect(mapStateToProps, mapDispatchToProps)(Doc)
