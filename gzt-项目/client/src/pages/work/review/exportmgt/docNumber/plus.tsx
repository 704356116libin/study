import React from 'react';
import { Select, Input } from 'antd';

export interface PlusProps {
  value: any;
  onChange: Function;
}

export default function Plus({
  value,
  onChange
}: PlusProps) {

  function handleChange(type: string, val: string) {
    if (type === 'startNumber' && isNaN(Number(val))) {
      return
    }
    const nextValue = {
      ...value,
      [type]: val
    }
    onChange && onChange(nextValue)
  }

  const { dight, startNumber, step } = value;

  return (
    <div>
      <div>增长值</div>
      <div className="menu-item-con">
        <div style={{ marginBottom: '12px', textAlign: 'right', color: '#1890ff' }}>{`${startNumber}`.padStart(dight, '0')}</div>
        <div className="plus-item">
          <span className="plus-item-label">位数</span>
          <Select style={{ width: 60 }} size="small" value={dight} onChange={(val: string) => handleChange('dight', val)}>
            {
              [...Array(9).keys()].map((index) => (
                <Select.Option key={index + 1} value={index + 1}>{index + 1}</Select.Option>
              ))
            }
          </Select>
        </div>
        <div className="plus-item">
          <span className="plus-item-label">起始值</span>
          <Input size="small" style={{ width: 60 }} value={startNumber} onChange={(e) => handleChange('startNumber', e.target.value)} />
        </div>
        <div className="plus-item">
          <span className="plus-item-label">步增长量</span>
          <Select style={{ width: 60 }} size="small" value={step} onChange={(val: string) => handleChange('step', val)}>
            {
              [...Array(9).keys()].map((index) => (
                <Select.Option key={index + 1} value={index + 1}>{index + 1}</Select.Option>
              ))
            }
          </Select>
        </div>
      </div>
    </div>
  )
}
