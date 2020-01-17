import React, { useState, useEffect } from 'react';
import DepartmentTree from '../departmentTree';
import { ModalProps } from 'antd/lib/modal';
import { Modal } from 'antd';

/**新增部门Modal */

export interface DepartmentTreeModalProps extends ModalProps {
  dataSource: any;
  visible: boolean;
  depVisible: boolean;
  onCancel: any;
  onOk: any;
  currentDataInfo?: any
}

export default function DepartmentTreeModal(props: DepartmentTreeModalProps) {
  const { dataSource, visible, depVisible, onCancel, width, onOk, currentDataInfo } = props;

  const [treeSelectedData, setTreeSelectedData] = useState({} as any);

  useEffect(() => {
    if (currentDataInfo) {
      const { name, id } = currentDataInfo;
      const data = {
        'departmentName': name,
        'selectedKeys': [id]
      }
      setTreeSelectedData(data)
    }
  }, [currentDataInfo])

  function departmentTreeInfo(params: any) {
    setTreeSelectedData(params)
  }
  function addOk() {
    onOk(treeSelectedData && treeSelectedData)
  }

  return (
    <Modal
      title="新增部门"
      visible={visible}
      onOk={addOk}
      onCancel={onCancel}
      width={width || 520}
    >
      <div className='beautiful-scroll-bar-hover' style={{ height: 360, overflowX: 'hidden' }}>
        {
          dataSource && <DepartmentTree
            dataSource={dataSource}
            depVisible={depVisible}
            onChange={departmentTreeInfo}
            selectedKeys={treeSelectedData && treeSelectedData.selectedKeys}
          />
        }
      </div>
    </Modal>
  )
}