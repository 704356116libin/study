
import * as React from 'react';
import { Layout, List, Row, Col, Icon, Button, Divider, message, Spin, Form } from 'antd';
import { History } from 'history';
import { Link } from 'react-router-dom';
import { connect } from 'dva';
import './index.scss';
import ChooseGroup from './ChooseGroup';
import { FormComponentProps } from 'antd/lib/form';
const { Content } = Layout;
const NAMESPACE = 'Partner';

export interface ApplicationListProps extends FormComponentProps {
  applicationList: any;
  location: any;
  history: History;
  dealPartnerApply: any;
  showPartnerApplicationList: any;
  listLoading: boolean;
  showPartnerList: any;
  // partnerList: any;
  partnerOperating: Function;
}
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    listLoading: state.loading.effects[`${NAMESPACE}/queryApplicationList`],
  }
}

const mapDispatchToProps = (dispatch: any) => {
  return {
    /**合作企业申请列表 */
    showPartnerApplicationList: () => {
      dispatch({
        type: `${NAMESPACE}/queryApplicationList`
      });
    },
    /**处理合作企业的申请 */
    dealPartnerApply: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/dealPartnerApply`,
        payload: { params, cb }
      });
    },
    /**合作伙伴分组列表 */
    showPartnerList: (cb: any) => {
      dispatch({
        type: `${NAMESPACE}/queryPartnerList`,
        payload: cb
      });
    },
    /**合作伙伴新建分组 */
    partnerOperating: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/operatingPartnerGroup`,
        payload: { params, cb },
      });
    }
  }
}


@connect(mapStateToProps, mapDispatchToProps)
class applicationList extends React.Component<ApplicationListProps, any>{
  state = {
    chooseGroupVisible: false,
    record_id: '',
    partnerList: []
  }
  componentDidMount() {
    this.props.showPartnerApplicationList();

    // 获取分组选择信息并更新state
    this.props.showPartnerList((partnerList: any) => {
      this.setState({
        partnerList
      })
    });
  }
  /**处理公司合作企业申请 */
  detailPartner = (params: any) => {
    this.props.dealPartnerApply(params, () => {
      message.info('操作成功');
    });
  }
  chooseGroupClassify = (item: any) => {
    this.setState({
      chooseGroupVisible: true,
      record_id: item.id
    })
  }
  chooseGroupOk = (groupId: number) => {
    this.setState({
      chooseGroupVisible: false
    })
    this.detailPartner({
      'type_id': groupId,
      'record_id': this.state.record_id,
      'operate_type': 'agree'
    })
  }
  /**添加分组 */
  addGroup = (name: string) => {
    let columnName = this.state.partnerList.some(({ name }: any) => name !== name);
    if (name === "" || name === undefined) {
      message.info('分组名称不能为空哦~');
    } else if (columnName) {
      message.success('分组名称不能重复');
    } else {
      // 请求后台如果成功添加后，添加
      this.props.partnerOperating({
        operating: 'add',
        type_name: name
      }, (id: number) => {
        this.setState((prevState: any) => {
          return {
            partnerList: prevState.partnerList.concat({
              id,
              name
            })
          }
        })
      })
    }
  }
  render() {
    const { applicationList, listLoading, } = this.props;
    const { chooseGroupVisible, partnerList } = this.state;
    return (
      <Content className="addpartner-wrapper wrapper">
        <div className="addpartner-top">
          <div style={{ float: 'left' }}>
            <span className="goback" onClick={this.props.history.goBack}> <Icon type="arrow-left" />返回</span>
          </div>
          <div style={{ float: 'right' }}>
            <Link to="/addPartner">
              <Button type="primary">搜索合作企业</Button>
            </Link>
          </div>
        </div>
        <div style={{ padding: '24px' }} className='text-center'>
          成员申请列表
        </div>
        {
          applicationList &&
          <div>
            <Spin spinning={listLoading}>
              <header style={{ height: '40px', lineHeight: '40px', background: '#f5f5f5' }}>
                <Row style={{ padding: '0 30px' }}>
                  <Col span={3}>
                    合作企业申请
                </Col>
                  <Col span={3}>
                    联系人
                </Col>
                  <Col span={3}>
                    手机号
                </Col>
                  <Col span={3}>
                    邮箱
                </Col>
                  <Col span={10}>
                    申请加入内容
                </Col>
                  <Col span={2}>
                    操作
                </Col>
                </Row>
              </header>
              <List
                itemLayout="vertical"
                size="large"
                rowKey="id"
                dataSource={applicationList}
                renderItem={(item: any) => (
                  <Row className="partner-list">
                    <List.Item className="clearfix">
                      <Col span={3}>
                        <span >{item.invite_company_name}</span>
                      </Col>
                      <Col span={3}>
                        <span >{item.partner_user_name}</span>
                      </Col>
                      <Col span={3}>
                        <span >{item.partner_user_tel}</span>
                      </Col>
                      <Col span={3}>
                        <span >{item.partner_user_email}</span>
                      </Col>
                      <Col span={10}>
                        <span >{item.apply_description}</span>
                      </Col>
                      <Col span={2}>
                        <span >
                          {
                            item.operating === 2 ? (
                              <> <span className="primary-color" onClick={
                                () => this.chooseGroupClassify(item)}
                              >同意</span>
                                <Divider type="vertical" />
                                <span onClick={() => this.detailPartner({ 'record_id': item.id, 'operate_type': 'refuse' })}>拒绝</span>
                              </>
                            ) :
                              item.operating === 0 ?
                                (<span>已拒绝</span>)
                                :
                                (<span>已同意</span>)
                          }
                        </span>
                      </Col>
                    </List.Item>
                  </Row>
                )}
              />
            </Spin>
            {
              partnerList &&
              <ChooseGroup
                visible={chooseGroupVisible}
                onChooseOk={this.chooseGroupOk}
                onCancel={() => { this.setState({ chooseGroupVisible: false }) }}
                groupList={partnerList}
                onAdd={this.addGroup}
              />
            }
          </div>
        }
      </Content >
    )
  }
}
export default Form.create()(applicationList)
