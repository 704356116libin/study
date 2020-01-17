import * as React from 'react';
import { History } from 'history';
import { Icon, Form, Button, Upload, Tooltip, Layout, message } from 'antd';
import { FormComponentProps } from 'antd/lib/form';
import { connect } from 'dva';
import { postForm } from '../../../utils/request';
import TextLabel from '../../../components/textLabel';
import NotifiMeds from '../../../components/notifiMeds';
import SelectCc from '../../../components/selectCc';
import SimulationReviewForm from '../../../components/reviewFormLibrary/simulationReviewForm';
import AssociateReview from './common/AssociateReview';
import SelectApprover from '../../../components/selectApprover';
import ShowApprovers from '../../../components/selectApprover/showApprovers';
import SelectParticipant from '../../../components/selectParticipant';
import SelectPrincipal from '../../../components/selectPrincipal';

const FormItem = Form.Item;
const NAMESPACE = 'Review';

const mapStateToProps = (state: any) => {
  return {
    template: state[NAMESPACE].template,
    loading: state.loading.effects[`${NAMESPACE}/queryTemplateById`],
    someBaseData: state[NAMESPACE].someBaseData
  }
}

const mapDispatchToProps = (dispatch: any) => {
  return {
    /** 获取指定模板信息 */
    queryTemplateById: (id: string, cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/queryTemplateById`,
        payload: {
          id,
          cb
        }
      })
    },
    /** 获取指定模板信息 */
    querySomeBaseData: (cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/querySomeBaseData`,
        payload: {
          cb
        }
      })
    }
  }
}

interface ReviewInitiateProps extends FormComponentProps {
  history: History;
  location: any;
  template: any;
  /** 获取指定模板信息 by id */
  queryTemplateById: (id: string, cb?: Function) => void;
  someBaseData: any;
  /** 获取一些基础表单信息 */
  querySomeBaseData: (cb?: Function) => void;
}

@connect(mapStateToProps, mapDispatchToProps)
class ReviewInitiate extends React.Component<ReviewInitiateProps, any> {

  state = {
    showNeedApproval: false,
    assistVisible: false,
    requestAssistData: null,
    formData: []
  }

  constructor(props: any, ...args: any) {
    super(props, ...args);
    // props.cacheLifecycles.didCache(this.componentDidCache.bind(this));
    props.cacheLifecycles.didRecover(this.componentDidRecover.bind(this));
  }
  // 保存上次离开时的页面 和 状态
  // componentDidCache() {
  // }
  // 恢复上次离开时的页面 和 状态
  componentDidRecover() {
    if (!this.props.location.state) {
      this.props.history.replace('/work/review')
    } else if (this.props.location.state.type === 'ASSIGN') {

    } else {
      this.props.queryTemplateById(this.props.location.state.id)
    }
  }
  componentDidMount() {

    if (!this.props.location.state) {
      this.props.history.replace('/work/review')
    } else if (this.props.location.state.type === 'ASSIGN') {

    } else {
      this.props.queryTemplateById(this.props.location.state.id, ({
        process_template,
        cc_users
      }: any) => {
        this.props.form.setFieldsValue({
          process_template,
          cc_users
        })
      });
      this.props.querySomeBaseData();
    }
  }

  handleSubmit = (e: React.FormEvent<any>) => {
    e.preventDefault();
    this.props.form.validateFieldsAndScroll(async (err, values) => {
      if (!err) {
        // 获取所有选中的id
        const checkedIds = {
          organizational: values.join_user_data.checkedPersonnels.organizational.map(({ key }: any) => key),
          externalContact: values.join_user_data.checkedPersonnels.externalContact.map(({ key }: any) => key),
          partner: values.join_user_data.checkedPersonnels.partner.map(({ key }: any) => key)
        }
        values.join_user_data = {
          ...values.join_user_data,
          checkedIds
        };

        if (this.props.location.state.type === 'ASSIGN') {

          const result = await postForm('/api/c_pst_appoint', {
            body: {
              pst_id: this.props.location.state.pst_id,
              form_template: JSON.stringify(this.props.location.state.formData),
              ...values
            }
          });

          if (result.status === 'success') {
            message.success('提交成功');
            this.props.form.resetFields();
            this.props.history.push('/work/review/all');
          } else {
            message.error('服务器异常，请稍后再试')
          }
        } else {
          // 当前用的模板id
          values.template_id = this.props.location.state.id;


          // 处理 送审时间
          if (values.submit_time) {
            values.submit_time = values.submit_time.format('YYYY-MM-DD HH:mm:ss')
          }
          const form_values = {};
          this.state.formData.forEach(({ field, value }: any) => {
            form_values[field.name] = value || null
          })

          values.form_values = form_values;
          values = {
            ...this.props.template,
            ...values,
            form_template: this.state.formData
          }

          const result = await postForm('/api/c_pst_create', {
            body: values
          });

          if (result.status === 'success') {
            message.success('提交成功');
            this.props.history.push('/work/review/all');
          } else if (result.status === 'fail') {
            message.error(result.message)
          }
        }
      }

    })
  }

  onRequestAssistance = () => {
    this.setState({
      assistVisible: true
    })
  }

