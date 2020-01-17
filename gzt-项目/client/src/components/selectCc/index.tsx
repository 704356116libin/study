import * as React from 'react';
import { Button } from 'antd';
import SelectPersonnelModal from '../selectPersonnelModal';
import PersonnelAvatar from '../personnelAvatar';
import './index.scss'

/**
 * 选中的组织结构树信息
 */
export interface CheckedInfo {
  checkedKeys: string[],
  checkedPersonnels: any[]
}

export interface SelectCcProps {
  ccInfo?: CheckedInfo,
  onChange?: any
}

/**
 * 组织结构树组件
 */
export default class SelectCc extends React.Component<SelectCcProps, any> {

  // 自定义form组件中,state中接受父组件传递的参数，之后用于动态处理数据
  state = {
    visible: false,
    version: 0
  }
  // 显示并更新版本
  // SelectPersonnelModal组件在没有点击确定的情况下，不会返回新值，但是内部状态却会发生变化，
  // 为了保持统一，每次打开的时候更新版本，SelectPersonnelModal组件判断版本变化就从props上获取值赋给state
  showModal = () => {
    this.setState({
      visible: true,
      version: Date.now()
    });
  }

  okModal = (checkedInfo: any, e: any) => {
    this.setState({
      visible: false,
    });
    // 接受父组件传递的onChange事件，把处理好的数据传递给父组件
    const onChange = this.props.onChange;
    if (onChange) {
      onChange(checkedInfo);
    }
  }

  cancelModal = (e: any) => {
    this.setState({
      visible: false,
    });
  }

  /**
   * 移除当前人员
   */
  removePersonnel = (key: number, linKey: any[]) => {

    const onChange = this.props.onChange;
    const keys = linKey.map((item) => item.key);
    const checkedKeys = this.props.ccInfo && (this.props.ccInfo as any).checkedKeys.filter((iKey: any) => !keys.includes(iKey));
    const checkedPersonnels = this.props.ccInfo && (this.props.ccInfo as any).checkedPersonnels.filter((item: any) => item.key !== key);

    onChange && onChange({
      checkedKeys,
      checkedPersonnels
    });

  }

  render() {

    const { visible, version } = this.state;
    const { ccInfo } = this.props;
    const modalProps = {
      visible,
      centered: true,
      onOk: this.okModal,
      onCancel: this.cancelModal,
      checkedKeys: ccInfo && ccInfo.checkedKeys,
      checkedPersonnels: ccInfo && ccInfo.checkedPersonnels,
      version
    }
    return (
      <div style={{ maxWidth: '540px' }}>
        {
          ccInfo && ccInfo.checkedPersonnels.map(({ key, title, linKey }: any) => (
            <React.Fragment key={key}>
              <PersonnelAvatar
                name={title}
                onClose={() => this.removePersonnel(key, linKey)}
              />
              <div style={{
                width: '10px',
                display: 'inline-block'
              }} />
            </React.Fragment>
          ))
        }
        <Button type="dashed" icon="plus" onClick={this.showModal} />
        <SelectPersonnelModal {...modalProps} />
      </div>
    )
  }
}