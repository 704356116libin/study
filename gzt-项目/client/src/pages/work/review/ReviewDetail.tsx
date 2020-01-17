import React, { createRef } from 'react';
import { Layout, Tabs, Button, message, Row, Col, List, Divider, Icon, Timeline, Modal, Input, Radio, Card, Avatar } from 'antd';
import { connect } from 'dva';
import { match, Link } from 'react-router-dom';
import { Location, History } from 'history';
import ApprovalItem from '../approval/Base/ApprovalItem';
import TextLabel from '../../../components/textLabel';
import SelectPersonnelModal from '../../../components/selectPersonnelModal';
import AnnexList from '../../../components/annexList';
import ReviewFormLibrary from '../../../components/reviewFormLibrary';
import Form, { WrappedFormUtils } from 'antd/lib/form/Form';
import moment from 'moment';
import request, { get } from '../../../utils/request';
import SimulationReviewForm from '../../../components/reviewFormLibrary/simulationReviewForm';
import MergeReviewForm from '../../../components/reviewFormLibrary/mergeReviewForm';
import { Dispatch } from 'redux';
import Exportpack from './exportmgt/exportpack';
import { RadioChangeEvent } from 'antd/lib/radio';
import ReviewPersonnels from './reviewPersonnels';
import PersonalCardModal from '../../../layouts/personalCard';
import NotifiMeds from '../../../components/notifiMeds';
import './reviewDetail.scss';

const { TabPane } = Tabs;
const { Content, Footer } = Layout;
const RadioGroup = Radio.Group;
const RadioButton = Radio.Button;

/** 添加表单 */
const AddForm = Form.create<any>()(({ form, onChange, formData, onSubmit }: any) => {

  return (
    <Form onSubmit={(e) => onSubmit(e, form)}>
      <SimulationReviewForm
        form={form}
        formData={formData}
        onChange={onChange}
        layout={{
          labelCol: {
            xxl: { span: 3 },
            xl: { span: 5 },
            lg: { span: 7 },
            md: { span: 9 }
          },
          wrapperCol: {
            xxl: { span: 10 },
            xl: { span: 19 },
            lg: { span: 17 },
            md: { span: 21 }
          }
        }}
      />
      {
        formData && formData.length !== 0 ? (
          <Form.Item
            wrapperCol={{
              xxl: { span: 10, offset: 3 },
              xl: { span: 19, offset: 5 },
              lg: { span: 17, offset: 7 },
              md: { span: 21, offset: 9 }
            }}
          >
            <Button type="primary" htmlType="submit">更新</Button>
          </Form.Item>
        ) : null
      }
    </Form>
  )
})

export interface StateToReviewDetailProps {
  /** 评审详情 */
  review: any;
  /** 评审相关附件列表 */
  files: any;
  /** 评审动态列表 */
  timeline: any;
  /** 关联审批列表 */
  linkApprovals: any;
  /** 关联评审列表 */
  linkReviews: any;
  /** 报告模板列表 */
  reports: any;
  /** 个人打包列表 */
  exportpacks: any;
  /** 所有追加的表单数据 */
  allAddFormData: any;
}
export interface DispatchToReviewDetailProps {
  /** 获取评审详情 ById */
  queryReviewById: (id: string, cb?: Function) => void;
  /** 获取评审动态列表 */
  queryTimeline: (params: any) => void;
  /** 处理一系列按钮操作 */
  mutationsReview: (pathname: string, body: any, id: string, params: any, cb: Function) => void;
  /** 获取关联审批列表 */
  queryLinkApprovals: (id: string) => void;
  /** 获取关联评审列表 */
  queryLinkReviews: (id: string) => void;
  /** 获取报告模板列表 */
  queryReports: Function;
  /** 获取个人打包列表 */
  queryExportpacks: Function;
  /** 获取所有追加表单的数据 */
  queryAllAddformData: (id: string, cb: Function) => void;
}


const NAMESPACE = 'Review';

const mapStateToProps = (state: any): StateToReviewDetailProps => {
  const hereState = state[NAMESPACE];
  return {
    review: hereState.review,
    files: hereState.files,
    linkApprovals: hereState.linkApprovals,
    linkReviews: hereState.linkReviews,
    timeline: hereState.timeline,
    reports: hereState.reports,
    exportpacks: hereState.exportpacks,
    allAddFormData: hereState.allAddFormData,
  }
}

