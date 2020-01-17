import React from 'react';
import { Button, Form, Input, Upload, Icon, DatePicker, Radio, Checkbox, Select } from 'antd';
import { FormItemData } from './index';

const FormItem = Form.Item;
const RadioGroup = Radio.Group;
const CheckboxGroup = Checkbox.Group;
const { RangePicker } = DatePicker;
const { Option } = Select;

/**
 * 不带form的
 */
export default function SimulationForm({
  formData = [],
  ...rest
}: {
  formData: FormItemData[];
  [propName: string]: any;
}) {

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
    <div {...rest}>
      {
        formData.map(({ type, field }, k) => {
          switch (type) {
            case 'INPUT':
              return (
                <FormItem
                  key={type + k}
                  {...formItemLayout}
                  label={field.label}
                  required={field.required}
                >
                  <Input placeholder={field.placeholder} />
                </FormItem>
              )
            case 'TEXTAREA':
              return (
                <FormItem
                  key={type + k}
                  {...formItemLayout}
                  label={field.label}
                  required={field.required}
                >
                  <Input.TextArea placeholder={field.placeholder} />
                </FormItem>
              )
            case 'RADIO':
              return (
                <FormItem
                  key={type + k}
                  {...formItemLayout}
                  label={field.label}
                  required={field.required}
                  className="vertical-item"
                >
                  <RadioGroup>
                    {field.radioOptions.map(({ key, value }: any) => (
                      <Radio
                        style={{ display: 'block', height: '30px', lineHeight: '30px' }}
                        key={key}
                        value={value}
                      >
                        {value}
                      </Radio>
                    ))}
                  </RadioGroup>
                </FormItem>
              )
            case 'CHECKBOX':
              return (
                <FormItem
                  key={type + k}
                  {...formItemLayout}
                  label={field.label}
                  required={field.required}
                  className="vertical-item"
                >
                  <CheckboxGroup>
                    {field.checkboxOptions.map(({ key, value }: any) => (
                      <Checkbox
                        style={{ display: 'block', marginLeft: 0, height: '30px', lineHeight: '30px' }}
                        key={key}
                        value={value}
                      >
                        {value}
                      </Checkbox>
                    ))}
                  </CheckboxGroup>
                </FormItem>
              )
            case 'DATEPICKER':
              return (
                <FormItem
                  key={type + k}
                  {...formItemLayout}
                  label={field.label}
                  required={field.required}
                >
                  <DatePicker
                    showTime
                    format="YYYY-MM-DD HH:mm:ss"
                    placeholder="选择时间"
                  />
                </FormItem>

              )
            case 'DATERANGE':
              return (
                <FormItem
                  key={type + k}
                  {...formItemLayout}
                  label={field.label}
                  required={field.required}
                >
                  <RangePicker
                    showTime
                    format="YYYY-MM-DD"
                    placeholder={['开始时间', '结束时间']}
                  />
                </FormItem>

              )
            case 'NUMBER':
              return (
                <FormItem
                  key={type + k}
                  {...formItemLayout}
                  label={field.label}
                  required={field.required}
                >
                  <Input addonAfter={field.unit} type="number" placeholder={field.placeholder} />
                </FormItem>
              )
            case 'MONEY':
              return (
                <FormItem
                  key={type + k}
                  {...formItemLayout}
                  label={field.label}
                  required={field.required}
                >
                  <Input type="number" placeholder={field.placeholder} />
                </FormItem>
              )
            case 'SELECT':
              return (
                <FormItem
                  key={type + k}
                  {...formItemLayout}
                  label={field.label}
                  required={field.required}
                >
                  <Select>
                    {field.selectOptions.map(({ key, value }: any) => (
                      <Option
                        key={key}
                        value={value}
                      >
                        {value}
                      </Option>
                    ))}
                  </Select>
                </FormItem>
              )
            case 'ANNEX':
              return (
                <FormItem
                  key={type + k}
                  {...formItemLayout}
                  label={field.label}
                  required={field.required}
                >
                  <Upload>
                    <Button>
                      <Icon type="upload" />上传附件
                      </Button>
                  </Upload>
                </FormItem>
              )
            default:
              return '暂时还没有这个表单组件哦'
          }
        })
      }
    </div>
  )
}