import * as React from 'react';
import { Icon, Form, Layout, Input, Button, Radio, Checkbox, Select, message, Spin, Modal, Card, Avatar } from 'antd';
import SelectPersonnelModal, { CheckedPersonnelsInfo } from '../../../../components/selectPersonnelModal';
import TextLabel from '../../../../components/textLabel';
import SelectCc from '../../../../components/selectCc';
import SelectApprover from '../../../../components/selectApprover';
import { FormComponentProps } from 'antd/lib/form';
import { History, Location } from 'history';
import { RadioChangeEvent } from 'antd/lib/radio';
import AddSelect from '../../../../components/addSelect';
import CustomReviewForm from '../../../../components/customReviewForm';
import { connect } from 'dva';
import request from '../../../../utils/request';
import generateRandomString from '../../../../utils/generateRandomString';
import decryptId from '../../../../utils/decryptId';

const FormItem = Form.Item;
const RadioGroup = Radio.Group;
const Option = Select.Option;

export interface CreateTemplateProps extends FormComponentProps {

  /** 获取模板分组信息 */
  queryTemplateGroup: Function;
  /** 模板分组信息 */
  templateGroup: string[];
  /** 获取指定模板信息 by id */
  queryTemplateById: Function;
  /** 获取模板信息loading */
  templateLoading: boolean;
  /** 指定模板详细信息 */
  template: any;
  /** 获取模板列表信息 用于刷新 */
  queryTemplates: Function;
  /** 获取已有流程信息 */
  queryProcesses: Function;
  /** 已有流程信息 */
  processes: any;
  /** 获取单个流程详细信息 by id */
  queryProcessById: Function;
  /** 单个流程详细信息 */
  process: any;
  history: History;
  location: Location;
}

