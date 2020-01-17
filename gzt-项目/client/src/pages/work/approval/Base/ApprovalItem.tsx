
import * as React from 'react'
// import { useState } from 'react';
import { Row, Col, List } from 'antd';
import TextLabel from '../../../../components/textLabel';
interface ApprovalItemProps {
  dataSource: any,
  ApprovalListInfo?: any
  type?: any,
  ApprovalDetailInfo?: Function,
  headerState?: any
}
const mapStatusToColor = {
  "已通过": '#34d058',
  "审批中": '#ff851a',
  "已拒绝": '#E61717',
  "已归档": '#333',
  "已撤销": '#999'
}
export default class ApprovalItem extends React.Component<ApprovalItemProps, any>{
  state = {
    pageSize: 10,
    nowPage: 1
  }

  /**
   * 分页
   * @param pageNumber 
   * @param pageSize 
   */
  paginationChange = (pageNumber: any, pageSizes: any) => {

    this.props.ApprovalListInfo({
      now_page: pageNumber,
      page_size: pageSizes,
      type: this.props.type
    });
  }
  onShowSizeChange = (pageNumber: any, pageSizes: any) => {

    this.setState({
      pageSize: pageSizes,
      nowPage: pageNumber
    })
    this.props.ApprovalListInfo({
      now_page: pageNumber,
      page_size: pageSizes,
      type: this.props.type
    });
  }
  approvalDetails = (id: number) => {
    this.props.ApprovalDetailInfo && this.props.ApprovalDetailInfo({
      id,
    });
  }
  render() {
    const { dataSource } = this.props;
    const paginationProps = {
      showSizeChanger: true,
      showQuickJumper: true,
      pageSize: this.state.pageSize,
      pageSizeOptions: ['5', '10', '15'],
      total: dataSource && dataSource.all_count,
      onChange: this.paginationChange,
      defaultCurrent: this.state.nowPage,
      onShowSizeChange: this.onShowSizeChange,
      showTotal: (total: any) => `共 ${total} 条数据`
    }

    return (
      <List
        dataSource={dataSource && (dataSource.approval_data || dataSource.data) }
        size="small"
        pagination={paginationProps}
        renderItem={
          (item: any) => (
            <Row className="approvalList" onClick={() => this.approvalDetails(item.id)}>
              {/* 申请人 */}
              <Col span={3}>
                {item.sponsor}
              </Col>
              {/* 申请类型 */}
              <Col span={3}>
                {item.type}
              </Col>
              {/* 申请梗概 */}
              <Col span={6} style={{ paddingRight: "10px" }}>
                {item.content && item.content.map(({ type, field, value }: any, index: any) => {
                  if (type === 'DATERANGE') {
                    value = Array.isArray(value) ? value.join(' ~ ') : '请假时间飞到火星了'
                  }
                  return (
                    <div key={index}><TextLabel text={field.label} />{value}</div>
                  )
                })
                }
              </Col>
              {/* 发起时间/完成时间 */}
              {
                this.props.headerState === "archive" ?
                  (<Col span={3}>{item.archive_time}</Col>)
                  :
                  (<Col span={3}>{item.created_at}</Col>)
              }
              {/* 归档人 */}
              {
                this.props.headerState === "archive" ?
                  (<Col span={3}>{item.sponsor}</Col>)
                  :
                  ""
              }

              {
                this.props.headerState !== "archive" ?
                  (<Col span={3}>{item.completed_time}</Col>)
                  :
                  ""
              }

              {/* 当前状态 */}
              <Col span={3}>
                {
                  (() => {
                    return (
                      <span style={{ color: mapStatusToColor[item.currentState] }}>{item.currentState === "审批中" ? `${item.approver}${item.currentState}` : item.currentState}</span>
                    )
                  })()
                }
              </Col>
            </Row>
          )
        }
      />
    )
  }
}

