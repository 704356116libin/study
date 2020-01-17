import React, { Ref, forwardRef, useImperativeHandle } from 'react';
import { Tooltip, Input } from "antd";
import './index.scss'
/**
 * 格式化数字
 * @param value 
 */
function formatNumber(value: string) {
  value += '';
  const list = value.split('.');
  const prefix = list[0].charAt(0) === '-' ? '-' : '';
  let num = prefix ? list[0].slice(1) : list[0];
  let result = '';
  while (num.length > 3) {
    result = `,${num.slice(-3)}${result}`;
    num = num.slice(0, num.length - 3);
  }
  if (num) {
    result = num + result;
  }
  return `${prefix}${result}${list[1] ? `.${list[1]}` : ''}元`;
}

/**
 * 结合 Tooltip 组件，实现一个数值输入框，方便内容超长时的全量展现。(拿的 antd 官网的 demo)
 * @param props 
 */
function NumericInput(props: any, ref: Ref<any>) {

  useImperativeHandle(ref, () => ({}));

  const onChange = (e: any) => {
    const { value } = e.target;
    const reg = /^-?(0|[1-9][0-9]*)(\.[0-9]*)?$/;
    if ((!Number.isNaN(value) && reg.test(value)) || value === '' || value === '-') {
      props.onChange(value);
    }
  }

  // '.' at the end or only '-' in the input box.
  const onBlur = () => {
    const { value, onBlur, onChange } = props;
    if ((value && value.charAt(value.length - 1) === '.') || value === '-') {
      onChange(value.slice(0, -1));
    }
    if (onBlur) {
      onBlur();
    }
  }

  const { value } = props;
  const title = value ? (
    <span className="numeric-input-title">
      {value !== '-' ? formatNumber(value) : '-'}
    </span>
  ) : '输入一个数字';

  return (
    <Tooltip
      trigger="focus"
      title={title}
      placement="topLeft"
      overlayClassName="numeric-input"
    >
      <Input
        maxLength={25}
        {...props}
        onChange={onChange}
        onBlur={onBlur}
      />
    </Tooltip>
  )
}
export default forwardRef(NumericInput)