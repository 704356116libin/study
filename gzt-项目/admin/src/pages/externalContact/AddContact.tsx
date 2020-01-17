
import * as React from 'react';
import { Layout, Input, Icon, List, Row, Col, Spin, Modal, Form, Button, message } from 'antd';
import { connect } from 'dva';
import { History } from 'history';
import { FormComponentProps } from 'antd/lib/form';
import './index.scss';

const { Content } = Layout;
const Search = Input.Search;
const { TextArea } = Input;
const FormItem = Form.Item;

const NAMESPACE = 'Contact';
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    listLoading: state.loading.effects[`${NAMESPACE}/queryCompanyContact`]
  }
}
const mapDispatchToProps = (dispatch: any) => {
  return {
    /**搜索外部联系人信息 */
    showContactInfo: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryCompanyContact`,
        payload: { params }
      })
    },
    /**邀请外部联系人 */
    inviteContact: (body: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/inviteContact`,
        payload: { body, cb }
      })
    }
  }
}

/**搜索外部联系人信息，添加外部联系人 */
interface AddContactProps extends FormComponentProps {
  history: History,
  showContactInfo: any,
  companyContactList: any,
  inviteContact: any,
  listLoading: any
}

@connect(mapStateToProps, mapDispatchToProps)
class addContact extends React.Component<AddContactProps, any> {

  state = {
    visible: false,
    id: '',
    apply_description: '',
    currentSearchValue: ''
  }

  handlePressEnter = (value: string) => {
    this.props.showContactInfo({
      condition: value
    })
    this.setState({
      currentSearchValue: value
    })
  }
  addContact = (item: any) => {
    this.setState({
      visible: true,
      id: item.id
    })
  }
  handleOK = () => {
    this.props.form.validateFieldsAndScroll((err, values) => {
      if (!err) {
        this.setState({
          visible: false
        })
        this.props.inviteContact({
          user_id: this.state.id,
          description: values.desc
        }, () => {
          message.info('发送邀请成功');
          // 刷新
          this.props.showContactInfo({ condition: this.state.currentSearchValue })
        })
      }
    })
  }
  handleChange = (e: any) => {
    const value = e.target.value;
    this.setState({
      apply_description: value
    })
  }
  render() {
    const { companyContactList, listLoading, } = this.props;
    const { handleOK } = this;
    const { visible } = this.state;
    const { getFieldDecorator } = this.props.form;
    return (
      <Content className="addpartner-wrapper wrapper">
        <div className="addpartner-top">
          <span className="goback" onClick={this.props.history.goBack}> <Icon type="arrow-left" />返回</span>
        </div>
        <div className="text-center search-partner" >
          <Search
            placeholder="请输入外部联系人名称/手机号/邮箱"
            enterButton="搜索"
            style={{ width: 500 }}
            onSearch={value => this.handlePressEnter(value)}
            allowClear={true}
          />
        </div>
        <div>
          {
            companyContactList &&
            <Spin spinning={listLoading}>
              <div>
                <header style={{ height: '40px', lineHeight: '40px', background: '#f5f5f5' }}>
                  <Row style={{ padding: '0 30px' }}>
                    <Col span={5}>
                      用户昵称
                    </Col>
                    <Col span={5}>
                      手机号
                    </Col>
                    <Col span={9}>
                      邮箱
                    </Col>
                    <Col span={5}>
                      操作
                    </Col>
                  </Row>
                </header>
                <List
                  itemLayout="vertical"
                  size="large"
                  rowKey="id"
                  dataSource={companyContactList}
                  renderItem={(item: any) => (
                    <Row className="partner-list">
                      <List.Item className="clearfix">
                        <Col span={5}>
                          <span >{item.name}</span>
                        </Col>
                        <Col span={5}>
                          <span >{item.tel}</span>
                        </Col>
                        <Col span={5}>
                          <span >{item.email}</span>
                        </Col>
                        <Col span={4}>
                          <span >{
                            item.operating === "邀请成为外部联系人" ?
                              (<Button type='primary' onClick={() => this.addContact(item)}>{item.operating} </Button>)
                              :
                              (<span> {item.operating}</span>)
                          }</span>
                        </Col>
                      </List.Item>
                    </Row>
                  )}
                />
              </div>
            </Spin>
          }
          <Modal
            title='附加验证信息'
            visible={visible}
            centered
            mask
            maskClosable={false}
            onOk={handleOK}
            onCancel={() => { this.setState({ visible: false }) }}
            destroyOnClose={true}
            width={600}
          >
            <Form>
              <FormItem
                wrapperCol={{ span: 24 }}
              >   {getFieldDecorator('desc', {
                rules: [
                  { required: true, message: '请输入名称' },
                ]
              })(
                <TextArea placeholder="请输入验证内容、方便快速验证通过" onChange={this.handleChange} autosize={{ minRows: 3, maxRows: 6 }} />
              )}
              </FormItem>
            </Form >
          </Modal>
        </div>
      </Content>
    )
  }
}
export default Form.create()(addContact)
