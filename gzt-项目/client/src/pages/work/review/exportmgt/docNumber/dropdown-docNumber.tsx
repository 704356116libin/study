import React, { useState } from 'react';
import { Dropdown, Menu, Input } from "antd";
import classnames from 'classnames';
import moment from 'moment';
import Plus from './plus';

export interface DocNumberDropdownProps {
  value: any;
  onChange: Function;
  onClose: Function;
}

export default function DocNumberDropdown({
  value,
  onChange,
  onClose
}: DocNumberDropdownProps) {

  const [docNumberMenuVisible, setDocNumberMenuVisible] = useState(false);

  /** 增长值 */
  function handlePlusChange(value: any) {
    const { dight, startNumber } = value;
    onChange && onChange({
      type: 'plus',
      title: '增长值',
      value: `${startNumber}`.padStart(dight, '0'),
      rule: value
    })
  }
  /** 标签 */
  function handleLabelChange(e: any) {
    const value = e.target.value;
    onChange && onChange({
      type: 'label',
      title: '标签',
      value
    })
  }
  /** 处理日期 */
  function handleDate(type: string, date: string) {
    onChange && onChange({
      type: 'date',
      title: '日期',
      value: date,
      rule: type
    })
  }
  /** 点击 x 号 */
  function handleRileClose(e: React.MouseEvent<HTMLElement, MouseEvent>) {
    e.stopPropagation();
    onClose && onClose()
  }

  /** 控制文号规则菜单项显示隐藏 */
  function handleDocNumberMenuVisibleChange(flag: boolean) {
    setDocNumberMenuVisible(flag);
  }

  /** 更改激活项 */
  function handleMenuChange({ item, key: type }: any) {
    if (value.type !== type) {
      // 重置
      let nextValue;
      if (type === 'label') {
        nextValue = {
          type: 'label',
          title: '标签',
          value: 'BQ'
        }
      } else if (type === 'date') {
        nextValue = {
          type: 'date',
          title: '日期',
          value: '2019',
          rule: '年'
        }
      } else if (type === 'plus') {
        nextValue = {
          type: 'plus',
          title: '增长值',
          value: '0001',
          rule: {
            dight: 4,
            startNumber: 1,
            step: 1,
          }
        }
      }
      onChange && onChange(nextValue)
    }
  }

  const menu = (
    <Menu style={{ width: 130 }} onClick={handleMenuChange}>
      <Menu.Item key="label" className={classnames('menu-item', value.type === 'label' ? 'active' : '')}>
        <div>标签</div>
        <div className="menu-item-con">
          <Input value={value.type === 'label' && value.value} onChange={handleLabelChange} />
          <div>比如：标签或"BQ"或"（",1-6个字符,可以包含符号</div>
        </div>
      </Menu.Item>
      <Menu.Item key="date" className={classnames('menu-item', value.type === 'date' ? 'active' : '')}>
        <div>日期</div>
        <div className="menu-item-con">
          <div className={classnames('date-item', value.rule === '年' ? 'active' : '')} onClick={() => handleDate('年', moment().format('YYYY'))}>年{moment().format('YYYY')}</div>
          <div className={classnames('date-item', value.rule === '年月' ? 'active' : '')} onClick={() => handleDate('年月', moment().format('YYYYMM'))}>年月{moment().format('YYYYMM')}</div>
          <div className={classnames('date-item', value.rule === '年-月' ? 'active' : '')} onClick={() => handleDate('年-月', moment().format('YYYY-MM'))}>年-月{moment().format('YYYY-MM')}</div>
          <div className={classnames('date-item', value.rule === '年月日' ? 'active' : '')} onClick={() => handleDate('年月日', moment().format('YYYYMMDD'))}>年月日{moment().format('YYYYMMDD')}</div>
          <div className={classnames('date-item', value.rule === '年-月-日' ? 'active' : '')} onClick={() => handleDate('年-月-日', moment().format('YYYY-MM-DD'))}>年-月-日{moment().format('YYYY-MM-DD')}</div>
        </div>
      </Menu.Item>
      <Menu.Item key="plus" className={classnames('menu-item', value.type === 'plus' ? 'active' : '')}>
        <Plus value={value.type === 'plus' && value.rule} onChange={handlePlusChange} />
      </Menu.Item>
    </Menu>
  )

  return (
    <Dropdown
      overlay={menu}
      onVisibleChange={handleDocNumberMenuVisibleChange}
      visible={docNumberMenuVisible}
      trigger={['click']}
    >
      <div className="doc-number-wrapper">
        <div>{value.title}</div>
        <div className="doc-number-value">{value.type === 'date' ? `${value.rule}${value.value}` : value.value}</div>
        <i className="doc-number-close" onClick={handleRileClose}>x</i>
      </div>
    </Dropdown>
  )
}