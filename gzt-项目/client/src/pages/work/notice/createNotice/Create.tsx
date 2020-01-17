import * as React from 'react';
import { Layout, Select, Form, Icon, Input, Upload, Button, Checkbox, message, Tooltip } from 'antd';
import req, { postForm } from '../../../../utils/request';
import { connect } from 'dva';
import BraftEditor from '../../../../components/braftEditor';
import NotifiMeds from '../../../../components/notifiMeds';
import SelectPersonnelModal from '../../../../components/selectPersonnelModal';
import PreviewForm from './PreviewForm'
import { History } from 'history';
import generateRandomString from '../../../../utils/generateRandomString';
import SelectParticipant from '../../../../components/selectParticipant';
import './create.scss';

const { Content } = Layout;
const Option = Select.Option;
const FormItem = Form.Item;
const Dragger = Upload.Dragger;
const NAMESPACE = 'Notice'; // dva model 命名空间
const mapStateToProps = (state: any) => {
  return state[NAMESPACE]
};
/**
 * 新建一个公告
 */
interface CreateNoticeProps {
  showNoticeTypeInfo: () => void,
  noticeList: any,
  form: any,
  handleFormSubmit: any,
  history: History
  location: any,
  showNoticeContent: any,
  showNoticeInfo: Function;
  // loading
}
const mapDispatchToProps = (dispatch: any) => {
  return {
    /** 展示栏目信息 */
    showNoticeTypeInfo: () => {
      dispatch({
        type: `${NAMESPACE}/queryInitList`
      });
    },
    /** 提交表单 */
    handleFormSubmit: (value: any, reload: any) => {
      dispatch({
        type: `${NAMESPACE}/handleFormInfo`,
        payload: {
          value,
          reload
        }
      });
    },
    showNoticeContent: (value: any, setFieldsValue: any) => {
      dispatch({
        type: `${NAMESPACE}/queryNoticeContent`,
        payload: {
          value,
          setFieldsValue
        }
      });
    },
    /** 展示全部公告的信息 用于刷新*/
    showNoticeInfo: (value: any) => {
      dispatch({
        type: `${NAMESPACE}/queryNoticeInfo`,
        payload: value
      });
    },
  }
}

@connect(mapStateToProps, mapDispatchToProps)
class Create extends React.Component<CreateNoticeProps, any>
{
  state = {
    currentColumnType: this.props.noticeList[0].name,
    selectPersonnelVisible: false,
    previewVisible: false,
    rangeInfo: 'all',
    allowDownloadState: 0,
    formDatas: {},
    type: '',
    deletefilesId: []
  }
  handleChange = (value: any, option: any) => {
    this.setState({
      currentColumnType: option.props.children
    })
  }
  goBackPage = () => {
    message.success('提交成功');
    this.props.showNoticeInfo({
      now_page: 1,
      page_size: 10,
    });
    this.props.history.push('/work/notice');
  }
  componentDidMount() {
    this.props.showNoticeTypeInfo();
    if (this.props.location.state) {// 通过全部等其他方式点击编辑进入创建
      const { files: uploadedFiles, notice: { c_notice_column_id, content, guard_json, title } } = this.props.location.state.detailInfo;

      const type = this.props.location.state.type
      // 处理后端返回的文件列表信息为 Upload 组件需要的数据格式
      const files = uploadedFiles.map((item: any) => {
        return {
          uid: item.id,
          name: item.name,
          status: 'done',
          url: item.oss_path,
          guise: true // 后台返回的没有真正的文件信息，伪装，即为标志
        }
      })

      this.props.form.setFieldsValue({
        files,
        c_notice_column_id: c_notice_column_id.slice(6),
        content: BraftEditor.createEditorState(content),
        guard_json,
        title
      })
      this.setState({
        type
      })
    }
  }

