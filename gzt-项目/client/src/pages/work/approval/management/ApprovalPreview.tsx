import React from 'react';

import { Button, Form, Input, Modal } from 'antd';
import { FormComponentProps } from 'antd/lib/form';
// import SelectApprover from '../../../../components/selectApprover';
import ShowApprovers from '../../../../components/selectApprover/showApprovers';
import SelectCc from '../../../../components/selectCc';
import { useEffect } from 'react';
import ParseFormItem from '../../../../components/formLibrary/parseFormItem';
import './index.scss';

const FormItem = Form.Item;

export interface ApprovalPreviewProps extends FormComponentProps {
  formDatas?: any;
  visible: boolean;
  onCancel: any;
}

/**
 * 可用于预览新建审批模板
 * @param props ParseFormProps
 */
function ApprovalPreview(props: ApprovalPreviewProps) {

  const { getFieldDecorator, setFieldsValue } = props.form;
  const { visible, onCancel, formDatas } = props;
  console.log(formDatas);

  useEffect(() => {
    const { cc_users, description, form_template, process_template, name } = formDatas;
    setFieldsValue({
      description,
      name,
      form_template,
      process_template,
      cc_users
    })
  }, [formDatas]);

  const formItemLayout = {
    labelCol: {
      xs: { span: 24 },
      sm: { span: 6 },
    },
    wrapperCol: {
      xs: { span: 24 },
      sm: { span: 12 },
    },
  };
  const formApprovalItemLayout = {
    labelCol: {
      xs: { span: 24 },
      sm: { span: 6 },
    },
    wrapperCol: {
      xs: { span: 24 },
      sm: { span: 18 }
    },
  }
  return (
    <Modal
      title="表单预览"
      visible={visible}
      width={720}
      onCancel={onCancel}
      maskClosable={false}
      footer={
        <div style={{ textAlign: 'left' }}>
          <Button type="primary" onClick={onCancel}>确定</Button>
        </div>
      }
    >
      <Form style={{ marginTop: '30px', width: '100%', maxHeight: '600px', overflow: 'auto' }}>
        {(() => {
          if (formDatas) {
            const { approval_method, description, form_template, name } = formDatas;
            return (
              <div>
                <FormItem
                  {...formItemLayout}
                  label='审批名称'
                >
                  {getFieldDecorator('name', {
                    initialValue: name
                  })(
                    <Input />
                  )}
                </FormItem>
                <FormItem
                  {...formItemLayout}
                  label='流程说明'
                >
                  {getFieldDecorator('description', {
                    initialValue: description
                  })(
                    <Input />
                  )}
                </FormItem>
                <ParseFormItem formData={form_template} />
                <FormItem
                  {...formApprovalItemLayout}
                  label='审批人员:'
                  style={{ display: approval_method === '固定流程' ? "block" : "none" }}
                >
                  {getFieldDecorator('process_template',
                    {
                      valuePropName: 'approvers',
                    }
                  )(
                    <ShowApprovers />
                    // approval_method === "固定流程" ? <ShowApprovers /> : <SelectApprover />
                  )}
                </FormItem>
                <FormItem
                  {...formApprovalItemLayout}
                  label="抄送人员"
                >
                  {getFieldDecorator('cc_users', {
                    valuePropName: 'ccInfo'
                  })(<SelectCc />)}
                </FormItem>
              </div>
            )
          } else {
            return null;
          }
        })()
        }
      </Form>
    </Modal>
  )
}
export default Form.create<ApprovalPreviewProps>()(ApprovalPreview) 