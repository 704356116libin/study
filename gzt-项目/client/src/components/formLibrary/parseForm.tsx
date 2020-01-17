// 解析拖拽表单最终拖出来的数据，或者从数据库拿到的数据为表单

import React from 'react';
import { Button, Form, Input, Upload, Icon, DatePicker, Radio, Checkbox, Select } from 'antd';
import { FormComponentProps } from 'antd/lib/form';
import { FormItemData } from './index';
import './index.scss';

const FormItem = Form.Item;
const RadioGroup = Radio.Group;
const CheckboxGroup = Checkbox.Group;
const { RangePicker } = DatePicker;
const { Option } = Select;

export interface ParseFormProps extends FormComponentProps {
  formData?: FormItemData[];
}

/**
 * 解析FormLibrary拖拽完成后的数据
 * 可用于预览,带form
 * @param props ParseFormProps
 */
function ParseForm(props: ParseFormProps) {

  const { getFieldDecorator } = props.form;
  // getFormData
  const formData = props.formData || [];
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

  return (
    <Form style={{ marginTop: '30px', width: '100%', maxHeight: '600px', overflow: 'auto' }}>
      {
        formData.map((item, k) => {
          switch (item.type) {
            case 'INPUT':
              return (
                <FormItem
                  key={item.type + k}
                  {...formItemLayout}
                  label={item.field.label}
                >
                  {getFieldDecorator(`cf-input-${k}`, {
                    rules: [{
                      required: item.field.required, message: `请输入${item.field.label}!`,
                    }],
                  })(
                    <Input placeholder={item.field.placeholder} />
                  )}
                </FormItem>
              )
            case 'TEXTAREA':
              return (
                <FormItem
                  key={item.type + k}
                  {...formItemLayout}
                  label={item.field.label}
                >
                  {getFieldDecorator(`cf-textarea-${k}`, {
                    rules: [{
                      required: item.field.required, message: `请输入${item.field.label}!`,
                    }],
                  })(
                    <Input.TextArea placeholder={item.field.placeholder} />
                  )}
                </FormItem>
              )
            case 'RADIO':
              return (
                <FormItem
                  key={item.type + k}
                  {...formItemLayout}
                  label={item.field.label}
                  className="vertical-item"
                >
                  {getFieldDecorator(`cf-radio-${k}`, {
                    rules: [{
                      required: item.field.required, message: `请输入${item.field.label}!`,
                    }],
                  })(
                    <RadioGroup>
                      {item.field.radioOptions.map(({ key, value }: any) => (
                        <Radio
                          style={{ display: 'block', height: '30px', lineHeight: '30px' }}
                          key={key}
                          value={value}
                        >
                          {value}
                        </Radio>
                      ))}
                    </RadioGroup>
                  )}
                </FormItem>
              )
            case 'CHECKBOX':
              return (
                <FormItem
                  key={item.type + k}
                  {...formItemLayout}
                  label={item.field.label}
                  className="vertical-item"
                >
                  {getFieldDecorator(`checkbox${k}`, {
                    rules: [{
                      required: item.field.required, message: `请输入${item.field.label}!`,
                    }],
                  })(
                    <CheckboxGroup>
                      {item.field.checkboxOptions.map(({ key, value }: any) => (
                        <Checkbox
                          style={{ display: 'block', marginLeft: 0, height: '30px', lineHeight: '30px' }}
                          key={key}
                          value={value}
                        >
                          {value}
                        </Checkbox>
                      ))}
                    </CheckboxGroup>
                  )}
                </FormItem>
              )
            case 'DATEPICKER':
              return (
                <FormItem
                  key={item.type + k}
                  {...formItemLayout}
                  label={item.field.label}
                >
                  {getFieldDecorator(`cf-datapicker-${k}`, {
                    rules: [{
                      required: item.field.required, message: `请输入${item.field.label}!`,
                    }],
                  })(
                    <DatePicker
                      showTime
                      format="YYYY-MM-DD HH:mm:ss"
                      placeholder="选择时间"
                    />
                  )}
                </FormItem>

              )
            case 'DATERANGE':
              return (
                <FormItem
                  key={item.type + k}
                  {...formItemLayout}
                  label={item.field.label}
                >
                  {getFieldDecorator(`cf-datarange-${k}`, {
                    rules: [{
                      required: item.field.required, message: `请输入${item.field.label}!`,
                    }],
                  })(
                    <RangePicker
                      showTime
                      format="YYYY-MM-DD"
                      placeholder={['开始时间', '结束时间']}
                    />
                  )}
                </FormItem>

              )
            case 'NUMBER':
              return (
                <FormItem
                  key={item.type + k}
                  {...formItemLayout}
                  label={item.field.label}
                >
                  {getFieldDecorator(`cf-number-${k}`, {
                    rules: [{
                      required: item.field.required, message: `请输入${item.field.label}!`,
                    }],
                  })(
                    <Input addonAfter={item.field.unit} type="number" placeholder={item.field.placeholder} />
                  )}
                </FormItem>
              )
            case 'MONEY':
              return (
                <FormItem
                  key={item.type + k}
                  {...formItemLayout}
                  label={item.field.label}
                >
                  {getFieldDecorator(`cf-money-${k}`, {
                    rules: [{
                      required: item.field.required, message: `请输入${item.field.label}!`,
                    }],
                  })(
                    <Input type="number" placeholder={item.field.placeholder} />
                  )}
                </FormItem>
              )
            case 'SELECT':
              return (
                <FormItem
                  key={item.type + k}
                  {...formItemLayout}
                  label={item.field.label}
                >
                  {getFieldDecorator(`cf-select-${k}`, {
                    rules: [{
                      required: item.field.required, message: `请输入${item.field.label}!`,
                    }],
                  })(
                    <Select>
                      {item.field.selectOptions.map(({ key, value }: any) => (
                        <Option
                          key={key}
                          value={value}
                        >
                          {value}
                        </Option>
                      ))}
                    </Select>
                  )}
                </FormItem>
              )
            case 'ANNEX':
              return (
                <FormItem
                  key={item.type + k}
                  {...formItemLayout}
                  label={item.field.label}
                >
                  {getFieldDecorator(`cf-annex-${k}`, {
                    rules: [{
                      required: item.field.required, message: `请输入${item.field.label}!`,
                    }],
                  })(
                    <Upload>
                      <Button>
                        <Icon type="upload" />上传附件
                      </Button>
                    </Upload>
                  )}
                </FormItem>
              )
            default:
              return '暂时还没有这个表单组件哦'
          }
        })
      }
    </Form>


  )
}
export default Form.create<ParseFormProps>()(ParseForm) 