  dealWithForm = (state: string) => {
    this.props.form.validateFieldsAndScroll((err: any, values: any) => {
      if (!err) {
        const contentHtml = values.content.toHTML();// 富文本内容
        values.content = contentHtml;
        values.type = this.state.currentColumnType;
        values.operate_type = state;
        values.allow_download = this.state.allowDownloadState;
        values.c_notice_column_id = generateRandomString(6) + values.c_notice_column_id;

        // const staffId = this.state.rangeInfo !== "all"
        //   ?
        //   (this.state.rangeInfo as any).checkedPersonnels.map((item: any) => item.key)
        //   :
        //   '';

        // const departmentId = this.state.rangeInfo !== "all"
        //   ?
        //   (this.state.rangeInfo as any).checkedKeys.filter((item: any) => !staffId.includes(item))
        //   :
        //   '';

        // const rangeInfo = this.state.rangeInfo;
        // this.state.rangeInfo !== "all"
        //   ? values.guard_json = {
        //     "user_ids": staffId,
        //     "department_ids": departmentId, rangeInfo
        //   }
        //   :
        //   values.guard_json = "all";
        let isNull = true;
        if (values.guard_json !== undefined) {
          for (const key in values.guard_json.checkedPersonnels) {
            if (values.guard_json.checkedPersonnels[key].length !== 0) {
              isNull = false;
              break;
            }
          }
        }
        values.guard_json = isNull ? 'all' : values.guard_json;

        this.props.handleFormSubmit(values, this.goBackPage);
      }
    });
  }

  handleSubmit = () => {
    this.props.form.validateFieldsAndScroll((err: any, values: any) => {
      if (!err) {
        values.c_notice_column_id = generateRandomString(6) + values.c_notice_column_id;
        values.allow_download = this.state.allowDownloadState;
        values.notice_id = this.props.location.state && this.props.location.state.id;
        values.content = values.content.toHTML();

        if (values.files.length !== 0) { //过滤已存在的文件
          values.files = values.files.filter((file: any) => !file.guise)
        }

        values.deletefilesId = this.state.deletefilesId;

        if (this.props.location.state && this.props.location.state.type === 'publishNotice') {
          (async () => {
            const result = await postForm('/api/c_notice_updateNotice', {// 更新
              body: values
            });
            if (result.status === 'success') {
              this.goBackPage();
            } else {
              message.success(result.message);
            }
          })()
        } else if (this.props.location.state && this.props.location.state.type === 'draftNotice') {
          (async () => {
            const result = await postForm('/api/c_notice_updateNotice', {// 先更新
              body: values
            });
            if (result.status === 'success') {
              const result1 = await req('/api/c_notice_publish', {// 再次发布
                method: 'POST',
                body: {
                  notice_id: values.notice_id
                }
              });
              if (result1.status === 'success') {
                this.goBackPage();
              } else {
                message.success('服务器异常，请稍后再试');
              }
            }
          })()
        } else {// 新建公告直接发布
          this.dealWithForm("publish");
        }
      }
    })
  }

