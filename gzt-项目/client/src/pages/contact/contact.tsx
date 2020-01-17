import React, { useState, useEffect, useRef } from 'react';
import { Layout, Menu, Icon, Empty } from 'antd';
import { Link } from 'react-router-dom';
import { connect } from 'dva';
import { SelectParam } from 'antd/lib/menu';
import { History } from 'history';
import { Dispatch } from 'redux';

const { Sider } = Layout;

interface StateToContactProps {
  /** 公司列表信息 */
  companys: any;
}
interface DispatchToContactProps {
  /** 获取加入的公司列表信息 */
  queryCompanys: () => void;
  /** 取消获取公司列表信息 */
  cancelQueryCompanys: () => void;
}
interface ContactProps extends DispatchToContactProps, StateToContactProps {
  children: React.ReactChild
  history: History;

}

const NAMESPACE = 'Workbench';
const mapStateToProps: (state: any) => StateToContactProps = (state) => ({
  companys: state[NAMESPACE].companys
});

const mapDispatchToProps: (dispatch: Dispatch) => DispatchToContactProps = (dispatch) => ({
  queryCompanys() {
    dispatch({
      type: `${NAMESPACE}/queryCompanys`,
    })
  },
  cancelQueryCompanys() {
    dispatch({
      type: `${NAMESPACE}/cancelQueryCompanys`
    })
  }
});

function Contact(props: ContactProps) {

  const [selectedKeys, setSelectedKeys] = useState();
  const hasJumpRef = useRef(false);

  useEffect(() => {
    props.queryCompanys();
  }, [])

  useEffect(() => {
    if (props.companys && props.companys.relate_companys.length !== 0) {
      console.log(props.companys, 333333333);

      setSelectedKeys([props.companys.relate_companys[0].id]);
      if (!hasJumpRef.current) {
        props.history.push(`/contact/company/${props.companys.relate_companys[0].id}`);
        hasJumpRef.current = true;
      }
    }
  }, [props.companys])

  function handleSelect({ selectedKeys }: SelectParam) {
    setSelectedKeys(selectedKeys);

  }

  const { companys } = props;
  const hasCompany = companys && companys.relate_companys.length !== 0;

  return (
    <Layout style={{ height: 'calc(100vh - 61px)' }}>
      <Sider width="210" theme="light" className="big-sider">
        {
          hasCompany ? (
            <Menu
              mode="inline"
              selectedKeys={selectedKeys}
              onSelect={handleSelect}
            >
              {
                companys.relate_companys.map(({ id, name }: any) => (
                  <Menu.Item key={id}>
                    <Link to={`/contact/company/${id}`}>
                      <Icon type="smile" />
                      <span className="nav-text">{name}</span>
                    </Link>
                  </Menu.Item>
                ))
              }
            </Menu>
          ) : (
              <Empty />
            )
        }
      </Sider>

      {props.children}

    </Layout>
  )

}
export default connect(mapStateToProps, mapDispatchToProps)(Contact)
