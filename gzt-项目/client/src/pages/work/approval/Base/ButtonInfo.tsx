import * as React from 'react';
import { Radio } from 'antd';
const { useState, useEffect } = React;
const RadioButton = Radio.Button;
const RadioGroup = Radio.Group;
interface ApprovedButtonProps {
  onChangeApprovedInfo: any,
  version:number
}

export default function ButtonInfo({
  onChangeApprovedInfo,
  version

}: ApprovedButtonProps) {
  const [radioValue, setradioValue] = useState('all');
  useEffect(() => {
    setradioValue('all')
  }, [version])
  function onChange(e: any) {
    const status = e.target.value;
    onChangeApprovedInfo(status);
    setradioValue(status);
  }

  return (
    <RadioGroup onChange={onChange} value={radioValue} style={{ marginLeft: "10px" }}>
      <RadioButton value="all">全部</RadioButton>
      <RadioButton value="approvalPassed">审批通过</RadioButton>
      <RadioButton value="approval">审批中</RadioButton>
      <RadioButton value="approvalNotPassed">审批不通过</RadioButton>
      <RadioButton value="revoked">已撤销</RadioButton>
    </RadioGroup>
  )
}
