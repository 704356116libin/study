import React, { useEffect } from 'react';
import { Modal, Form, Input } from 'antd';
import { ModalProps } from 'antd/lib/modal';
import HigherDepartment from '../HigherDepartment';
const FormItem = Form.Item;

export interface DepartmentModalProps extends ModalProps {
  visible: boolean;
  onCancel: any;
  dataSource: any;
  form: any;
  width?: string | number;
  currentDataInfo: any;
  onSubmit: any;
  defaultSelected?: any
}

function DepartmentModal(props: DepartmentModalProps) {
  const { visible, onCancel, width, dataSource, form, onSubmit, currentDataInfo } = props;
  const { getFieldDecorator, setFieldsValue } = form;
  // 初始化 node_id
  useEffect(() => {
    setFieldsValue({
      node_id: currentDataInfo.node_id
    })
  }, [currentDataInfo.node_id])

  function onCreateDepOk() {
    onSubmit(form);
  }

  const formItemsLayout = {
    labelCol: {
      xs: { span: 24 },
      sm: { span: 6 },
    },
    wrapperCol: {
      xs: { span: 24 },
      sm: { span: 10 },
    },
  }
  return (
    <Modal
      title="新增部门"
      visible={visible}
      onOk={onCreateDepOk}
      onCancel={onCancel}
      width={width || 520}
    >
      <div>
        <Form>
          <FormItem
            {...formItemsLayout}
            label='上级部门'
          >   {getFieldDecorator('node_id', {
            rules: [
              { required: true, message: '上级部门' },
            ],
          })(
            <HigherDepartment
              currentDefaultInfo={currentDataInfo}
              dataSource={dataSource}
            />
          )}
          </FormItem>
          <FormItem
            {...formItemsLayout}
            label='部门名称'
          >
            {getFieldDecorator('name', {
              rules: [
                { required: true, message: '部门名称' },
              ],
            })(
              <Input />
            )}
          </FormItem>
        </Form>
      </div>
    </Modal>
  )
}
export default Form.create<DepartmentModalProps>()(DepartmentModal)