import * as React from 'react';
import { Checkbox } from 'antd';

const CheckboxGroup = Checkbox.Group;
const plainOptions = ['实时通知', '邮件', '短信', '语音电话'];
const notifiMap = {
  need_notify: '实时通知',
  need_email: '邮件',
  need_sms: '短信',
  need_voice_sms: '语音电话'
}

export default class NotifiMeds extends React.Component<any, any>{

  constructor(props: any) {
    super(props);
    this.state = {
      defaultValue: props.value || ['实时通知']
    }
    this.props.onChange && this.props.onChange({
      need_notify: 1,
      need_email: 0,
      need_sms: 0,
      need_voice_sms: 0
    })
  }

  onChange = (value: any) => {

    let notificationWay; // 处理通知方式数据格式
    if (value.length !== 0) {
      notificationWay = {
        need_notify: value.includes(notifiMap.need_notify) ? 1 : 0,
        need_email: value.includes(notifiMap.need_email) ? 1 : 0,
        need_sms: value.includes(notifiMap.need_sms) ? 1 : 0,
        need_voice_sms: value.includes(notifiMap.need_voice_sms) ? 1 : 0
      }
    } else {
      notificationWay = {
        need_notify: 0,
        need_email: 0,
        need_sms: 0,
        need_voice_sms: 0
      }
    }
    if (this.props.onChange) {
      this.props.onChange(notificationWay)
    }
  }

  render() {

    return <CheckboxGroup options={plainOptions} defaultValue={this.state.defaultValue} onChange={this.onChange} />

  }
}