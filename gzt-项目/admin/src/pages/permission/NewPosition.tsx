
import * as React from 'react';
import { FormComponentProps } from 'antd/lib/form';
import { Layout, Form, Input, List, Row, Col, Checkbox, Button, Divider, Icon, Spin, message } from 'antd';
import { History } from 'history';
import { connect } from 'dva';
import update from 'immutability-helper';
import req from '../../utils/request';
import { Link } from 'react-router-dom';
import './index.scss';

const { Content, Header } = Layout;
const FormItem = Form.Item;
const CheckboxGroup = Checkbox.Group;
interface NewPositionProps extends FormComponentProps {
  history: History;
  showBasePermission: any,
  listLoading: boolean,
  editPositionInfo: any,
  basePermissionList: any,
  editPermissionList: any,
  location: any
}

const NAMESPACE = 'Company';
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    listLoading: state.loading.effects[`${NAMESPACE}/queryBasePermission`]
  }
}

const mapDispatchToProps = (dispatch: any) => {
  return {
    showBasePermission: () => {
      dispatch({
        type: `${NAMESPACE}/queryBasePermission`,
      });
    },
    /** 编辑职务 */
    editPositionInfo: (params: any, cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/queryPositionInfo`,
        payload: { params, cb }
      });
    }
  }
}
const formItemLayout = {
  labelCol: {
    xs: { span: 24 },
    sm: { span: 2 },
  },
  wrapperCol: {
    xs: { span: 24 },
    sm: { span: 6 },
  },
};

@connect(mapStateToProps, mapDispatchToProps)
class NewPosition extends React.Component<NewPositionProps, any> {
  state = {
    checkboxState: {}
  }
  componentDidMount() {
    this.props.showBasePermission();
    if (this.props.location.state.type === 'edit') {//通过编辑进入
      this.props.editPositionInfo({ role_id: this.props.location.state.roleId }, (per: any) => {
        this.setState({
          checkboxState: per
        })
      })
    }
  }
  componentDidUpdate(prevProps: any) {
    if (this.props.location.state.type === 'edit') {
      if (this.props.editPermissionList !== prevProps.editPermissionList) { // 判断是否变化
        this.props.form.setFieldsValue({
          name: this.props.editPermissionList && this.props.editPermissionList.name
        })
      }
    }
  }
  handleSubmit = (e: any) => {
    e.preventDefault();
    this.props.form.validateFieldsAndScroll((err, values) => {
      if (!err) {
        let checkedOption = [];
        for (const key in this.state.checkboxState) {
          checkedOption.push(this.state.checkboxState[key]);
        }
        values.per_array = checkedOption.flat();
        if (this.props.location.state.type === 'edit') {// 通过编辑进入

          values.id = this.props.editPermissionList && this.props.editPermissionList.id;
          (async () => {
            const result = await req('/api/management_role_save_edit', {
              method: 'POST',
              body: values
            })
            if (result.status === 'success') {
              message.info('职务编辑成功');
              this.props.history.push('/permission');
            } else if (result.status === 'fail') {
              message.success(result.message);
            } else {
              message.success('服务器繁忙,请稍后再试~');
            }
          })();

        } else {
          (async () => {
            const result = await req('/api/management_role_add', {
              method: 'POST',
              body: values
            })
            if (result.status === 'success') {
              message.info('新建成功');
              this.props.history.push('/permission');
            } else if (result.status === 'fail') {
              message.success(result.message);
            } else {
              message.success('服务器繁忙,请稍后再试~');
            }
          })();
        }
      }
    })
  }

  onCheckChange = (checkedValue: any, value: any) => {
    this.setState({
      checkboxState: update(this.state.checkboxState, {
        [value]: {
          $set: checkedValue
        }
      })
    })
  }

  render() {
    const { getFieldDecorator } = this.props.form;
    const { listLoading, basePermissionList } = this.props;
    const { checkboxState } = this.state;

    return (
      <Content className="permission-wrapper wrapper">
        <Header className="white" style={{ height: 40 }}>
          <span className="cursor-pointer" onClick={this.props.history.goBack}>
            <Icon type="arrow-left" />返回</span>
        </Header>
        <Divider />
        <Form onSubmit={this.handleSubmit}>
          <FormItem
            {...formItemLayout}
            label="职务名称"
          >
            {getFieldDecorator('name', {
              rules: [{
                required: true, message: '名称不能为空',
              }],
            })(
              <Input placeholder='请输入职务名称' />
            )}
          </FormItem>
          <header style={{ height: '40px', lineHeight: '40px', background: '#f5f5f5' }}>
            <Row style={{ padding: '0 30px' }}>
              <Col span={4}>应用名称</Col>
              <Col span={20}>权限</Col>
            </Row>
          </header>
          <Spin spinning={listLoading}>
            <List
              dataSource={basePermissionList}
              size="small"
              renderItem={
                (item: any) => (
                  <Row className="list-item" type="flex">
                    <Col span={4}>
                      {item.name}
                    </Col>
                    <Col span={20}>
                      <CheckboxGroup onChange={(checkedValue) => this.onCheckChange(checkedValue, item.id)} value={checkboxState[item.id]}>
                        {
                          item.data && item.data.map((item: any, index: any) => {
                            return <div key={index}>
                              <Checkbox value={item.id}>{item.description}</Checkbox>
                            </div>
                          })
                        }
                      </CheckboxGroup>
                    </Col>
                  </Row>
                )
              }
            />
            <div style={{ marginTop: '10px' }}>
              <Button type="primary" htmlType="submit" className='btn'>保存</Button>
              <Button><Link to="/permission">取消</Link></Button>
            </div>
          </Spin>
        </Form>
      </Content>
    )
  }
}
export default Form.create()(NewPosition)
