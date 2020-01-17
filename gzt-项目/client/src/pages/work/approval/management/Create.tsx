import * as React from 'react';
import { Layout, Row, Col, Form, Input, Select, Icon, Button, Radio, message } from 'antd';
import { History } from 'history';
import SelectCc from '../../../../components/selectCc';
import SelectApprover from '../../../../components/selectApprover';
import SelectPersonnelModal from '../../../../components/selectPersonnelModal';
import DraggableForm from '../../../../components/customDragFormControl';
import { connect } from 'dva';
import AddSelect from '../../../../components/addSelect';
import req from '../../../../utils/request';
import './index.scss';
import ApprovalPreview from './ApprovalPreview';

const { Content, Header } = Layout;
const FormItem = Form.Item;
const Option = Select.Option;
const RadioGroup = Radio.Group;
export interface CreateProps {
  groupList: any;
  history: History;
  location: any;
  form: any;
  showApprovalTypeList: any;
  templateSelectInfo: any;
  showApprovalGroupType: any;
  managementTemplateInfo: any;
  managementDetailInfo: any;
  showManagementTemList: Function;
}

const NAMESPACE = 'Approval'; // dva model 命名空间
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    // listLoading: state.loading.effects[`${NAMESPACE}/queryAssistList`],
  }
};

const mapDispatchToProps = (dispatch: any) => {
  return {
    showApprovalTypeList: (cb: any) => {
      dispatch({
        type: `${NAMESPACE}/queryTemplateSelectList`,
        payload: cb
      });
    },
    showApprovalGroupType: (newType: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/queryApprovalGroupTypeInner`,
        payload: { newType, cb }
      });
    },
    managementTemplateInfo: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryManagementTemplateInfo`,
        payload: params
      });
    },
    /** 模板列表 用于刷新 */
    showManagementTemList: () => {
      dispatch({
        type: `${NAMESPACE}/queryManagementTemList`,
      })
    },
  }
}

@connect(mapStateToProps, mapDispatchToProps)
class CreateApproval extends React.Component<CreateProps, any> {
  // static getDerivedStateFromProps(nextProps: any, state: any) {
  //   // console.log(nextProps, 232312323);
  //   // Should be a controlled component.
  //   if (nextProps.templateSelectInfo) {
  //     return {
  //       ...state,
  //       options: nextProps.templateSelectInfo
  //     };
  //   }
  //   return null;
  // }

  state = {
    formDatas: {},
    over: false,
    projectNumber: true,
    selectPersonnelVisible: false,
    previewVisible: false,
    projectNumberValue: 'XMBH',
    selectValue: '',
    // currentGroupStatus: false
    options: [],
    processStatus: '自由流程',
    rangeInfo: 'all',
  }

  componentDidMount() {
    // 获取分组选择信息并更新state
    this.props.showApprovalTypeList((options: any) => {
      this.setState({
        options
      })
    });
    this.props.location.state && this.props.managementTemplateInfo({ id: this.props.location.state.id });
  }
  componentDidUpdate(prevProps: any) {
    console.log(this.props.location.state, 99999999999999999);

    if (this.props.location.state && this.props.managementDetailInfo && this.props.managementDetailInfo.length !== 0) {
      if (this.props.managementDetailInfo !== prevProps.managementDetailInfo) { // 判断是否变化
        const actionType = this.props.location.state.type;

        const { name, form_template, per, process_template, cc_users, approval_method, description, type, approval_number } = this.props.managementDetailInfo[0];

        const fields: any = {
          name,
          approval_number,
          description,
          form_template,
          process_template,
          cc_users
        }

        if (actionType !== 'insert') {
          fields.type_id = type.type_id
        }
        this.props.form.setFieldsValue(fields)
        this.setState({
          processStatus: approval_method,
          projectNumberValue: approval_number,
          projectNumber: false,
          rangeInfo: per !== null ? per.rangeInfo : 'all'
        })
      }
    }
  }
  handleSubmit = (e: any) => {
    e.preventDefault();
    if (!this.props.location.state || this.props.location.state.type === 'insert') {// 新增（无论是自定义还是通过已有模板）
      console.log(this.props.location.state, "this.props.location.state");
      this.props.form.validateFieldsAndScroll((err: any, values: any) => {
        if (!err) {

          // values.type =
          //   {
          //     type_id: values.type.key,
          //     name: values.type.label
          //   };
          // values.type_id = values.type.key;

          console.log("表单数据：", values);
          const staffId = this.state.rangeInfo !== "all" ? (this.state.rangeInfo as any).checkedPersonnels.map((item: any) => item.key) : '';
          const departmentId = this.state.rangeInfo !== "all" ? (this.state.rangeInfo as any).checkedKeys.filter((item: any) => !staffId.includes(item)) : '';
          const rangeInfo = this.state.rangeInfo;
          this.state.rangeInfo !== "all" ? values.per = { staffId, departmentId, rangeInfo } : values.per = "all";
          (async () => {
            const result = await req('/api/c_approval_template_add', {
              method: 'POST',
              body: values
            });
            if (result.status === 'success') {
              message.success('提交成功');
              this.props.showManagementTemList();
              this.props.history.push('/work/approval/management');
            } else {
              message.success('服务器错误，请稍后再试');
            }
          })();
        }
      })
    } else {// 通过编辑之后再次提交
      this.props.form.validateFieldsAndScroll((err: any, values: any) => {
        if (!err) {
          console.log("表单数据：", values);
          const staffId = this.state.rangeInfo !== "all" ? (this.state.rangeInfo as any).checkedPersonnels.map((item: any) => item.key) : '';
          const departmentId = this.state.rangeInfo !== "all" ? (this.state.rangeInfo as any).checkedKeys.filter((item: any) => !staffId.includes(item)) : '';
          const rangeInfo = this.state.rangeInfo;
          this.state.rangeInfo !== "all" ? values.per = { staffId, departmentId, rangeInfo } : values.per = "all";
          values.id = this.props.location.state.id;
          (async () => {
            const result = await req('/api/c_approval_template_save', {
              method: 'POST',
              body: values
            });
            if (result.status === 'success') {
              message.success('提交成功');
              this.props.showManagementTemList();
              this.props.history.push('/work/approval/management');
            } else {
              message.success('服务器错误，请稍后再试');
            }
          })();
        }
      })
    }
  }

