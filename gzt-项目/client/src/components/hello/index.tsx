import * as React from 'react'
import Greetings from '../../assets/images/dynamic/Greetings.png'

export interface HelloInfo {
  /** 用户名 */
  name: string;
  helloInfo: string[];
}
/** 展示欢迎信息 */
export default function Hello({
  name,
  helloInfo
}: HelloInfo) {
  return (
    <div style={{ textAlign: 'center' }}>
      <div style={{ marginTop: 'calc(20% - 100px)' }}>
        <img src={Greetings} alt="" />
        <p style={{ paddingTop: '30px', fontSize: '16px', color: '#333' }}>
          {name}，
          {helloInfo[0]}，
          {helloInfo[1]}
        </p>
      </div>
    </div>
  )
}