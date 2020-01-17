import React, { useState } from 'react';
import { Button, Form, message } from 'antd';
import { FormComponentProps } from 'antd/lib/form';
import { FormItemData } from '../../formLibrary';
import ParseFormItem from '../../../components/formLibrary/parseFormItem';
import req from '../../../utils/request';

export interface FormAreaProps extends FormComponentProps {
  id: string;
  formData?: FormItemData[];
  canSubmit?: boolean;
}

/**
 * 协助模块用户自定义表单数据
 * @param props FormAreaProps
 */
function FormArea(props: FormAreaProps) {

  const [nextFormData, setNextFormData]: [FormItemData[], any] = useState([]);
  const [uploading, setUploading] = useState(false);
  const formData = props.formData || [];
  const canSubmit = props.canSubmit !== false;

  function handleSubmit(e: any) {
    e.preventDefault();
    props.form.validateFieldsAndScroll((err, values) => {
      if (!err) {
        setUploading(true);
        (async () => {
          const result = await req('/api/c_assist_saveForm', {
            method: 'POST',
            body: {
              id: props.id,
              formArea: nextFormData
            }
          });

          setUploading(false);
          if (result.status === 'success') {
            message.success('更新成功');
          } else {
            message.success('服务器错误，请稍后再试');
          }
        })();
      }
    })
  }
  // 更新formdata
  function onChange(formDatas: FormItemData[]) {
    setNextFormData(formDatas)
  }

  return (
    <Form
      onSubmit={handleSubmit}
      style={{ width: '100%', maxHeight: '600px', overflow: 'auto' }}>
      {
        <ParseFormItem
          form={props.form}
          onChange={onChange}
          formData={formData}
        />
      }
      {canSubmit
        ? (
          <Form.Item
            wrapperCol={{ span: 12, offset: 6 }}
          >
            <Button type="primary" htmlType="submit" loading={uploading}>更新数据</Button>
          </Form.Item>
        )
        : null
      }
    </Form >
  )
}
export default Form.create<FormAreaProps>()(FormArea) 