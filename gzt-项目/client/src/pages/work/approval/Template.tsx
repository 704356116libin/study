import * as React from 'react';
import { History } from 'history';
import { Icon, Form, Button, message, Spin, Layout } from 'antd';
import { FormComponentProps } from 'antd/lib/form';
import NotifiMeds from '../../../components/notifiMeds';
import SelectApprover from '../../../components/selectApprover';
import ShowApprovers from '../../../components/selectApprover/showApprovers';
import SelectCc from '../../../components/selectCc';
import ParseFormItem from '../../../components/formLibrary/parseFormItem';
import { connect } from 'dva';
import './approval.scss';
import { Authorization } from '../../../utils/getAuthorization';

const { Content } = Layout
const FormItem = Form.Item;
const NAMESPACE = 'Approval'; // dva model 命名空间

interface CreateTemplateProps extends FormComponentProps {
  history: History;
  location: any;
  handleFormSubmit: any;
  correspondTemplateInfo: any;
  cancelAgainApply: any;
  correspondTemplate: any;
  detailLoading: any;
  againDetailLoading: any;
  showApprovalList: Function;
}

const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    detailLoading: state.loading.effects[`${NAMESPACE}/queryCorrespondTemplateInfo`],
    againDetailLoading: state.loading.effects[`${NAMESPACE}/queryAgainApplyInfo`]
  }
};

const mapDispatchToProps = (dispatch: any) => {
  return {
    correspondTemplateInfo: (value: any) => {
      dispatch({
        type: `${NAMESPACE}/queryCorrespondTemplateInfo`,
        payload: value
      });
    },
    cancelAgainApply: (value: any) => {
      dispatch({
        type: `${NAMESPACE}/queryAgainApplyInfo`,
        payload: value
      });
    },
    showApprovalList: (value: any) => {
      dispatch({
        type: `${NAMESPACE}/queryApprovalList`,
        payload: value
      });
    },
  }
}

@connect(mapStateToProps, mapDispatchToProps)
class CreateTemplate extends React.Component<CreateTemplateProps, any> {
  state = {
    showReturnArea: false,
    editorState: '',
    limitTime: '',
    formData: []
  }
  componentDidMount() {
    if (this.props.location.state && this.props.location.state.type === "cancel") {//撤销后的审批重新提交
      this.props.cancelAgainApply({ id: this.props.location.state.id })
    } else if (this.props.location.state && this.props.location.state.type === "insert") {
      this.props.correspondTemplateInfo({ id: this.props.location.state.id });
    } else { // 浏览器无法获取状态时
      this.props.history.push('/work/approval/create');
    }
  }
  componentDidUpdate(prevProps: any) {
    if (this.props.location.state) {
      if (this.props.correspondTemplate !== prevProps.correspondTemplate) { // 判断是否变化
        const { tem } = this.props.correspondTemplate;
        const { form_template, process_template, cc_users, approval_method, description, type } = tem[0];
        this.props.form.setFieldsValue({
          type_id: type && type.name,
          description,
          form_template,
          process_template,
          cc_users,
          approval_method,
        })
      }
    }
  }

  handleSubmit = (e: any) => {
    e.preventDefault();
    this.props.form.validateFieldsAndScroll((err, values) => {
      if (!err) {
        const tem = this.props.correspondTemplate.tem[0];
        values.approval_method = tem.approval_method;
        values.type_id = tem.type.type_id;
        values.approval_number = tem.approval_number;
        values.form_template = this.state.formData;
        values.userIds = values.cc_users
          ? values.cc_users.checkedPersonnels.map(({ key }: any) => key)
          : [];
        // 清除自定义表单自身的values值，不需要（上传文件的时候还会造成传双份）
        for (const key in values) {
          if (values.hasOwnProperty(key)) {
            if (/^cf-[a-z]+/.test(key)) {
              delete values[key];
            }
          }
        }
        const formData = new FormData();
        for (const key in values) {
          if (key === 'form_template') {
            let i = 0;
            values[key].forEach(({ type, value }: any, k: number) => {
              if (type === 'ANNEX') {
                value && value.forEach(({ originFileObj }: any) => {
                  formData.append('files' + i, originFileObj)
                })
                i++;
                values[key][k]['value'] = [];
              }
            });

            formData.append(key, JSON.stringify(values[key]))
          } else {
            formData.append(key, typeof values[key] === 'object' ? JSON.stringify(values[key]) : values[key]);
          }
        }
        (async () => {
          const response = await fetch('/api/c_approval_create', {
            method: 'POST',
            headers: {
              Authorization
            },
            body: formData
          });
          const result = await response.json();
          if (result.status === 'success') {
            message.success('提交成功');
            this.props.showApprovalList({
              type: 'initiate',//根据路由获取类型
              status: "all",
              type_id: "all",
              page: 1,
              page_size: 10
            });
            this.props.history.push('/work/approval/initiate');
          } else {
            message.success('服务器繁忙，请稍后再试');
          }
        })();

      }
    })
  }

