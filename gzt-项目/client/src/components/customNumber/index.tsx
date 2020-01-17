import React, { useState, useEffect } from 'react';
import { Button, Input, Icon } from 'antd';
import S from './index.module.scss';

export interface Rule {
  label?: string;
}
export interface CustomNumberProps {
  rule?: Rule
}

export default function CustomNumber({
  rule = {}
}: CustomNumberProps) {

  const [label, setLabel]: [string | undefined, any] = useState('');
  const [custom, setCustom] = useState(false);

  useEffect(() => {
    if (rule.label) {
      setCustom(true);
    }
  }, []);

  if (!custom) {
    return (
      <Button type="primary" onClick={() => setCustom(true)}>添加编号</Button>
    )
  } else {

    return (
      <div>
        <div style={{ position: 'relative', width: '95px', display: 'inline-block', marginRight: '10px' }} >
          <Input value={label}
            maxLength={4}
            onChange={(e) => setLabel(e.target.value)}
            onBlur={(e) => e.target.value === '' && setCustom(false)}
          />
          <Icon type="delete" className={S.delete} onClick={() => setCustom(false)} />
        </div>
        <div style={{ display: "inline-block" }}>
          编号实例展示：<span>{label}</span>—201812282222
        </div>
        <p style={{ marginBottom: 0, color: '#333' }}>编号标签可以设置比如：编号或"BH"1-4个字符</p>
      </div>
    )
  }
}