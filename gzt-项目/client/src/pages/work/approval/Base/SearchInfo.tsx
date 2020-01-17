import * as React from 'react';
import { Icon, Input } from 'antd';
export default function SearchInfo() {
  function onChangeSearchValue(e: any) {
    console.log(e.target.value,850541);
  }
  return (
    <Input
      placeholder="请输入内容"
      prefix={<Icon type="search" style={{ color: 'rgba(0,0,0,.25)' }} />}
      onChange={onChangeSearchValue}
      size="default"
      allowClear={true}
      onPressEnter={e => onChangeSearchValue(e)}
      style={{ width: '200px', marginLeft: '30px' }} />
  )
}
