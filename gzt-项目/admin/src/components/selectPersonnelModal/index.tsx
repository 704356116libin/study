import React, { useState, useEffect } from 'react';
import { Modal, Avatar } from 'antd'
import OrganizationTree from '../tree-organization'
import './index.scss'

export interface CheckedPersonnelsInfo {
  checkedKeys: any;
  checkedPersonnels: any;
}
export interface SelectedPersonnelInfo {
  key: string;
  title: string;
}
export interface SelectPersonnelModalProps {

  visible?: boolean;
  onOk?: (selectedPersonnelInfo: CheckedPersonnelsInfo | SelectedPersonnelInfo, e: React.MouseEvent<any, MouseEvent>) => void;
  checkable?: boolean;
  onCancel?: any;
  checkedKeys?: any;
  checkedPersonnels?: any;
  onSelect?: any;
  /**
   * props版本 用于重置组织结构树状态
   */
  version?: number;
}

/**
 * 选择人员组件
 */
export default function SelectPersonnelModal(props: SelectPersonnelModalProps) {

  const {
    visible,
    checkable = true,
    onOk,
    onCancel,
    onSelect,
    version
  } = props;

  const [checkedPersonnels, setCheckedPersonnels]: [any[], any] = useState([]);
  const [checkedKeys, setCheckedKeys]: [any[], any] = useState([]);
  const [selectedKeys, setSelectedKeys]: [any[], any] = useState([]);

  useEffect(() => {
    if (props.checkedKeys) {
      setCheckedKeys(props.checkedKeys)
      setCheckedPersonnels(props.checkedPersonnels)
    }
  }, [version, props.checkedKeys]);
  /**
   * select树选择
   * @param selectKeys 
   * @param e 
   */
  function handleTreeSelect(selectKeys: any, e: any) {
    if (!checkable) {

      setSelectedKeys(selectKeys);

      const nextPersonnels = [];
      for (const item of e.selectedNodes) {
        if (item.props.keyInfo.type === 'personnel') {
          nextPersonnels.push({
            ...item.props.keyInfo,
            linKey: item.props.linKey
          })
        }
      }
      setCheckedPersonnels(nextPersonnels)
    }
    onSelect && onSelect(selectKeys, e)
  }
  /**
   * checked处理
   * @param currentCheckedKeys 
   * @param e 
   */
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
    setCheckedPersonnels(nextPersonnels)
  }
  function okModal(e: any) {
    // 多选
    if (checkable) {
      onOk && onOk({ checkedKeys, checkedPersonnels }, e)
    } else {// 单选
      onOk && onOk(checkedPersonnels.length !== 0 ? { key: checkedPersonnels[0].key, title: checkedPersonnels[0].title } : {} as any, e)
    }

  }
  /**
   * 取消选中的人员
   * @param linKey 
   */
  function cancelPersonnel(linKey: any[], ) {

    const keys = linKey.map((item) => item.key);

    if (checkable) {
      setCheckedKeys(checkedKeys.filter((Key) => !keys.includes(Key)));
      setCheckedPersonnels(checkedPersonnels.filter((item) => !keys.includes(item.key)));
    } else {
      setSelectedKeys(selectedKeys.filter((Key) => !keys.includes(Key)));
      setCheckedPersonnels(checkedPersonnels.filter((item) => !keys.includes(item.key)));
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
      className="select-personnel"
    >
      <div style={{ display: 'inline-block', verticalAlign: 'top' }}>
        <div style={{ lineHeight: '40px' }}>选择：</div>
        <div style={{ height: '360px' }}>
          <OrganizationTree
            width={280}
            selectedKeys={selectedKeys}
            onSelect={handleTreeSelect}
            checkable={checkable}
            checkedKeys={checkedKeys}
            onCheck={handleTreeCheck}
          />
        </div>
      </div>
      <div style={{ display: 'inline-block', marginLeft: '25px', verticalAlign: 'top' }}>
        <div style={{ lineHeight: '40px' }}>已选：</div>
        <div className="selected-personnels">
          {checkedPersonnels.map((item: any) => (
            <div key={item.key} className="selected-personnel" >
              <Avatar size={20} className="personnel-avatar">
                {item.title.substr(item.title.length - 1, item.title.length)}
              </Avatar>
              <span style={{ verticalAlign: 'middle' }}>{item.title}</span>
              <i
                className="close"
                onClick={() => cancelPersonnel(item.linKey)}
              >
                &times;
              </i>
            </div>
          ))}
        </div>
      </div>
    </Modal>
  )
}
