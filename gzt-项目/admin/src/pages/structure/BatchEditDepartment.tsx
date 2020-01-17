import React from 'react';
import { Modal, Form } from 'antd';
import { FormComponentProps } from 'antd/lib/form';

import HigherDepartment from '../../components/HigherDepartment';
const FormItem = Form.Item;

export interface BatchEditDepartmentProps extends FormComponentProps {
  visible: boolean;
  onHandleCancel: any;
  choosePersonNumber: number;
  dataSource: any;
  currentDataInfo: any;
  onHandleOk: Function;
}

function BatchEditDepartment(props: BatchEditDepartmentProps) {
 
  const { visible, onHandleCancel, form, choosePersonNumber, dataSource, currentDataInfo, onHandleOk } = props;
  const { getFieldDecorator } = form;
  const formItemLayout = {
    labelCol: {
      xs: { span: 24 },
      sm: { span: 8 },
    },
    wrapperCol: {
      xs: { span: 24 },
      sm: { span: 16 },
    },
  };
  function onHandle() {
    onHandleOk(form);
  }
  return (
    <Modal
      title="批量修改部门"
      visible={visible}
      onCancel={onHandleCancel}
      onOk={onHandle}
    >
      <Form>
        <FormItem  {...formItemLayout}
          label='选中人数'
        >
          <div>{choosePersonNumber}人</div>
        </FormItem>
        <FormItem
          {...formItemLayout}
          label='部门名称'
        >
          {getFieldDecorator('department_id', {
            rules: [
              { required: true, message: '部门名称' },
            ],
          })(
            <HigherDepartment
              currentDefaultInfo={currentDataInfo}
              dataSource={dataSource}
            />
          )}
        </FormItem>
      </Form>

    </Modal >
  )
}
export default Form.create<BatchEditDepartmentProps>()(BatchEditDepartment);
