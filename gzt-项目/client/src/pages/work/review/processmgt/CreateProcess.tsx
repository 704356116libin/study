import * as React from 'react';
import { Icon, Form, Layout, Input, Button, Select, message } from 'antd';
import SelectPersonnelModal, { CheckedPersonnelsInfo } from '../../../../components/selectPersonnelModal';
import TextLabel from '../../../../components/textLabel';
import SelectApprover from '../../../../components/selectApprover';
import { FormComponentProps } from 'antd/lib/form';
import { History, Location } from 'history';
import AddSelect from '../../../../components/addSelect';
import { connect } from 'dva';
import request from '../../../../utils/request';
import decryptId from '../../../../utils/decryptId';
import generateRandomString from '../../../../utils/generateRandomString';

const FormItem = Form.Item;
const Option = Select.Option;

export interface CreateProcessProps extends FormComponentProps {
  location: Location;
  processGroup: string[];
  /** 获取模板分组信息 */
  queryProcessGroup: Function;
  /** 获取模板详细信息 by id */
  queryProcessById: Function;
  /** 获取流程列表信息 */
  queryProcesses: Function;
  history: History;
}

const NAMESPACE = 'Review';
const mapStateToProps = (state: any) => {
  return {
    processGroup: state[NAMESPACE].processGroup
  }
};
const mapDispatchToProps = (dispatch: any) => {
  return {
    queryProcessGroup: (cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/queryProcessGroup`,
        payload: cb
      });
    },
    queryProcessById: (id: string, cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/queryProcessById`,
        payload: {
          id, cb
        }
      });
    },
    /** 获取流程列表信息 (用于刷新) */
    queryProcesses: () => {
      dispatch({
        type: `${NAMESPACE}/queryProcesses`
      });
    }
  }
}
@connect(mapStateToProps, mapDispatchToProps)
class CreateProcess extends React.Component<CreateProcessProps, any>{

  state = {
    selectPersonnelVisible: false,
    selectedPersonnelInfo: 'all' as CheckedPersonnelsInfo | 'all',
    processGroupLoading: false,
    processGroup: undefined as any
  }

  componentDidMount() {
    this.setState({
      processGroupLoading: true
    });
    if (this.props.location.state) {
      this.props.queryProcessById(this.props.location.state.data.id, ({
        name,
        description,
        type,
        per,
        process_template
      }: any) => {
        this.props.form.setFieldsValue({
          name,
          description,
          process_type_id: decryptId(type.id),
          per,
          process_template
        })
      });
    }
    this.props.queryProcessGroup((processGroup: any) => {
      this.setState({
        processGroupLoading: false,
        processGroup
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
        values.process_type_id = generateRandomString(6) + values.process_type_id;

        let url = '/api/c_pst_add_process_template';
        let messageText = '创建成功';
        if (this.props.location.state && this.props.location.state.type === 'UPDATE') {
          url = '/api/c_pst_update_process_template';
          messageText = '修改成功';
          values.id = this.props.location.state.data.id;
        }

        const result = await request(url, {
          method: 'POST',
          body: values
        })
        console.log(result);

        if (result.status === 'success') {
          message.success(messageText);
          this.props.queryProcesses();
          this.props.history.replace('/work/review/processmgt');
        } else {
          message.error('服务器错误，请稍后再试')
        }

      }
    })
  }

  handleProcessAdd = async (addOptionValue: any) => {
    if (addOptionValue === '') {
      message.info('分组名称不能为空哦~');
      return
    }
    const result = await request('/api/c_pst_add_process_template_type', {
      method: 'POST',
      body: {
        name: addOptionValue
      }
    });

    if (result.status === 'success') {
      message.success('新建成功');
      this.setState({
        processGroup: [...this.state.processGroup, {
          count: 0,
          id: result.data.id,
          name: addOptionValue,
          type: 'type'
        }]
      })
      this.props.queryProcesses();
    } else {
      message.error('服务器异常，请稍后再试')
    }
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
  render() {

    const { getFieldDecorator } = this.props.form;
    const {
      selectPersonnelVisible,
      selectedPersonnelInfo,
      processGroupLoading,
      processGroup
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

    const title = this.props.location.state ?
      this.props.location.state.type === 'INSERT' ?
        '新建流程' :
        '编辑流程' :
      '新建流程';

    return (

      <Layout className="review-process-create white" >
        <div style={{ padding: '0 20px', height: '56px', lineHeight: '56px', border: '1px solid #eee' }}>
          <span className="goback" onClick={() => this.props.history.replace('/work/review/processmgt')}>
            <Icon type="arrow-left" />返回
          </span>
          <TextLabel style={{ marginLeft: '20px' }} text={title} colon={false} />
        </div>
        <Form onSubmit={this.handleSubmit} style={{ marginTop: '30px' }}>
          <FormItem
            {...formItemLayout}
            label='流程名称'
          >
            {getFieldDecorator('name', {
              rules: [
                {
                  required: true,
                  message: '请输入流程名称',
                }],
            })(
              <Input placeholder="请为这个流程起个名字" />
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label='分组选择'
          >
            {getFieldDecorator('process_type_id', {
              rules: [
                {
                  required: true,
                  message: '请选择',
                }],
            })(
              <AddSelect placeholder="请选择分组" loading={processGroupLoading} onAdd={this.handleProcessAdd}>
                {processGroup && processGroup.map((option: any, index: any) => (
                  <Option value={decryptId(option.id)} key={index}>{option.name}</Option>
                ))}
              </AddSelect>
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label='流程说明'
          >
            {getFieldDecorator('description', {
              initialValue: ''
            })(
              <Input placeholder="可以为这个流程添加一个描述" />
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
            label='审批人员（固定流程）'
          >
            {getFieldDecorator('process_template', {
              valuePropName: 'approvers',
              rules: [
                {
                  required: true,
                  message: '请选择审批人员',
                }
              ]
            }
            )(
              <SelectApprover />
            )}
          </FormItem>
          <FormItem
            {...tailFormItemLayout}
          >
            <Button type="primary" htmlType="submit" style={{ marginRight: "10px" }}>发布</Button>
          </FormItem>

        </Form>
        <SelectPersonnelModal
          visible={selectPersonnelVisible}
          onOk={this.selectPersonnelOk}
          onCancel={this.selectPersonnelCancel}
          version={Date.now()}
        />
      </Layout>
    )
  }
}
export default Form.create()(CreateProcess)