const mapDispatchToProps = (dispatch: Dispatch): DispatchToReviewDetailProps => {
  return {
    queryReviewById(id, cb) {
      dispatch({
        type: `${NAMESPACE}/queryReviewById`,
        payload: { id, cb }
      })
    },
    queryTimeline(params) {
      dispatch({
        type: `${NAMESPACE}/queryTimeline`,
        payload: { params }
      })
    },
    queryLinkApprovals(id) {
      dispatch({
        type: `${NAMESPACE}/queryLinkApprovals`,
        payload: { id }
      })
    },
    queryLinkReviews(id) {
      dispatch({
        type: `${NAMESPACE}/queryLinkReviews`,
        payload: { id }
      })
    },
    mutationsReview(pathname, body, id, params, cb) {
      dispatch({
        type: `${NAMESPACE}/mutationsReview`,
        payload: {
          pathname, body, id, params, cb
        }
      })
    },
    queryReports: () => {
      dispatch({
        type: `${NAMESPACE}/queryReports`
      });
    },
    queryExportpacks: () => {
      dispatch({
        type: `${NAMESPACE}/queryExportpacks`
      });
    },
    queryAllAddformData: (id, cb) => {
      dispatch({
        type: `${NAMESPACE}/queryAllAddformData`,
        payload: { id, cb }
      });
    },
  }
}

interface ReviewDetailProps extends DispatchToReviewDetailProps, StateToReviewDetailProps {
  match: match<{ reviewId: string }>;
  history: History;
  location: Location;
}

/** 评审详情 */
@connect(mapStateToProps, mapDispatchToProps)
export default class ReviewDetail extends React.Component<ReviewDetailProps, any>{

  packageName: React.RefObject<any> = createRef();

  state = {
    transferPrincipalVisible: false,
    reviewFormLibraryVisible: false,
    addFormItems: [] as any[],
    allAddFormItems: [] as any[],
    activeKey: '1',
    completeModalVisible: false,
    summary: '',
    exportReportVisible: false,
    exportType: 'aloneExport',
    addPackModalVisible: false,
    ckeckedExports: [],
    combinedExports: [],
    exportPackName: '',
    currentEditingPack: null,
    businessCardVisible: false,
    cardInfo: null,
    notificationWay: {},
    invalidVisible: false,
    invalidLoading: false,
    invalidReason: '',
    currentCompleteType: 'all'
  }

  componentDidMount() {
    // 获取评审id
    const ReviewId = this.props.match.params.reviewId;
    this.props.queryReviewById(ReviewId, (data: any) => {
      // 负责人追加表单
      const principal = data.detail_info.inside_user.duty_user;
      if (principal.is_my && principal.form_data) {
        this.setState({
          addFormItems: principal.form_data
        })
      }

      for (const key in data.detail_info) {
        const item = data.detail_info[key];
        if (Array.isArray(item)) {
          for (const person of item) {
            if (person.is_my && person.form_data) {
              this.setState({
                addFormItems: person.form_data
              })
            }
          }
        } else {
          for (const person of data.detail_info.inside_user.inside_join_user) {
            if (person.is_my && person.form_data) {
              this.setState({
                addFormItems: person.form_data
              })
            }
          }
        }
      }
    });
    this.props.queryLinkReviews(ReviewId);
    this.props.queryLinkApprovals(ReviewId);
    this.props.queryTimeline({
      pst_id: ReviewId,
      noe_page: 1,
      page_size: 10
    });

  }
  componentDidUpdate(prevProps: any) {
    // 获取评审id
    const ReviewId = this.props.match.params.reviewId;

    if (prevProps.match.params.reviewId !== ReviewId) {
      this.props.queryReviewById(ReviewId);
      this.props.queryReviewById(ReviewId);
      this.props.queryLinkReviews(ReviewId);
      this.props.queryLinkApprovals(ReviewId);
      this.props.queryTimeline({
        pst_id: ReviewId,
        noe_page: 1,
        page_size: 10
      });
      this.setState({
        activeKey: '1'
      })
    }

  }
  /** 代理按钮操作所需要的公共数据 */
  proxyMutations = (pathname: string, body: any, cb: Function) => {
    this.props.mutationsReview(
      pathname,
      body,
      this.props.review.id,
      this.props.location.state.currentPosition,
      cb
    )
  }
  /** 接收 */
  onReceive = (params: any) => {
    this.proxyMutations('/api/c_pst_receive', params, () => {
      message.success('接收成功');
    })
  }
  /** 拒绝 */
  onRefuse = (params: any) => {
    this.proxyMutations('/api/c_pst_refuse_receive', params, () => {
      message.success('拒绝成功');
    })
  }
  /** 同意 */
  onAgree = (params: any) => {
    this.proxyMutations('/api/c_pst_duty_agree_join', params, () => {
      message.success('提交成功');
    })
  }
  /** 打回 */
  onGoBack = (params: any) => {
    this.proxyMutations('/api/c_pst_back', params, () => {
      message.success('打回成功');
    })
  }
  /** 显示移交负责人模态框 */
  showTransferPrincipalModal = () => {
    this.setState({
      transferPrincipalVisible: true
    })
  }
  /** 关闭移交负责人模态框 */
  transferPrincipalCancel = () => {
    this.setState({
      transferPrincipalVisible: false
    })
  }
  /** 移交负责人 */
  okTransferPrincipal = ({ key: duty_user_id }: any) => {
    const { id: pst_id, company_id } = this.props.review;

    const params = {
      pst_id,
      company_id,
      duty_user_id
    }
    this.proxyMutations('/api/c_pst_transfer_duty', params, () => {
      message.success('移交成功');
      this.setState({
        transferPrincipalVisible: false
      })
    })
  }
  /** 编辑 */
  onEdit = () => {
    console.log('编辑');
  }
  /** 撤回 */
  onRetract = () => {
    const { id } = this.props.review;
    this.proxyMutations('/api/c_pst_retract', {
      id
    }, () => {
      message.success('撤回成功');
    })
  }
  /** 召回 */
  onRecall = () => {
    const { id } = this.props.review;
    this.proxyMutations('/api/c_pst_recall', {
      id
    }, () => {
      message.success('召回成功');
    })
  }
  /** 显示作废模态框 */
  onInvalid = () => {
    this.setState({
      invalidVisible: true
    })
  }
  /** 作废 */
  submitInvalid = () => {
    this.setState({
      invalidLoading: true
    })
    const { id } = this.props.review;
    this.proxyMutations('/api/c_pst_cancle', {
      pst_id: id,
      cancle_reason: this.state.invalidReason
    }, () => {
      message.success('作废成功');
      this.setState({
        invalidLoading: false,
        invalidVisible: false
      })
    })
  }

