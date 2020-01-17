import * as React from 'react'
import { Avatar } from 'antd'

interface Reviewmessage {
  type: string;
  data: any;
}

interface ReviewmessageProps {
  message: Reviewmessage;
  onClick?: () => void;
}

/**
 * 动态 -> 工作通知模块 -> 评审通
 *      
 */
export default function Reviewmessage({
  message,
  onClick
}: ReviewmessageProps) {

  const { data: { time } } = message;

  return (
    <section style={{ float: 'left' }}>
      <div style={{ float: 'left', marginRight: '12px' }}>
        <Avatar size={36} style={{ background: 'var(--review-color)', cursor: 'pointer' }}>评审通</Avatar>
      </div>
      <div onClick={onClick} style={{ float: 'left', width: '420px', height: '200px', background: '#fff', boxShadow: '0px 0px 1px 0px #cccccc', cursor: 'pointer' }}>
        <div style={{ padding: '0 12px', height: '36px', lineHeight: '36px', color: '#fff', background: 'var(--review-color)' }}>这里是评审通</div>
        <div style={{ padding: '12px' }}>
          发起时间: {time}
        </div>
      </div>
    </section>
  )
}