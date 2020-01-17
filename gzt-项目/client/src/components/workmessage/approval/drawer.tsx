import * as React from 'react'
import { Drawer, Icon, Button, Timeline, Spin, Avatar, Row, Col, message, Popconfirm, Divider, Tooltip } from 'antd'
import AnnexList from '../../../components/annexList';
import req, { get } from '../../../utils/request';
import ModalForm from './modalForm'
import { connect } from 'dva';
import SelectPersonnelModal from '../../../components/selectPersonnelModal';
import "./index.scss"
import TextLabel from '../../../components/textLabel';
import { Link } from 'react-router-dom';
import ShowCollapse from '../../../pages/work/approval/Base/ShowCollapse';
import PrintPdf from '../../../pages/work/approval/Base/PrintPdf';
import PersonalCardModal from '../../../layouts/personalCard';

interface ApprovalDrawerProps {
  visible: boolean;
  loading?: boolean;
  onClose: any;
  details?: any;
  updateList?: any;
  onCloseDrawer?: any;
  getContainer?: string;
  onUpdate?: any;
}
const timeLineStatusToColor = {
  "审批中": '#1890ff',
  "待接收": '#52c41a',
  "拒绝": '#f5222d'
}
const mapStatusToColor = {
  "通过": '#1890ff',
  "审批中": '#E8A54C',
  "拒绝": '#f5222d',
  "未接收": '#7cd252',
  "已转交": '#17C295'
}
const NAMESPACE = 'Approval'; // dva model 命名空间
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE]
  }
};
@connect(mapStateToProps)
export default class ApprovalDrawer extends React.Component<ApprovalDrawerProps, any> {
  state = {
    fullScreen: false,
    agreeVisible: false,
    refuseVisible: false,
    transferVisible: false,
    reasonVisible: false,
    urgentVisible: false,
    printVisible: false,
    pdfLoading: false,
    completeSummary: '',
    file: '',
    chooseInfo: {} as any,
    businessCardVisible: false,
    cardInfo: null
  }

  toggleFullScreen = () => {
    this.setState({
      fullScreen: !this.state.fullScreen
    })
  }
  /**
   * 关闭抽屉
   */
  onCloseDrawer = () => {
    this.props.onClose();
  }

  /**
   * 拒绝
   */
  onRefuse = () => {
    this.setState({
      refuseVisible: true
    })
  }

  /**
   * @param id 审批id
   * @param completeSummary 拒绝的内容
   * @param notificationWay 拒绝的通知方式
   * 提交拒绝
   */

  submitRefuse = ({ completeSummary, notificationWay }: any) => {
    const id = this.props.details && this.props.details.id;
    (async () => {
      const result = await req('/api/c_approval_refuse', {
        method: 'POST',
        body: {
          approval_id: id,
          opinion: completeSummary,
          notification_way: notificationWay
        }
      })
      if (result.status === 'success') {
        this.props.onUpdate && this.props.onUpdate({ id });//执行父组件的方法，刷新抽屉
        this.props.updateList && this.props.updateList();//刷新列表
      }
      this.setState({
        refuseVisible: false
      })
    })()
  }

  /**
   * 同意
   */
  onAgreeModal = () => {
    this.setState({
      agreeVisible: true
    })
  }

  /**
   * @param id 审批id
   * @param completeSummary 同意的内容
   * @param notificationWay 同意的通知方式
   * 提交同意
   */
  submitAgree = ({ completeSummary, notificationWay }: any) => {
    const id = this.props.details && this.props.details.id;
    (async () => {
      const result = await req('/api/c_approval_agree', {
        method: 'POST',
        body: {
          approval_id: id,
          opinion: completeSummary,
          notification_way: notificationWay
        }
      })
      this.setState({
        agreeVisible: false
      })
      if (result.status === 'success') {
        this.props.onUpdate && this.props.onUpdate({ id });;
        this.props.updateList && this.props.updateList();
      } else {
        message.info('服务器繁忙，请稍后再试~')
      }
    })()
  }

  /**
   * @param id 审批id
   * 撤销
   */
  onRevoke = () => {
    const id = this.props.details && this.props.details.id;
    (async () => {
      const result = await get('/api/c_approval_cancel', {
        params: {
          approval_id: id,
        }
      })
      if (result.status === 'success') {
        message.success('撤销成功');
        this.onCloseDrawer();
        this.props.updateList && this.props.updateList()
      } else {
        message.info('服务器繁忙，请稍后再试~')
      }
    })()
  }

