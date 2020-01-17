import React, { useState, useEffect } from 'react';
import { Modal, Avatar, Tabs } from 'antd'
import OrganizationTree from '../tree-organization'
import { get } from '../../utils/request';
import SimpleTree from '../tree-simple';
import './index.scss'

const { TabPane } = Tabs;

export interface SelectedPersonnelInfo {
  checkedKeys: any;
  checkedPersonnels: any;
}
export interface selectParticipantModalProps {
  visible?: boolean;
  onOk?: (selectedPersonnelInfo: SelectedPersonnelInfo, e: React.MouseEvent<any, MouseEvent>) => void;
  checkable?: boolean;
  onCancel?: any;
  checkedKeys?: any;
  checkedPersonnels?: any;
  onSelect?: any;
  /** props版本 用于重置组织结构树状态 */
  version?: number;
}

export interface checkedPersonnels {
  organizational: any[];
  partner: any[];
  externalContact: any[];
}

/** 选择人员组 */
export default function SelectParticipantModal(props: selectParticipantModalProps) {

  const {
    visible,
    checkable = true,
    onOk,
    onCancel,
    version
  } = props;
  // 组织结构
  const [orgnizationDept, setOrgnizationDept] = useState();
  const [checkedKeys, setCheckedKeys]: [any[], any] = useState([]);
  const [selectedKeys, setSelectedKeys]: [any[], any] = useState([]);
  // 合作伙伴
  const [partner, setPartner] = useState();
  const [partnerCheckedKeys, setPartnerCheckedKeys] = useState([]);
  const [partnerSelectedKeys, setPartnerSelectedKeys]: [any[], any] = useState([]);
  // 外部联系人
  const [externalContact, setExternalContact] = useState();
  const [externalContactCheckedKeys, setExternalContactCheckedKeys] = useState([]);
  const [externalContactSelectedKeys, setExternalContactSelectedKeys] = useState([]);
  // 单选
  const [selectedPersonnel, setSelectedPersonnel] = useState();
  // 多选 选中的人员/合作伙伴
  const [checkedPersonnels, setCheckedPersonnels]: [checkedPersonnels, any] = useState({
    organizational: [],
    partner: [],
    externalContact: []
  });
  // 首次加载拿到数据
  useEffect(() => {
    (async () => {
      const result = await get('/api/c_department_getCompanyAll?activation=1');
      setOrgnizationDept(result.getAllTree);
      setPartner(result.getAllPartner);
      setExternalContact(result.getAllExternalContact);
    })()

  }, []);

  useEffect(() => {
    if (props.checkedKeys) {
      setCheckedKeys(props.checkedKeys.organizational);
      setPartnerCheckedKeys(props.checkedKeys.partner);
      setExternalContactCheckedKeys(props.checkedKeys.externalContact);
      setCheckedPersonnels(props.checkedPersonnels);
    }
  }, [version, props.checkedKeys, props.checkedPersonnels]);

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
  function handleSelect(selectedKeys: any, e: any) {
    // 不能取消
    if (selectedKeys.length === 0) {
      return
    }
    const keyInfo = e.selectedNodes[0].props.keyInfo;
    const linKey = e.selectedNodes[0].props.linKey;
    if (keyInfo.type === 'personnel') {
      setPartnerSelectedKeys([]);
      setExternalContactSelectedKeys([]);
      setSelectedKeys(selectedKeys);
      setSelectedPersonnel(keyInfo);

      setCheckedPersonnels({
        externalContact: [],
        partner: [],
        organizational: [{
          ...keyInfo,
          linKey
        }]
      })
    }
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
  function handlePartnerSelect(selectedKeys: any, e: any) {
    // 不能取消
    if (selectedKeys.length === 0) {
      return
    }
    const keyInfo = e.selectedNodes[0].props.keyInfo;
    const linKey = e.selectedNodes[0].props.linKey;
    if (keyInfo.type === 'partner') {
      setSelectedKeys([]);
      setExternalContactSelectedKeys([]);
      setPartnerSelectedKeys(selectedKeys);
      setSelectedPersonnel(keyInfo);
      setCheckedPersonnels({
        externalContact: [],
        partner: [{
          ...keyInfo,
          linKey
        }],
        organizational: []
      })
    }
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
  function handleExternalContactSelect(selectedKeys: any, e: any) {
    // 不能取消
    if (selectedKeys.length === 0) {
      return
    }
    const keyInfo = e.selectedNodes[0].props.keyInfo;
    const linKey = e.selectedNodes[0].props.linKey;
    if (keyInfo.type === 'externalContact') {
      setSelectedKeys([]);
      setPartnerSelectedKeys([]);
      setExternalContactSelectedKeys(selectedKeys);
      setSelectedPersonnel(keyInfo);

      setCheckedPersonnels({
        externalContact: [{
          ...keyInfo,
          linKey
        }],
        partner: [],
        organizational: []
      })
    }
  }
  function okModal(e: any) {
    // 多选
    if (checkable) {
      onOk && onOk({
        checkedKeys: {
          organizational: checkedKeys,
          partner: partnerCheckedKeys,
          externalContact: externalContactCheckedKeys,
        },
        checkedPersonnels
      }, e)
    } else {// 单选
      onOk && onOk({
        checkedKeys: {
          organizational: checkedKeys,
          partner: partnerCheckedKeys,
          externalContact: externalContactCheckedKeys,
        },
        checkedPersonnels
      }, e)
    }
  }
  /**
   * 取消选中的人员
   * @param linKey 
   */
  function cancelPersonnel(linKey: any[], type: string) {

    const keys = linKey.map((item) => item.key);

    switch (type) {
      case 'organizational':
        setCheckedKeys(checkedKeys.filter((Key) => !keys.includes(Key)));
        setCheckedPersonnels({
          ...checkedPersonnels,
          organizational: checkedPersonnels.organizational.filter((item) => !keys.includes(item.key))
        });
        break;
      case 'partner':

        setPartnerCheckedKeys(partnerCheckedKeys.filter((Key) => !keys.includes(Key)));
        setCheckedPersonnels({
          ...checkedPersonnels,
          partner: checkedPersonnels.partner.filter((item) => !keys.includes(item.key))
        });
        break;
      case 'externalContact':
        setExternalContactCheckedKeys(externalContactCheckedKeys.filter((Key) => !keys.includes(Key)));
        setCheckedPersonnels({
          ...checkedPersonnels,
          externalContact: checkedPersonnels.externalContact.filter((item) => !keys.includes(item.key))
        });
        break;
      default:
        break;
    }
  }

  return (
    <Modal
      visible={visible}
      title="选择人员"
      centered
      width={633}
      bodyStyle={{ padding: '0 24px 24px' }}
      onOk={okModal}
      onCancel={onCancel}
      className="select-participant"
    >
      <div style={{ display: 'inline-block', verticalAlign: 'top', width: '280px' }}>
        <div style={{ lineHeight: '40px' }}>选择：</div>
        <Tabs
          defaultActiveKey="organizational"
          style={{ boxShadow: '0 0 1px 0 #ddd' }}
          tabBarStyle={{ background: '#fafafa' }}
        >
          <TabPane tab="组织结构" key="organizational" style={{ height: 360 }}>
            <OrganizationTree
              width={280}
              selectedKeys={selectedKeys}
              onSelect={handleSelect}
              checkable={checkable}
              checkedKeys={checkedKeys}
              onCheck={handleTreeCheck}
              orgnizationDept={orgnizationDept}
            />
          </TabPane>
          <TabPane tab="合作伙伴" key="partner" style={{ height: 360 }}>
            <SimpleTree
              width={280}
              selectedKeys={partnerSelectedKeys}
              onSelect={handlePartnerSelect}
              checkable={checkable}
              checkedKeys={partnerCheckedKeys}
              onCheck={handlePartnerCheck}
              dataSource={partner}
              treeInfo={{
                title: '合作伙伴',
                typePropName: 'partner'
              }}
            />
          </TabPane>
          <TabPane tab="外部联系人" key="externalContact" style={{ height: 360 }}>
            <SimpleTree
              width={280}
              selectedKeys={externalContactSelectedKeys}
              onSelect={handleExternalContactSelect}
              checkable={checkable}
              checkedKeys={externalContactCheckedKeys}
              onCheck={handleExternalContactCheck}
              dataSource={externalContact}
              treeInfo={{
                title: '外部联系人',
                typePropName: 'externalContact'
              }}
            />
          </TabPane>
        </Tabs>
      </div>
      <div style={{ display: 'inline-block', marginLeft: '25px', verticalAlign: 'top' }}>
        <div style={{ lineHeight: '40px' }}>已选：</div>
        <div className="selected-participants">
          {checkable ? checkedPersonnels.organizational.map((item: any) => (
            <div key={item.key} className="selected-participant" >
              <Avatar size={20} className="participant-avatar">
                {item.title.substr(item.title.length - 1, item.title.length)}
              </Avatar>
              <span style={{ verticalAlign: 'middle' }}>{item.title}</span>
              <i
                className="close"
                onClick={() => cancelPersonnel(item.linKey, 'organizational')}
              >
                &times;
              </i>
            </div>
          )) : null}
          {checkable ? checkedPersonnels.partner.map((item: any) => (
            <div key={item.key} className="selected-participant" >
              <Avatar size={20} className="participant-avatar">
                {item.title.substr(item.title.length - 1, item.title.length)}
              </Avatar>
              <span style={{ verticalAlign: 'middle' }}>{item.title}</span>
              <i
                className="close"
                onClick={() => cancelPersonnel(item.linKey, 'partner')}
              >
                &times;
              </i>
            </div>
          )) : null}
          {checkable ? checkedPersonnels.externalContact.map((item: any) => (
            <div key={item.key} className="selected-participant" >
              <Avatar size={20} className="participant-avatar">
                {item.title.substr(item.title.length - 1, item.title.length)}
              </Avatar>
              <span style={{ verticalAlign: 'middle' }}>{item.title}</span>
              <i
                className="close"
                onClick={() => cancelPersonnel(item.linKey, 'externalContact')}
              >
                &times;
              </i>
            </div>
          )) : null}
          {
            !checkable && selectedPersonnel ? (
              <div key={selectedPersonnel.key} className="selected-participant" >
                <Avatar size={20} className="participant-avatar">
                  {selectedPersonnel.title.substr(selectedPersonnel.title.length - 1, selectedPersonnel.title.length)}
                </Avatar>
                <span style={{ verticalAlign: 'middle' }}>{selectedPersonnel.title}</span>
              </div>
            ) : null
          }
        </div>
      </div>
    </Modal>
  )
}
