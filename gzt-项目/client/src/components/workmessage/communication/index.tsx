import * as React from 'react'
import { Avatar } from 'antd'

interface Communicationmessage {
  type: string;
}

interface CommunicationmessageProps {
  message: Communicationmessage;
  onClick?: () => void;
}

/**
 * 动态 -> 工作通知模块 -> 沟通
 *      
 */
export default function Communicationmessage({
  message,
  onClick

}: CommunicationmessageProps) {

  const { type } = message;

  return (
    <div style={{ float: 'left' }}>
      <div style={{ float: 'left', marginRight: '12px' }}>
        <Avatar size={36} style={{ background: '#70BEEA', cursor: 'pointer' }}>{type}</Avatar>
      </div>
      <div onClick={onClick} style={{ float: 'left', width: '420px', height: '200px', background: '#fff', boxShadow: '0px 0px 1px 0px #cccccc', cursor: 'pointer' }}>
        <div style={{ padding: '0 12px', height: '36px', lineHeight: '36px', color: '#fff', background: '#70BEEA' }}>这里是{type}</div>
        <div style={{ padding: '12px' }}>
          11111
          </div>
      </div>
    </div>
  )
}