  handleFormTemplate = (formData: any) => {
    console.log(formData, "formData");
    this.setState({
      formData
    })
  }
  showProcessInfo = (params: any) => {
    console.log(params, 74554);
  }

  normFile = (e: any) => {
    if (Array.isArray(e)) {
      return e;
    }
    return e && e.fileList;
  }

  handleEditorChange = (editorState: any) => {
    this.setState({ editorState })
  }

  onChange = (info: any) => {
    if (info.file.status !== 'uploading') {
      console.log(info.file, info.fileList);
    }
    if (info.file.status === 'done') {
      message.success(`${info.file.name} 文件上传成功`);
    } else if (info.file.status === 'error') {
      message.error(`${info.file.name} 文件上传失败.`);
    }
  }
  /**
   * 删除已上传的文件
   */
  onRemove = (file: any) => {
    const fileList = (this.props.form.getFieldsValue(["updatingfiles"]) as any).updatingfiles;
    if (file.guise) { // 后端返回
      console.log('请在服务器端也把我删了，蟹蟹');

      // todo .. 请求后端接口删除服务器对应的文件
    }
    const index = fileList.indexOf(file);
    const newFileList = fileList.slice();
    newFileList.splice(index, 1);
    this.props.form.setFieldsValue({
      "updatingfiles": newFileList
    })
  }

  beforeUpload = (file: any) => {
    const fileList = (this.props.form.getFieldsValue(["updatingfiles"]) as any).updatingfiles;
    this.props.form.setFieldsValue({
      "updatingfiles": [...fileList, file]
    })
    return false;
  }

  render() {
    const { getFieldDecorator } = this.props.form;
    const { correspondTemplate, detailLoading, againDetailLoading } = this.props;

    const loading = this.props.location.state && this.props.location.state.type === "cancel" ? againDetailLoading : detailLoading;

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
    const formApprovalItemLayout = {
      labelCol: {
        xs: { span: 24 },
        sm: { span: 6 },
      },
      wrapperCol: {
        xs: { span: 24 },
        sm: { span: 10 }
      },
    };

    const title = this.props.location.state && this.props.location.state.name;
    return (
      <Layout style={{ height: 'calc(100vh - 97px)', overflowY: 'auto', background: '#fff' }}>
        <Spin spinning={loading} delay={300}>
          <div style={{ padding: '0 20px', height: '56px', lineHeight: '56px', border: '1px solid #eee' }}>
            <span className="goback" onClick={() => this.props.history.replace('/work/approval/create')}> <Icon type="arrow-left" />
              返回
            </span>
            <span>{title}</span>
          </div>
          <Content style={{ padding: 24, minHeight: 280, textAlign: 'left' }}>
            {(() => {
              if (correspondTemplate) {
                const { tem } = correspondTemplate;
                const { approval_method, form_template } = tem[0];
                return (
                  <Form onSubmit={this.handleSubmit} style={{ marginTop: '30px' }} >
                    <ParseFormItem
                      formData={form_template}
                      form={this.props.form}
                      onChange={this.handleFormTemplate}
                    />
                    <FormItem
                      {...formApprovalItemLayout}
                      label='审批人员:'
                    >
                      {getFieldDecorator('process_template',
                        {
                          rules: [
                            {
                              required: approval_method !== "固定流程",
                              message: '请选择审批人员',
                            }
                          ],
                          valuePropName: 'approvers',
                        }
                      )(
                        approval_method === "固定流程" ? <ShowApprovers onClick={this.showProcessInfo} /> : <SelectApprover />
                      )}
                    </FormItem>
                    <FormItem
                      {...formApprovalItemLayout}
                      label="抄送人员"
                    >
                      {getFieldDecorator('cc_users', {
                        valuePropName: 'ccInfo'
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
                )
              } else {
                return null;
              }
            })()
            }
          </Content>
        </Spin>
      </Layout>
    )
  }
}
export default Form.create()(CreateTemplate)