  /**
   *  @param id 审批id
   *  归档
   */
  onArchive = () => {
    const id = this.props.details && this.props.details.id;
    (async () => {
      const result = await get('/api/c_approval_archive', {
        params: {
          approval_id: id,
        }
      })
      if (result.status === 'success') {
        message.success('归档成功');
        this.onCloseDrawer();
        this.props.updateList && this.props.updateList();
      } else {
        message.info('服务器繁忙，请稍后再试~')
      }
    })()
  }

  /**
   * 转交
   */
  onTransfer = () => {
    this.setState({
      transferVisible: true
    })
  }

  /**
   * 展示选择转交人员树
   */
  okTransferModal = (checkedInfo: any, e: any) => {

    this.setState({
      transferVisible: false,
      reasonVisible: true,
      chooseInfo: checkedInfo
    })
  }

  /**
   *  @param id 审批id
   *  @param completeSummary 转交的内容
   *  @param notificationWay 转交的通知方式
   *  @param transferee_id 转交人id
   *  提交转交
   */
  submitTransfer = ({ completeSummary, notificationWay }: any) => {
    const id = this.props.details && this.props.details.id;
    (async () => {
      const result = await req('/api/c_approval_transfer', {
        method: 'POST',
        body: {
          approval_id: id,
          opinion: completeSummary,
          transferee_id: this.state.chooseInfo.key,
          notification_way: notificationWay
        }
      })
      if (result.status === 'success') {
        message.success(result.message);
        this.onCloseDrawer();
        this.props.updateList && this.props.updateList();
      } else if (result.status === 'fail') {
        message.info(result.message);
      } else {
        console.log(result.message);
        message.info('服务器异常，请稍后再试');
      }
      this.setState({
        reasonVisible: false
      })
    })()
  }

  /**
   * 催办
   */
  onUrgent = () => {
    this.setState({
      urgentVisible: true
    })
  }

  /**
   *  @param id 审批id
   *  @param completeSummary 催办的内容
   *  @param notificationWay 催办的通知方式
   * 提交催办
   */

  submitUrgent = ({ completeSummary, notificationWay }: any) => {
    const id = this.props.details && this.props.details.id;
    (async () => {
      const result = await req('/api/c_approval_urgent', {
        method: 'POST',
        body: {
          approval_id: id,
          opinion: completeSummary,
          notification_way: notificationWay
        }
      })
      this.setState({
        urgentVisible: false
      })
      if (result.status === 'success') {
        this.props.onUpdate && this.props.onUpdate({ id });;
        this.props.updateList && this.props.updateList();
        message.info('提交催办成功')
      } else {
        message.info('服务器繁忙，请稍后再试~')
      }
    })()
  }

  /**
   * @param details 详细数据信息
   * 导出excel
   */

  onExport = (details: any) => {
    this.exportFile(details, 'excel')
  }

  /**
   *  导出pdf
   */

  onExportPdf = (details: any) => {
    this.exportFile(details, 'pdf');
  }

  /**
   * @param details 详细数据信息
   * @param type 导出类型(excel/pdf)
   * 导出Excel，PDF 函数
   */

  exportFile = (details: any, type: string) => {
    (async () => {
      const result = await req('/api/c_approval_export', {
        method: 'POST',
        body: {
          data: details,
          export_type: type
        },
        getFile: true
      });
      let blobUrl = window.URL.createObjectURL(result.blob);
      const a = document.createElement('a');
      a.download = decodeURI(result.headers.get('filename'));//获取文件名
      a.href = blobUrl;
      a.click();
      window.URL.revokeObjectURL(blobUrl);
      message.info('导出成功');
    })()
  }

