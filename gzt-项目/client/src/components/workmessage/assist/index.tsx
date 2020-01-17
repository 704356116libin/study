import * as React from 'react'
import { Avatar } from 'antd'
import { connect } from 'dva';
import TextLabel from '../../textLabel';

interface Assistmessage {
  type: string;
  data: any;
}

interface AssistmessageProps {
  message: Assistmessage;
  onStart?: () => void;
  onEnd?: (details: any) => void;
  showAssistDetails?: any;
  /**
   * 是否需要刷新
   */
  needUpdate?: boolean;
}

/**
 * 动态 -> 工作通知模块 -> 协助
 *      
 */
const NAMESPACE = 'Assist'; // dva model 命名空间

const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE]
  }
};

const mapDispatchToProps = (dispatch: any) => {
  return {
    showAssistDetails: (id: number, cb: (details: any) => void) => {
      dispatch({
        type: `${NAMESPACE}/queryAssistDetails`,
        payload: id,
        cb
      });
    }
  }
}

@connect(mapStateToProps, mapDispatchToProps)
export default class AssistWindow extends React.Component<AssistmessageProps, any>{

  componentDidUpdate() {
    console.log(this.props.needUpdate, 666666, this.props.message.data.id);

    if (this.props.needUpdate) {
      this.showDetail(this.props.message.data.id);
    }
  }

  showDetail = (id: any) => {
    this.props.onStart && this.props.onStart();
    this.props.showAssistDetails(id, this.props.onEnd);
  }

  render() {

    const { showDetail } = this;
    const { message } = this.props;
    const {
      data: {
        created_at, description, id, limit_time, status, title
      }
    } = message;

    return (
      <div style={{ float: 'left' }}>
        <div style={{ float: 'left', marginRight: '12px' }}>
          <Avatar size={36} style={{ background: 'var(--assist-color)', cursor: 'pointer' }}>协助</Avatar>
        </div>
        <div onClick={() => showDetail(id)} style={{ float: 'left', width: '420px', height: '200px', background: '#fff', boxShadow: '0px 0px 1px 0px #cccccc', cursor: 'pointer' }}>
          <div style={{ padding: '0 12px', height: '36px', lineHeight: '36px', color: '#fff', background: 'var(--assist-color)' }}>协助</div>
          <div style={{ padding: '12px' }}>
            <div><TextLabel text="协助标题" />{title}</div>
            <div><TextLabel text="协助描述" /><div dangerouslySetInnerHTML={{ __html: description }} /></div>
            <div><TextLabel text="开始时间" />{created_at}</div>
            <div><TextLabel text="完成时间" />{limit_time ? limit_time : '尽快完成'}</div>
            <div><span>{status}</span></div>
          </div>
        </div>
      </div>
    )
  }
}