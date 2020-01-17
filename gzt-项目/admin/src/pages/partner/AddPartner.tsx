
import * as React from 'react';
import { Layout, Input, Icon, List, Row, Col, Spin, Modal, Form, Button, message } from 'antd';
import { connect } from 'dva';
import { History } from 'history';

import './index.scss';
import { FormComponentProps } from 'antd/lib/form';
const { Content } = Layout;
const Search = Input.Search;
const { TextArea } = Input;
const FormItem = Form.Item;
const NAMESPACE = 'Partner';
/**搜索合作企业信息，添加合作企业 */
interface AddPartnerProps extends FormComponentProps {
  history: History,
  showPartnerInfo: any,
  companyPartnerList: any,
  invitePartner: any,
  listLoading: any
}
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    listLoading: state.loading.effects[`${NAMESPACE}/queryCompanyPartner`],
  }
}

const mapDispatchToProps = (dispatch: any) => {
  return {
    /**搜索合作企业信息 */
    showPartnerInfo: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryCompanyPartner`,
        payload: { params }
      });
    },
    /**添加合作企业 */
    invitePartner: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/invitePartner`,
        payload: { params, cb }
      });
    },
  }
}
@connect(mapStateToProps, mapDispatchToProps)
class addPartner extends React.Component<AddPartnerProps, any> {

  state = {
    visible: false,
    id: '',
    partnerName: '',
    apply_description: ''
  }

  componentDidMount() {
    /**todo */
  }
  handlePressEnter = (value: string) => {
    this.props.showPartnerInfo({
      name: value
    })
  }
  addPartner = (item: any) => {
    this.setState({
      visible: true,
      id: item.id,
      partnerName: item.name
    })
  }
  handleOK = () => {
    this.setState({
      visible: false
    })
    this.props.invitePartner({
      invite_company_id: this.state.id,
      apply_description: this.state.apply_description,
      partnerName: this.state.partnerName
    }, () => {
      message.info('发送邀请成功');
      // this.props.showPartnerInfo({})
    })
  }
  handleChange = (e: any) => {
    const value = e.target.value;
    this.setState({
      apply_description: value
    })
  }
  render() {
    const { companyPartnerList, listLoading, } = this.props;
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
            placeholder="请输入公司名称或企业号"
            enterButton="搜索"
            style={{ width: 500 }}
            onSearch={value => this.handlePressEnter(value)}
            allowClear={true}
          />
        </div>
        <div>
          {
            companyPartnerList &&
            <Spin spinning={listLoading}>
              <div>
                <header style={{ height: '40px', lineHeight: '40px', background: '#f5f5f5' }}>
                  <Row style={{ padding: '0 30px' }}>
                    <Col span={5}>
                      企业名称
                 </Col>
                    <Col span={5}>
                      企业号
                 </Col>
                    <Col span={5}>
                      企业人数
                 </Col>
                    <Col span={5}>
                      认证状态
                 </Col>
                    <Col span={4}>
                      操作
                 </Col>
                  </Row>
                </header>
                <List
                  itemLayout="vertical"
                  size="large"
                  rowKey="id"
                  // pagination={paginationProps}
                  dataSource={companyPartnerList}
                  renderItem={(item: any) => (
                    <Row className="partner-list">
                      <List.Item className="clearfix">
                        <Col span={5}>
                          <span >{item.name}</span>
                        </Col>
                        <Col span={5}>
                          <span >{item.number}</span>
                        </Col>
                        <Col span={5}>
                          <span >{item.company_number}</span>
                        </Col>
                        <Col span={5}>
                          <span >{item.verifie_status === 0 ? '未认证' : '已认证'}</span>
                        </Col>
                        <Col span={4}>
                          <span >{
                            item.operating === "加合作伙伴" ?
                              (<Button type='primary' onClick={() => this.addPartner(item)}>{item.operating} </Button>)
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
                ],
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
export default Form.create()(addPartner);