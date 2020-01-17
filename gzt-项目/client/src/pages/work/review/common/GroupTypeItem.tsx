import React, { useState } from 'react';
import { Row, Col, Card, Avatar, message, Spin, Popconfirm, Icon } from 'antd';
import { Link } from 'react-router-dom';
import TextLabel from '../../../../components/textLabel';
import request from '../../../../utils/request';

const { Meta } = Card;

export interface GroupTypeItemProps {
  /** 点击编辑跳转路由 */
  pathname?: string;
  /** 类型 */
  type: 'template' | 'process' | 'export';
  /** 数据 */
  params: any;
  /** 启用禁用 请求的api */
  changeEnableStateUrl: string;
  /** 启用禁用 状态改变后触发 */
  onEnableStateChange?: Function;
  /** 移动分组 状态改变后触发 */
  onGroupMove?: Function;
  /** 删除请求的api */
  deleteUrl?: string;
  /** 删除 状态改变后触发 */
  onDeleteChange?: Function;

}

/**
 * 分组下具体流程item
 */
export default function GroupTypeItem({
  pathname,
  params,
  type,
  onEnableStateChange,
  onGroupMove,
  changeEnableStateUrl,
  deleteUrl,
  onDeleteChange

}: GroupTypeItemProps) {

  const [loading, setLoading] = useState(false);

  const {
    allow_user_names, id, name, description, updated_at, approval_method, is_show, need_approval,
  } = params;
  /** 处理 禁用/启用状态 */
  async function handleEnableState() {

    setLoading(true);

    const body = type === 'export' ? { id, is_show: is_show === 1 ? 'disable' : 'enable' } : { id };

    const result = await request(changeEnableStateUrl, {
      method: 'POST',
      body
    });
    setLoading(false);
    if (result.status === 'success') {
      message.success(is_show === 1 ? '禁用成功' : '启用成功');
      onEnableStateChange && onEnableStateChange();
    } else {
      message.error('服务器异常，请稍后再试')
    }
  }

  function handleMoveGroup() {
    onGroupMove && onGroupMove();
  }

  async function handleDelete() {
    if (!deleteUrl) {
      return
    }

    setLoading(true);
    const result = await request(deleteUrl, {
      method: 'POST',
      body: {
        id
      }
    });
    setLoading(false);
    if (result.status === 'success') {
      message.success('删除成功');
      onDeleteChange && onDeleteChange();
    } else {
      message.error('服务器异常，请稍后再试')
    }
  }

  const deleteText = is_show === 1
    ? '确定删除该模板吗?删除后，可能会影响到正在进行中的评审，请确认当前模板下没有正在进行中的评审'
    : '确定删除该审批流程吗?删除后，可能会影响到正在进行中的评审，请确认当前审批流程下没有正在进行中的评审';

  return (
    <Spin spinning={loading}>
      <Row type="flex" style={{ padding: '0 10px', alignItems: 'center', background: '#fff', borderBottom: '1px solid #ddd' }}>
        <Col span={type === 'template' ? 6 : 8}>
          <Card bordered={false}>
            <Meta
              avatar={
                <Avatar size="large" shape="square" style={{ background: '#1890ff' }}>
                  {name}
                </Avatar>
              }
              title={name}
              description={description}
            />
          </Card>
        </Col>
        <Col span={type === 'template' ? 6 : 8}>
          更新时间：<span>{updated_at}</span>
        </Col>
        {
          type === 'template' ?
            need_approval === true ? (
              <Col span={4}>
                {approval_method}
              </Col>
            ) : (
                <Col span={4}>当前模板不需要审批</Col>
              )
            : null
        }
        <Col span={4}>
          <TextLabel text="可见范围" />
          <span style={{ color: '#e8a54c' }}>
            {/* 少于7人全部展示, 超过的话显示总人数 */}
            {
              allow_user_names.length === 1 ?
                allow_user_names[0] :
                allow_user_names.length > 7 ?
                  `${allow_user_names.slice(0, 7)}等${allow_user_names.length}人可见`
                  : `${allow_user_names}`
            }
          </span>
        </Col>
        <Col span={4} style={{ textAlign: 'right' }}>
          {pathname && (
            <Link to={
              {
                pathname,
                state: {
                  type: 'UPDATE',
                  data: {
                    id
                  }
                }
              }
            }>
              <span className="primary-color cursor-pointer" style={{ padding: '0 12px' }}>编辑</span>
            </Link>
          )}

          {
            is_show !== 1 && (
              <Popconfirm
                placement="topLeft"
                icon={<Icon type="question-circle-o" style={{ color: 'red' }} />}
                title={
                  <div style={{ maxWidth: 450 }}>
                    {deleteText}
                  </div>
                }
                onConfirm={handleDelete}
                okText="确定"
                cancelText="取消"
              >
                <span
                  className="primary-color cursor-pointer"
                  style={{ padding: '0 12px' }}>
                  删除
              </span>
              </Popconfirm>
            )
          }
          <span
            onClick={handleEnableState}
            className="primary-color cursor-pointer"
            style={{ padding: '0 12px' }}>{is_show === 1 ? '禁用' : '启用'}
          </span>
          {
            is_show === 1 && (
              <span
                onClick={handleMoveGroup}
                className="primary-color cursor-pointer"
                style={{ padding: '0 12px' }}>
                移动到
              </span>
            )
          }
        </Col>
      </Row>
    </Spin>
  )
}