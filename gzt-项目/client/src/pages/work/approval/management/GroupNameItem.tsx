import * as React from 'react';
import { Row, Col, Divider, Popconfirm } from 'antd';

interface GroupNameItemProps {
  contents: any,
  onRemoveTemType: any
  onShowEditModal: any
}
export default function GroupNameItem({
  contents,
  onRemoveTemType,
  onShowEditModal
}: GroupNameItemProps) {

  const { type, typeId, count } = contents;
  function showEditModal(type: any, typeId: any) {
    onShowEditModal(type, typeId)
  }
  function removeTemType(typeId: any) {
    onRemoveTemType(typeId)
  }
  return (
    <header style={{ height: '40px', lineHeight: '40px', background: '#f5f5f5' }}>
      <Row>
        <Col span={4} style={{ paddingLeft: '30px' }}>
          类型名称：<span>{type}</span>
        </Col>
        <Col span={17}>
          审批数量：<span>{count}</span>
        </Col>
        <Col span={3}>
          <span className="editList parmary-color cursor-pointer" onClick={() => showEditModal(type, typeId)}>编辑</span>
          <Divider type="vertical" className="parmary-bg" />
          <Popconfirm title="是否要删除此分类？" onConfirm={() => removeTemType(typeId)}>
            <span className="sortList parmary-color cursor-pointer" onClick={(e: any) => { e.stopPropagation() }}>删除</span>
          </Popconfirm>
        </Col>
      </Row>
    </header>
  )
}
