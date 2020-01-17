import React from 'react';
import { Modal } from 'antd';
import TextLabel from '../../components/textLabel';

export interface DisableStaffProps {
  visible: boolean;
  onHandleCancel: any;
  choosePersonNumber: number;
  onHandleOk: any;
}

export default function DisableStaff(props: DisableStaffProps) {
  const { visible, onHandleCancel, choosePersonNumber, onHandleOk } = props;
  return (
    <Modal
      title="批量冻结"
      visible={visible}
      onOk={onHandleOk}
      onCancel={onHandleCancel}

    >
      <div><TextLabel text="选中人数" /> <span>{choosePersonNumber}</span> 人</div>
    </Modal>
  )

}