  saveDraft = () => {// 保存草稿
    if (this.state.type === 'draftNotice') {// 判断新建公告是否是直接保存草稿
      this.props.form.validateFieldsAndScroll((err: any, values: any) => {
        values.c_notice_column_id = 'aaaaaa' + values.c_notice_column_id;
        values.allow_download = this.state.allowDownloadState;
        values.notice_id = this.props.location.state && this.props.location.state.id;
        values.content = values.content.toHTML();
        if (!err) {
          (async () => {
            const result = await req('/api/c_notice_updateNotice', {// 执行更新
              method: 'POST',
              body: values
            });
            if (result.status === 'success') {
              this.goBackPage();
            } else if (result.status === 'fail') {
              message.success(result.message);
            }
          })()
        }
      })
    } else {
      this.dealWithForm("draft");
    }
  }
  onAllowDown = (e: any) => {
    if (e.target.checked) {
      this.setState({
        allowDownloadState: 1
      })
    } else {
      this.setState({
        allowDownloadState: 0
      })
    }
  }
  /**
   * 删除已存在的文件
   */
  onRemove = (file: any) => {
    const fileList = this.props.form.getFieldValue("files");
    const index = fileList.indexOf(file);
    const newFileList = fileList.slice();
    newFileList.splice(index, 1);
    if (file.guise) { // 后端返回
      //保存删除掉的文件id
      this.setState((prevState: any) => ({
        deletefilesId: prevState.deletefilesId.concat(file.uid)
      }))
    } else {
      this.props.form.setFieldsValue({
        "files": newFileList
      })
    }
  }
  beforeUpload = (file: any) => {

    const fileList = this.props.form.getFieldValue("files");
    this.props.form.setFieldsValue({
      "files": [...fileList, file]
    })
    return false;
  }
  normFile = (e: any) => {
    if (Array.isArray(e)) {
      return e;
    }
    return e && e.fileList;
  }

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
  // 关闭人员选择 Modal
  cancelModal = (e: any) => {
    this.setState({
      selectPersonnelVisible: false,
    });
  }
  // 预览
  previewNotice = () => {
    this.props.form.validateFieldsAndScroll((err: any, values: any) => {
      if (!err) {
        values.allow_download = this.state.allowDownloadState;
        let newValue = {};
        const content = values.content.toHTML();
        if (this.props.location.state && (this.props.location.state.type === 'publichNotice' || this.props.location.state.type === 'draftNotice')) {// 如果是通过 详情的编辑跳转过来的
          const { organiser, created_at, browse_user_count } = this.props.location.state.detailInfo.notice;
          newValue = {
            ...values,
            organiser,
            created_at,
            content,
            browse_user_count
          };
        } else {
          newValue = {
            ...values,
            content,
            organiser: 'xxxx'
          }
        }
        console.log(newValue, 'valuesvaluesvaluesvalues')
        this.setState({
          previewVisible: true,
          formDatas: {
            notice: newValue,
            files: values.files
          }
        })
      } else {
        message.info('请填写完必填项之后，再次预览')
      }
    })

  }
  previewCancel = () => {
    this.setState({
      previewVisible: false,
    })
  }

