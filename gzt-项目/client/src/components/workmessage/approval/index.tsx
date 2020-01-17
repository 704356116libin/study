import * as React from 'react'
import { Avatar } from 'antd'
import TextLabel from '../../textLabel';
import { connect } from 'dva';

interface ApprovalmessageProps {
  message?: any;
  onApprovalStart?: () => void;
  onApprovalEnd?: any;
  showDetailInfo?: any;
  needUpdate?: any
}
const NAMESPACE = 'Approval'; // dva model 命名空间
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE]
  }
}
const mapDispatchToProps = (dispatch: any) => {
  return {
    showDetailInfo: (params: any, cb: (detail: any) => void) => {
      dispatch({
        type: `${NAMESPACE}/queryApprovalDetail`,
        payload: {
          params,
          cb
        }
      });
    },
  }
}
/**
 * 动态 -> 工作通知模块 -> 审批
 */
@connect(mapStateToProps, mapDispatchToProps)
export default class Approvalmessage extends React.Component<ApprovalmessageProps, any> {
  componentDidUpdate() {
    if (this.props.needUpdate) {//如果需要更新
      this.showDetail(this.props.message.data.id);
    }
  }
  showDetail = (id: any) => {
    this.props.onApprovalStart && this.props.onApprovalStart();
    this.props.showDetailInfo({ id }, this.props.onApprovalEnd);
  }
  render() {
    const { message } = this.props;
    const { data: { id, applicant, complete_time, created_at, approval_content, status, type } } = message;
    return (
      <div style={{ float: 'left' }}>
        <div style={{ float: 'left', marginRight: '12px' }}>
          <Avatar size={36} style={{ background: 'var(--approval-color)', cursor: 'pointer' }}>审批</Avatar>
        </div>
        <div
          onClick={() => { this.showDetail(id) }}
          style={{ float: 'left', width: '420px', minHeight: '200px', background: '#fff', boxShadow: '0px 0px 1px 0px #cccccc', cursor: 'pointer' }}>
          <div style={{ padding: '0 12px', height: '36px', lineHeight: '36px', color: '#fff', background: 'var(--approval-color)' }}>审批</div>
          <div style={{ padding: '12px' }}>
            <div><TextLabel text="发起人" />{applicant}</div>
            <div><TextLabel text="审批类型" />{type}</div>
            <div><TextLabel text="审批梗概" />
              {
                approval_content && approval_content.map(({ type, field, value }: any, index: any) => {
                  if (type === 'DATERANGE') {
                    value = Array.isArray(value) ? value.join(' ~ ') : '请假时间飞到火星了'
                  }
                  return (
                    <div key={index}>
                      <TextLabel text={field.label} />{value}
                    </div>
                  )
                })
              }
            </div>
            <div><TextLabel text="发起时间" />{created_at}</div>
            {
              complete_time ? <div><TextLabel text="完成时间" />{complete_time}</div> : ''
            }
            <div><TextLabel text="当前状态" />{status}</div>
          </div>
        </div>
      </div>
    )
  }

}