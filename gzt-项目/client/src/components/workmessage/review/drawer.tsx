import * as React from 'react'
import { Drawer } from 'antd'

interface ReviewDrawerProps {
  visible: boolean;
  onClose: () => void;
}

export default function ReviewDrawer({
  visible,
  onClose
}: ReviewDrawerProps) {

  return (


    <Drawer
      title="评审通"
      placement="right"
      mask={false}
      onClose={onClose}
      visible={visible}
      width="540"
      getContainer=".dynamic-content"
    >
      <p>Some contents...</p>
      <p>Some contents...</p>
      <p>Some contents...</p>
    </Drawer>
  )

}