  normFile = (e: any, s: any) => {
    // 限制单个文件
    const isLt20M = e.file.size / 1024 / 1024 < 20;

    if (Array.isArray(e)) {
      return e;
    }

    if (isLt20M || e.file.status === 'removed') {
      return e && e.fileList
    } else {
      message.error('支持20M以下的文件！');
      return e && e.fileList.filter((file: any) => file.uid !== e.file.uid);
    }
  }
  /** 删除已上传的文件 */
  onRemove = (file: any) => {
    const fileList = this.props.form.getFieldValue("files");
    const index = fileList.indexOf(file);
    const newFileList = fileList.slice();
    newFileList.splice(index, 1);

    if (file.guise) { // 后端返回
      // 保存删除掉的文件id
      this.setState((prevState: any) => ({
        deletefilesId: prevState.deletefilesId.concat(file.uid)
      }));

    } else {
      this.props.form.setFieldsValue({
        "files": newFileList
      })
    }
  }

  beforeUpload = (file: any) => {

    const fileList = (this.props.form.getFieldsValue(["files"]) as any).files;
    this.props.form.setFieldsValue({
      "files": [...fileList, file]
    })
    return false;
  }
  /** 用户自定义的表单区域 change */
  handleFormChange = (formData: any) => {
    this.setState({
      formData
    })
  }
  render() {

    if (!this.props.location.state) {
      return null
    }

    const { getFieldDecorator } = this.props.form;
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

    const { type, name, disable, formData } = this.props.location.state;
    const { template, someBaseData } = this.props;

    const finalformData = type === 'ASSIGN'
      ? formData
      : template && template.form_template;

    return (
      <Layout className="review-initiate" >
        <div style={{ padding: '0 20px', height: '56px', lineHeight: '56px', border: '1px solid #eee' }}>
          <span className="goback" onClick={() => this.props.history.replace('/work/review/templates')}>
            <Icon type="arrow-left" />返回
          </span>
          <TextLabel style={{ marginLeft: '20px' }} text={name} colon={false} />
        </div>
        <Form onSubmit={this.handleSubmit} style={{ marginTop: '30px', width: '1000px' }}>
          <SimulationReviewForm
            someBaseData={someBaseData}
            form={this.props.form}
            formData={finalformData}
            onChange={this.handleFormChange}
            disable={disable === undefined ? false : disable}
          />
          <FormItem
            {...formItemLayout}
            label="附件上传"
            extra="支持.doc,.docx,.pdf,.xls,.xlsx,.ppt,.pptx,.zip,.rar类型文件，20M以内"
          >
            {getFieldDecorator('files', {
              valuePropName: 'fileList',
              getValueFromEvent: this.normFile,
              initialValue: []
            })(
              <Upload
                name="files"
                listType="picture"
                multiple={true}
                onRemove={this.onRemove}
                beforeUpload={this.beforeUpload}
              >
                <Button>
                  <Icon type="upload" /> 点击或拖拽文件
                </Button>
              </Upload>
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="关联评审"
          >
            {getFieldDecorator('associated_psts', {
              initialValue: [],
            })(
              <AssociateReview />
            )}
          </FormItem>

          <FormItem
            {...formItemLayout}
            label="负责人"
          >
            {getFieldDecorator('duty_user_data', {
              valuePropName: 'selectedInfo',
              rules: [{ required: true, message: '请选择负责人!' }],
            })(
              <SelectPrincipal placeholder="请选择负责人" />
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label={(
              <span>
                参与人&nbsp;
                  <Tooltip title="参与此次评审的人员 / 企业">
                  <Icon type="question-circle-o" />
                </Tooltip>
              </span>
            )}
          >
            {getFieldDecorator('join_user_data', {
              valuePropName: 'checkedInfo',
              initialValue: {
                checkedKeys: [],
                checkedPersonnels: {
                  organizational: [],
                  partner: [],
                  externalContact: []
                }
              }
            })(
              <SelectParticipant />
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label='相关操作审批人员:'
          >
            {
              template && template.need_approval ?
                getFieldDecorator('process_template',
                  {
                    valuePropName: 'approvers',
                    initialValue: template.process_template,
                    rules: [
                      {
                        required: template.approval_method === "自由流程",
                        message: '请选择审批人员流程'
                      }
                    ]
                  }
                )(
                  template.approval_method === "固定流程" ? <ShowApprovers /> : <SelectApprover />

                )
                : '当前模板不需要审批'
            }
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="抄送人员"
          >
            {getFieldDecorator('cc_users', {
              valuePropName: 'ccInfo',
              initialValue: {
                checkedKeys: [],
                checkedPersonnels: []
              }
            })(<SelectCc />)}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="通知方式"
          >
            {getFieldDecorator('notification_way', {
            })(
              <NotifiMeds />
            )}
          </FormItem>
          <FormItem {...tailFormItemLayout} >
            <Button type="primary" htmlType="submit" style={{ marginRight: '20px' }}>提交</Button>
          </FormItem>
        </Form>
      </Layout>
    )
  }
}
export default Form.create()(ReviewInitiate)
