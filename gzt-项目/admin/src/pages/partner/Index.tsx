import React from 'react';
import { Layout, Button, List, Popconfirm, Icon, Input, Form, Row, Col, Spin, message } from 'antd';
import classNames from 'classnames';
import { connect } from 'dva';
import { FormComponentProps } from 'antd/lib/form';
import { Link } from 'react-router-dom';
import EditGroupName from '../../components/EditGroupName';
import AddGroup from '../../components/AddGroup';
import './index.scss';

const { Content, Sider } = Layout;
const ListItem = List.Item;
const NAMESPACE = 'Partner';
const Search = Input.Search;
export interface partnerProps extends FormComponentProps {
  groupLoading: boolean;
  listLoading: boolean;
  showPartnerList: any;
  showPartnerCorrespondList: any;
  partnerOperating: any;
  partnerList: any;
  partnerCorrespondList: any;
  releaseRelationship: any;
  showCompanyPartner: Function;
}


const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    groupLoading: state.loading.effects[`${NAMESPACE}/queryPartnerList`],
    listLoading: state.loading.effects[`${NAMESPACE}/queryPartnerCorrespondList`],
  }
};
const mapDispatchToProps = (dispatch: any) => {
  return {
    /**合作伙伴分组列表 */
    showPartnerList: (value: string) => {
      dispatch({
        type: `${NAMESPACE}/queryPartnerList`,
        payload: value,
      });
    },
    /** 分组对应合作伙伴信息列表  */
    showPartnerCorrespondList: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryPartnerCorrespondList`,
        payload: { params }
      });
    },
    /**分组名的增,删,改 */
    partnerOperating: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/operatingPartnerGroup`,
        payload: { params, cb },
      });
    },
    /**解除关系 */
    releaseRelationship: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/releaseRelationship`,
        payload: { params, cb },
      });
    },
    /**搜索公司合作伙伴 */
    showCompanyPartner: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/queryCompanyPartnerByName`,
        payload: { params, cb },
      });
    }
  }
}
@connect(mapStateToProps, mapDispatchToProps)
class Partner extends React.Component<partnerProps, any>{
  state = {
    currentActive: true,
    currentKey: -1,
    currentGroupName: '',
    currentColumnId: 'all',
    editGroupVisible: false,
    addGroupVisible: false,
    pageSize: 10,  // 每页的条数
    nowPage: 1, // 当前页数
  }
  componentDidMount() {
    this.props.showPartnerList();
    this.props.showPartnerCorrespondList({
      id: this.state.currentColumnId,
      page_size: this.state.pageSize,
      now_page: this.state.nowPage
    });

  }
  /**新建分组 */
  addGrouping = () => {
    this.setState({
      addGroupVisible: true,
    })
  }
  showPartnerAllInfo = () => {
    this.setState({
      currentActive: true,
      currentKey: -1,
      currentColumnId: 'all'
    })
    this.props.showPartnerCorrespondList({
      id: 'all',
      page_size: this.state.pageSize,
      now_page: this.state.nowPage
    })
  }
  /**分组对应合作伙伴列表 */
  onCurrentInfo = (item: any, k: any) => {
    this.setState({
      currentKey: k,
      currentActive: false,
      currentColumnId: item.id
    })
    this.props.showPartnerCorrespondList({
      id: item.id,
      page_size: this.state.pageSize,
      now_page: this.state.nowPage
    })
  }
  /**删除分组 */
  removeGroupingInfo = (type_id: any) => {
    this.props.partnerOperating({
      type_id,
      operating: 'delete'
    }, () => {
      message.info('删除成功')
    })
  }
  /** 显示编辑模态框  */
  showEditModal = (item: any) => {
    this.setState({
      editGroupVisible: true,
      maskClosable: false,
      currentGroupName: item.name,
      currentColumnId: item.id
    })
  }
  /**
   * 分页
   */
  paginationChange = (pageNumber: any, pageSize: any) => {
    this.props.showPartnerCorrespondList({
      id: this.state.currentColumnId,
      page_size: pageSize,
      now_page: pageNumber
    })
  }
  /**
  * 分页
  */
  onShowSizeChange = (pageNumber: number, pageSize: number) => {
    this.props.showPartnerCorrespondList({
      id: this.state.currentColumnId,
      page_size: pageSize,
      now_page: pageNumber
    })
    this.setState({
      nowPage: pageNumber,
      pageSize
    })
  }
  /** 编辑栏目确定事件 */
  editHandleOk = (form: any) => {
    form.validateFieldsAndScroll((err: any, values: any) => {
      if (!err) {
        let columnName = this.props.partnerList.some(({ name }: any) => name === values.names);
        if (columnName) {
          message.success('分组名称不能重复');
        } else {
          this.setState({
            editGroupVisible: false,
          });
          this.props.partnerOperating({
            type_id: this.state.currentColumnId,
            operating: 'alter',
            type_name: values.names
          }, () => {
            message.success('修改成功');
          })
        }
      }
    });
  }
  /**添加分组 */
  addOk = () => {
    this.props.form.validateFieldsAndScroll((err: any, values: any) => {
      if (!err) {
        let columnName = this.props.partnerList.some(({ name }: any) => name === values.name);
        if (columnName) {
          message.success('分组名称不能重复');
        } else {
          this.setState({
            addGroupVisible: false,
          });
          this.props.partnerOperating({
            operating: 'add',
            type_name: values.name
          }, () => {
            message.success('新建成功');
          })
        }
      }
    })
  }
  /**解除关系 */
  releaseRelationship = (e: any, company_id: number) => {
    e.preventDefault();
    e.stopPropagation();
    this.props.releaseRelationship(
      { company_id },
      () => {
        message.info('解除成功');
        this.props.showPartnerCorrespondList({
          id: this.state.currentColumnId,
          page_size: this.state.pageSize,
          now_page: this.state.nowPage
        })
      })
  }
  /**搜索 */
  onPressEnter = (name: string) => {
    this.props.showCompanyPartner({
      name,
      page_size: this.state.pageSize,
      now_page: this.state.nowPage
    })
  }

