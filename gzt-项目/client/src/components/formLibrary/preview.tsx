// 预览表单效果弹出框

import React from 'react';
import ParseForm from './parseForm';
import { Modal, Button } from 'antd'
import ParseReview from '../reviewFormLibrary/parseReview';

export interface PreviewProps {
  /**
   * 预览表单的类型 评审 |
   */
  type?: 'review' | 'custom';
  visible: boolean;
  onCancel: any;
  data: any[];
}

/**
 * 拖拽预览组件
 * @param props {data: 拖拽组件拖拽完成后的数据}
 */
export default function Preview({
  type = 'custom',
  visible,
  onCancel,
  data
}: PreviewProps) {

  return (
    <Modal
      title="表单预览"
      centered
      visible={visible}
      onCancel={onCancel}
      width={600}
      maskClosable={false}
      footer={
        <div style={{ textAlign: 'left' }}>
          <Button type="primary" onClick={onCancel}>确定</Button>
        </div>
      }
    >
      {
        type === 'review' ? <ParseReview style={{ width: 1000 }} layout="compact" formData={data} /> : <ParseForm formData={data} />
      }

    </Modal>
  )
}