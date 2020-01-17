import React, {  useEffect } from 'react';
import { Modal, Input, Form } from 'antd';
import { ModalProps } from 'antd/lib/modal';
const FormItem = Form.Item;
export interface EditDepModalProps extends ModalProps {
  width?: string | number,
  visible: boolean;
  onCancel: any;
  currentDataInfo: any;
  onSubmit: any;
  form: any;
}

function EditDepModal(props: EditDepModalProps) {
  const { visible, width, currentDataInfo, onCancel, onSubmit, form } = props;
  const { getFieldDecorator, setFieldsValue } = form;
  useEffect(() => {
    setFieldsValue({//设置input的默认值
      names: currentDataInfo.name
    })
  }, [currentDataInfo.name])// 直接监听对象可能存在引用地址的不同，造成一直渲染执行
 
  return (
    <Modal
      title="编辑部门"
      visible={visible}
      onCancel={onCancel}
      width={width || 520}
      style={{ height: 600 }}
      onOk={() => onSubmit(form)}
    >
      <Form>
        <FormItem
          wrapperCol={{ span: 15, offset: 1 }}
        >   {getFieldDecorator('names', {
          rules: [
            { required: true, message: '请输入名称' },
          ],
        })(
          <Input />
        )}
        </FormItem>
      </Form>
    </Modal >
  )
}
export default Form.create<EditDepModalProps>()(EditDepModal);