const NAMESPACE = 'Review';
const mapStateToProps = (state: any) => {
  return {
    templateGroup: state[NAMESPACE].templateGroup,
    template: state[NAMESPACE].template,
    templateLoading: state.loading.effects[`${NAMESPACE}/queryTemplateById`] || false,
    processes: state[NAMESPACE].processes,
    process: state[NAMESPACE].process
  }
};
const mapDispatchToProps = (dispatch: any) => {
  return {
    queryTemplateGroup: (cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/queryTemplateGroup`,
        payload: cb
      });
    },
    queryTemplateById: (id: string, cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/queryTemplateById`,
        payload: {
          id, cb
        }
      });
    },
    /** 获取模板列表信息 (用于刷新) */
    queryTemplates: () => {
      dispatch({
        type: `${NAMESPACE}/queryTemplates`
      });
    },
    /**  获取流程列表信息 */
    queryProcesses: () => {
      dispatch({
        type: `${NAMESPACE}/queryProcesses`
      });
    },
    queryProcessById: (id: string, cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/queryProcessById`,
        payload: {
          id, cb
        }
      });
    }
  }
}
@connect(mapStateToProps, mapDispatchToProps)
class CreateTemplate extends React.Component<CreateTemplateProps, any>{

  state = {
    showNeedApproval: false,
    processType: '自由流程',
    selectPersonnelVisible: false,
    selectedPersonnelInfo: 'all' as CheckedPersonnelsInfo | 'all',
    templateGroupLoading: false,
    existingProcessVisible: false,
    templateGroup: undefined as any
  }

  componentDidMount() {
    this.setState({
      templateGroupLoading: true
    })
    if (this.props.location.state) {
      this.props.queryTemplateById(this.props.location.state.data.id, ({
        name,
        type,
        description,
        per,
        form_template,
        need_approval,
        approval_method,
        process_template,
        cc_users
      }: any) => {
        const fields: any = {
          name,
          description,
          per,
          form_template,
          need_approval,
          approval_method: need_approval ? approval_method : '自由流程',
          process_template: need_approval ? process_template : [],
          cc_users
        };
        if (this.props.location.state.type !== 'INSERT') {
          fields.type_id = decryptId(type.id)
        }
        this.props.form.setFieldsValue(fields)

        // 如果需要审批 设置对应的state 显示对应内容
        if (need_approval) {
          if (approval_method === '固定流程') {
            this.setState({
              showNeedApproval: true,
              processType: '固定流程'
            })

          } else {
            this.setState({
              showNeedApproval: true
            })
          }
        }

      })
    }
    this.props.queryTemplateGroup((templateGroup: any) => {
      this.setState({
        templateGroupLoading: false,
        templateGroup
      })
    });
  }
  handleSubmit = (e: React.FormEvent<any>) => {
    e.preventDefault();
    this.props.form.validateFieldsAndScroll(async (err, values) => {
      if (!err) {
        const staffId = this.state.selectedPersonnelInfo !== "all" ? (this.state.selectedPersonnelInfo as any).checkedPersonnels.map((item: any) => item.key) : '';
        const departmentId = this.state.selectedPersonnelInfo !== "all" ? (this.state.selectedPersonnelInfo as any).checkedKeys.filter((item: any) => !staffId.includes(item)) : '';
        const selectedPersonnelInfo = this.state.selectedPersonnelInfo;
        this.state.selectedPersonnelInfo !== "all" ? values.per = { staffId, departmentId, selectedPersonnelInfo } : values.per = "all";

        // 重新拼接 6 位 字符串
        values.type_id = generateRandomString(6) + values.type_id;
        // 表单name值数组
        values.form_names = values.form_template.map((item: any) => (item.field.name));
        // 自由流程过滤掉审批人
        if (values.approval_method === '自由流程') {
          values.process_template = [];
        }

        let url = '/api/c_pst_add_pst_template';
        let messageText = '创建成功';
        if (this.props.location.state && this.props.location.state.type === 'UPDATE') {
          url = '/api/c_pst_update_pst_template';
          messageText = '修改成功';
          values.id = this.props.location.state.data.id;
        }
        console.log(values, 88888888888);

        const result = await request(url, {
          method: 'POST',
          body: values
        })
        console.log(result);

        if (result.status === 'success') {
          message.success(messageText);
          this.props.queryTemplates();
          this.props.history.replace('/work/review/templatemgt');
        } else {
          message.error('服务器错误，请稍后再试')
        }

      }
    })
  }
  // 相关操作需要审批
  needApproval = (e: any) => {
    this.setState({
      showNeedApproval: e.target.checked
    })
  }
  // 切换流程类型  自由 | 固定
  processChange = (e: RadioChangeEvent) => {
    this.setState({
      processType: e.target.value
    })
  }
  // 人员选择完毕
  selectPersonnelOk = (selectedPersonnelInfo: any) => {
    console.log(selectedPersonnelInfo);

    this.setState({
      selectPersonnelVisible: false,
      selectedPersonnelInfo
    })
  }
  // 关闭模态框
  selectPersonnelCancel = () => {
    this.setState({
      selectPersonnelVisible: false
    })
  }
  // 展示模态框
  showSelectPersonnelModal = () => {
    this.setState({
      selectPersonnelVisible: true
    })
  }
  /** 从现有流程选择 */
  selectExistingProcess = () => {
    this.props.queryProcesses();
    this.setState({
      existingProcessVisible: true
    })
    console.log(6666666666);
  }

  /** 确定 */
  okExistingProcess = () => {
    this.setState({
      existingProcessVisible: false
    })
  }

  /** 取消 modal */
  cancelExistingProcess = () => {
    this.setState({
      existingProcessVisible: false
    })
  }

  /** 单个流程点击 */
  onItemClick = (id: string) => {
    this.setState({
      existingProcessVisible: false
    });
    this.props.queryProcessById(id, ({ process_template }: any) => {
      this.props.form.setFieldsValue({
        process_template
      })
    });
  }

  handleTemplateAdd = async (addOptionValue: any) => {
    if (addOptionValue === '') {
      message.info('分组名称不能为空哦~');
      return
    }
    const result = await request('/api/c_pst_add_pst_template_type', {
      method: 'POST',
      body: {
        name: addOptionValue
      }
    });

    if (result.status === 'success') {
      message.success('新建成功');
      this.setState({
        templateGroup: [...this.state.templateGroup, {
          count: 0,
          id: result.data.id,
          name: addOptionValue,
          type: 'type'
        }]
      })
      this.props.queryTemplates();
    } else {
      message.error('服务器异常，请稍后再试')
    }
  }

  render() {
    const { templateLoading, processes } = this.props;
    const { existingProcessVisible } = this.state;
    const { getFieldDecorator } = this.props.form;
    const {
      showNeedApproval,
      processType,
      selectPersonnelVisible,
      selectedPersonnelInfo,
      templateGroupLoading,
      templateGroup
    } = this.state;

    const formItemLayout = {
      labelCol: {
        xs: { span: 24 },
        sm: { span: 6 },
      },
      wrapperCol: {
        xs: { span: 24 },
        sm: { span: 10 },
      },
    };
    const tailFormItemLayout = {
      wrapperCol: {
        xs: {
          span: 24,
          offset: 0,
        },
        sm: {
          span: 18,
          offset: 6,
        },
      },
    };

    const locationState = this.props.location.state;
    const title = locationState
      ? locationState.type === 'INSERT'
        ? '新建评审模板'
        : '更新评审模板'
      : '新建评审模板';

    console.log('processes:', processes);


    return (

      <Layout className="review-template-create white" >
        <div style={{ padding: '0 20px', height: '56px', lineHeight: '56px', border: '1px solid #eee' }}>
          <span className="goback" onClick={() => this.props.history.replace('/work/review/templatemgt')}>
            <Icon type="arrow-left" />返回
          </span>
          <TextLabel style={{ marginLeft: '20px' }} text={title} colon={false} />
        </div>
        <Spin spinning={templateLoading}>
          <Form onSubmit={this.handleSubmit} style={{ marginTop: '30px' }}>
            <FormItem
              {...formItemLayout}
              label='模板名称'
            >
              {getFieldDecorator('name', {
                rules: [
                  {
                    required: true,
                    message: '请输入模板名称',
                  }]
              })(
                <Input placeholder="请为这个模板起个名字" />
              )}
            </FormItem>
            <FormItem
              {...formItemLayout}
              label='分组选择'
            >
              {getFieldDecorator('type_id', {
                rules: [
                  {
                    required: true,
                    message: '请选择',
                  }],
              })(
                <AddSelect placeholder="请选择分组" loading={templateGroupLoading} onAdd={this.handleTemplateAdd}>
                  {templateGroup && templateGroup.map((option: any, index: any) => (
                    <Option value={decryptId(option.id)} key={index}>{option.name}</Option>
                  ))}
                </AddSelect>
              )}
            </FormItem>
            <FormItem
              {...formItemLayout}
              label='模板说明'
            >
              {getFieldDecorator('description', {
                initialValue: ''
              })(
                <Input placeholder="可以为这个模板添加一个描述" />
              )}
            </FormItem>
            <FormItem
              {...formItemLayout}
              label='使用范围'
            >
              {getFieldDecorator('per', {
              })(
                <div style={{ paddingLeft: '11px', marginTop: '5px', borderRadius: '3px', lineHeight: '30px', height: '33px', border: '1px solid #eee', cursor: 'pointer' }} onClick={this.showSelectPersonnelModal} >
                  {selectedPersonnelInfo === 'all' ? '全部人员可见' : selectedPersonnelInfo.checkedPersonnels.map((item: any) => (item.title)).join(',')}
                </div>
              )}
            </FormItem>
            <FormItem
              {...formItemLayout}
              label='表单设置'
            >
              {getFieldDecorator('form_template', {
                valuePropName: 'formInfo', // 自定义form组件 此属性定义的值作为值传递给子组件(通过this.props.formInfo 获取)
                rules: [
                  {
                    required: true,
                    message: '请至少选择一个表单控件'
                  }],
              }
              )(
                <CustomReviewForm />
              )}
            </FormItem>
            <FormItem
              {...formItemLayout}
              label="相关操作需要审批"
              extra="进行发起、撤回、编辑、递交、移交、召回、作废操作时需要审批"
            >
              {getFieldDecorator('need_approval', {
                valuePropName: 'checked',
                initialValue: false
              })(
                <Checkbox onChange={this.needApproval}>是</Checkbox>
              )}
            </FormItem>
            <FormItem
              {...formItemLayout}
              label='审批人员流程设置'
              style={{ display: showNeedApproval ? "block" : "none" }}
            >
              {getFieldDecorator('approval_method', {
                rules: [
                  {
                    required: showNeedApproval,
                    message: '请选择审批流程',
                  }],
                initialValue: '自由流程'
              })(
                <RadioGroup onChange={this.processChange}>
                  <Radio value="自由流程">自由流程（申请人手动选择审批人）</Radio>
                  <Radio value="固定流程">
                    固定流程（申请人按照规定好的审批人进行审核）
                    <Button
                      onClick={this.selectExistingProcess}
                      type="primary"
                      style={{ display: processType === '固定流程' ? "inline-block" : "none" }}
                    >
                      从已有流程选择
                    </Button>
                  </Radio>
                </RadioGroup>
              )}
            </FormItem>
            <FormItem
              {...formItemLayout}
              label='审批人员'
              style={{ display: showNeedApproval && processType === '固定流程' ? "block" : "none" }}
            >
              {getFieldDecorator('process_template', {
                valuePropName: 'approvers',
                rules: [
                  {
                    required: processType === '固定流程',
                    message: '请选择审批人员',
                  }
                ]
              }
              )(
                <SelectApprover />
              )}
            </FormItem>
            <FormItem
              {...formItemLayout}
              label='抄送人员'
            >
              {getFieldDecorator('cc_users', {
                valuePropName: 'ccInfo',
                initialValue: {
                  checkedKeys: [],
                  checkedPersonnels: []
                }
              })(
                <SelectCc />
              )}
            </FormItem>
            <FormItem
              {...tailFormItemLayout}
            >
              <Button type="primary" htmlType="submit" style={{ marginRight: "10px" }}>发布</Button>
              <Button className="type-btn">预览</Button>

            </FormItem>

          </Form>

        </Spin>
        <SelectPersonnelModal
          visible={selectPersonnelVisible}
          onOk={this.selectPersonnelOk}
          onCancel={this.selectPersonnelCancel}
          version={Date.now()}
        />
        <Modal
          visible={existingProcessVisible}
          title="已有审批流程"
          onOk={this.okExistingProcess}
          onCancel={this.cancelExistingProcess}
        >
          {
            processes && processes.enable.map(({ id, name, count, data }: any) => (
              count !== 0 && (
                <div key={id}>
                  <div style={{ marginBottom: 16 }}>
                    {name}（{count}）
                  </div>
                  <div className="clearfix">
                    {data.map(({ id, name, description }: any) => (
                      <Card
                        key={id}
                        size="small"
                        hoverable
                        bordered
                        style={{ float: 'left', margin: '0 16px 16px 0', width: 220 }}
                        onClick={() => this.onItemClick(id)}
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
              )))
          }
        </Modal>
      </Layout>
    )
  }
}
export default Form.create()(CreateTemplate)
