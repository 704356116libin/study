import * as React from 'react'
import { Drawer } from 'antd'

interface CommunicationDrawerProps {
  visible: boolean;
  onClose: () => void;
}

export default function CommunicationDrawer({
  visible,
  onClose
}: CommunicationDrawerProps) {

  return (


    <Drawer
      title="沟通"
      placement="right"
      mask={false}
      onClose={onClose}
      visible={visible}
      width="360"
      getContainer=".dynamic-content"
    >
      <p>Some contents...</p>
      <p>Some contents...</p>
      <p>Some contents...</p>
    </Drawer>
  )

}