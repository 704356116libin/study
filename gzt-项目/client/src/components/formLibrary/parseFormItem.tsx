import React, { useState, useEffect } from 'react';
import { Button, Form, Input, Upload, Icon, DatePicker, Radio, Checkbox, Select } from 'antd';
import { FormItemData, FormItemType } from './index';
import { WrappedFormUtils } from 'antd/lib/form/Form';
import update from 'immutability-helper';
import moment from 'moment';
import './index.scss';

const FormItem = Form.Item;
const RadioGroup = Radio.Group;
const CheckboxGroup = Checkbox.Group;
const { RangePicker } = DatePicker;
const { Option } = Select;

export interface ParseFormItemProps {
  formData?: FormItemData[];
  /** antd form 表单校验方法 */
  form?: WrappedFormUtils;
  onChange?: (formData: FormItemData[]) => void;
}

/**
 * 解析 FormLibrary 拖拽完成后的数据 或者从数据库拿到的原始数据
 * 可用于预览,带form
 */
export default function ParseFormItem(props: ParseFormItemProps) {

  const { getFieldDecorator, setFieldsValue } = props.form || {} as WrappedFormUtils;
  const formData = props.formData || [];
  const [allFormData, setAllFormData] = useState(formData);

  useEffect(() => { // 更新formData
    setAllFormData(formData)
  }, [props.formData]);

  const onChange = props.onChange;
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
  /** 有一个表单触发 onChange 更新全部数据 */
  function updateOverallData(e: any, k: any, type: FormItemType) {
    let value;
    // 日期和附件等较特殊的控件 特殊处理
    if (type === 'DATEPICKER' || type === 'DATERANGE' || 'CHECKBOX') {// 日期 日期区间
      value = e;
    } else if (type === 'ANNEX') {// 附件
      value = e.fileList
    } else {
      value = e.target.value
    }

    const nextFormData = update(allFormData, {
      [k]: {
        $merge: {
          value
        }
      }
    })
    // 把值传递出去
    onChange && onChange(nextFormData);
    // 更新状态
    setAllFormData(nextFormData)
  }
  /** 文件上传之前*/
  function beforeUpload(id: string, file: any, fileList: any[], ) {
    setFieldsValue && setFieldsValue({
      [id]: fileList
    })
    return false;
  }
  return (
    <>
      {
        formData.map(({ type, field, value: initialValue }, k) => {
          const rule = {// 校验规则
            required: field.required,
            message: `请输入${field.label}!`
          };

          switch (type) {
            case 'INPUT':
              return (
                <FormItem
                  key={type + k}
                  {...formItemLayout}
                  label={field.label}
                  required={field.required}
                >
                  {
                    // 存在的话校验，不存在的话不校验
                    getFieldDecorator
                      ? getFieldDecorator(`cf-input-${k}`,
                        initialValue
                          ? { rules: [rule], initialValue }
                          : { rules: [rule] }
                      )(
                        <Input
                          placeholder={field.placeholder}
                          onChange={(e) => updateOverallData(e, k, 'INPUT')}
                        />
                      )
                      : <Input placeholder={field.placeholder} />
                  }
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
                  {
                    getFieldDecorator
                      ? getFieldDecorator(`cf-textarea-${k}`,
                        initialValue
                          ? { rules: [rule], initialValue }
                          : { rules: [rule] }
                      )(
                        <Input.TextArea
                          placeholder={field.placeholder}
                          onChange={(e) => updateOverallData(e, k, 'TEXTAREA')}
                        />
                      )
                      : <Input.TextArea placeholder={field.placeholder} />
                  }
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
                  {
                    getFieldDecorator
                      ? getFieldDecorator(`cf-radio-${k}`,
                        initialValue
                          ? { rules: [rule], initialValue }
                          : { rules: [rule] }
                      )(
                        <RadioGroup>
                          {field.radioOptions.map(({ key, value }: any) => (
                            <Radio
                              style={{ display: 'block', height: '30px', lineHeight: '30px' }}
                              key={key}
                              value={value}
                              onChange={(e) => updateOverallData(e, k, 'RADIO')}
                            >
                              {value}
                            </Radio>
                          ))}
                        </RadioGroup>
                      )
                      : (
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
                      )
                  }
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
                  {
                    getFieldDecorator
                      ? getFieldDecorator(`cf-checkbox-${k}`,
                        initialValue
                          ? { rules: [rule], initialValue }
                          : { rules: [rule] }
                      )(
                        <CheckboxGroup onChange={(e) => updateOverallData(e, k, 'CHECKBOX')}>
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
                      )
                      : (
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
                      )
                  }
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
                  {
                    getFieldDecorator
                      ? getFieldDecorator(`cf-datapicker-${k}`,
                        initialValue
                          ? { rules: [rule], initialValue: moment(initialValue, 'YYYY-MM-DD HH:mm:ss') }
                          : { rules: [rule] }
                      )(
                        <DatePicker
                          showTime
                          format="YYYY-MM-DD HH:mm:ss"
                          placeholder="选择时间"
                          onChange={(_, dateString) => updateOverallData(dateString, k, 'DATEPICKER')}
                        />
                      )
                      : (
                        <DatePicker
                          showTime
                          format="YYYY-MM-DD HH:mm:ss"
                          placeholder="选择时间"
                        />
                      )
                  }
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
                  {
                    getFieldDecorator
                      ? getFieldDecorator(`cf-datarange-${k}`,
                        initialValue
                          ? { rules: [rule], initialValue: initialValue.map((dateString) => moment(dateString, 'YYYY-MM-DD HH:mm:ss')) }
                          : { rules: [rule] }
                      )(
                        <RangePicker
                          showTime
                          format="YYYY-MM-DD"
                          placeholder={['开始时间', '结束时间']}
                          onChange={(_, dateStrings) => updateOverallData(dateStrings, k, 'DATERANGE')}
                        />
                      ) : (
                        <RangePicker
                          showTime
                          format="YYYY-MM-DD"
                          placeholder={['开始时间', '结束时间']}
                        />
                      )
                  }
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
                  {
                    getFieldDecorator
                      ? getFieldDecorator(`cf-number-${k}`,
                        initialValue
                          ? { rules: [rule], initialValue }
                          : { rules: [rule] }
                      )(
                        <Input addonAfter={field.unit} type="number" placeholder={field.placeholder} onChange={(e) => updateOverallData(e, k, 'NUMBER')} />
                      ) : (
                        <Input addonAfter={field.unit} type="number" placeholder={field.placeholder} />
                      )
                  }
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
                  {
                    getFieldDecorator
                      ? getFieldDecorator(`cf-money-${k}`,
                        initialValue
                          ? { rules: [rule], initialValue }
                          : { rules: [rule] }
                      )(
                        <Input type="number" placeholder={field.placeholder} onChange={(e) => updateOverallData(e, k, 'MONEY')} />
                      ) : (
                        <Input type="number" placeholder={field.placeholder} />
                      )
                  }
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
                  {
                    getFieldDecorator
                      ? getFieldDecorator(`cf-select-${k}`,
                        initialValue
                          ? { rules: [rule], initialValue }
                          : { rules: [rule] }
                      )(
                        <Select onChange={(e) => updateOverallData(e, k, 'SELECT')}>
                          {field.selectOptions.map(({ key, value }: any) => (
                            <Option
                              key={key}
                              value={value}
                            >
                              {value}
                            </Option>
                          ))}
                        </Select>
                      ) : (
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
                      )
                  }
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
                  {
                    getFieldDecorator
                      ? getFieldDecorator(`cf-annex-${k}`,
                        initialValue
                          ? { rules: [rule], initialValue }
                          : { rules: [rule] }
                      )(
                        <Upload
                          listType="picture"
                          beforeUpload={(...args) => beforeUpload(`cf-annex-${k}`, ...args)}
                          onChange={(args) => updateOverallData(args, k, 'ANNEX')}
                        >
                          <Button>
                            <Icon type="upload" />上传附件
                              </Button>
                        </Upload>
                      )
                      : (
                        <Upload listType="picture">
                          <Button>
                            <Icon type="upload" />上传附件
                          </Button>
                        </Upload>
                      )
                  }
                </FormItem>
              )
            default:
              return '暂时还没有这个表单组件哦'
          }
        })
      }
    </>
  )
}