  /** 处理表单添加 */
  handleAddForm = () => {
    this.setState({
      reviewFormLibraryVisible: true
    })
  }

  /** 处理表单添加 */
  reviewFormLibraryCancel = () => {
    this.setState({
      reviewFormLibraryVisible: false
    })
  }

  /** 处理表单添加 */
  handleAddFormOk = (formData: any) => {
    this.setState({
      reviewFormLibraryVisible: false,
      addFormItems: formData
    })
  }

  /** 提交添加的表单数据 */
  handleUpdateAddForm = (e: React.FormEvent, form: WrappedFormUtils) => {
    e.preventDefault();
    form.validateFieldsAndScroll(async (err, values) => {
      if (!err) {
        const result = await request('/api/c_pst_update_join_form_data', {
          method: 'POST',
          body: {
            pst_id: this.props.review.id,
            form_data: this.state.addFormItems
          }
        })
        if (result.status === 'success') {
          message.success('更新成功');
        } else {
          message.error('服务器异常，请稍后再试')
        }
      }
    })
  }

  handleActiveKey = (key: string) => {
    this.setState({
      activeKey: key
    })
  }
  /** 显示完成模态框 */
  showCompleteModal = (type?: string) => {
    this.setState({
      completeModalVisible: true,
      currentCompleteType: type
    })
    if (type === 'all') { // 总的完成按钮
      this.props.queryAllAddformData(this.props.review.id, (allFormData: any) => {
        let allAddFormItems = [];
        for (const key in allFormData.company_internal[0].form_data) {
          allAddFormItems.push(...allFormData.company_internal[0].form_data[key].form_data)
        }
        // for (const key in allAddFormData.form_data.company_partner) {
        //   allAddFormDataItems.push(...allAddFormData.form_data.company_internal[0].form_data[key].form_data)
        // }
        // for (const key in allAddFormData.form_data.outside_user) {
        //   allAddFormDataItems.push(...allAddFormData.form_data.company_internal[0].form_data[key].form_data)
        // }
        this.setState({
          allAddFormItems
        })
      });
    } else {
      this.setState({
        allAddFormItems: this.state.addFormItems
      })
    }

  }
  /** 关闭完成模态框 */
  handleCompleteModalCancel = () => {
    this.setState({
      completeModalVisible: false
    })
  }

  /** 处理参与人完成 */
  complete = async () => {
    this.setState({
      completeModalVisible: false
    })
    const { id } = this.props.review;

    this.proxyMutations('/api/c_pst_finish', {
      pst_id: id,
      form_data: this.state.addFormItems,
      complete_summary: this.state.summary,
      finish_form: this.state.allAddFormItems
    }, () => {
      message.success('提交成功');
    })

  }

