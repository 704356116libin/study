import React from 'react';
import { Modal, Form, Input } from 'antd';
import { ModalProps } from 'antd/lib/modal';
const FormItem = Form.Item;
export interface AddGroupProps extends ModalProps {
  visible: boolean,
  handleOK: any,
  handleCancel: any,
  width?: string | number
  form: any
}

export default function AddGroup(props: AddGroupProps) {
  const { visible, handleOK, handleCancel, width, form } = props;
  const { getFieldDecorator } = form;

  return (
    <Modal
      title='添加分组'
      visible={visible}
      centered
      mask
      maskClosable={false}
      onOk={handleOK}
      onCancel={handleCancel}
      destroyOnClose={true}
      width={width || 600}
    >
      <Form>
        <FormItem
          wrapperCol={{ span: 15, offset: 1 }}
          label=''
        >   {getFieldDecorator('name', {
          rules: [
            { required: true, message: '请输入名称' },
          ],
        })(
          <div>
            <Input placeholder="请输入名称(限10个字符)" max="10" className="categoryInput" />
          </div>
        )}
        </FormItem>
      </Form >
    </Modal>
  )

}