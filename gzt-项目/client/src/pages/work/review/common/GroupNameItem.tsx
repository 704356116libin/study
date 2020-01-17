import React from 'react';
import { Row, Col, Popconfirm } from 'antd';


export interface GroupNameItemProps {
  /** 分组名称 */
  type: string;
  /** 分组下流程( 模板 ) 数量 */
  count: number;
  /** 点击 `重命名` */
  onRenameGroup?: Function;
  /** 点击 `删除分组` */
  onDeleteGroup?: Function;
}

/**
 * 分组名称item
 */
export default function GroupNameItem({
  type,
  count,
  onRenameGroup,
  onDeleteGroup
}: GroupNameItemProps) {

  function handleRenameGroup() {
    onRenameGroup && onRenameGroup()
  }

  function confirm() {
    onDeleteGroup && onDeleteGroup()
  }

  return (
    <Row type="flex" style={{ marginTop: '10px', padding: '0 8px', lineHeight: '40px', background: '#f9f9f9' }}>
      <Col span={6}>
        <span style={{ color: '#222' }}>{type}</span>
        <span>（{count}）</span>
      </Col>
      <Col span={18} style={{ textAlign: 'right' }}>
        <span
          onClick={handleRenameGroup}
          className="primary-color cursor-pointer"
          style={{ padding: '0 12px' }}>
          重命名
        </span>
        {
          count === 0 ? (
            <Popconfirm placement="left" title="确定删除该分组吗？" onConfirm={confirm} okText="确定" cancelText="取消">
              <span
                className="primary-color cursor-pointer"
                style={{ padding: '0 12px' }}
              >
                删除该组
              </span>
            </Popconfirm>
          ) : null
        }
      </Col>
    </Row>
  )
}