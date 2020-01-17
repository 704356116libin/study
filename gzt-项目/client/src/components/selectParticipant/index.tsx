import * as React from 'react';
import { Button } from 'antd';
import SelectParticipantModal, { checkedPersonnels } from '../selectParticipantModal';
import PersonnelAvatar from '../personnelAvatar';
// import './index.scss'

/**
 * 选中的组织结构树信息
 */
export interface CheckedInfo {
  checkedKeys: any,
  checkedPersonnels: checkedPersonnels
}

export interface SelectParticipantProps {
  checkedInfo?: CheckedInfo;
  onChange?: any;
  checkable?: boolean;
}

/**
 * 组织结构树组件
 */
export default class SelectParticipant extends React.Component<SelectParticipantProps, any> {

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

    onChange && onChange(checkedInfo);

  }

  cancelModal = (e: any) => {
    this.setState({
      visible: false,
    });
  }

  /**
   * 移除当前人员
   */
  removePersonnel = (key: number, linKey: any[], type: string) => {

    const onChange = this.props.onChange;
    const keys = linKey.map((item) => item.key);
    if (!this.props.checkedInfo) {
      return
    }
    let checkedKeys;
    let checkedPersonnels;
    switch (type) {
      case 'organizational':
        checkedKeys = {
          ...this.props.checkedInfo.checkedKeys,
          organizational: this.props.checkedInfo.checkedKeys.organizational.filter((iKey: any) => !keys.includes(iKey))
        }
        checkedPersonnels = {
          ...this.props.checkedInfo.checkedPersonnels,
          organizational: this.props.checkedInfo.checkedPersonnels.organizational.filter((item: any) => item.key !== key)
        }
        break;
      case 'partner':
        checkedKeys = {
          ...this.props.checkedInfo.checkedKeys,
          partner: this.props.checkedInfo.checkedKeys.partner.filter((iKey: any) => !keys.includes(iKey))
        }
        checkedPersonnels = {
          ...this.props.checkedInfo.checkedPersonnels,
          partner: this.props.checkedInfo.checkedPersonnels.partner.filter((item: any) => item.key !== key)
        }
        break;
      case 'externalContact':
        checkedKeys = {
          ...this.props.checkedInfo.checkedKeys,
          externalContact: this.props.checkedInfo.checkedKeys.externalContact.filter((iKey: any) => !keys.includes(iKey))
        }
        checkedPersonnels = {
          ...this.props.checkedInfo.checkedPersonnels,
          externalContact: this.props.checkedInfo.checkedPersonnels.externalContact.filter((item: any) => item.key !== key)
        }
        break;
      default:
        break;
    }

    onChange && onChange({
      checkedKeys,
      checkedPersonnels
    });

  }

  render() {

    const { visible, version } = this.state;
    const { checkedInfo, checkable } = this.props;
    const modalProps = {
      visible,
      centered: true,
      onOk: this.okModal,
      onCancel: this.cancelModal,
      checkedKeys: checkedInfo && checkedInfo.checkedKeys,
      checkedPersonnels: checkedInfo && checkedInfo.checkedPersonnels,
      checkable: checkable === undefined ? true : checkable,
      version
    }

    return (
      <div style={{ maxWidth: '540px' }}>
        {
          checkedInfo && checkedInfo.checkedPersonnels.organizational.map(({ key, title, linKey }: any) => (
            <React.Fragment key={key}>
              <PersonnelAvatar
                name={title}
                onClose={() => this.removePersonnel(key, linKey, 'organizational')}
              />
              <div style={{
                width: '10px',
                display: 'inline-block'
              }} />
            </React.Fragment>
          ))
        }
        {
          checkedInfo && checkedInfo.checkedPersonnels.partner.map(({ key, title, linKey }: any) => (
            <React.Fragment key={key}>
              <PersonnelAvatar
                name={title}
                onClose={() => this.removePersonnel(key, linKey, 'partner')}
              />
              <div style={{
                width: '10px',
                display: 'inline-block'
              }} />
            </React.Fragment>
          ))
        }
        {
          checkedInfo && checkedInfo.checkedPersonnels.externalContact.map(({ key, title, linKey }: any) => (
            <React.Fragment key={key}>
              <PersonnelAvatar
                name={title}
                onClose={() => this.removePersonnel(key, linKey, 'externalContact')}
              />
              <div style={{
                width: '10px',
                display: 'inline-block'
              }} />
            </React.Fragment>
          ))
        }
        <Button type="dashed" icon="plus" onClick={this.showModal} />
        <SelectParticipantModal {...modalProps} />
      </div>
    )
  }
}