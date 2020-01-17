import React, { useState, useRef, forwardRef, useImperativeHandle, Ref } from 'react';
import SelectPersonnelModal from '../selectPersonnelModal';
import { Input } from 'antd';

/**
 * 选中的组织结构树信息
 */
export interface SelectedInfo {
  title: string;
  key: string;
  type: string;
}

export interface SelectPrincipalProps {
  placeholder?: string;
  selectedInfo?: SelectedInfo,
  onChange?: any
}

/** 选择人员/合作伙伴/外部联系人 单选*/
function SelectPrincipal(props: SelectPrincipalProps, ref: Ref<any>) {

  useImperativeHandle(ref, () => ({}));

  const inputRef = useRef(null as any);
  const [visible, setVisible] = useState(false);
  const [version, setVersion] = useState(0);
  // 显示并更新版本
  // SelectPersonnelModal组件在没有点击确定的情况下，不会返回新值，但是内部状态却会发生变化，
  // 为了保持统一，每次打开的时候更新版本，SelectPersonnelModal组件判断版本变化就从props上获取值赋给state
  function showModal() {
    setVisible(true);
    setVersion(Date.now());
    inputRef.current.blur();
  }

  function okModal(selectedInfo: any, e: any) {

    console.log(selectedInfo, 999999999);
    
    setVisible(false);
    // 接受父组件传递的onChange事件，把处理好的数据传递给父组件
    const onChange = props.onChange;

    onChange && onChange(selectedInfo);

  }

  function cancelModal(e: any) {
    setVisible(false);
  }

  const { placeholder, selectedInfo } = props;
  const modalProps = {
    visible,
    centered: true,
    onOk: okModal,
    onCancel: cancelModal,
    checkable: false,
    version
  }
  return (
    <div style={{ maxWidth: '540px' }}>
      <Input ref={inputRef} type="text" placeholder={placeholder} value={selectedInfo && selectedInfo.title} onFocus={showModal} />
      <SelectPersonnelModal {...modalProps} />
    </div>
  )
}
export default forwardRef(SelectPrincipal)