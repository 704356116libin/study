import React, { useState, useEffect } from 'react';
import { Layout, Breadcrumb } from 'antd';
import { AntTreeNodeSelectedEvent } from 'antd/lib/tree';
import ContactTree from '../../components/tree-contact';
import { connect } from 'dva';
import OrganizationTable from './table-organization';
import PartnerTable from './table-partner';
import ExternalContactTable from './table-externalContact';

const NAMESPACE = 'Contact';

const mapStateToProps = (state: any) => ({
  ...state[NAMESPACE]
});

const mapDispatchToProps = (dispatch: Function) => ({
  queryDepartments(params: any) {
    dispatch({
      type: `${NAMESPACE}/queryDepartments`,
      payload: { params }
    })
  },
  queryPartners(params: any) {
    dispatch({
      type: `${NAMESPACE}/queryPartners`,
      payload: { params }
    })
  },
  queryExternalContacts(params: any) {
    dispatch({
      type: `${NAMESPACE}/queryExternalContacts`,
      payload: { params }
    })
  },
})



export interface LinKeyItem {
  key: string;
  title: string;
}

export type LinKey = LinKeyItem[];

function Colleague(props: any) {

  const [selectedLinKeys, setSelectedLinKeys]: [LinKey, any] = useState([]);
  const [selectedKeys, setSelectedKeys]: [any, any] = useState([]);
  const [partners, setPartners]: [any, any] = useState([]);
  const [externalContacts, setExternalContacts]: [any, any] = useState([]);
  const [activeKey, setActiveKey]: [any, any] = useState('organization');

  useEffect(() => {
    // 重置状态
    setActiveKey('organization');
    setPartners();
    setExternalContacts();
  }, [props.match.params.companyId]);

  useEffect(() => {
    setExternalContacts(props.externalContacts);
  }, [props.externalContacts]);

  useEffect(() => {
    setPartners(props.partners);
  }, [props.partners]);


  /** 点击节点 设置右侧面包屑导航 向服务器请求对应数据 */
  function handleSelect(type: string, selecteKeys: any[], e: AntTreeNodeSelectedEvent | false, linKey: any) {
    // 首次手动触发 onSelect 没有e
    e === false ? setSelectedLinKeys(linKey) : setSelectedLinKeys(e.node.props.linKey);
    setSelectedKeys({ type, selecteKeys });

    if (type === 'organization') {
      props.queryDepartments({
        node_id: selecteKeys[0],
        page_size: 10,
        now_page: 1,
        is_enable: 1
      })
    } else if (type === 'partner') {
      props.queryPartners({
        company_id: props.match.params.companyId,
        id: selecteKeys[0] === 'partners' ? 'all' : selecteKeys[0],
        page_size: 10,
        now_page: 1,
      })
    } else if (type === 'externalContact') {
      props.queryExternalContacts({
        company_id: props.match.params.companyId,
        id: selecteKeys[0] === 'externalContacts' ? 'all' : selecteKeys[0],
        page_size: 10,
        now_page: 1,
      })
    }
  }

  function handleTabsChange(activeKey: string, linkey: any[]) {
    setActiveKey(activeKey);
    setSelectedLinKeys(linkey);
  }

  // /**
  //  * 点击面包屑导航 设置选中状态
  //  * @param key 当前面包屑导航的key值
  //  */
  // function setKeys(key: string) { // 点击当前return
  //   // 当前点击的面包屑导航属于哪个层级
  //   const currentKey = selectedLinKeys.findIndex((item) => key === item.key);
  //   setSelectedLinKeys(selectedLinKeys.filter((_, index) => index <= currentKey));
  // }

  return (
    <Layout hasSider style={{ margin: '24px', background: '#fff', boxShadow: '0px 0px 6px 0px #cccccc' }}>

      <ContactTree
        companyId={props.match.params.companyId}
        selectedKeys={selectedKeys}
        onSelect={handleSelect}
        checkable={false}
        style={{ flex: '0 0 280px' }}
        showLeaf={false}
        onTabsChange={handleTabsChange}
        activeKey={activeKey}
      />

      <div style={{ flex: 'auto', padding: '0 24px', overflowY: 'auto' }}>
        <Breadcrumb style={{ padding: '12px 0' }}>
          {
            selectedLinKeys.map(({ key, title }, k, arr) => {
              if (k < arr.length - 1) {
                return (
                  <Breadcrumb.Item key={key} >
                    <span className="primary-color" style={{ cursor: 'pointer' }}>
                      {title}
                    </span>
                  </Breadcrumb.Item>
                )
              } else {
                return (
                  <Breadcrumb.Item key={key} >
                    {title}
                  </Breadcrumb.Item>
                )
              }
            })
          }
        </Breadcrumb>
        {
          activeKey === 'organization' ?
            <OrganizationTable dataSource={props.departments && props.departments.users} /> :
            activeKey === 'partner' ?
              <PartnerTable dataSource={partners} /> :
              activeKey === 'externalContact' ?
                <ExternalContactTable dataSource={externalContacts} /> :
                null
        }

      </div>
    </Layout>
  )
}
export default connect(mapStateToProps, mapDispatchToProps)(Colleague)