import React, { useState, useImperativeHandle, Ref, forwardRef } from 'react';
import { Button, Modal, Table } from 'antd';
import { connect } from 'dva';

const columns = [{
  title: '项目名称',
  dataIndex: 'name',
}, {
  title: '状态',
  dataIndex: 'state',
}];

const NAMESPACE = 'Review';

const mapStateToProps = (state: any) => ({
  canLinkReviews: state[NAMESPACE].canLinkReviews,
  loading: state[NAMESPACE].canLinkReviews,
})

const mapDispatchToProps = (dispatch: any) => ({
  queryCanLinkReviews() {
    dispatch({
      type: `${NAMESPACE}/queryCanLinkReviews`
    })
  }
})

export interface AssociateReviewProps {
  /** 关联评审 id 数组 */
  value?: string[];
  /** form 表单 onChange */
  onChange?: Function;
  /** 获取可以关联的评审列表 */
  queryCanLinkReviews: Function;
  /** 可以关联的评审列表 */
  canLinkReviews: any;
}

/** 可关联评审列表 */
function AssociateReview({
  value = [],
  onChange,
  queryCanLinkReviews,
  canLinkReviews
}: AssociateReviewProps, ref: Ref<any>) {

  const [visible, setVisible] = useState(false);
  const [selectedIds, setSelectedIds] = useState();

  // antd form 表单中 自定义表单控件需要
  useImperativeHandle(ref, () => ({}));

  function handleOk() {
    // todo ... 关联评审数组id 等待从后端拿到真实值
    onChange && onChange(selectedIds);
    setVisible(false)
  }

  function handleLink() {
    setVisible(true);
    queryCanLinkReviews();
  }

  const rowSelection = {
    onChange: (selectedRowKeys: any, selectedRows: any) => {
      setSelectedIds(selectedRows.map(({ id }: any) => id))
    }
  }

  return (
    <div>
      <Button onClick={handleLink}>关联以往评审</Button>
      <div>
        {
          value.map((item) => (
            <div>{item}</div>
          ))
        }
      </div>
      <Modal
        title="关联评审"
        visible={visible}
        onCancel={() => setVisible(false)}
        onOk={handleOk}
      >
        <Table
          rowKey={(record: any) => record.id}
          size="middle"
          rowSelection={rowSelection}
          columns={columns}
          dataSource={canLinkReviews}
        />
      </Modal>
    </div>
  )
}

export default connect(mapStateToProps, mapDispatchToProps, undefined, { forwardRef: true })(forwardRef(AssociateReview))