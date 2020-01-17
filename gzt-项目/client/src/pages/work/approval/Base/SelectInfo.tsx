import * as React from 'react';
import { Select } from 'antd';
const { useState, useEffect } = React;
const { Option } = Select;

interface SelectInfoProps {
  onChangeSelected: any,
  dataSource?: any,
  version: number
}
export default function SelectInfo({
  onChangeSelected,
  dataSource,
  version
}: SelectInfoProps) {
  const [searchValue, setsearchValue] = useState('all');
  useEffect(() => {
    setsearchValue('all')
  }, [version])
  function handleChange(type_id: string) {
    onChangeSelected(type_id)
    setsearchValue(type_id);
  }
  return (
    <Select style={{ marginLeft: '30px', width: 150 }} value={searchValue} onChange={handleChange}>
      <Option value="all">全部</Option>
      {
        dataSource && dataSource.map((item: any, index: any) => {
          const { type_id, name } = item
          return (
            <Option value={type_id} key={index}>{name}</Option>
          )
        })
      }
    </Select>
  )
}