  handleAddFormChange = (formData: any) => {
    this.setState({
      addFormItems: formData
    })
  }
  /** 归档 */
  onArchive = () => {
    const { id } = this.props.review;
    this.proxyMutations('/api/c_pst_archive', {
      id
    }, () => {
      message.success('归档成功');
    })
  }
  /** 导出 */
  cancelExportModal = () => {
    this.setState({
      exportReportVisible: false
    })
  }
  /** 展示导出报告模态框 */
  showExportModal = () => {
    this.props.queryReports();
    this.props.queryExportpacks();
    this.setState({
      exportReportVisible: true
    })
  }
  /** 切换导出报告模板类型 */
  handleExportTypeChange = (e: RadioChangeEvent) => {
    this.setState({
      exportType: e.target.value
    })
  }
  /** 组合导出 模板单击事件 */
  handleCombined = (item: any, e: React.MouseEvent<HTMLDivElement, MouseEvent>) => {
    this.setExports('combinedExports', item)
  }
  /** 打包导出 模板单击事件 */
  handlePackage = (item: any, e: React.MouseEvent<HTMLDivElement, MouseEvent>) => {
    this.setExports('ckeckedExports', item)
  }
  /** 设置选中的 导出模板列表 */
  setExports = (exportsType: string, item: any) => {
    this.setState((prevState: any) => {
      if (prevState[exportsType].some(({ id }: any) => id === item.id)) {
        return null
      }
      return {
        [exportsType]: [...prevState[exportsType], item]
      }
    })
  }
  /** 删除已选 导出模板 */
  removeExport = (exportsType: string, id: string) => {
    this.setState((prevState: any) => ({
      [exportsType]: prevState[exportsType].filter((item: any) => item.id !== id)
    }))
  }
  /** 展示打包设置模态框 */
  showAddPackModal = () => {
    this.setState({
      addPackModalVisible: true,
      currentEditingPack: null,
      ckeckedExports: [],
      exportPackName: ''
    })
  }
  /** 展示打包设置模态框 */
  cancelAddPackModal = () => {
    this.setState({
      addPackModalVisible: false
    })
  }
  /** 提交打包 */
  handleAddPackOk = () => {
    if (this.state.ckeckedExports.length === 0) {
      message.info('请至少选择一个模板！');
      return
    }
    if (!this.state.exportPackName) {
      message.info('请输入导出模板名称！');
      return
    }
    let body: any = {
      name: this.state.exportPackName,
      temIds: this.state.ckeckedExports.map(({ id }) => id)
    }
    let successText = '创建成功';
    if (this.state.currentEditingPack !== null) {
      body.id = this.state.currentEditingPack;
      successText = '更新成功';
    }
    (async () => {
      const result = await request('/api/c_pst_createExportPackage', {
        method: 'POST',
        body
      })
      if (result.status === 'success') {
        message.success(successText);
        this.setState({
          addPackModalVisible: false
        })
        this.props.queryExportpacks();
      }
    })()
  }
  /** 组合导出 */
  handleExport = () => {
    this.handleExports(this.state.combinedExports);
  }
  /** 打包导出编辑 */
  handlePackEdit = (id: any, exportPackName: any, ckeckedExports: any) => {
    this.setState({
      addPackModalVisible: true,
      ckeckedExports,
      exportPackName,
      currentEditingPack: id
    })
  }
  /** 打包导出 */
  handlePackExport = (packageExports: any) => {
    this.handleExports(packageExports);
  }
  /** 执行导出 */
  handleExports = async (exportList: any[]) => {
    const result = await request('/api/c_pst_exportTemplatePackage', {
      getFile: true,
      method: 'POST',
      body: {
        pst_id: this.props.review.id,
        temIds: exportList.map(({ id }) => id)
      }
    })
    let blobUrl = window.URL.createObjectURL(result.blob);
    const a = document.createElement('a');
    a.download = decodeURI(result.headers.get('filename'));//获取文件名
    a.href = blobUrl;
    a.click();
    window.URL.revokeObjectURL(blobUrl);
    message.info('导出成功');
  }
  /** 删除打包 */
  handlePackDelete = (id: string) => {
    (async () => {
      await request('/api/c_pst_deleteExportPackage', {
        method: 'POST',
        body: {
          id
        }
      })
      message.info('删除成功');
      // 刷新
      this.props.queryExportpacks();
    })()
  }
  /** 修改模板包名称 */
  changeExportPackName = (e: any) => {
    this.setState({
      exportPackName: e.target.value
    })
  }
  /** 单独导出 */
  handleAlone = ({ id }: any) => {
    this.setState({
      exportReportVisible: false
    })
    this.props.history.push({
      pathname: '/work/review/export',
      state: {
        report_id: id,
        pst_id: this.props.review.id
      }
    })
  }
  /**  */
  handleAddFormItemClose = (k: number) => {

    this.setState((prevState: { allAddFormItems: any[], [stateName: string]: any }) => ({
      allAddFormItems: prevState.allAddFormItems.filter((_, i) => i !== k)
    }))
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
  /** 通知 */
  notifiChange = (values: any) => {
    this.setState({
      notificationWay: values
    })
  }

  render() {

    const { review, files, timeline, linkApprovals, linkReviews, reports, exportpacks } = this.props;
    const {
      transferPrincipalVisible,
      reviewFormLibraryVisible,
      addFormItems,
      allAddFormItems,
      activeKey,
      completeModalVisible,
      summary,
      exportReportVisible,
      exportType,
      addPackModalVisible,
      ckeckedExports,
      combinedExports,
      exportPackName,
      invalidVisible,
      invalidReason,
      invalidLoading,
      currentCompleteType
    } = this.state;



    const btnStatusMap = (key: string, params?: any, type?: string) => {
      return {
        /** 接收 */
        btn_receive: (
          <Button key={key} onClick={() => this.onReceive(params)} type="primary" style={{ marginRight: 15 }}>
            接收
          </Button>
        ),
        /** 拒绝 */
        btn_refuse_receive: (
          <Button key={key} onClick={() => this.onRefuse(params)} type="danger">
            拒绝
          </Button>
        ),
        /** 同意 */
        btn_agree: (
          <Button key={key} onClick={() => this.onAgree(params)} type="primary" style={{ marginRight: 15 }}>
            同意
          </Button>
        ),
        /** 打回 */
        btn_back: (
          <Button key={key} onClick={() => this.onGoBack(params)} type="danger" style={{ marginRight: 15 }}>
            打回
          </Button>
        ),
        /** 编辑 */
        btn_editor: (
          <Button key={key} onClick={this.onEdit} style={{ marginRight: 15 }}>
            <Link to={{ pathname: '/work/assist/template' }}>编辑</Link>
          </Button>
        ),
        /** 指派 */
        btn_appoint: (
          <Button key={key}>
            <Link to={{
              pathname: "/work/review/initiate",
              state: {
                type: 'ASSIGN',
                pst_id: review.id,
                formData: review.form_template,
                disable: true
              }
            }}>指派</Link>
          </Button>
        ),
        /** 负责人移交 */
        btn_transfer_duty: (
          <Button key={key} onClick={this.showTransferPrincipalModal}>移交</Button>
        ),
        /** 参与人转交 */
        btn_transfer_join: (
          <Button key={key}>转交</Button>
        ),
        /** 递交 */
        btn_deliver: (
          <Button key={key}>递交</Button>
        ),
        /** 召回 */
        btn_recall: (
          <Button key={key} onClick={this.onRecall}>召回</Button>
        ),
        /** 作废 */
        btn_cancle: (
          <Button key={key} onClick={this.onInvalid}>作废</Button>
        ),
        /** 撤回 */
        btn_retract: (
          <Button key={key} onClick={this.onRetract}>撤回</Button>
        ),
        /** 完成 */
        btn_finish: (
          <Button key={key} onClick={() => this.showCompleteModal(type)}>完成</Button>
        ),
        /** 归档 */
        btn_archive: (
          <Button key={key} onClick={this.onArchive}>归档</Button>
        ),
        /** 导出 */
        btn_export: (
          <Button key={key} onClick={this.showExportModal}>导出报告</Button>
        ),
        /** 数据修正 */
        btn_data_alter: (
          <Button key={key}>数据修正</Button>
        )
      }
    }
    /** 可以添加表单 */
    const canAddForm = review
      ? !['已完成', '已归档'].includes(review.state)
        ? !['待审核', '已完成', '待接收'].includes(review.detail_info.current_user_info.state)
        : false
      : false;

    /** 是否显示人员信息 */
    const showPersonalInfo = review && review.state !== '待指派';

    return (
      <Layout className="review-detail" >
        <div style={{ padding: '0 20px', height: '56px', lineHeight: '56px', border: '1px solid #eee' }}>
          <span className="goback" onClick={this.props.history.goBack}> <Icon type="arrow-left" />返回</span>
        </div>
        <Content className="review-detail-main">

          <Tabs className="review-detail-tabs" activeKey={activeKey} onChange={this.handleActiveKey} tabBarStyle={{ padding: '0 24px' }}>

            <TabPane tab="项目收文" key="1" style={{ padding: '0 24px' }}>
              {
                review && review.form_template.map(({ type, field, value }: any, index: any) => {

                  if (!value) {
                    return null
                  }
                  if (type === 'DATEPICKER') {
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
                    <Row key={index}>
                      <Col xxl={3} xl={5} lg={7} md={9} style={{ textAlign: 'right' }}><TextLabel text={field.label} /></Col>
                      <Col xxl={10} xl={19} lg={17} md={21}>{value}</Col>
                    </Row>
                  )
                })
              }
              <Divider />
              {
                review && review.append_form.map(({ user_name, form_data }: any, k: number) => (
                  <React.Fragment key={k}>
                    <Row>
                      <Col span={18}>
                        {form_data.map(({ type, field, value }: any, index: any) => {

                          if (!value) {
                            return null
                          }
                          if (type === 'DATEPICKER') {
                            value = moment(value).format('YYYY-MM-DD HH:mm');
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
                            <Row key={index}>
                              <Col xxl={3} xl={5} lg={7} md={9} style={{ textAlign: 'right' }}><TextLabel text={field.label} /></Col>
                              <Col xxl={10} xl={19} lg={17} md={21}>{value}</Col>
                            </Row>
                          )
                        })}
                      </Col>
                      <Col span={6}>
                        {user_name}添加的表单
                    </Col>
                    </Row>
                    <Divider />
                  </React.Fragment>
                ))
              }
              {
                canAddForm && (
                  <>
                    <AddForm
                      onChange={this.handleAddFormChange}
                      formData={addFormItems}
                      onSubmit={this.handleUpdateAddForm}
                    />
                    <Row>
                      <Col xxl={3} xl={5} lg={7} md={9} style={{ textAlign: 'right' }}><TextLabel text="表单添加" /></Col>
                      <Col xxl={10} xl={19} lg={17} md={21}><Button icon="plus" onClick={this.handleAddForm} /></Col>
                    </Row>
                  </>
                )
              }
              <ReviewPersonnels
                visible={showPersonalInfo}
                review={review}
                btnStatusMap={btnStatusMap}
              />
            </TabPane>
            <TabPane tab="资料清单" key="2" style={{ padding: '0 24px' }}>
              <AnnexList dataSource={files} />
            </TabPane>
            <TabPane tab="关联评审" key="3" style={{ padding: '0 24px' }}>
              <div style={{ paddingBottom: '12px', border: '1px solid #ddd' }}>
                <header className="review-header">
                  <Row style={{ padding: '0 30px' }}>
                    <Col span={9}>
                      评审概览
                    </Col>
                    <Col span={5}>
                      发起时间
                    </Col>
                    <Col span={5}>
                      完成时间
                    </Col>
                    <Col span={5}>
                      状态
                    </Col>
                  </Row>
                </header>
                <List
                  dataSource={linkReviews}
                  size="small"
                  pagination={{
                    style: { textAlign: 'center' },
                    pageSizeOptions: ['10', '20', '40'],
                    showQuickJumper: true,
                    showSizeChanger: true,
                    showTotal: total => `共 ${total} 条数据`
                  }}
                  renderItem={
                    (item: any, k: number) => (
                      <a href={`/work/review/detail/${item.id}`} target="_black">
                        <Row className="review-list">
                          <Col span={9} className="review-list-title">
                            {item.type}
                          </Col>
                          <Col span={5}>
                            {item.created_at}
                          </Col>
                          <Col span={5}>
                            {item.completed_time}
                          </Col>
                          <Col span={5}>
                            {
                              (() => {
                                const status = item.is_cancel === 1 ? "已撤销" : item.state
                                return <span>{status}</span>
                              })()
                            }
                          </Col>
                        </Row>
                      </a>
                    )
                  }
                />
              </div>
            </TabPane>
            <TabPane tab="审批列表" key="4" style={{ padding: '0 24px' }}>
              <ApprovalItem
                dataSource={linkApprovals}
                headerState="approvalType"
              // ApprovalListInfo={this.showPaginationInfo}
              // type={approvalType}
              // ApprovalDetailInfo={this.showApprovalDetails}
              />
            </TabPane>
            <TabPane tab="协助列表" key="5" style={{ padding: '0 24px' }}>
              协助列表
            </TabPane>
          </Tabs>
          <div className="review-detail-timeline">
            <div className="review-detail-timeline-title">评审人员流程信息展示: </div>
            <div style={{ padding: '24px', width: '660px', height: 'calc(100% - 45px)', overflowY: 'auto' }}>
              <Timeline>
                {
                  timeline === undefined
                    ? (
                      <Timeline.Item dot={<Icon type="loading" />}>
                        正在加载相关记录。。。
                      </Timeline.Item>
                    )
                    : timeline.length !== 0 ? (
                      timeline.map(({ user_info, info, created_at, type }: any, index: number) => (
                        <Timeline.Item key={index}>
                          <div>
                            <span>
                              <span className="cursor-pointer" onClick={() => this.showBusinessCard(user_info.id)}>
                                <Avatar src={user_info.avator}>
                                  {user_info.name}
                                </Avatar>
                              </span>
                              <span>{info}</span>
                            </span>
                            <span style={{ float: 'right' }}>{created_at}</span>
                          </div>
                          <p>{type}</p>
                        </Timeline.Item>
                      ))
                    )
                      : (
                        <Timeline.Item>
                          暂无相关记录。
                        </Timeline.Item>
                      )

                }
              </Timeline>
            </div>
          </div>

        </Content>
        <Footer className="review-detail-footer">
          {
            // 可操作行为 按钮列表
            (() => {
              if (review) {
                const btns = [];
                if (!Array.isArray(review.detail_info.pst)) {


                  for (const key in review.detail_info.pst.btn_status) {
                    review.detail_info.pst.btn_status[key] && btns.push(btnStatusMap(key, { need_data: review.detail_info.pst.need_data, type: review.detail_info.type }, 'all')[key]);
                  }
                } else {
                  for (const key in review.btn_status) {
                    review.btn_status[key] && btns.push(btnStatusMap(key, null, 'all')[key]);
                  }
                }

                return btns
              }
              return null
            })()
          }
        </Footer>
        <SelectPersonnelModal
          visible={transferPrincipalVisible}
          onCancel={this.transferPrincipalCancel}
          onOk={this.okTransferPrincipal}
          checkable={false}
        />
        <ReviewFormLibrary
          mode="delete"
          deleteData={review && review.form_template}
          defaultData={addFormItems}
          visible={reviewFormLibraryVisible}
          onCancel={this.reviewFormLibraryCancel}
          onOk={this.handleAddFormOk}
        />
        <Modal
          visible={exportReportVisible}
          onCancel={this.cancelExportModal}
          title="报告导出"
          footer={null}
          width={800}
          getContainer={() => document.getElementsByClassName('work-review')[0] as any}
          wrapClassName="modal-review"
        >
          <Row className="modal-review-sectitle">
            <Col span={18} >
              <RadioGroup
                onChange={this.handleExportTypeChange}
                defaultValue="aloneExport"
                buttonStyle="solid"
              >
                <RadioButton value="aloneExport">单个导出</RadioButton>
                <RadioButton value="combinedExport">组合导出</RadioButton>
                <RadioButton value="packageExport">打包导出</RadioButton>
              </RadioGroup>

            </Col>
            <Col span={6} style={{ textAlign: 'right' }}>
              <Button type="primary" ghost icon="plus" onClick={this.showAddPackModal}>
                新增打包
              </Button>
            </Col>
          </Row>
          {
            exportType === 'aloneExport'
              ? (
                <Exportpack
                  className="modal-review-list"
                  datasource={reports && reports.enable}
                  onItemClick={this.handleAlone}
                />
              )
              : exportType === 'combinedExport'
                ? (
                  <div>

                    <Exportpack
                      className="modal-review-list"
                      datasource={reports && reports.enable}
                      onItemClick={this.handleCombined}
                    />
                    <div style={{ padding: '0 24px' }}>
                      <p>已选：</p>
                      <div className="clearfix" style={{ margin: '12px 0', padding: '24px 6px 24px 24px', background: '#FCFCFC', border: '1px solid #D0D0D0' }}>
                        {combinedExports.length === 0
                          ? '点击上方选择的导出模板，会在这里展示'
                          : combinedExports.map(({ id, name, description }: any) => (
                            <Card
                              key={id}
                              size="small"
                              hoverable
                              bordered
                              style={{ float: 'left', margin: '0 16px 16px 0', width: 220 }}
                              className="review-export-item"
                            >
                              <Card.Meta
                                className="review-tempitem"
                                avatar={<Avatar shape="square" size={42} style={{ background: '#1890ff', fontSize: 14 }}>{name && name.substr(0, 2)}</Avatar>}
                                title={<span style={{ fontSize: 14 }}>{name}</span>}
                                description={<div className="overflow-ellipsis" style={{ fontSize: 12 }}>{description}</div>}
                              />
                              <i className="export-close" onClick={() => this.removeExport('combinedExports', id)}>x</i>
                            </Card>
                          ))}
                      </div>
                      {combinedExports.length > 0 && <Button type="primary" onClick={this.handleExport}>导出</Button>}
                    </div>
                  </div>
                )
                : (
                  <div className="modal-review-list" style={{ padding: '24px' }}>
                    {
                      !exportpacks || exportpacks.length === 0
                        ? <div style={{ padding: '24px' }}>暂时没有已打包的数据, 可以点击右上方新增打包。</div>

                        : exportpacks.map(({ id, name, export_template }: any) => (
                          <div key={id} style={{ marginBottom: '24px' }}>
                            <div className="clearfix">
                              <span style={{ lineHeight: '32px' }}>{name}</span>
                              <div style={{ float: 'right' }}>
                                <Button style={{ marginLeft: '12px' }} type="primary" onClick={() => this.handlePackExport(export_template)}>导出</Button>
                                <Button
                                  style={{ marginLeft: '12px' }}
                                  type="dashed"
                                  onClick={() => this.handlePackEdit(id, name, export_template)}
                                >编辑</Button>
                                <Button style={{ marginLeft: '12px' }} type="danger" onClick={() => this.handlePackDelete(id)}>删除</Button>
                              </div>
                            </div>
                            <Divider style={{ margin: '12px 0' }} />
                            <div className="clearfix">
                              {export_template.map(({ id, name, description }: any) => (
                                <Card
                                  key={id}
                                  size="small"
                                  hoverable
                                  bordered
                                  style={{ float: 'left', margin: '0 16px 16px 0', width: 220 }}
                                >
                                  <Card.Meta
                                    className="review-tempitem"
                                    avatar={<Avatar shape="square" size={42} style={{ background: '#1890ff', fontSize: 14 }}>{name && name.substr(0, 2)}</Avatar>}
                                    title={<span style={{ fontSize: 14 }}>{name}</span>}
                                    description={<div className="overflow-ellipsis" style={{ fontSize: 12 }}>{description}</div>}
                                  />
                                </Card>
                              ))}
                            </div>
                          </div>
                        ))
                    }
                  </div>
                )
          }
        </Modal>
        <Modal
          width={554}
          visible={addPackModalVisible}
          title="打包常用导出"
          onCancel={this.cancelAddPackModal}
          onOk={this.handleAddPackOk}
        >
          <Exportpack
            className="modal-review-list"
            datasource={reports && reports.enable}
            onItemClick={this.handlePackage}
          />
          <p>已选：</p>
          <div className="clearfix" style={{ margin: '12px 0', padding: '24px 6px 24px 24px', background: '#FCFCFC', border: '1px solid #D0D0D0' }}>
            {ckeckedExports.length === 0
              ? '点击上方选择的导出模板，会在这里展示'
              : ckeckedExports.map(({ id, name, description }: any) => (
                <Card
                  size="small"
                  hoverable
                  bordered
                  style={{ float: 'left', margin: '0 16px 16px 0', width: 220 }}
                  className="review-export-item"
                >
                  <Card.Meta
                    className="review-tempitem"
                    avatar={<Avatar shape="square" size={42} style={{ background: '#1890ff', fontSize: 14 }}>{name && name.substr(0, 2)}</Avatar>}
                    title={<span style={{ fontSize: 14 }}>{name}</span>}
                    description={<div className="overflow-ellipsis" style={{ fontSize: 12 }}>{description}</div>}
                  />
                  <i className="export-close" onClick={() => this.removeExport('ckeckedExports', id)}>x</i>
                </Card>
              ))}
          </div>
          <Input type="text" placeholder="导出模板包名称" value={exportPackName} onChange={this.changeExportPackName} />
        </Modal>
        <Modal
          visible={completeModalVisible}
          onOk={this.complete}
          onCancel={this.handleCompleteModalCancel}
          title="完成"
          width={800}
        >
          {
            (() => {
              if (currentCompleteType === 'all') {
                const Ass = Form.create<any>()((props: any) => {
                  return (
                    <MergeReviewForm
                      formData={allAddFormItems}
                      onClose={this.handleAddFormItemClose}
                      form={props.form}
                    />
                  )
                })
                return < Ass />
              } else {
                return addFormItems.map(({ type, field, value }: any, index: any) => {
                  if (!value) {
                    return null
                  }
                  if (type === 'DATEPICKER') {
                    value = moment(value).format('YYYY-MM-DD HH:mm:ss')
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
                    <Row key={index}>
                      <Col span={4} style={{ textAlign: 'right' }}><TextLabel text={field.label} /></Col>
                      <Col span={18}>{value}</Col>
                    </Row>
                  )
                })
              }
            })()
          }

          {/* {
            addFormItems.map(({ type, field, value }: any, index: any) => {

              if (!value) {
                return null
              }
              if (type === 'DATEPICKER') {
                value = moment(value).format('YYYY-MM-DD HH:mm:ss')
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
                <Row key={index}>
                  <Col span={4} style={{ textAlign: 'right' }}><TextLabel text={field.label} /></Col>
                  <Col span={18}>{value}</Col>
                </Row>
              )
            })
          } */}
          <Row style={{ marginTop: 12 }}>
            <Col span={4} style={{ textAlign: 'right' }}>总结：</Col>
            <Col span={20}>
              <Input.TextArea
                placeholder="可以输入你的完成总结"
                value={summary}
                onChange={(e) => this.setState({ summary: e.target.value })}
              />
            </Col>
          </Row>
        </Modal>
        <Modal
          title="作废"
          visible={invalidVisible}
          onOk={this.submitInvalid}
          onCancel={() => this.setState({ invalidVisible: false })}
          confirmLoading={invalidLoading}
        >
          <Row type="flex">
            <Col span={5} style={{ paddingRight: '10px', textAlign: 'right' }}><TextLabel text="原因" /></Col>
            <Col span={19}>
              <Input.TextArea
                placeholder="请输入作废原因"
                autosize={{ minRows: 3, maxRows: 10 }}
                value={invalidReason}
                onChange={(e) => this.setState({ invalidReason: e.target.value })}
              />
            </Col>
          </Row>
          <Row type="flex" style={{ marginTop: '20px' }}>
            <Col span={5} style={{ paddingRight: '10px', textAlign: 'right' }}><TextLabel text="通知方式" /></Col>
            <Col span={19}>
              <NotifiMeds onChange={this.notifiChange} />
            </Col>
          </Row>
        </Modal>
        <PersonalCardModal
          visible={this.state.businessCardVisible}
          onCancel={() => this.setState({ businessCardVisible: false })}
          dataSource={this.state.cardInfo}
        />
      </Layout>
    )
  }
}