  /**
   * 预览
   */
  onPreview = (details: any) => {
    this.setState({
      printVisible: true,
      pdfLoading: true
    });
    (async () => {
      const result = await req('/api/c_approval_export', {
        method: 'POST',
        body: {
          data: details,
          export_type: "pdf"
        },
        getFile: true
      });
      let blobUrl = await window.URL.createObjectURL(result.blob);
      this.setState({
        file: blobUrl,
        pdfLoading: false
      })
    })()
  }
  /** 展示用户名片 */
  showBusinessCard = async (user_id: string | number) => {
    this.setState({
      businessCardVisible: true
    })
    const result = await get(`/api/u_get_card_info?user_id=${user_id}`);
    if (result.status === 'success') {
      this.setState({
        cardInfo: result.data
      })
    }

  }
  render() {
    const { visible, details, loading } = this.props;
    const { fullScreen, agreeVisible, refuseVisible, transferVisible, reasonVisible, urgentVisible, printVisible, file, pdfLoading } = this.state;
    const agreeModalProps = {
      title: "审批意见",
      visible: agreeVisible,
      onCancel: () => {
        this.setState({
          agreeVisible: false
        })
      },
      onOk: this.submitAgree,
      placeholder: '请输入完成意见'
    }
    const refuseModalProps = {
      title: "拒绝意见",
      visible: refuseVisible,
      onCancel: () => {
        this.setState({
          refuseVisible: false
        })
      },
      onOk: this.submitRefuse,
      placeholder: '请输入拒绝意见'
    }
    const receivePersonProps = {
      checkable: false,
      visible: transferVisible,
      onCancel: () => {
        this.setState({
          transferVisible: false
        })
      },
      onOk: this.okTransferModal
    }

    const transferProps = {
      // title: "转交给"+(this.state.chooseInfo as any).checkedPersonnels.map((item: any) => item.title),
      title: "转交信息",
      visible: reasonVisible,
      onCancel: () => {
        this.setState({
          reasonVisible: false
        })
      },
      onOk: this.submitTransfer,
      placeholder: '请输入转交说明'
    }

    const urgentProps = {
      title: "催办信息",
      visible: urgentVisible,
      onCancel: () => {
        this.setState({
          urgentVisible: false
        })
      },
      onOk: this.submitUrgent
    }

    return (
      <>
        <Drawer
          title={
            <div className="clearfix">
              <span>
                申请
              </span>
              <span title={fullScreen ? '缩小' : '放大'} className="drawer-full-screen" onClick={this.toggleFullScreen}><Icon type="fullscreen" /></span>
            </div>
          }
          placement="right"
          mask={false}
          onClose={this.onCloseDrawer}
          visible={visible}
          width={fullScreen ? '100%' : '600'}
          getContainer={this.props.getContainer}
          className="approval-drawer"
        >
          <Spin wrapperClassName="spin-full-wrapper" spinning={loading} delay={300}>
            {
              (() => {
                if (details) {
                  const { id, files, approval_number, end_status, process_template, sponsor_data, button_status, cc_my, form_template } = details;
                  return (

                    <div className="approval-drawer-con">
                      <div style={{ textAlign: 'right' }}>
                        {
                          button_status.map((item: any, index: any) => {
                            const { permission, title } = item;
                            return (
                              (() => {
                                if (permission) {
                                  switch (title) {
                                    case '导出Excel':
                                      return (
                                        <Tooltip key={index} placement="bottom" title="导出Excel" >
                                          <span onClick={() => this.onExport(details)}><Icon type="file-excel" className='operate-btn' /></span>
                                        </Tooltip>
                                      )
                                    case '下载PDF':
                                      return (
                                        <Tooltip key={index} placement="bottom" title="导出PDF">
                                          <span onClick={() => this.onExportPdf(details)}><Icon type="file-pdf" className='operate-btn' /></span>
                                        </Tooltip>
                                      )
                                    case '预览':
                                      return (
                                        <Tooltip key={index} placement="bottom" title="预览">
                                          <span onClick={() => this.onPreview(details)}><Icon type="file-protect" className='operate-btn' /></span>
                                        </Tooltip>
                                      )
                                    // case '打印':
                                    //   return (
                                    //     <>
                                    //       <Tooltip placement="bottom" title="打印">
                                    //         <span key={index} onClick={this.onPrint}><Icon type="printer" className='operate-btn' /></span>
                                    //       </Tooltip>

                                    //     </>
                                    //   )
                                    default:
                                      return null
                                  }
                                } else {
                                  return null;
                                }
                              })()
                            )
                          })
                        }
                      </div>
                      <div style={{ borderBottom: '1px solid #eee' }}>
                        {approval_number !== 'null' ? (<p><TextLabel text="审批编号" />{approval_number}</p>) : null}
                        {/* <p>方式：{name}</p> */}
                        {/* <p>类型：{approval_method}</p> */}
                        {

                          form_template && form_template.map(({ type, field, value }: any, index: any) => {
                            if (!value) {
                              return null
                            }
                            if (type === 'DATERANGE') {
                              value = Array.isArray(value) ? value.join(' ~ ') : '请假时间飞到火星了，请联系申请人重新申请'
                            } else if (type === 'ANNEX' && value.length !== 0) {
                              return (
                                <div key={index}>
                                  <TextLabel text={field.label} />
                                  {value && value.map(({ name, oss_path }: any, k: any) => {
                                    return (
                                      <div key={k}><a target="_blank" rel="noopener noreferrer" href={oss_path}>{name}</a></div>
                                    )
                                  })
                                  }
                                </div>
                              )
                            }
                            return (
                              <p key={index}>
                                <TextLabel text={field.label} />{value}
                              </p>
                            )

                          })
                        }

                        <p><TextLabel text="状态" /> {end_status}</p>
                        {files && files.length !== 0 && <div> <TextLabel text="附件" /><AnnexList dataSource={files} /></div>}

                      </div>
                      <div style={{ marginTop: '30px', paddingBottom: '100px' }}>
                        {/* <div>
                          <span>sponsor_data：</span>
                          {sponsor_data.time}
                          {sponsor_data.type}
                          {sponsor_data.user_id}
                          {sponsor_data.user_name}
                          发起人的信息
                        </div> */}
                        {/* <Timeline pending={end_status==='审批中'}> */}

                        <Timeline>
                          <Timeline.Item color="blue">
                            <Row className='process-info' type="flex" justify="space-around" align="middle">
                              <Col span={16} >
                                <span className="cursor-pointer" onClick={() => this.showBusinessCard(sponsor_data.user_id)}>
                                  <Avatar style={{ marginRight: '16px' }} >{sponsor_data.user_name}</Avatar>
                                </span>
                                <TextLabel text={sponsor_data.user_name} /><span>发起的了申请</span>
                              </Col>
                              <Col span={8}>
                                <span>{sponsor_data.time}</span>
                              </Col>
                            </Row>
                          </Timeline.Item>
                          {
                            process_template && process_template.map((item: any, key: any) => {
                              const { approval_level, approval_type, class_status, data } = item;
                              let currentTimeline;
                              if (key === process_template.length - 1) {
                                if (class_status === "通过") {
                                  currentTimeline = <Icon type="check-circle" style={{ fontSize: '16px', color: 'green' }} />
                                } else if (class_status === "审批中") {
                                  currentTimeline = <Icon type="loading" style={{ fontSize: '16px', color: '#1890ff' }} />
                                } else if (class_status === "待接收") {
                                  currentTimeline = <Icon type="clock-circle-o" style={{ fontSize: '16px', color: '#1890ff' }} />
                                } else {
                                  currentTimeline = <Icon type="close-circle" style={{ fontSize: '16px', color: '#f5222d' }} />
                                }
                              }
                              return (
                                <Timeline.Item dot={currentTimeline} color={timeLineStatusToColor[class_status]} key={approval_level}>
                                  <ShowCollapse
                                    type={approval_type}
                                    title={
                                      <div>
                                        {
                                          approval_type === "countersign"
                                            ? <Avatar icon="user" style={{ marginRight: '16px' }} />
                                            : approval_type === "orSign"
                                              ? <Avatar icon="user" style={{ marginRight: '16px' }} />
                                              : null
                                        }
                                        {
                                          approval_type === "countersign"
                                            ? <span>{data.length}人会签: </span>
                                            : approval_type === "orSign"
                                              ? <span>{data.length}或签: </span>
                                              : null
                                        }
                                        <span>{class_status}</span>
                                      </div>
                                    }
                                    render={
                                      data.map((userInfo: any, index: any) => {
                                        const { user_id, user_name, status, time, transferee_user, opinion } = userInfo;
                                        return (
                                          <Row key={index} className='process-info' type="flex" align="middle">
                                            <Col span={10} >
                                              <span className="cursor-pointer" onClick={() => this.showBusinessCard(user_id)}>
                                                <Avatar style={{ marginRight: '16px' }} >
                                                  {user_name}
                                                </Avatar>
                                              </span>

                                              <span>{user_name}</span>
                                            </Col>
                                            <Col span={6}>
                                              <span style={{ color: mapStatusToColor[status] }}>{status}</span>
                                            </Col>
                                            {
                                              class_status !== "待接收"
                                                ?
                                                (
                                                  <Col span={8}>
                                                    <span>{time}</span>
                                                  </Col>
                                                )
                                                : <Col span={8} />
                                            }
                                            {
                                              (status === "已转交" && transferee_user !== '' && transferee_user !== null)
                                                ? <div style={{ marginLeft: '48px' }}><TextLabel text="转交于" />{transferee_user.user_name}</div>
                                                : null
                                            }

                                            {
                                              (opinion !== '' && opinion !== null)
                                                ? <div style={{ marginLeft: '48px' }}> <TextLabel text="意见" />{opinion}</div>
                                                : null
                                            }

                                          </Row>
                                        )
                                      })
                                    }
                                  />
                                </Timeline.Item>
                              )
                            })
                          }
                        </Timeline>
                        {
                          (() => {
                            if (cc_my.length !== 0) {
                              return (
                                <div>
                                  <Divider />
                                  <p>
                                    抄送人员 <span style={{ paddingLeft: '10px' }}> 审批过程中抄送人员同步可见</span>
                                  </p>
                                  {
                                    cc_my.map((item: any, index: any) => {
                                      return (
                                        <span key={index} className="cursor-pointer" onClick={() => this.showBusinessCard(item.id)}>
                                          <Avatar style={{ marginRight: '10px' }}>
                                            {item.name}
                                          </Avatar>
                                        </span>
                                      )
                                    })
                                  }
                                </div>
                              )
                            } else {
                              return null;
                            }
                          })()
                        }
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
                          button_status.map((item: any, index: any) => {
                            const { permission, title } = item;
                            return (
                              (() => {
                                if (permission) {
                                  switch (title) {
                                    case '同意':
                                      return (
                                        <Button key={index} onClick={this.onAgreeModal} className="apply-btn" type="primary">同意</Button>
                                      )
                                    case '拒绝':
                                      return (
                                        <Button key={index} className="apply-btn" type="danger" onClick={this.onRefuse}>
                                          拒绝
                                        </Button>
                                      )
                                    case '转交':
                                      return (
                                        <Button key={index} className="apply-btn" onClick={this.onTransfer}>转交</Button>
                                      )
                                    case '撤销':
                                      return (
                                        <Popconfirm key={index} title="确定要撤销这个审批吗?" onConfirm={this.onRevoke} okText="确定" cancelText="取消">
                                          <Button className="apply-btn">撤销</Button>
                                        </Popconfirm>
                                      )
                                    case '归档':
                                      return (
                                        <Popconfirm key={index} title="确定要归档吗?" onConfirm={this.onArchive} okText="确定" cancelText="取消">
                                          <Button className="apply-btn">归档</Button>
                                        </Popconfirm>
                                      )
                                    case '催办':
                                      return (
                                        <Button key={index} className="apply-btn" onClick={this.onUrgent}>催办</Button>
                                      )
                                    case '再次申请':
                                      return (
                                        <Link
                                          key={index}
                                          to={{
                                            pathname: '/work/approval/template',
                                            state: { id, type: 'cancel' }
                                          }} >
                                          <Button className="apply-btn" onClick={this.onCloseDrawer}>重新提交</Button>
                                        </Link>
                                      )
                                    default:
                                      return null
                                  }
                                } else {
                                  return null;
                                }
                              })()
                            )
                          })
                        }
                      </div>
                    </div>
                  )
                } else return null;
              })()
            }
          </Spin>

        </Drawer>
        <ModalForm {...agreeModalProps} />
        <ModalForm {...refuseModalProps} />
        <ModalForm {...transferProps} />
        <ModalForm {...urgentProps} />
        <PrintPdf
          visible={printVisible}
          onCancel={() => this.setState({ printVisible: false })}
          file={file}
          loading={pdfLoading}
          exportPdf={() => this.onExportPdf(this.props.details)}
          exportExcel={() => this.onExport(this.props.details)}
        />
        <SelectPersonnelModal {...receivePersonProps} />
        <PersonalCardModal
          visible={this.state.businessCardVisible}
          onCancel={() => this.setState({ businessCardVisible: false })}
          dataSource={this.state.cardInfo}
        />
      </>
    )
  }
}