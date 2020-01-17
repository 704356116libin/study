import * as React from 'react';
import { Row, Col } from 'antd';
interface ListHeaderProps {
  headerState?: any
}
export default function ListHeader({
  headerState
}: ListHeaderProps) {
  if (headerState === "archive") {// 已归档
    return (
      <header style={{ height: '40px', lineHeight: '40px', background: '#f5f5f5' }}>
        <Row style={{ padding: '0 30px' }}>
          <Col span={3}>
            申请人
            </Col>
          <Col span={3}>
            申请类型
            </Col>
          <Col span={6}>
            申请内容
            </Col>
          <Col span={3}>
            归档时间
            </Col>
          <Col span={3}>
            归档人
            </Col>
          {/* <Col span={3}>
              审批人
            </Col> */}
          <Col span={6}>
            审批结果
            </Col>
        </Row>
      </header>
    )
  } else {
    return (
      <header style={{ height: '40px', lineHeight: '40px', background: '#f5f5f5' }}>
        <Row style={{ padding: '0 30px' }}>
          <Col span={3}>
            申请人
            </Col>
          <Col span={3}>
            申请类型
            </Col>
          <Col span={6}>
            申请梗概
            </Col>
          <Col span={3}>
            发起时间
            </Col>
          <Col span={3}>
            完成时间
            </Col>
          {/* <Col span={3}>
              审批人
            </Col> */}
          <Col span={6}>
            当前状态
            </Col>
        </Row>
      </header>
    )
  }
}

