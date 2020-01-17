import React from 'react';
import { Layout, Button, List, Popconfirm, Icon, Input, Form, Row, Col, Spin, message } from 'antd';
import classNames from 'classnames';
import { connect } from 'dva';
import { FormComponentProps } from 'antd/lib/form';
import { Link } from 'react-router-dom';
import AddGroup from '../../components/AddGroup';
import EditGroupName from '../../components/EditGroupName';
import './index.scss';

const { Content, Sider } = Layout;
const ListItem = List.Item;
const NAMESPACE = 'Contact';
const Search = Input.Search;
export interface ExternalContactProps extends FormComponentProps {
  groupLoading: boolean;
  listLoading: boolean;
  showContactList: any;
  showContactCorrespondList: any;
  contactOperating: any;
  contactList: any;
  contactCorrespondList: any;
  releaseRelationship: any;
  showCompanyContact: Function
}


const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    groupLoading: state.loading.effects[`${NAMESPACE}/queryContactList`],
    listLoading: state.loading.effects[`${NAMESPACE}/queryContactCorrespondList`],
  }
};
const mapDispatchToProps = (dispatch: any) => {
  return {
    /**外部联系人分组列表 */
    showContactList: (value: string) => {
      dispatch({
        type: `${NAMESPACE}/queryContactList`,
        payload: value,
      });
    },
    /** 分组对应外部联系人信息列表  */
    showContactCorrespondList: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryContactCorrespondList`,
        payload: { params }
      });
    },

    /** 新建分类 */
    showPartnerGrouping: (value: string) => {
      dispatch({
        type: `${NAMESPACE}/queryPartnerClassify`,
        payload: value,
      });
    },
    /**分组名的增,删,改 */
    contactOperating: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/operatingContactGroup`,
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
    showCompanyContact: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryCompanyContactByname`,
        payload: { params },
      });
    }
  }
}
@connect(mapStateToProps, mapDispatchToProps)
class Partner extends React.Component<ExternalContactProps, any>{
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
    this.props.showContactList();
    this.props.showContactCorrespondList({
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
      currentColumnId: 'all',
    })
    this.props.showContactCorrespondList({
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
    this.props.showContactCorrespondList({
      id: item.id,
      page_size: this.state.pageSize,
      now_page: this.state.nowPage
    })
  }
  /**删除分组 */
  removeGroupingInfo = (type_id: any) => {
    this.props.contactOperating({
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
    this.props.showContactCorrespondList({
      id: this.state.currentColumnId,
      page_size: pageSize,
      now_page: pageNumber
    })
  }
  /**
  * 分页
  */
  onShowSizeChange = (pageNumber: number, pageSize: number) => {
    this.props.showContactCorrespondList({
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
        let columnName = this.props.contactList.some(({ name }: any) => name === values.names);
        if (columnName) {
          message.success('分组名称不能重复');
        } else {
          this.setState({
            editGroupVisible: false,
          });
          this.props.contactOperating({
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
  addOk = () => {
    this.props.form.validateFieldsAndScroll((err: any, values: any) => {
      if (!err) {
        let columnName = this.props.contactList.some(({ name }: any) => name === values.name);
        if (columnName) {
          message.success('分组名称不能重复');
        } else {
          this.setState({
            addGroupVisible: false,
          });
          this.props.contactOperating({
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
  releaseRelationship = (e: any, user_id: number) => {
    e.preventDefault();
    e.stopPropagation();
    this.props.releaseRelationship({ user_id }, () => {
      message.info('解除成功');
      this.props.showContactCorrespondList({
        id: this.state.currentColumnId,
        page_size: this.state.pageSize,
        now_page: this.state.nowPage
      });
    })
  }
  /**搜索 */
  onPressEnter = (condition: string) => {
    this.props.showCompanyContact({
      condition,
      page_size: this.state.pageSize,
      now_page: this.state.nowPage
    })
  }
  render() {
    const { listLoading, groupLoading, contactList, contactCorrespondList } = this.props;
    const { currentActive, currentKey, editGroupVisible, addGroupVisible, currentGroupName, pageSize, nowPage } = this.state;

    const paginationProps = {
      showSizeChanger: true,
      showQuickJumper: true,
      pageSize,
      pageSizeOptions: ['10', '15', '20'],
      total: contactCorrespondList && contactCorrespondList.count,
      onChange: this.paginationChange,
      defaultCurrent: nowPage,
      onShowSizeChange: this.onShowSizeChange,
      showTotal: (total: any) => `共 ${total} 条数据`
    }

    return (
      <Content className="contact-wrapper" >
        <Sider className="siderDepartment" width={240} >
          <div className="addGrouping" >
            <Button type="primary" icon="plus" block onClick={this.addGrouping}>新建分组</Button>
          </div>
          <div style={{ borderTop: '1px solid #eee' }} className={classNames("contact-left-list", { active: currentActive })}>
            <div className="allList" onClick={this.showPartnerAllInfo}>
              <span>全部</span>
            </div>
          </div>
          <Spin spinning={groupLoading}>
            <List
              dataSource={contactList && contactList}
              size="small"
              renderItem={
                (item: any, k: number) => (
                  <div onClick={() => this.onCurrentInfo(item, k)} className="cursor-pointer">
                    <ListItem key={item.name} className={classNames("contact-left-list", { active: currentKey === k })} >
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
          <div className="content-wrapper">
            <div style={{ float: 'left' }}>
              <Link to='/addContact'>
                <Button type='primary' className="btn">添加外部联系人</Button>
              </Link>
              {/* <Link to="/contactList">
                <Button type='default' className="btn">外部联系人申请列表</Button>
              </Link> */}
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
                  联系人
                </Col>
                <Col span={3}>
                  手机
                </Col>
                <Col span={3}>
                  邮箱
                </Col>
                <Col span={9}>
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
              dataSource={contactCorrespondList && contactCorrespondList.data}
              renderItem={(item: any) => (
                <Row className="contact-list">
                  <List.Item className="clearfix">
                    <Col span={3}>
                      <span >{item.name}</span>
                    </Col>
                    <Col span={3}>
                      <span >{item.tel}</span>
                    </Col>
                    <Col span={3}>
                      <span >{item.email}</span>
                    </Col>
                    <Col span={9}>
                      <span >{item.address}</span>
                    </Col>
                    <Col span={4}>
                      {/* <span onClick={() => { this.releaseRelationship(item.id) }} style={{ color: '#E61717' }}> 解除合作伙伴 </span> */}

                      <Popconfirm title="确定要删除外部联系人？" onConfirm={e => this.releaseRelationship(e, item.id)}>
                        <span style={{ color: '#E61717' }}> 删除外部联系人 </span>
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



