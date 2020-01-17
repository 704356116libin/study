import * as React from 'react';
import { Layout, Form, Input, Button, message } from 'antd';
import { FormComponentProps } from 'antd/lib/form';
import request from '../../utils/request';
import { History } from 'history';
import { connect } from 'dva';

const { Content } = Layout;

interface CompanyRegisterProps extends FormComponentProps {
  history: History;
  queryCompanys: Function;
  queryUserPermission: Function;
  resetWorkbench: Function;
}
const NAMESPACE = 'Workbench'; // dva model 命名空间
const USERINFO = 'UserInfo'; // dva model 命名空间

const mapDispatchToProps = (dispatch: any) => {
  return {
    resetWorkbench: () => {
      dispatch({
        type: `${NAMESPACE}/resetWorkbench`,
      })
    },
    queryCompanys: () => {
      dispatch({
        type: `${NAMESPACE}/queryCompanys`
      });
    },
    queryUserPermission: () => {
      dispatch({
        type: `${USERINFO}/queryUserPermission`
      });
    }
  }
}

@connect(undefined, mapDispatchToProps)
class CompanyRegister extends React.Component<CompanyRegisterProps, any>{

  handleSubmit = (e: React.FormEvent<any>) => {
    e.preventDefault();

    this.props.form.validateFieldsAndScroll(async (err, values) => {
      if (!err) {
        const result = await request('/api/c_create', {
          method: 'POST',
          body: values
        })
        if (result.status === 'success') {
          message.success(result.message);
          this.props.queryCompanys();
          this.props.queryUserPermission();
          this.props.resetWorkbench();
          this.props.history.push('/work');
        } else {
          message.error(result.message);
        }
      }
    })
  }

  render() {

    const { getFieldDecorator } = this.props.form;

    return (
      <Layout style={{ height: 'calc(100vh - 61px)' }}>
        <Content style={{ margin: '30px auto', width: '540px' }}>
          <Form onSubmit={this.handleSubmit}>
            <Form.Item>
              {
                getFieldDecorator('name', {
                  rules: [{
                    required: true, message: '请输入企业名称'
                  }]
                })(
                  <Input placeholder="请输入企业名称" />
                )
              }
            </Form.Item>
            <Form.Item>
              <Button type="primary" htmlType="submit" block>创建</Button>
            </Form.Item>
          </Form>
        </Content>
      </Layout>
    );
  }
}
export default Form.create<CompanyRegisterProps>()(CompanyRegister)