import * as React from 'react';
import { Drawer, Input, Button, Modal, Spin, Icon, Popconfirm, message, Row, Col, Divider, Tooltip, Radio } from 'antd';
import FormLibrary from '../../../components/formLibrary';
import NotifiMeds from '../../../components/notifiMeds';
import AnnexList from '../../../components/annexList';
import TextLabel from '../../../components/textLabel';
import { Link } from 'react-router-dom';
import req, { get } from '../../../utils/request';
import FormArea from './formArea';
import { DrawerProps } from 'antd/lib/drawer';
import SelectPrincipal from '../../selectPrincipal';
import "./index.scss"

interface AssistDrawerProps extends DrawerProps {
  details: any;
  loading?: boolean;
  drawerClose?: any;
  onUpdate?: (id: number) => void;
  updateList?: any;
}

const { useState, useEffect } = React;

export default function AssistDrawer({
  visible,
  drawerClose,
  details,
  getContainer = '.dynamic-content',
  loading,
  onUpdate,
  updateList
}: AssistDrawerProps) {

  // 全屏
  const [fullScreen, setFullScreen] = useState(false);

  // 自定义表单
  const [formLibraryVisible, setFormLibraryVisible] = useState(false);
  const [formDatas, setFormDatas]: [any[], any] = useState([]);

  // 完成
  const [completeVisible, setCompleteVisible] = useState(false);
  const [completeSummary, setCompleteSummary] = useState('');

  // 审核
  const [reviewVisible, setReviewVisible] = useState(false);
  const [reviewOpinion, setReviewOpinion] = useState('');
  const [reviewResult, setReviewResult] = useState(1);

  // 撤销
  const [revokeVisible, setRevokeVisible] = useState(false);
  const [revokeReason, setRevokeReason] = useState('');
  const [revokeLoading, setRevokeLoading] = useState(false);

  // 转交
  const [transferVisible, setTransferVisible] = useState(false);
  const [currentTransfer, setCurrentTransfer] = useState();
  const [transferSummary, setTransferSummary] = useState();

  // 恢复loading
  const [restoreLoading, setRestoreLoading] = useState(false);

  // 通知
  const [notificationWay, setNotificationWay] = useState({});

  function showFormLibrary(formData: any) {
    // formData
    setFormLibraryVisible(true);
  }

  function hideFormLibrary() {
    setFormLibraryVisible(false);
  }

  function onFormDndOver(formData: any[]) {
    setFormDatas(formData);
    setFormLibraryVisible(false);
  }

  function onCloses(e: any) {
    hideFormLibrary();
    drawerClose && drawerClose(e);
    setFullScreen(false);
  }
  /**
   * 通知
   */
  function notifiChange(values: any) {
    setNotificationWay(values);
  }
  /**
   * 判断当前最高权限身份
   */
  function identy() {
    return details.identity.发起人 ?
      "发起人" :
      details.identity.负责人 ?
        "负责人" : "参与人";
  }
  /**
   * 编辑
   */
  function onEdit() {
    hideFormLibrary()
  }
  /**
   * 撤销
   */
  function onRevoke() {
    setRevokeVisible(true);
  }

  function submitRevoke() {
    setRevokeLoading(true);
    const id = details.id;
    (async () => {
      const result = await req('/api/c_assist_cancel', {
        method: 'POST',
        body: {
          id,
          initiate_opinion: revokeReason,
          notification_way: notificationWay
        }
      })
      if (result.status === 'success') {
        setRevokeLoading(false);
        setRevokeVisible(false);
        onUpdate && onUpdate(id); // 刷新抽屉
        updateList && updateList(); // 刷新列表
      }
    })()
  }

  /**
   * 接收
   */
  function onReceive() {
    const id = details.id;
    (async () => {
      const result = await req('/api/c_assist_receiveButton', {
        method: 'POST',
        body: {
          id,
          identy: identy()
        }
      })
      if (result.status === 'success') {
        onUpdate && onUpdate(id);
        updateList && updateList();
      }
    })()
  }
  /**
   * 拒绝
   */
  function onRefuse() {
    const id = details.id;
    (async () => {
      const result = await get('/api/c_assist_rejectButton', {
        params: {
          id,
          identy: identy()
        }
      })

      if (result.status === 'success') {
        onUpdate && onUpdate(id);
        updateList && updateList();
      }
    })()
  }

  /** 转交 */
  function onTransfer() {
    setTransferVisible(true);
  }
  /** 提交转交 */
  function submitTransfer() {
    const id = details.id;
    const type = identity.负责人
      ? '负责人' :
      '内部';
    (async () => {
      const result = await req('/api/c_assist_transferButton', {
        method: 'POST',
        body: {
          type,
          collaborative_task_id: id,
          transferred_person: currentTransfer.key,
          transfer_reason: transferSummary,
          notification_way: notificationWay
        }
      })
      if (result.status === 'success') {
        setTransferVisible(false);
        onUpdate && onUpdate(id);
        updateList && updateList();
      }
    })()
  }
  /** 完成 */
  function onComplete() {
    setCompleteVisible(true);
  }
  /** 提交完成 */
  function submitComplete() {
    const id = details.id;
    (async () => {
      const result = await req('/api/c_assist_carryOutButton', {
        method: 'POST',
        body: {
          id,
          identy: identy(),
          opinion: completeSummary,
          notification_way: notificationWay
        }
      })

      if (result.status === 'success') {
        setCompleteVisible(false);
        onUpdate && onUpdate(id);
        updateList && updateList();
      }
    })()
  }
  /**
   * 审核
   */
  function onReview() {
    setReviewVisible(true);
  }
  /**
   * 提交审核
   */
  function submitReview() {
    const id = details.id;
    (async () => {
      const result = await req('/api/c_assist_auditButton', {
        method: 'POST',
        body: {
          id,
          isAgree: reviewResult,
          opinion: reviewOpinion,
          notification_way: notificationWay
        }
      })

      if (result.status === 'success') {
        setReviewVisible(false);
        onUpdate && onUpdate(id);
        updateList && updateList();
      }
    })()
  }
  /**
   * 恢复
   */
  function onRestore() {
    setRestoreLoading(true);
    const id = details.id;
    (async () => {
      const result = await get('/api/c_assist_recoveryTask', {
        params: {
          id
        }
      })

      if (result.status === 'success') {
        setRestoreLoading(false);
        message.success('恢复成功');
        onUpdate && onUpdate(id);
        updateList && updateList();
      }
    })()
  }
  /**
   * 删除
   */
  function onDelete() {
    const id = details.id;
    (async () => {
      const result = await req('/api/c_assist_deleteTask', {
        method: 'DELETE',
        body: {
          id
        }
      })

      if (result.status === 'success') {
        message.success('删除成功');
        drawerClose && drawerClose();
        updateList && updateList();
      }
    })()
  }
  function toggleFullScreen() {
    setFullScreen(!fullScreen);
  }

  function handleButton(identit: any, myStatus: any, assistStatus: any, isCancel: any) {
    let buttons: any[] = [];
    if (!identit || !myStatus) {
      return buttons
    }
    if (assistStatus === '已完成') {
      buttons = ['该协助已完成']
      return buttons
    }
    if (identit.发起人) {
      if (identit.负责人) {
        if (isCancel === 1) {
          buttons = ['恢复', '删除']
        } else {
          switch (myStatus) {
            case '进行中':
              buttons = ['完成', '编辑', '撤销', '转交']
              break
            default:
              break
          }
        }
      } else {
        if (isCancel === 1) {
          buttons = ['恢复', '删除']
        } else {
          switch (myStatus) {
            case '待接收':
              buttons = ['编辑', '撤销']
              break
            case '已拒绝':
              buttons = ['已拒绝']
              break
            case '进行中':
              buttons = ['编辑', '撤销']
              break
            case '待审核':
              buttons = ['审核', '撤销']
              break
            case '已完成':
              buttons = ['该协助已完成']
              break
            default:
              break
          }
        }
      }
    } else if (identit.负责人) {
      if (isCancel === 1) {
        buttons = ['该协助已撤销']
      } else {
        switch (myStatus) {
          case '待接收':
            buttons = ['接收', '拒绝']
            break
          case '进行中':
            buttons = ['完成', '编辑', '转交']
            break
          case '已拒绝':
            buttons = ['已拒绝']
            break
          case '已完成':
            if (assistStatus === '待审核') {
              buttons = ['待审核']
            } else if (assistStatus === '已完成') {
              buttons = ['该协助已完成']
            }
            break
          default:
            break
        }
      }
    } else if (identit.参与人) {
      if (isCancel === 1) {
        buttons = ['该协助已撤销']
      } else {
        switch (myStatus) {
          case '待接收':
            buttons = ['接收', '拒绝']
            break
          case '已接收':
            buttons = ['完成', '转交']
            break
          case '进行中':
            if (assistStatus === '待接收') {
              buttons = ['待负责人接收']
            } else {
              buttons = ['完成']
            }
            break
          case '已完成':
            if (assistStatus === '进行中') {
              buttons = ['待负责人完成']
            }
            else if (assistStatus === '待审核') {
              buttons = ['待发起人审核']
            } else if (assistStatus === '已完成') {
              buttons = ['该协助已完成']
            }
            break
          case '已拒绝':
            buttons = ['已拒绝']
            break
          default:
            break
        }
      }
    }
    return buttons
  }
  const buttonMap = (key: string | number) => {
    return {
      "恢复": (
        <Popconfirm key={key} title="确定要恢复这个协助吗?" onConfirm={onRestore} okText="确定" cancelText="取消">
          <Button style={{ marginRight: 15 }} loading={restoreLoading}>恢复</Button>
        </Popconfirm>
      ),
      "删除": (
        <Popconfirm key={key} title="确定要删除这个协助吗?" onConfirm={onDelete} okText="确定" cancelText="取消">
          <Button type="danger">删除</Button>
        </Popconfirm>
      ),
      "完成": (
        <Button key={key} onClick={onComplete} type="primary" style={{ marginRight: 15 }}>
          完成
        </Button>
      ),
      "编辑": (
        <Button key={key} onClick={onEdit} style={{ marginRight: 15 }}>
          <Link to={{ pathname: '/work/assist/template', state: { details } }}>编辑</Link>
        </Button>
      ),
      "审核": (
        <Button key={key} onClick={onReview} type="primary" style={{ marginRight: 15 }}>
          审核
        </Button>
      ),
      "撤销": (
        <Button key={key} onClick={onRevoke} style={{ marginRight: 15 }}>
          撤销
        </Button>
      ),
      "接收": (
        <Button key={key} onClick={onReceive} type="primary" style={{ marginRight: 15 }}>
          接收
        </Button>
      ),
      "拒绝": (
        <Button key={key} onClick={onRefuse} type="danger">
          拒绝
        </Button>
      ),
      "转交": (
        <Button key={key} onClick={onTransfer}>
          转交
        </Button>
      ),
      "已拒绝": (
        <Button key={key} type="danger" style={{ marginRight: 15 }}>
          已拒绝
        </Button>
      ),
      "待审核": (
        <Button key={key} style={{ marginRight: 15 }}>
          待审核
        </Button>
      ),
      "待负责人接收": (
        <Button key={key} style={{ marginRight: 15 }}>
          待负责人接收
        </Button>
      ),
      "待负责人完成": (
        <Button key={key} style={{ marginRight: 15 }}>
          待负责人完成
        </Button>
      ),
      "待发起人审核": (
        <Button key={key} style={{ marginRight: 15 }}>
          待发起人审核
        </Button>
      ),
      "该协助已完成": (
        <Button key={key} style={{ marginRight: 15 }}>
          该协助已完成
        </Button>
      ),
      "该协助已撤销": (
        <Button key={key} style={{ marginRight: 15 }}>
          该协助已撤销
        </Button>
      ),
    }
  }

  const statusColorMap = {
    "待接收": "#ccc",
    "已拒绝": "#cf1322",
    "进行中": "#1890ff",
    "已完成": "#1890ff"
  }
  useEffect(() => {
    setFormDatas(details.formArea);
    hideFormLibrary();
  }, [details])

  const { initiate, title, description, principal, participate, limit_time, files, edit_form, identity, my_status, assist_status, is_cancel } = details;


  return (
    <div>
      <Drawer
        title={
          <div className="clearfix">
            <span title={fullScreen ? '缩小' : '放大'} className="drawer-full-screen" onClick={toggleFullScreen}><Icon type="fullscreen" /></span>
            <span>
              {initiate ? initiate[0].name : '火星人'}发起的协作
            </span>
          </div>
        }
        placement="right"
        mask={false}
        onClose={onCloses}
        visible={visible}
        width={fullScreen ? '100%' : '700'}
        getContainer={getContainer}
        className="assist-drawer"
      >
        <Spin wrapperClassName="spin-full-wrapper" spinning={loading} delay={300}>
          <div className="assist-drawer-con">
            <p>
              <TextLabel text="标题" />{title}
            </p>
            <div>
              <p><TextLabel text="描述" /></p>
              <div dangerouslySetInnerHTML={{ __html: description }} />
            </div>
            <p><TextLabel text="完成时间" />{limit_time ? limit_time : '不限时间'}</p>
            {(() => {
              if (formDatas) {
                return (
                  <div style={{ marginBottom: '1em' }}>
                    <p className="clearfix">
                      <TextLabel text="自定义表单区域" />
                      {
                        assist_status === '已完成'
                          ? null
                          : is_cancel === 1
                            ? null
                            : edit_form
                              ?
                              (
                                <Tooltip placement="topRight" title="编辑表单">
                                  <Button style={{ float: 'right' }} type="primary" icon="form" onClick={showFormLibrary} />
                                </Tooltip>
                              )
                              : null
                      }
                    </p>
                    <div style={{ padding: '30px 0 0', borderWidth: '1px', borderColor: '#999', borderStyle: 'dashed solid dashed solid' }}>
                      <FormArea
                        formData={formDatas as any}
                        id={details.id}
                        canSubmit={
                          assist_status === '已完成'
                            ? false
                            : is_cancel === 1
                              ? false
                              : edit_form
                                ? true
                                : false
                        }
                      />
                    </div>
                  </div>
                )
              } else {
                return (
                  edit_form
                    ? (
                      <div style={{ marginBottom: '1em' }}>
                        <p className="clearfix">
                          <TextLabel text="自定义表单区域" />
                          {assist_status === '已完成'
                            ? null
                            : is_cancel === 1
                              ? null
                              : edit_form
                                ? (
                                  <Tooltip placement="topRight" title="编辑表单">
                                    <Button style={{ float: 'right' }} type="primary" icon="form" onClick={showFormLibrary} />
                                  </Tooltip>
                                )
                                : null
                          }
                        </p>
                        <div style={{ padding: '15px', borderWidth: '1px', borderColor: '#999', borderStyle: 'dashed solid dashed solid' }}>
                          暂无表单
                      </div>
                      </div>
                    )
                    : null
                )
              }
            })()}
            {files && files.length !== 0 && <div><TextLabel text="附件" /><AnnexList dataSource={files} /></div>}
            <Divider />
            <p><TextLabel text="发起人" /> {initiate && initiate[0].name}</p>
            {principal && <p><TextLabel text="负责人" /><span title={principal[0].status} style={{ color: statusColorMap[principal[0].status] }}> {principal[0].name}</span></p>}
            {participate && <p><TextLabel text="参与人" /> {participate.map((item: any, k: number) => (
              <span key={k} title={item.status} style={{ color: statusColorMap[item.status] }}>{item.name}{k === participate.length - 1 ? '' : '、'}</span>
            ))}</p>}
            <Divider />
            {principal && principal[0].opinion && <p><TextLabel text="任务总结" /> {principal[0].opinion}</p>}
            {initiate && initiate[0].opinion && <p><TextLabel text="审核意见" /> {initiate[0].opinion}</p>}
          </div>
          <div
            style={{
              position: 'absolute',
              left: 0,
              bottom: 0,
              width: '100%',
              borderTop: '1px solid #e9e9e9',
              padding: '10px 16px',
              background: '#fff',
            }}
          >
            {
              handleButton(identity, my_status, assist_status, is_cancel).map((item, key) => buttonMap(key)[item])
            }
          </div>
        </Spin>
      </Drawer>
      <FormLibrary
        visible={formLibraryVisible}
        onOk={onFormDndOver}
        onCancel={hideFormLibrary}
        defalutData={formDatas}
      />
      <Modal
        title="协助转交"
        visible={transferVisible}
        onOk={submitTransfer}
        onCancel={() => setTransferVisible(false)}
      >
        <Row type="flex">
          <Col span={5} style={{ paddingRight: '10px', textAlign: 'right' }}><TextLabel text="转交给" /></Col>
          <Col span={19}>
            <SelectPrincipal
              placeholder="请选择转交人员"
              selectedInfo={currentTransfer}
              onChange={setCurrentTransfer} />
          </Col>
        </Row>
        <Row type="flex" style={{ marginTop: '20px' }}>
          <Col span={5} style={{ paddingRight: '10px', textAlign: 'right' }}><TextLabel text="总结" /></Col>
          <Col span={19}>
            <Input.TextArea
              autosize={{ minRows: 3, maxRows: 10 }}
              value={transferSummary}
              onChange={(e) => setTransferSummary(e.target.value)}
            />
          </Col>
        </Row>
        <Row type="flex" style={{ marginTop: '20px' }}>
          <Col span={5} style={{ paddingRight: '10px', textAlign: 'right' }}><TextLabel text="通知方式" /></Col>
          <Col span={19}>
            <NotifiMeds onChange={notifiChange} />
          </Col>
        </Row>
      </Modal>
      <Modal
        title="完成协助"
        visible={completeVisible}
        onOk={submitComplete}
        onCancel={() => setCompleteVisible(false)}
      >
        <Row type="flex">
          <Col span={5} style={{ paddingRight: '10px', textAlign: 'right' }}><TextLabel text="总结" /></Col>
          <Col span={19}>
            <Input.TextArea
              autosize={{ minRows: 3, maxRows: 10 }}
              value={completeSummary}
              onChange={(e) => setCompleteSummary(e.target.value)}
            />
          </Col>
        </Row>
        <Row type="flex" style={{ marginTop: '20px' }}>
          <Col span={5} style={{ paddingRight: '10px', textAlign: 'right' }}><TextLabel text="通知方式" /></Col>
          <Col span={19}>
            <NotifiMeds onChange={notifiChange} />
          </Col>
        </Row>
      </Modal>
      <Modal
        title="审核协助"
        visible={reviewVisible}
        onOk={submitReview}
        onCancel={() => setReviewVisible(false)}
      >
        <Row type="flex">
          <Col span={5} style={{ paddingRight: '10px', textAlign: 'right' }}><TextLabel text="协助完成" /></Col>
          <Col span={19}>
            <Radio.Group onChange={(e) => { setReviewResult(e.target.value) }} value={reviewResult}>
              <Radio value={1}>同意</Radio>
              <Radio value={0}>不同意</Radio>
            </Radio.Group>
          </Col>
        </Row>
        <Row type="flex" style={{ marginTop: '20px' }}>
          <Col span={5} style={{ paddingRight: '10px', textAlign: 'right' }}><TextLabel text="意见" /></Col>
          <Col span={19}>
            <Input.TextArea
              autosize={{ minRows: 3, maxRows: 10 }}
              value={reviewOpinion}
              onChange={(e) => setReviewOpinion(e.target.value)}
            />
          </Col>
        </Row>
        <Row type="flex" style={{ marginTop: '20px' }}>
          <Col span={5} style={{ paddingRight: '10px', textAlign: 'right' }}><TextLabel text="通知方式" /></Col>
          <Col span={19}>
            <NotifiMeds onChange={notifiChange} />
          </Col>
        </Row>
      </Modal>
      <Modal
        title="撤销协助"
        visible={revokeVisible}
        onOk={submitRevoke}
        onCancel={() => setRevokeVisible(false)}
        confirmLoading={revokeLoading}
      >
        <Row type="flex">
          <Col span={5} style={{ paddingRight: '10px', textAlign: 'right' }}><TextLabel text="总结" /></Col>
          <Col span={19}>
            <Input.TextArea
              autosize={{ minRows: 3, maxRows: 10 }}
              value={revokeReason}
              onChange={(e) => setRevokeReason(e.target.value)}
            />
          </Col>
        </Row>
        <Row type="flex" style={{ marginTop: '20px' }}>
          <Col span={5} style={{ paddingRight: '10px', textAlign: 'right' }}><TextLabel text="通知方式" /></Col>
          <Col span={19}>
            <NotifiMeds onChange={notifiChange} />
          </Col>
        </Row>
      </Modal>
    </div >
  )
}