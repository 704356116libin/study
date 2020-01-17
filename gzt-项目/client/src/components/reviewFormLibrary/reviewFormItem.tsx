import React, { useState, Ref, forwardRef, useImperativeHandle, useEffect } from 'react';
import { Select, Input, DatePicker, Radio, InputNumber } from 'antd';
import NumericInput from '../numericInput';
import moment from 'moment';

const { Option } = Select;
const RadioGroup = Radio.Group;

export const itemType = (someBaseData?: any, onChange?: (e: any) => void, disabled?: boolean ) => {
  // 用于预览的假数据
  const previewBaseData = {
    action_label: [
      '火速处理',
      '时间紧急',
    ],
    count_type: [
      "预算",
      "概算",
      "预算价",
      "专项款",
      "预支款",
      "其他"
    ],
    project_type: [
      "市政工程",
      "园林",
      "水利"
    ],
    project_construction: [
      "交通局",
      "农业局",
      "水利局",
      "扶贫办",
    ],
    service_department: [
      "经济开发部",
      "农业部"
    ],
  }
  // 从服务器端获取到的真实数据
  const { action_label, count_type, project_type, project_construction,service_department } = someBaseData || previewBaseData;

  return {
    "分类": {
      formId: 'category',
      buildOption: (required: boolean, value: any) => ({
        rules: [{
          required, message: '请选择类别!',
        }],
        initialValue: value
      }),
      component: (
        <Select
          showSearch
          placeholder="请选择分类"
          getPopupContainer={(triggerNode: any) => triggerNode.parentNode}
          onChange={onChange}
          disabled={disabled}
        >
          {
            count_type.map((category: string, index: number) => (
              <Select.Option key={index} value={category}>{category}</Select.Option>
            ))
          }
        </Select>
      )
    },
    "工程分类": {
      formId: 'project_category',
      buildOption: (required: boolean, value: any) => ({
        rules: [{
          required, message: '请选择工程分类!',
        }],
        initialValue: value
      }),
      component: (
          <Select
              onChange={onChange}
              showSearch
              placeholder="请选择工程分类"
              getPopupContainer={(triggerNode: any) => triggerNode.parentNode}
              disabled={disabled}
          >
            {
              project_type.map((project: string, index: number) => (
                  <Select.Option key={index} value={project}>{project}</Select.Option>
              ))
            }
          </Select>
      )
    },
    "建设单位": {
      formId: 'project_construction',
      buildOption: (required: boolean, value: any) => ({
        rules: [{
          required, message: '请选择建设单位!',
        }],
        initialValue: value
      }),
      component: (
        <Select
          onChange={onChange}
          showSearch
          placeholder="请选择建设单位"
          getPopupContainer={(triggerNode: any) => triggerNode.parentNode}
          disabled={disabled}
        >
          {
            project_construction.map((project: string, index: number) => (
              <Select.Option key={index} value={project}>{project}</Select.Option>
            ))
          }
        </Select>
      )
    },
    "项目名称": {
      formId: 'project_name',
      buildOption: (required: boolean, value: any) => ({
        rules: [{
          required, message: '请输入工程名称!',
        }],
        initialValue: value
      }),
      component: (
        <Input placeholder="请输入工程名称" onChange={onChange} disabled={disabled}/>
      )
    },
    "送审金额": {
      formId: 'amount_of_review',
      buildOption: (required: boolean, value: any) => ({
        rules: [{
          required, message: '请输入送审金额!',
        }],
        initialValue: value
      }),
      component: (
        <NumericInput
          addonAfter="元"
          placeholder="请输入送审金额"
          onChange={onChange}
          disabled={disabled}
        />
      )
    },
    "审定金额": {
      formId: 'approved_amount',
      buildOption: (required: boolean, value: any) => ({
        rules: [{
          required, message: '请输入送审金额!',
        }],
        initialValue: value
      }),
      component: (
        <NumericInput
          addonAfter="元"
          placeholder="请输入送审金额"
          onChange={onChange}
          disabled={disabled}
        />
      )
    },
    "送审时间": {
      formId: 'submit_time',
      buildOption: (required: boolean, value: any) => ({
        rules: [{
          required, message: '请输入送审时间!',
        }],
        initialValue: moment(value)
      }),
      component: (
        <DatePicker
          onChange={onChange}
          allowClear={false}
          showTime
          format="YYYY-MM-DD HH:mm:ss"
          getCalendarContainer={(triggerNode: any) => triggerNode.parentNode}
          disabled={disabled}
        />
      )
    },
    "业务科室": {
      formId: 'service_department',
      buildOption: (required: boolean, value: any) => ({
        rules: [{
          required, message: '请输入送审业务负责科室!',
        }],
        initialValue: value
      }),
      component: (
        <Select
          showSearch
          getPopupContainer={(triggerNode: any) => triggerNode.parentNode}
          onChange={onChange}
          disabled={disabled}
        >
          {
            service_department.map((department: string, index: number) => (
              <Option key={index} value={department}>{department}</Option>
            ))
          }
        </Select>
      )
    },
    "标签": {
      formId: 'action_label',
      buildOption: (required: boolean, value: any) => ({
        rules: [{
          required, message: '请选择标签!',
        }],
        initialValue: value
      }),
      component: (
        <Select
          onChange={onChange}
          showSearch
          getPopupContainer={(triggerNode: any) => triggerNode.parentNode}
          disabled={disabled}
        >
          {
            action_label.map((label: string, index: number) => (
              <Option key={index} value={label}>{label}</Option>
            ))
          }
        </Select>
      )
    },
    "完成时间": {
      formId: 'limit_time',
      buildOption: (required: boolean, value: any) => ({
        rules: [{
          required, message: '请选择完成时间!',
        }],
        initialValue: value
      }),
      component: forwardRef(({ value, onChange }: any, ref: Ref<any>) => {

        // 在 antd form 表单中自定义 function 表单控件需要
        useImperativeHandle(ref, () => ({}));

        const [customDays, setCustomDays] = useState([3, 7, 15].includes(value) ? 30 : value);
        const [days, setDays] = useState(value);

        useEffect(() => {
          if (!value) {
            setDays(3)
          }
          if ([3, 7, 15].includes(days)) {
            setDays(value);
          } else {
            setCustomDays(value)
          }
        }, [value])

        function handleChange(customDay: any) {
          setCustomDays(customDay);
          onChange && onChange(customDay);
        }
        function handleRadioChange(e: any) {
          if (e.target.value === "customDays") {
            setDays(customDays);
            onChange && onChange(customDays);
          } else {
            setDays(e.target.value);
            onChange && onChange(e.target.value);
          }
        }
        const checked = [3, 7, 15].includes(days) ? days : 'customDays';

        return (
          <RadioGroup value={checked} onChange={handleRadioChange} disabled={disabled}>
            <Radio value={3}>3天</Radio>
            <Radio value={7}>7天</Radio>
            <Radio value={15}>15天</Radio>
            <Radio value="customDays"><InputNumber value={customDays} disabled={checked !== 'customDays'} onChange={handleChange} /></Radio>
          </RadioGroup>
        )
      })
    }
  }
}
