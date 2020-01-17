import * as React from 'react';
import { Row, Col } from 'antd';

export default function ListHeader() {
  return (
    <header style={{ height: '40px', lineHeight: '40px', background: '#f5f5f5' }}>
      <Row style={{ padding: '0 30px' }}>
        <Col span={3}>职务名称</Col>
        <Col span={3}>人数</Col>
        <Col span={14}>人员</Col>
        <Col span={4}>操作</Col>
      </Row>
    </header>
  )
}

