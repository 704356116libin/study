import React from 'react';
import { Modal } from 'antd';
// import { FormComponentProps } from 'antd/lib/form';
// import { useEffect } from 'react';
// import BraftEditor from 'braft-editor';
// import TextLabel from '@/components/textLabel';
// import AnnexList from '@/components/annexList';
import Contents from '../Contents';

// const FormItem = Form.Item;
export interface NoticePreviewProps {
  formDatas: any;
  visible: boolean;
  onCancel: any;
}

/**
 * 可用于预览公告
 * @param props ParseFormProps
 */
export default function PreviewForm(props: NoticePreviewProps) {
  const { visible, onCancel, formDatas } = props;

  // useEffect(() => {
  //   const { title, content, updatingfiles } = formDatas;
  //   props.form.setFieldsValue({
  //     title,
  //     content,
  //     updatingfiles
  //   })
  // }, [formDatas]);

  return (
    <Modal
      title="公告预览"
      visible={visible}
      width={720}
      onCancel={onCancel}
      maskClosable={false}
      footer={null}
    >
      <div>
        <Contents detailInfo={formDatas} />
      </div>
    </Modal>
  )
}