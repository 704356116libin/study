import React from 'react';
import { Switch } from 'antd'

interface SwitchList{
    item: {
        name: string;
        show: boolean;
    }
    onChange: (checked: boolean, name: string) => void;
  }

/**
 * 展示用户信息组件 接受onChange方法, item 属性
 * @param props SwitchList
 */
export default function SwitchList({onChange, item}: SwitchList){
  function switchChange(checked: boolean) {
    onChange(checked, item.name);
  }
  return (
    <p app-name={item.name}>
        <span>{item.name}</span>
        <span style={{ float: 'right' }}>
            <Switch checkedChildren="开" unCheckedChildren="关" defaultChecked={item.show}  onChange={switchChange} />
        </span>
    </p>
  )
}