  render() {
    const { noticeList } = this.props;
    const { getFieldDecorator } = this.props.form;
    const { rangeInfo, selectPersonnelVisible, previewVisible, formDatas } = this.state;
    const controls: any[] = ['headings', 'font-size', 'font-family', 'separator', 'bold', 'italic', 'underline', 'text-color', 'separator', 'list-ul', 'list-ol', 'link', 'separator', 'media']
    const modalProps = {
      visible: selectPersonnelVisible,
      onOk: this.okModal,
      onCancel: this.cancelModal,
      checkedKeys: rangeInfo === 'all' ? [] : (rangeInfo as any).checkedKeys,
      checkedPersonnels: rangeInfo === 'all' ? [] : (rangeInfo as any).checkedPersonnels
    }
    const previewProps = {
      visible: previewVisible,
      onCancel: this.previewCancel,
      formDatas
    }
    const formItemLayout = {
      labelCol: {
        span: 3,
      },
      wrapperCol: {
        span: 5,
      },
    };
    const formFileLayout = {
      labelCol: {
        span: 3,
      },
      wrapperCol: {
        span: 10,
      },
    };

    return (
      <Layout style={{ height: 'calc(100vh - 97px)', overflowY: 'auto', }}>
        <div className="nowPosition">
          <div style={{ padding: '0 20px', height: '56px', lineHeight: '56px', borderBottom: '1px solid #eee' }}>
            <span className="goback" onClick={this.props.history.goBack}> <Icon type="arrow-left" />返回</span>
          </div>
        </div>
        <Form>
          <div style={{ padding: 24, background: '#fff', minHeight: 280, textAlign: 'center' }}>
            <div className="text-left">
              <Content>
                <FormItem
                  {...formItemLayout}
                  label='栏目选择'
                >

                  {getFieldDecorator('c_notice_column_id', {
                    rules: [
                      {
                        required: true,
                        message: '请选择栏目！'
                      }],
                    // initialValue: noticeList && noticeList[0].id
                  })(
                    <Select onChange={this.handleChange} dropdownStyle={{ zIndex: 99 }} placeholder="请选择栏目">
                      {
                        noticeList && noticeList.map((item: any) => (
                          <Option
                            key={item.id}
                            value={typeof item.id === 'string' ? item.id.slice(6) : item.id}
                          >
                            {item.name}
                          </Option>
                        ))
                      }
                    </Select>
                  )}
                </FormItem>
                <FormItem
                  {...formItemLayout}
                  label='标题'
                >   {getFieldDecorator('title', {
                  initialValue: '',
                  rules: [
                    { required: true, message: '标题不能为空' },
                    { min: 2, message: '标题至少填写2个文字' },
                    { whitespace: true, message: '请输入标题' }
                  ],
                })(
                  <Input placeholder="请输入公告标题" maxLength={64} />
                )}
                </FormItem>
                <FormItem
                  {...formFileLayout}
                  label='公告内容'
                >   {getFieldDecorator('content', {
                  validateTrigger: 'onBlur',
                  rules: [{
                    required: true,
                    validator: (_: any, value: any, callback: any) => {
                      if (value.isEmpty()) {
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
                  label={(
                    <span>
                      可见范围&nbsp;
                      <Tooltip title="默认是公司内部全体员工">
                        <Icon type="question-circle-o" />
                      </Tooltip>
                    </span>
                  )}
                >
                  {getFieldDecorator('guard_json', {
                    valuePropName: 'checkedInfo'
                  })(
                    <SelectParticipant />
                  )}
                </FormItem>
                <FormItem
                  {...formFileLayout}
                  label='附件上传'
                >   {getFieldDecorator('files', {
                  valuePropName: 'fileList',
                  getValueFromEvent: this.normFile,
                  initialValue: []
                })(
                  <Dragger
                    name="files"
                    listType="picture"
                    onRemove={this.onRemove}
                    beforeUpload={this.beforeUpload}
                  >
                    <p className="ant-upload-drag-icon">
                      <Icon type="inbox" />
                    </p>
                    <p className="ant-upload-text">点击上传或者拖拽上传文件</p>
                    <p className="ant-upload-hint">支持单个或批量上传,支持.doc,.docx,.pdf,.xls,.xlsx,.ppt,.pptx,.zip,.rar类型文件，20M以内</p>
                  </Dragger>
                )}
                </FormItem>
                <FormItem
                  {...formItemLayout}
                  label='其他设置'
                >   {getFieldDecorator('allow_download')(
                  <Checkbox onChange={this.onAllowDown} >允许下载附件</Checkbox>
                )}
                </FormItem>
                <FormItem
                  {...formFileLayout}
                  label='提醒方式'
                >   {getFieldDecorator('notification_way')(
                  <NotifiMeds />
                )}
                </FormItem>
                <FormItem
                  wrapperCol={{ span: 10, offset: 3 }}
                >
                  <div>
                    <Button type="primary" style={{ marginRight: "10px" }} onClick={this.handleSubmit}>发布</Button>
                    {
                      (() => {
                        if (!this.props.location.state) {// 新建公告直接保存草稿
                          return <Button type="primary" style={{ marginRight: "10px" }} onClick={this.saveDraft}>保存草稿</Button>
                        } else if (this.props.location.state && this.props.location.state.type !== 'publishNotice') { // 草稿箱编辑后再次保存为草稿
                          return <Button type="primary" style={{ marginRight: "10px" }} onClick={this.saveDraft}>保存草稿</Button>
                        }
                        return null
                      }
                      )()
                    }
                    <Button type="primary" onClick={this.previewNotice}>预览</Button>
                  </div>
                </FormItem>
              </Content>
            </div>
          </div>
          <SelectPersonnelModal {...modalProps} />
          <PreviewForm {...previewProps} />
        </Form>
      </Layout>
    );
  }
}
export default Form.create()(Create)
