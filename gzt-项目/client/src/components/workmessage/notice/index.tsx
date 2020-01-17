import * as React from 'react'
import { Avatar, } from 'antd'
import { connect } from 'dva';
/**
 * 动态 -> 工作通知模块 -> 公告 -> 小窗口
 */
const NAMESPACE = 'Notice'; // dva model 命名空间
interface NoticeMessageProps {
  onClick: any,
  message?: any,
  handleClick?: (value: any) => void,
  showNoticeInfo?: any
}

const mapStateToProps = (state: any) => {
  return state[NAMESPACE]
};

const mapDispatchToProps = (dispatch: any) => {
  return {
    showNoticeInfo: (noticeId: any, companyId: any) => {
      dispatch({
        type: `${NAMESPACE}/queryNoticeContent`,
        payload: { noticeId, companyId }
      });
    }
  }
}
@connect(mapStateToProps, mapDispatchToProps)

export default class Noticemessage extends React.Component<NoticeMessageProps, any>{
  handleClick = () => {
    this.props.onClick();
    this.props.showNoticeInfo(this.props.message.data.id, this.props.message.data.company_id);
  }
  render() {
    const currentNoticeInfo = this.props.message.data;
    const { title, content, organiser, updated_at } = currentNoticeInfo;
    return (
      <div style={{ float: 'left' }}>
        <div style={{ float: 'left', marginRight: '12px' }}>
          <Avatar size={36} style={{ background: 'var(--notice-color)', cursor: 'pointer' }}>公告</Avatar>
        </div>
        <div onClick={this.handleClick} style={{ float: 'left', width: '420px', background: '#fff', boxShadow: '0px 0px 1px 0px #cccccc', cursor: 'pointer' }}>
          <div style={{ padding: '0 12px', height: '36px', lineHeight: '36px', color: '#fff', background: 'var(--notice-color)' }}>公告</div>
          <div style={{ padding: '12px', borderBottom: '1px solid #eee', maxHeight: '240px' }}>
            <div style={{ fontSize: '16px', color: '#333', paddingBottom: '5px' }}>{title}</div>
            <div style={{ paddingBottom: '5px' }}>
              {content}
            </div>
            <div>
              <span>{organiser}</span>
              <span>{updated_at}</span>
            </div>
          </div>
          <div style={{ padding: '12px', color: '#3AADFD' }}>
            <span>查看详情</span>
          </div>
        </div>
      </div>
    )
  }
}