  editProjectNumber = (e: any) => {
    this.setState({
      projectNumber: false
    })
  }

  handleProjectNumber = (e: any) => {
    this.setState({
      projectNumberValue: e.target.value
    })
  }

  removeProjectNumber = (e: any) => {
    this.setState({
      projectNumber: true
    })
  }
  //
  addOptions = (nextValue: any) => {
    if (nextValue === '') {
      message.info('分组名称不能为空哦~');
    } else {

      // 请求后台如果成功添加后，添加
      this.props.showApprovalGroupType(nextValue, (typeId: number) => {
        this.setState((prevState: any) => {
          return {
            options: prevState.options.concat({
              "type_id": typeId,
              "name": nextValue
            })
          }
        })
      })
    }
  }
  onProcessChange = (e: any) => {
    this.setState({
      processStatus: e.target.value
    })
  }
  /**
   * 展示人员选择modal
   */
  showRangeInfo = () => {
    this.setState({
      selectPersonnelVisible: true
    })
  }
  okModal = (checkedInfo: any, e: any) => {
    this.setState({
      selectPersonnelVisible: false,
      rangeInfo: checkedInfo
    })
  }
  // 关闭人员选择Modal
  cancelModal = (e: any) => {
    this.setState({
      selectPersonnelVisible: false,
    });
  }
  // 预览
  previewForm = () => {

    this.props.form.validateFieldsAndScroll((err: any, values: any) => {
      if (!err) {
        this.setState({
          previewVisible: true,
          formDatas: values
        })
      } else {
        message.info('请填写完必填项之后，再次预览')
      }
    })
  }
  previewModalCancel = () => {
    this.setState({
      previewVisible: false
    })
  }
  render() {

    const { getFieldDecorator } = this.props.form;
    const { projectNumber, projectNumberValue, options, processStatus, selectPersonnelVisible, previewVisible, rangeInfo, formDatas } = this.state;
    const modalProps = {
      visible: selectPersonnelVisible,
      onOk: this.okModal,
      onCancel: this.cancelModal,
      checkedKeys: rangeInfo === 'all' ? [] : (rangeInfo as any).checkedKeys,
      checkedPersonnels: rangeInfo === 'all' ? [] : (rangeInfo as any).checkedPersonnels
    }
    const approvalPreviewModal = {
      visible: previewVisible,
      formDatas,
      onCancel: this.previewModalCancel
    }
    const formItemLayout = {
      labelCol: {
        xs: { span: 24 },
        sm: { span: 6 },
      },
      wrapperCol: {
        xs: { span: 24 },
        sm: { span: 6 },
      },
    };
    const formItemsLayout = {
      labelCol: {
        xs: { span: 24 },
        sm: { span: 6 },
      },
      wrapperCol: {
        xs: { span: 24 },
        sm: { span: 4 },
      },
    }
    const formApprovalItemLayout = {
      labelCol: {
        xs: { span: 24 },
        sm: { span: 6 },
      },
      wrapperCol: {
        xs: { span: 24 },
        sm: { span: 12 }
      },
    }
    const formSetLayout = {
      labelCol: {
        xs: { span: 24 },
        sm: { span: 6 },
      },
      wrapperCol: {
        xs: { span: 24 },
        sm: { span: 7 }
      },
    }

    return (
      <Layout className="management">
        <Content>
          <Header className="white" style={{ borderBottom: "1px solid #eee", height: 60 }}>
            <Row className="sort-wrapper">
              <Col span={20}>
                <span className="goback" onClick={() => { this.props.history.push('/work/approval/management') }}> <Icon type="arrow-left" />返回</span>
              </Col>
            </Row>
          </Header>
          <Content style={{ padding: 24, background: '#fff', minHeight: 280, textAlign: 'center' }}>
            <Form onSubmit={this.handleSubmit} className="text-left">
              <FormItem
                {...formItemsLayout}
                label='审批名称'
              >
                {getFieldDecorator('name', {
                  rules: [
                    {
                      required: true,
                      message: '请输入审批模板名称',
                    }],
                })(
                  <Input placeholder="请输入审批模板名称" />
                )}
              </FormItem>
              <FormItem
                {...formItemsLayout}
                label='分组选择'
              >
                {getFieldDecorator('type_id', {
                  rules: [
                    {
                      required: true,
                      message: '请选择',
                    }],
                })(
                  <AddSelect
                    onAdd={this.addOptions}
                    placeholder="请选择分组"
                  >
                    {options && options.map((option: any, index: any) => (
                      <Option value={option.type_id} key={index}>{option.name}</Option>
                    ))}
                  </AddSelect>
                )}
              </FormItem>
              <FormItem
                {...formSetLayout}
                label='项目编号'
              >
                {getFieldDecorator('approval_number')(
                  <div>
                    {
                      (() => {
                        if (projectNumber) {
                          return (
                            <Button type="primary" onClick={this.editProjectNumber}>添加编号</Button>
                          )
                        } else {
                          return (
                            <div>
                              <div style={{ position: 'relative', width: '95px', display: 'inline-block', marginRight: '10px' }} >
                                <Input value={projectNumberValue} maxLength={4} onChange={this.handleProjectNumber} />
                                <Icon type="delete" className="delete" onClick={this.removeProjectNumber} />
                              </div>
                              <div style={{ display: "inline-block" }}>
                                编号实例展示：<span>{projectNumberValue}</span>—201812282222
                              </div>
                              <p style={{ marginBottom: 0, color: '#333' }}>编号标签可以设置比如：编号或“BH”1-4个字符</p>
                            </div>
                          )
                        }
                      })()
                    }
                  </div>
                )}
              </FormItem>
              <FormItem
                {...formItemsLayout}
                label='使用范围'
              >
                {getFieldDecorator('per', {
                })(
                  <div style={{ paddingLeft: '3px', marginTop: '5px', borderRadius: '3px', lineHeight: '30px', height: '33px', border: '1px solid #eee', cursor: 'pointer' }} onClick={this.showRangeInfo} >
                    {rangeInfo === "all" ? "全部人员可见" : (rangeInfo as any).checkedPersonnels.map((item: any) => (item.title)).join(',')}
                  </div>
                )}
              </FormItem>
              <FormItem
                {...formItemsLayout}
                label='流程说明'
              >
                {getFieldDecorator('description')(
                  <Input placeholder="请填写流程描述说明" />
                )}
              </FormItem>
              <FormItem
                {...formSetLayout}
                label='表单设置'
              >
                {getFieldDecorator('form_template', {
                  valuePropName: 'formInfo'// 自定义form组件 此属性定义的值作为值传递给子组件(通过this.props.formInfo 获取)
                }
                )(
                  <DraggableForm />
                )}
              </FormItem>
              <FormItem
                {...formItemLayout}
                label='审批人员流程设置'
              >
                {getFieldDecorator('approval_method', {
                  initialValue: processStatus,
                  rules: [
                    {
                      required: true,
                      message: '请选择审批流程',
                    }],
                })(
                  <RadioGroup onChange={this.onProcessChange}>
                    <Radio value="自由流程">自由流程（申请人手动选择审批人）</Radio>
                    <Radio value="固定流程">固定流程（申请人按照规定好的审批人进行审核）</Radio>
                  </RadioGroup>
                )}
              </FormItem>
              <FormItem
                {...formApprovalItemLayout}
                label='审批人员'
                style={{ display: processStatus === '固定流程' ? "block" : "none" }}
              >
                {getFieldDecorator('process_template', {
                  valuePropName: 'approvers'
                }
                )(
                  <SelectApprover />
                )}
              </FormItem>
              <FormItem
                {...formApprovalItemLayout}
                label='抄送人员'
              >
                {getFieldDecorator('cc_users', {
                  valuePropName: 'ccInfo',
                })(
                  <SelectCc />
                )}
              </FormItem>
              <FormItem
                wrapperCol={{ span: 10, offset: 6 }}
              >
                <div>
                  <Button type="primary" htmlType="submit" style={{ marginRight: "10px" }}>发布</Button>
                  <Button className="type-btn" onClick={this.previewForm}>预览</Button>
                </div>
              </FormItem>

            </Form>
            <SelectPersonnelModal {...modalProps} version={Date.now()} />
            <ApprovalPreview {...approvalPreviewModal} />
          </Content>
        </Content>
      </Layout >
    )
  }
}
export default Form.create()(CreateApproval);