  render() {
    const { listLoading, groupLoading, partnerList, partnerCorrespondList } = this.props;
    const { currentActive, currentKey, editGroupVisible, addGroupVisible, currentGroupName, pageSize, nowPage } = this.state;
    const paginationProps = {
      showSizeChanger: true,
      showQuickJumper: true,
      pageSize,
      pageSizeOptions: ['10', '15', '20'],
      total: partnerCorrespondList && partnerCorrespondList.count,
      onChange: this.paginationChange,
      defaultCurrent: nowPage,
      onShowSizeChange: this.onShowSizeChange,
      showTotal: (total: any) => `共 ${total} 条数据`
    }
    return (
      <Content className="partner-wrapper" >
        <Sider className="siderDepartment" width={240} >
          <div className="addGrouping" >
            <Button type="primary" icon="plus" block onClick={this.addGrouping}>新建分组</Button>
          </div>
          <div style={{ borderTop: '1px solid #eee' }} className={classNames("partner-left-list", { active: currentActive })}>
            <div className="allList" onClick={this.showPartnerAllInfo}>
              <span>全部</span>
            </div>
          </div>
          <Spin spinning={groupLoading}>
            <List
              dataSource={partnerList && partnerList}
              size="small"
              renderItem={
                (item: any, k: number) => (
                  <div onClick={() => this.onCurrentInfo(item, k)} className="cursor-pointer">
                    <ListItem key={item.name} className={classNames("partner-left-list", { active: currentKey === k })} >
                      <List.Item.Meta
                        style={{ width: '100%', paddingLeft: '20px' }}
                        title={
                          <div>
                            <span style={{ color: '#222' }}>{item.name}
                              <span className="operatColumn">
                                <Popconfirm title="是否要删除此分类？" onConfirm={() => this.removeGroupingInfo(item.id)}>
                                  <Icon type="delete"
                                    style={{ color: "#0CABF5", fontSize: '16px', }}
                                    onClick={(e: any) => { e.stopPropagation() }} />
                                </Popconfirm>
                              </span>
                              <a className="operatColumn"
                                onClick={e => {
                                  e.preventDefault();
                                  e.stopPropagation();
                                  this.showEditModal(item);
                                }}
                              >
                                <Icon type="form" style={{ color: "#0CABF5", fontSize: '16px', }} />
                              </a>
                            </span>
                          </div>
                        }
                      />
                    </ListItem>
                  </div>
                )
              }
            />
          </Spin>
          <AddGroup
            form={this.props.form}
            visible={addGroupVisible}
            handleOK={this.addOk}
            handleCancel={() => this.setState({ addGroupVisible: false })}
          />
          <EditGroupName
            visible={editGroupVisible}
            onOK={this.editHandleOk}
            onCancel={() => this.setState({ editGroupVisible: false })}
            currentGroupName={currentGroupName}
          />
        </Sider>
        <div style={{ marginLeft: '240px', width: 'calc(100% - 240px)' }}>
          <div className="partner-wrapper">
            <div style={{ float: 'left' }}>
              <Link to='/addPartner'>
                <Button type='primary' className="btn">添加合作伙伴</Button>
              </Link>
              <Link to="/applicationList">
                <Button type='default' className="btn">合作伙伴申请列表</Button>
              </Link>
            </div>
            <div>
              <Search
                placeholder="请输入名称"
                onSearch={value => this.onPressEnter(value)}
                style={{ width: 200 }}
                allowClear={true}
              />
            </div>
          </div>
          <Spin spinning={listLoading}>
            <header style={{ height: '40px', lineHeight: '40px', background: '#f5f5f5' }}>
              <Row style={{ padding: '0 30px' }}>
                <Col span={3}>
                  合作伙伴名称
                </Col>
                <Col span={3}>
                  联系人
                </Col>
                <Col span={3}>
                  手机
                </Col>
                <Col span={3}>
                  邮箱
                </Col>
                <Col span={6}>
                  地址
                </Col>
                <Col span={4}>
                  状态
                </Col>
              </Row>
            </header>
            <List
              itemLayout="vertical"
              size="large"
              rowKey="id"
              pagination={paginationProps}
              dataSource={partnerCorrespondList && partnerCorrespondList.data}
              renderItem={(item: any) => (
                <Row className="partner-list">
                  <List.Item className="clearfix">
                    <Col span={3}>
                      <span >{item.name}</span>
                    </Col>
                    <Col span={3}>
                      <span >{item.user_name}</span>
                    </Col>
                    <Col span={3}>
                      <span >{item.user_tel}</span>
                    </Col>
                    <Col span={3}>
                      <span >{item.user_email}</span>
                    </Col>
                    <Col span={6}>
                      <span >{item.address}</span>
                    </Col>
                    <Col span={4}>
                      <Popconfirm title="确定要解除合作关系？" onConfirm={e => this.releaseRelationship(e, item.id)}>
                        <span style={{ color: '#E61717' }}> 解除合作伙伴 </span>
                      </Popconfirm>
                    </Col>
                  </List.Item>
                </Row>
              )}
            />
          </Spin>
        </div>
      </Content>
    )
  }
}
export default Form.create()(Partner)



