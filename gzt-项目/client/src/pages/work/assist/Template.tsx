import * as React from 'react';
import { History, Location } from 'history';
import { Icon, Form, Input, Tooltip, Button, Upload, Radio, DatePicker, Checkbox, message } from 'antd';
import moment from 'moment';
import { FormComponentProps } from 'antd/lib/form';
import NotifiMeds from '../../../components/notifiMeds';
import { postForm } from '../../../utils/request';
import BraftEditor from '../../../components/braftEditor';
import SelectParticipant from '../../../components/selectParticipant';
import SelectPrincipal from '../../../components/selectPrincipal';
import { connect } from 'dva';
import './assist.scss';

const FormItem = Form.Item;
const RadioGroup = Radio.Group;
const CheckboxGroup = Checkbox.Group;

interface AssistTemplateProps extends FormComponentProps {
  history: History;
  location: Location;
  showAssistList: Function;
}
const NAMESPACE = 'Assist'; // dva model 命名空间

const mapDispatchToProps = (dispatch: any) => {
  return {
    showAssistList: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryAssistList`,
        payload: params
      });
    }
  }
}
@connect(undefined, mapDispatchToProps)
class AssistTemplate extends React.Component<AssistTemplateProps, any> {

  state = {
    showFormArea: false,
    editorState: '',
    limitTime: moment().add(1, 'hour').format('YYYY-MM-DD HH:mm:ss'),
    principalId: '',
    assistId: null,
    deletefilesId: []
  }

  componentDidMount() {

    if (this.props.location.state) {
      const { id, title, description, limit_time, edit_form, principal, participants, files: uploadedFiles } = this.props.location.state.details;
      if (id) {
        this.setState({ assistId: id });
      }
      // 处理后端返回的文件列表信息为Upload组件需要的数据格式
      const files = uploadedFiles.map((item: any) => {
        return {
          uid: item.id,
          name: item.name,
          status: 'done',
          url: item.oss_path,
          guise: true // 后台返回的没有真正的文件信息，伪装
        }
      })

      this.props.form.setFieldsValue({
        title,
        principalId: {
          title: principal[0].name,
          key: principal[0].id,
          type: 'personnel'
        },
        participantsId: participants,
        description: BraftEditor.createEditorState(description),
        limitTime: limit_time ? limit_time : 'unlimited',
        formEdit: !!edit_form,
        files
      })

      limit_time && this.setState({ limitTime: limit_time });
      edit_form && this.setState({ showFormArea: true });

    }
  }

  handleSubmit = (e: any) => {
    e.preventDefault();
    this.props.form.validateFieldsAndScroll((err, values) => {
      if (!err) {
        // state转html
        const contentHtml = values.description.toHTML() // or values.content.toRAW()
        values.description = contentHtml;

        // 存在参与人的话拼接成后台需要的格式 (,号拼接)
        // if (values.participantsId) {
        //   values.participantsId = values.participantsId.join(',');
        // }

        if (values.files.length !== 0) { //过滤已存在的文件
          values.files = values.files.filter((file: any) => !file.guise)
        }
        values.deletefilesId = this.state.deletefilesId;
        (async () => {
          let result;
          if (this.state.assistId) { // 编辑
            values.id = this.state.assistId;
            result = await postForm('/api/c_assist_editTask', {
              body: values
            });
          } else { // 发起
            result = await postForm('/api/c_assist_sendInvite', {
              body: values
            });
          }
          if (result.status === 'success') {
            message.success('提交成功');
            this.props.showAssistList({
              status: 'all',
              internalOrExternal: 'all',
              type: 'all',
              offset: 0,
              limit: 10
            })
            this.props.history.push('/work/assist');

          } else if (result.status === 'fail') {
            message.warning(result.message);
          } else {
            message.error('服务器异常，请稍后重试');
          }
        })();
      }
    })
  }
  /**
   * 允许协同编辑表单
   */
  enableForm = (e: any) => {
    this.props.form.setFieldsValue({ "formPeople": ['协助的发起人、负责人', '参与人'] })
    this.setState({
      showFormArea: e.target.checked
    })
  }
  /**
   * 允许编辑表单的人员
   */
  enableFormInfo = (checkedValue: any) => {
    if (checkedValue.length === 0) {
      this.props.form.setFieldsValue({ "formEdit": false })
      this.setState({
        showFormArea: false
      })
    }
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

  /**
   * 删除已上传的文件
   */
  onRemove = (file: any) => {
    const fileList = (this.props.form.getFieldsValue(["files"]) as any).files;
    const index = fileList.indexOf(file);
    const newFileList = fileList.slice();
    newFileList.splice(index, 1);

    if (file.guise) { // 后端返回
      // 保存删除掉的文件id
      this.setState((prevState: any) => ({
        deletefilesId: prevState.deletefilesId.concat(file.uid)
      }));
      // (async () => {
      //   const result = await req(`/api/c_assist_deleteFile?file_id=${file.uid}`, {
      //     method: 'DELETE'
      //   });
      //   if (result.status === 'success') {
      //     this.props.form.setFieldsValue({
      //       "files": newFileList
      //     })
      //   } else {
      //     message.success('服务器错误，请稍后再试');
      //   }

      // })()
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

  // onLimitTimeChange = (value: any) => {

  // }
  onDatePickerOk = (_: any, dateString: string) => {

    this.setState({ limitTime: dateString });
    this.props.form.setFieldsValue({ "limitTime": dateString })

  }
  render() {
    const { getFieldDecorator } = this.props.form;
    const controls: any = ['headings', 'font-size', 'font-family', 'separator', 'bold', 'italic', 'underline', 'text-color', 'separator', 'list-ul', 'list-ol', 'link', 'separator', 'media'];
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
    const formOptions = ['协助的发起人、负责人', '参与人'];

    // const initialLimitTimeValue = 'unlimited'

    return (

      <div className="assist-edit" style={{ height: 'calc(100vh - 97px)', overflowY: 'auto', background: '#fff' }}>
        <div style={{ padding: '0 20px', height: '56px', lineHeight: '56px', border: '1px solid #eee' }}>
          <span className="goback" onClick={this.props.history.goBack}> <Icon type="arrow-left" />返回</span>
        </div>
        <div >
          <Form onSubmit={this.handleSubmit} style={{ marginTop: '30px', width: '1000px' }}>
            <FormItem
              {...formItemLayout}
              label="标题"
            >
              {getFieldDecorator('title', {
                rules: [{
                  required: true, message: '请输入协助标题!',
                }],
              })(
                <Input />
              )}
            </FormItem>
            <FormItem
              labelCol={{
                xs: { span: 24 },
                sm: { span: 6 },
              }}
              wrapperCol={{
                xs: { span: 24 },
                sm: { span: 16 },
              }}
              label="描述"
            >
              {getFieldDecorator('description', {
                trigger: 'onBlur',
                validateTrigger: 'onBlur',
                rules: [{
                  required: true,
                  validator: (_, value, callback) => {
                    if (!value || value.isEmpty()) {
                      callback('请输入正文内容')
                    } else {
                      callback()
                    }
                  }
                }],
              })(
                <BraftEditor
                  placeholder="请输入正文内容"
                  controls={controls}
                />
              )}
            </FormItem>
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
                  accept=".doc,.docx,.pdf,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.jpg,.png,.txt"
                >
                  <Button>
                    <Icon type="upload" /> 点击或拖拽文件
                  </Button>
                </Upload>
              )}
            </FormItem>
            <FormItem
              {...formItemLayout}
              label="允许添加协作表单"
              style={{ marginBottom: this.state.showFormArea ? "0px" : "24px" }}
            >
              {getFieldDecorator('formEdit', {
                valuePropName: 'checked',
                initialValue: false
              })(
                <Checkbox onChange={this.enableForm} >是</Checkbox>
              )}
            </FormItem>
            <FormItem
              {...tailFormItemLayout}
              style={{ display: this.state.showFormArea ? "block" : "none" }}
            >
              <div>允许以下人员进行协同编辑</div>
              {getFieldDecorator('formPeople', {
                initialValue: ['协助的发起人、负责人', '参与人']
              })(
                <CheckboxGroup onChange={this.enableFormInfo} options={formOptions} />
              )}
            </FormItem>
            <FormItem
              {...formItemLayout}
              label="完成时间"
            >
              {getFieldDecorator('limitTime', {
                initialValue: 'unlimited',
                rules: [{
                  required: true
                }],
              })(
                <RadioGroup>
                  <Radio value="unlimited">不限时间</Radio>
                  <Radio value={this.state.limitTime}><DatePicker allowClear={false} onChange={this.onDatePickerOk} value={moment(this.state.limitTime, 'YYYY-MM-DD HH:mm:ss')} showTime format="YYYY-MM-DD HH:mm:ss" /></Radio>
                </RadioGroup>
              )}
            </FormItem>
            <FormItem
              {...formItemLayout}
              label="负责人"
            >
              {getFieldDecorator('principalId', {
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
                  <Tooltip title="参与此次协助的所有人员">
                    <Icon type="question-circle-o" />
                  </Tooltip>
                </span>
              )}
            >
              {getFieldDecorator('participantsId', {
                valuePropName: 'checkedInfo'
              })(
                <SelectParticipant />
              )}
            </FormItem>
            <FormItem
              {...formItemLayout}
              label="通知方式"
            >
              {getFieldDecorator('notification_way', {
                initialValue: ['实时通知']
              })(
                <NotifiMeds />
              )}
            </FormItem>
            <FormItem {...tailFormItemLayout} >
              <Button type="primary" htmlType="submit" style={{ marginRight: '20px' }}>提交</Button>
            </FormItem>
          </Form>
        </div>
      </div>
    )
  }
}
export default Form.create()(AssistTemplate)
