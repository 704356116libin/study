import * as React from 'react';
import { Row, Col, Divider, Popconfirm, Card, Avatar } from 'antd';
import { Meta } from 'antd/lib/list/Item';
import { Link } from 'react-router-dom';
import './gropIndex.scss'

interface GroupTypeItemProps {
  dataSource: any,
  // index: number,
  // key: number | string,
  // loading: any,
  onRemoveTemList: any,
  onHandleSwitchChange: any
}
export default function GroupTypeItem({
  dataSource: { id, name, description, updated_time, approval_method, is_show, per },
  onRemoveTemList,
  onHandleSwitchChange
}: GroupTypeItemProps) {

  function removeTemList(id: any) {
    onRemoveTemList(id)
  }
  function handleChange() {
    onHandleSwitchChange(id, is_show)
  }
  return (
    <Row type="flex" className='approval-group' >
      <Col span={6}>
        <Card bordered={false}>
          <Meta
            avatar={
              <Avatar shape="square" className='avatar'>
                {name}
              </Avatar>
            }
            title={name}
            description={description}
          // description={<div className="overflow-ellipsis" title={description}>{description}</div>}
          />
        </Card>
      </Col>
      <Col span={6}>
        更新编辑时间：<span>{updated_time}</span>
      </Col>
      <Col span={3}>
        {approval_method}
      </Col>
      <Col span={4}>
        <span style={{ color: '#e8a54c' }}>{per === '全体员工' ? '全体员工' : `${per.staff_names[0]}等${per.staff_names.length}人可见`} </span>
      </Col>
      <Col span={2}>
        <span className="cursor-pointer" onClick={handleChange} style={{ color: '#38adff' }} >{is_show === 1 ? '禁用' : '启用'}</span>
        {/* <Switch
          checkedChildren="启用"
          unCheckedChildren="禁用" 
        // loading={loading}
        // onChange={() => handleSwitchChange(id, is_show, index, key)}
        // checked={is_show === 1}
        {/* /> */}
      </Col>
      <Col span={3}>
        <Link to={
          {
            pathname: '/work/approval/newCreate',
            state: {
              type: 'update',
              id
            }
          }
        }>
          <span className="editList parmary-color cursor-pointer">编辑</span>
        </Link>
        <Divider type="vertical" className="parmary-bg" />
        <Popconfirm title="是否要删除此分类？" onConfirm={() => removeTemList(id)}>
          <span className="sortList parmary-color cursor-pointer" onClick={(e: any) => { e.stopPropagation() }}>删除</span>
        </Popconfirm>
      </Col>
    </Row>
  )
}
