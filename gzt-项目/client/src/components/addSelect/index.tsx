import React, { useState, useRef, useImperativeHandle, forwardRef } from 'react';
import { Icon, Input } from 'antd';
import Select, { SelectProps, SelectValue } from 'antd/lib/select';
import 'antd/lib/select/style/index.css';
const { Option } = Select;
/**
 * 
 */
export interface AddSelectProps extends SelectProps<SelectValue> {
  // allowAdd?: boolean;
  children?: any;
  /**
   * 新增选项回调函数,接收新增的value
   */
  onAdd?: (value: string) => void;
}

function AddSelect({
  children,
  onAdd,
  value,
  onChange,
  onSelect,
  dropdownMatchSelectWidth = true,
  labelInValue = false,
  ...restProps
}: AddSelectProps, ref: React.Ref<any>) {

  const select: any = useRef(null);
  const [inputFocus, setInputFocus]: any = useState(false);
  const [warpperEnter, setWarpperEnter]: any = useState(false);
  const [selectOpen, setSelectOpen] = useState(false);
  const [inputSort, setInputSort]: any = useState(false);
  const [addOptionValue, setAddOptionValue]: any = useState('');

  useImperativeHandle(ref, () => ({}));

  function onSelectChange(selectValue: SelectValue, options: any) {
    if (selectValue !== '自定义') {
      onChange && onChange(selectValue, options);
    }
  }

  function onSelectSelect(selectValue: SelectValue, options: any) {
    if (selectValue !== '自定义') {
      onSelect && onSelect(selectValue as string, options);
      setImmediate(() => {
        select.current.blur()
      })
    }
  }

  function onSelectFocus() {
    setSelectOpen(true);
  }

  function onSelectBlur() {
    if (!inputFocus) {
      setSelectOpen(false);
    }
  }

  function onInputClick(e: any) {
    e.stopPropagation();
    e.target.focus();
  }

  function onInputFocus(e: any) {
    e.stopPropagation();
    setInputFocus(true);
  }

  function onInputBlur(e: any) {
    e.stopPropagation();
    setInputFocus(false);
    if (!warpperEnter) {
      setSelectOpen(false);
    }
  }

  function onWarpperMouseEnter(e: any) {
    setWarpperEnter(true);
  }

  function onWarpperMouseLeave(e: any) {
    setWarpperEnter(false);
  }

  function clearAddOption() {
    setInputSort(false);
    setAddOptionValue('');
  }

  function addOption() {
    setInputSort(false);
    setAddOptionValue('');
    onAdd && onAdd(addOptionValue);
  }

  return (
    <div
      className="add-select-wrapper"
      onMouseEnter={onWarpperMouseEnter}
      onMouseLeave={onWarpperMouseLeave}
    >
      <Select
        {...restProps}
        ref={select}
        open={selectOpen}
        value={value}
        onChange={onSelectChange}
        onSelect={onSelectSelect}
        onFocus={onSelectFocus}
        onBlur={onSelectBlur}
        labelInValue={labelInValue}
        defaultActiveFirstOption={false}
        dropdownMatchSelectWidth={dropdownMatchSelectWidth}
        getPopupContainer={() => document.querySelector('.add-select-wrapper') as any}
      >
        {children}
        <Option
          style={{ borderTop: '1px solid #d9d9d9' }}
          value="自定义"
        >
          {(() => {
            if (!inputSort) {
              return (
                <>
                  <div
                    style={{ padding: '3px 0', cursor: 'pointer', textAlign: 'center' }}
                    onClick={() => setInputSort(true)}
                  >
                    <Icon type="plus" /> 新建分类
                  </div>
                </>
              )
            } else {
              return (
                <div>
                  <Input
                    style={{ width: 'calc(100% - 52px)', height: '28px' }}
                    onClick={(e) => onInputClick(e)}
                    onBlur={(e) => onInputBlur(e)}
                    onFocus={(e) => onInputFocus(e)}
                    value={addOptionValue}
                    onChange={(e) => setAddOptionValue(e.target.value)}
                  />
                  <Icon
                    type="close-circle"
                    style={{ marginLeft: '6px', fontSize: '20px', color: "#1890ff" }}
                    onClick={clearAddOption}
                  />
                  <Icon
                    type="check-circle"
                    theme="filled"
                    style={{ marginLeft: '6px', fontSize: '20px', color: '#1890ff' }}
                    onClick={addOption}
                  />
                </div>
              )
            }
          })()}
        </Option>
      </Select>
    </div>
  )
}
export default forwardRef(AddSelect)