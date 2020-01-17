import React from 'react';
import { Modal, Form, Select } from 'antd';
import { FormComponentProps } from 'antd/lib/form';
const FormItem = Form.Item;
const { Option } = Select;

export interface BatchEditPositionProps extends FormComponentProps {
  visible: boolean;
  onHandleCancel: any;
  choosePersonNumber: number;
  rolesInfo: any;
  onHandleOk: any
}

function BatchEditPosition(props: BatchEditPositionProps) {
  const { visible, onHandleCancel, form, choosePersonNumber, rolesInfo, onHandleOk } = props;
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
  return (

    <Modal
      title="批量修改职务"
      visible={visible}
      onCancel={onHandleCancel}
      onOk={() => onHandleOk(form)}
    >
      <Form>
        <FormItem  {...formItemLayout}
          label='选中人数'
        >
          <div>{choosePersonNumber}人</div>
        </FormItem>
        <FormItem
          {...formItemLayout}
          label='选择职务'
        >
          {getFieldDecorator('role_id', {
            rules: [
              { required: true, message: '选择职务' },
            ],
          })(
            <Select placeholder="请选择职务">
              {rolesInfo && rolesInfo.data.map((option: any, index: any) => (
                <Option value={option.id} key={index}>{option.name}</Option>
              ))}
            </Select>
          )}
        </FormItem>
      </Form>
    </Modal>
  )
}
export default Form.create<BatchEditPositionProps>()(BatchEditPosition);
