import React, { useEffect } from 'react';
import { Modal, Form, Input } from 'antd';
import { ModalProps } from 'antd/lib/modal';
const FormItem = Form.Item;
export interface EditGroupNameProps extends ModalProps {
  width?: string | number,
  form: any,
  visible: boolean,
  onCancel: any,
  currentGroupName: string,
  onOK: any,

}

function EditGroupName(props: EditGroupNameProps) {

  const { form, visible, onCancel, width, onOK, currentGroupName } = props;
  const { getFieldDecorator, setFieldsValue } = form;
  useEffect(() => {

    setFieldsValue({//设置input的默认值
      names: currentGroupName
    })
  }, [currentGroupName])

  return (
    <Modal
      title='编辑分组名'
      visible={visible}
      centered
      mask
      maskClosable={false}
      onOk={()=>onOK(form)}
      onCancel={onCancel}
      destroyOnClose={true}
      width={width || 600}
    >
      <Form >
        <FormItem
          wrapperCol={{ span: 15, offset: 1 }}
        >   {getFieldDecorator('names', {
          rules: [
            { required: true, message: '请输入名称' }
          ],
        })(
          <Input max="10" />
        )}
        </FormItem>
      </Form >
    </Modal>
  )
}
export default Form.create<EditGroupNameProps>()(EditGroupName)