import React, { useState, useEffect } from 'react';
import { Tabs } from 'antd'
import { get } from '../../utils/request';
import OrganizationTree from '../tree-organization'
import SimpleTree from '../tree-simple';
import classnames from 'classnames';
import './index.scss'

const { TabPane } = Tabs;

export interface SelectedPersonnelInfo {
  checkedKeys: any;
  checkedPersonnels: any;
}

export interface ContactTreeProps {
  className?: any;
  style?: React.CSSProperties;
  width?: string | number;
  checkable?: boolean;
  checkedKeys?: any;
  checkedPersonnels?: any;
  onSelect?: any;
  selectedKeys?: any;
  /** props版本 用于重置组织结构树状态 */
  version?: number;
  companyId?: string;
  showLeaf?: boolean;
  onTabsChange?: any;
  activeKey?: string;
}

export interface checkedPersonnels {
  organizational: any[];
  partner: any[];
  externalContact: any[];
}

/** 所有联系人树 */
export default function ContactTree(props: ContactTreeProps) {
  const {
    className,
    style,
    width = 280,
    onSelect,
    selectedKeys,
    checkable = true,
    version,
    companyId,
    showLeaf,
    onTabsChange,
    activeKey,
  } = props;
  // 组织结构
  const [orgnizationDept, setOrgnizationDept] = useState();
  const [checkedKeys, setCheckedKeys]: [any[], any] = useState([]);
  const [organizationSelectedKeys, setOrganizationSelectedKeys]: [any[], any] = useState([]);
  // 合作伙伴
  const [partner, setPartner] = useState();
  const [partnerCheckedKeys, setPartnerCheckedKeys] = useState([]);
  const [partnerSelectedKeys, setPartnerSelectedKeys]: [any[], any] = useState([]);
  // 外部联系人
  const [externalContact, setExternalContact] = useState();
  const [externalContactCheckedKeys, setExternalContactCheckedKeys] = useState([]);
  const [externalContactSelectedKeys, setExternalContactSelectedKeys] = useState([]);

  // 多选 选中的人员/合作伙伴
  const [checkedPersonnels, setCheckedPersonnels]: [checkedPersonnels, any] = useState({
    organizational: [],
    partner: [],
    externalContact: []
  });
  // 首次加载拿到数据
  useEffect(() => {
    (async () => {
      let result;
      if (companyId) {
        result = await get(`/api/c_department_getCompanyAll?company_id=${companyId}&activation=1`);
      } else {
        result = await get('/api/c_department_getCompanyAll?activation=1');
      }
      setOrgnizationDept(result.getAllTree);
      setPartner(result.getAllPartner);
      setExternalContact(result.getAllExternalContact);
      const { id: key, name: title } = result.getAllTree.data;
      onSelect && onSelect('organization', [key], false, [{ key, title }]);
      setPartnerSelectedKeys([]);
      setExternalContactSelectedKeys([]);

    })()

  }, [companyId]);

  // 如果 父组件想要控制 selectKeys 也是允许的
  useEffect(() => {
    switch (selectedKeys.type) {
      case 'organization':
        setOrganizationSelectedKeys(selectedKeys.selecteKeys);
        break;
      case 'partner':
        setPartnerSelectedKeys(selectedKeys.selecteKeys);
        break;
      case 'externalContact':
        setExternalContactSelectedKeys(selectedKeys.selecteKeys);
        break;
      default:
        break;
    }
  }, [selectedKeys]);

  useEffect(() => {
    if (props.checkedKeys) {
      setCheckedKeys(props.checkedKeys.organizational);
      setPartnerCheckedKeys(props.checkedKeys.partner);
      setExternalContactCheckedKeys(props.checkedKeys.externalContact);
      setCheckedPersonnels(props.checkedPersonnels);
    }
  }, [version, props.checkedKeys]);

  /** 组织结构 checked */
  function handleTreeCheck(currentCheckedKeys: any, e: any) {
    setCheckedKeys(currentCheckedKeys);
    const nextPersonnels = [];
    for (const item of e.checkedNodes) {
      if (item.props.keyInfo.type === 'personnel') {
        nextPersonnels.push({
          ...item.props.keyInfo,
          linKey: item.props.linKey
        })
      }
    }
    setCheckedPersonnels({
      ...checkedPersonnels,
      organizational: nextPersonnels
    })
  }
  /** 组织结构 selected */
  function handleSelect(organizationSelectedKeys: any, e: any) {
    // 不能取消
    if (organizationSelectedKeys.length === 0) {
      return
    }
    const keyInfo = e.selectedNodes[0].props.keyInfo;
    if (keyInfo.type === 'personnel') {
      setPartnerSelectedKeys([]);
      setExternalContactSelectedKeys([]);
    }
    setOrganizationSelectedKeys(organizationSelectedKeys);
    onSelect && onSelect('organization', organizationSelectedKeys, e);
  }
  /** 合作伙伴 checked */
  function handlePartnerCheck(currentCheckedKeys: any, e: any) {
    setPartnerCheckedKeys(currentCheckedKeys);
    const nextPersonnels = [];
    for (const item of e.checkedNodes) {
      if (item.props.keyInfo.type === 'partner') {
        nextPersonnels.push({
          ...item.props.keyInfo,
          linKey: item.props.linKey
        })
      }
    }
    setCheckedPersonnels({
      ...checkedPersonnels,
      partner: nextPersonnels
    })
  }
  /** 合作伙伴 selected */
  function handlePartnerSelect(currentSelectedKeys: any, e: any) {
    // 不能取消
    if (currentSelectedKeys.length === 0) {
      return
    }
    const keyInfo = e.selectedNodes[0].props.keyInfo;
    if (keyInfo.type === 'partner') {
      setOrganizationSelectedKeys([]);
      setExternalContactSelectedKeys([]);
    }
    setPartnerSelectedKeys(currentSelectedKeys);
    onSelect && onSelect('partner', currentSelectedKeys, e);
  }
  /** 外部联系人 checked */
  function handleExternalContactCheck(currentCheckedKeys: any, e: any) {

    setExternalContactCheckedKeys(currentCheckedKeys);
    const nextPersonnels = [];
    for (const item of e.checkedNodes) {
      if (item.props.keyInfo.type === 'externalContact') {
        nextPersonnels.push({
          ...item.props.keyInfo,
          linKey: item.props.linKey
        })
      }
    }
    setCheckedPersonnels({
      ...checkedPersonnels,
      externalContact: nextPersonnels
    })
  }
  /** 外部联系人 selected */
  function handleExternalContactSelect(currentSelectedKeys: any, e: any) {
    // 不能取消
    if (currentSelectedKeys.length === 0) {
      return
    }
    const keyInfo = e.selectedNodes[0].props.keyInfo;

    if (keyInfo.type === 'externalContact') {
      setOrganizationSelectedKeys([]);
      setPartnerSelectedKeys([]);
    }
    setExternalContactSelectedKeys(currentSelectedKeys);
    onSelect && onSelect('externalContact', currentSelectedKeys, e);
  }

  function handleTabsChange(activeKey: string) {
    let linKey;
    if (activeKey === 'organization') {
      linKey = [{
        key: orgnizationDept.data.id,
        title: orgnizationDept.data.name,
      }]
    } else if (activeKey === 'partner') {
      linKey = [{ key: activeKey, title: '合作伙伴' }];
    } else if (activeKey === 'externalContact') {
      linKey = [{ key: activeKey, title: '外部联系人' }];
    }
    onTabsChange && onTabsChange(activeKey, linKey);
  }


  return (
    <Tabs
      className={classnames(className, "tree-contact")}
      activeKey={activeKey}
      style={{ width, boxShadow: '0 0 1px 0 #ddd', ...style }}
      tabBarStyle={{ background: '#fafafa' }}
      onChange={handleTabsChange}
    >
      <TabPane tab="组织结构" key="organization">
        <OrganizationTree
          width={280}
          selectedKeys={organizationSelectedKeys}
          onSelect={handleSelect}
          checkable={checkable}
          checkedKeys={checkedKeys}
          onCheck={handleTreeCheck}
          orgnizationDept={orgnizationDept}
          showLeaf={showLeaf}
        />
      </TabPane>
      <TabPane tab="合作伙伴" key="partner">
        <SimpleTree
          width={280}
          selectedKeys={partnerSelectedKeys}
          onSelect={handlePartnerSelect}
          checkable={checkable}
          checkedKeys={partnerCheckedKeys}
          onCheck={handlePartnerCheck}
          dataSource={partner}
          showLeaf={showLeaf}
          treeInfo={{
            title: '合作伙伴',
            typePropName: 'partner'
          }}
        />
      </TabPane>
      <TabPane tab="外部联系人" key="externalContact">
        <SimpleTree
          width={280}
          selectedKeys={externalContactSelectedKeys}
          onSelect={handleExternalContactSelect}
          checkable={checkable}
          checkedKeys={externalContactCheckedKeys}
          onCheck={handleExternalContactCheck}
          dataSource={externalContact}
          showLeaf={showLeaf}
          treeInfo={{
            title: '外部联系人',
            typePropName: 'externalContact'
          }}
        />
      </TabPane>
    </Tabs>
  )
}
