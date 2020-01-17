import * as React from 'react';
import { History } from 'history';
import { Layout, List, Row, Col, Button, Icon, Form, Input, Modal, Popconfirm, Spin, message, Menu } from 'antd'; // Table,
import { connect } from 'dva';
import { Link } from 'react-router-dom';
import NoticeItem from './NoticeItem';
import { FormComponentProps } from 'antd/lib/form';
import './navigation.scss';
import './notice.scss';

const FormItem = Form.Item;
const { Search } = Input;
const { Content, Sider } = Layout;
const NAMESPACE = 'Notice';
const USERINFO = 'UserInfo';

interface CreateProps extends FormComponentProps {
  noticeList: any,
  initList: any,
  saveColumnClassify: any,
  editColumnClassify: any,
  removeNoticeColumn: any,
  showNoticeInfo: any,
  publishedInfo: any,
  onSearch: (value: string) => void,
  showSearchInfo: (...args: any) => void,
  showAllNotice: any,
  showAllInfo: any,
  columnCurrentInfo: any,
  draftInfo: any,
  showMyFollowsList: any,
  listLoading: boolean,
  allNoticeLoading: boolean,
  history: History;
  queryPartnerNotices: (params: any) => void;
  permission: any;
}

const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    permission: state[USERINFO].permission,
    listLoading: state.loading.effects[`${NAMESPACE}/queryInitList`],
    allNoticeLoading: state.loading.effects[`${NAMESPACE}/queryNoticeInfo`]
  }
};
const mapDispatchToProps = (dispatch: any) => {
  return {
    initList: (value: string) => {
      dispatch({
        type: `${NAMESPACE}/queryInitList`,
        payload: value,
      });
    },
    /**
     * 新建栏目分类
     */
    saveColumnClassify: (value: string) => {
      dispatch({
        type: `${NAMESPACE}/queryColumnClassify`,
        payload: value,
      });
    },
    /**
     * 重命名栏目
     */
    editColumnClassify: (value: string) => {
      dispatch({
        type: `${NAMESPACE}/queryColumnInfo`,
        payload: value,
      });
    },
    /**
     * 删除栏目信息
     */
    removeNoticeColumn: (columnId: number, reload: any, showInfo: any) => {
      dispatch({
        type: `${NAMESPACE}/removeNoticeColumn`,
        payload: {
          columnId,
          reload,
          showInfo
        }
      });
    },
    /**
     * 展示全部公告的信息
     */
    showNoticeInfo: (value: any) => {
      dispatch({
        type: `${NAMESPACE}/queryNoticeInfo`,
        payload: value
      });
    },
    /**
     * 搜索公告展示信息
     */
    showSearchInfo: (value: any) => {
      dispatch({
        type: `${NAMESPACE}/querySearchInfo`,
        payload: value
      })
    },
    /**
     * 左侧栏目展示对应内容
     */
    columnCurrentInfo: (value: any) => {
      dispatch({
        type: `${NAMESPACE}/querycolumnDetailInfo`,
        payload: value
      })
    },
    /**
     * 草稿箱信息
     */
    draftInfo: (value: any) => {
      dispatch({
        type: `${NAMESPACE}/queryDraftInfo`,
        payload: value
      });
    },
    /** 我的关注 */
    showMyFollowsList: (value: any) => {
      dispatch({
        type: `${NAMESPACE}/queryMyFollowsList`,
        payload: value
      });
    },
    queryPartnerNotices: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryPartnerNotices`,
        payload: { params }
      });
    },
  }
}
@connect(mapStateToProps, mapDispatchToProps)
class NoticeList extends React.Component<CreateProps, any>
{
  state = {
    show: false,
    okText: '保存',
    maskClosable: false,
    type: '全部公告',
    currentKey: -1,
    value: '',
    modalTitle: '',
    currentColumnName: '',
    currentColumnId: '',
    page_size: 10,  // 每页的条数
    now_page: 1, // 当前页数
    operatingType: 0,
    currentActiveColumn: 'all',// 左侧激活栏目
    column_id: ''
  }
  componentDidMount() {
    this.showColumnDetailInfo();
    this.showAllNotice();
  }

  /**
   * 展示栏目信息
   */
  showColumnDetailInfo = () => {
    this.props.initList("KGR0w611545");
  }
  /**
   * 全部公告
   */
  showAllNotice = () => {
    this.props.showNoticeInfo({
      now_page: this.state.now_page,
      page_size: this.state.page_size,
    });
  }
  /**
   * 显示新建模态框
   */
  showModal = () => {
    // e.preventDefault();
    this.setState({
      show: true,
      maskClosable: false,
      currentColumnName: '',
      modalTitle: '新建'
    })
  }

  /**
   * 显示编辑模态框
   */
  showEditModal = (item: any) => {
    this.setState({
      show: true,
      maskClosable: false,
      currentColumnName: item.name,
      modalTitle: '编辑',
      currentColumnId: item.id
    })
  }

  /**
   * 新建分类确认事件
   */
  handleOk = (e: React.MouseEvent<any>) => {
    e.preventDefault();
    const { form } = this.props;
    form.validateFieldsAndScroll((err: any, values: any) => {
      if (!err) {
        let columnName = this.props.noticeList.some(({ name }: any) => name === values.name);
        if (columnName) {
          message.success('栏目名称不能重复');
        } else {
          this.setState({
            show: false,
          });
          this.props.saveColumnClassify({ name: values.name });
        }
      }
    });
  }

  /**
   *  编辑栏目确定事件
   */
  editHandleOk = (e: React.MouseEvent<any>) => {
    e.preventDefault();
    const { form } = this.props;
    form.validateFieldsAndScroll((err: any, values: any) => {
      if (!err) {
        let columnName = this.props.noticeList.some(({ name }: any) => name === values.name);
        if (columnName) {
          message.success('栏目名称不能重复');
        } else {
          this.setState({
            show: false,
          });
          this.props.editColumnClassify({
            name: values.name,
            column_id: this.state.currentColumnId
          });
          this.showColumnDetailInfo();
          message.success('保存成功');
        }
      }
    });
  }

  /**
   * 模态框点击取消
   */
  handleCancel = () => {
    this.setState({
      show: false
    });
  }

  /**
   * 栏目对应公告
   */
  onCurrentInfo(item: any, k: any) {
    this.setState({
      type: item.type,
      currentKey: k,
      operatingType: 0,
      currentActiveColumn: 'column',
      column_id: item.id
    })
    this.props.columnCurrentInfo({
      column_id: item.id,
      now_page: this.state.now_page,
      page_size: this.state.page_size,
    });
  }
  /**
   * 删除栏目信息
   */
  removeColumnInfo(columnId: any) {
    this.props.removeNoticeColumn(columnId, this.showColumnDetailInfo, this.showInfo);
    this.setState({
      currentKey: -1,
      operatingType: 0,
      currentActiveColumn: 'column'
    })
  }
  showInfo = () => {
    message.info('该栏目下有相关公告不能删除~');
  }
  handleChange = (e: any) => {
    this.setState({ currentColumnName: e.target.value })
  }
  /**
   * 分页
   */
  paginationChange = (pageNumber: any, pageSize: any) => {
    this.props.showNoticeInfo({
      now_page: pageNumber,
      page_size: pageSize,
    });
  }
  /**
   * 搜索
   */
  onSearch = (title: any) => {
    this.props.showSearchInfo({
      title
    });
  }
  /**
   * 分页
   */
  onShowSizeChange = (pageNumber: number, pageSize: number) => {
    this.props.showNoticeInfo({
      now_page: pageNumber,
      page_size: pageSize,
    });
    this.setState({
      now_page: pageNumber,
      page_size: pageSize
    })
  }

  /**
   * 展示全部公告
   */
  showAllInfo = () => {
    this.props.showNoticeInfo({
      now_page: this.state.now_page,
      page_size: this.state.page_size,
    });
    this.setState({
      currentKey: -1,
      operatingType: 0,
      currentActiveColumn: 'all'
    })
  }

  /**
   * 草稿箱
   */
  draftNotice = () => {
    this.setState({
      currentKey: -1,
      operatingType: 1
    })
    this.draftListInfo();
  }
  /**
   * 草稿箱下列表信息
   */
  draftListInfo = () => {
    this.props.draftInfo({
      now_page: this.state.now_page,
      page_size: this.state.page_size,
    });
  }
  /**
   * 我的关注
   */
  myFollows = () => {
    this.setState({
      currentKey: -1,
      operatingType: 0,
      currentActiveColumn: 'follow'
    })
    this.myFollowsList();
  }
  /**
   * 我的关注列表信息
   */
  myFollowsList = () => {
    this.props.showMyFollowsList({
      now_page: this.state.now_page,
      page_size: this.state.page_size,
    });
  }
  showNoticeTop = (state: string, type: string) => {
    message.success(state);
    this.showNotice(type);
  }
  showNoticeFollow = (state: string, type: string) => {
    message.success(state);
    this.showNotice(type);
  }
  showNotice = (type: string) => {
    if (type === 'column') {
      this.props.columnCurrentInfo({
        column_id: this.state.column_id,
        now_page: this.state.now_page,
        page_size: this.state.page_size,
      });
    } else if (type === 'follow') {
      this.myFollowsList();
    } else {
      this.showAllNotice();// 全部公告 all
    }
  }
  /** 合作伙伴公告 */
  externalNotice = () => {
    this.setState({
      currentKey: -1,
      operatingType: 0,
      currentActiveColumn: 'follow'
    })
    this.props.queryPartnerNotices({
      now_page: this.state.now_page,
      page_size: this.state.page_size,
    })
  }
  /** 左侧菜单选中处理 */
  handleMenuClick = ({ item, key }: any) => {
    switch (key) {
      case 'all':
        this.showAllInfo()
        break;
      case 'new':
        this.showModal()
        break;
      case 'myFollows':
        this.myFollows()
        break;
      case 'externalNotice':
        this.externalNotice()
        break;
      case 'draftNotice':
        this.draftNotice()
        break;
      default:
        this.onCurrentInfo(item.props['data-item'], item.props['data-index'])
        break;
    }
  }
  render() {
    // publishedInfo 所有公告信息  noticeList 所有栏目
    const { noticeList, publishedInfo, listLoading, allNoticeLoading, permission } = this.props;
    const paginationProps = {
      showSizeChanger: true,
      showQuickJumper: true,
      pageSize: this.state.page_size,
      pageSizeOptions: ['10', '15', '20'],
      total: publishedInfo && publishedInfo.all_count,
      onChange: this.paginationChange,
      defaultCurrent: this.state.now_page,
      onShowSizeChange: this.onShowSizeChange,
      showTotal: (total: any) => `共 ${total} 条数据`
    }
    const { okText, show, maskClosable, currentColumnName, modalTitle, operatingType } = this.state;
    const { getFieldDecorator } = this.props.form;
    const addSortInfo =
      <div>
        <p style={{ color: '#000', fontSize: '18px' }}>分类名</p>
        <p style={{ color: '#B5B5B5', fontSize: '14px' }}>新建分类后默认排序到最后</p>
      </div>;
    // 新建分类后默认排序到最后,您可以在分类列表中拖拽调整排序
    const updateInfo =
      <div>
        <p style={{ color: '#000', fontSize: '18px' }}>编辑</p>
        <p style={{ color: '#B5B5B5', fontSize: '14px' }}>新建分类后默认排序到最后</p>
      </div>;
    let modalTitles;
    let modalOk;
    if (modalTitle === "新建") {
      modalTitles = addSortInfo;
      modalOk = this.handleOk;
    } else {
      modalTitles = updateInfo;
      modalOk = this.editHandleOk;
    }
    /** 公告管理权 */
    const PERMISSION_NOTICE_MANAGEMENT = permission && permission.includes('c_notice_manage_per');
    /** 公告编辑权 */
    const PERMISSION_NOTICE_EDIT = permission && permission.includes('c_notice_editor_per');
    /** 外部公告接收权 */
    const PERMISSION_NOTICE_RECEIVE_EXTERNAL = permission && permission.includes('c_external_notice_receive');

    return (
      <Layout className="work-notice" style={{ height: 'calc(100vh - 97px)' }}>
        <Sider width="220" theme="light" className="noticeSider" style={{ height: '100%', overflowY: 'auto' }}>
          {
            PERMISSION_NOTICE_EDIT ? (
              <div style={{ margin: '18px 0', paddingLeft: '20px' }}>
                <Link to="/work/notice/create">
                  <Button type="primary" className="addNotice" icon="plus">新建公告</Button>
                </Link>
              </div>
            ) : null
          }
          {
            PERMISSION_NOTICE_MANAGEMENT ? (
              <div style={{ borderTop: '1px solid #eee' }}>
                <div onClick={this.showModal} style={{ height: '38px', lineHeight: '38px', cursor: 'pointer', paddingLeft: 24 }}>
                  <a><Icon type="plus" style={{ paddingRight: '5px' }} />新建分类</a>
                </div>
              </div>
            ) : null
          }
          <Spin spinning={listLoading} delay={300}>
            <Menu
              mode="inline"
              // selectedKeys={[currentUrl]}
              style={{ borderRightColor: 'transparent' }}
              openKeys={['all']}
              theme="light"
              onClick={this.handleMenuClick}
            >
              <Menu.Item key="all" style={{ margin: 0 }}>
                <Icon type="ordered-list" />
                <span className="nav-text">全部公告</span>
              </Menu.Item>
              {
                noticeList.map((item: any, k: number) => (
                  <Menu.Item
                    className="navigation-list"
                    key={item.id}
                    style={{ margin: 0 }}
                    data-item={item}
                    data-idnex={k}
                  >
                    <span className="nav-text" style={{ marginLeft: 24 }}>{item.name}</span>
                    {
                      PERMISSION_NOTICE_MANAGEMENT ? (
                        <>
                          <span className="operatColumn" onClick={(e: any) => { e.stopPropagation() }} >
                            <Popconfirm title="是否要删除此分类？" onConfirm={() => this.removeColumnInfo(item.id)}>
                              <Icon type="delete" style={{ color: "#0CABF5", fontSize: '16px' }} />
                            </Popconfirm>
                          </span>
                          <span className="operatColumn"
                            onClick={e => {
                              e.stopPropagation();
                              this.showEditModal(item);
                            }}
                          >
                            <Icon type="form" style={{ color: "#0CABF5", fontSize: '16px' }} />
                          </span>
                        </>
                      ) : null
                    }
                  </Menu.Item>
                ))
              }
              {
                PERMISSION_NOTICE_RECEIVE_EXTERNAL ? (
                  <Menu.Item key="externalNotice" >
                    <Icon type="global" />
                    <span className="nav-text">外部公告</span>
                  </Menu.Item>
                ) : null
              }
              <Menu.Item key="myFollows">
                <Icon type="star" />
                <span className="nav-text">我的关注</span>
              </Menu.Item>
              {
                PERMISSION_NOTICE_EDIT ? (
                  <Menu.Item key="draftNotice">
                    <Icon type="form" />
                    <span className="nav-text">公告草稿箱</span>
                  </Menu.Item>
                ) : null
              }
            </Menu>
          </Spin>
          <Form>
            <Modal
              title={modalTitles}
              visible={show}
              centered
              mask
              maskClosable={maskClosable}
              onOk={modalOk}
              onCancel={this.handleCancel}
              cancelText="取消"
              okText={okText}
              destroyOnClose={true}
            >
              <div>
                <FormItem
                  wrapperCol={{ span: 15, offset: 1 }}
                  label=''
                >   {getFieldDecorator('name', {
                  rules: [
                    { required: true, message: '请输入名称' },
                  ],
                })(
                  <div>
                    <Input placeholder="请输入名称(限10个字符)" maxLength={10} value={currentColumnName} onChange={this.handleChange} className="categoryInput" />
                  </div>
                )}
                </FormItem>
              </div>
            </Modal>
          </Form>
        </Sider>
        <Content className="white">
          <div style={{ paddingBottom: '20px' }}>
            <div className="noticeWrapper">
              <Row type="flex" align="middle">
                <Col span={4}>
                  <div style={{ margin: '20px 0' }}>
                    <Search className="extraContentSearch" placeholder="请输入" onSearch={this.onSearch} />
                  </div>
                </Col>
              </Row>
              <header style={{ height: '40px', lineHeight: '40px', background: '#f5f5f5' }}>
                <Row style={{ padding: '0 30px' }}>
                  <Col span={5}>
                    标题
                 </Col>
                  <Col span={5}>
                    公告类型
                 </Col>
                  <Col span={5}>
                    发布人
                 </Col>
                  <Col span={5}>
                    发布时间
                 </Col>
                  <Col span={4}>
                    操作
                 </Col>
                </Row>
              </header>
              <Spin spinning={allNoticeLoading} delay={300}>
                <List
                  itemLayout="vertical"
                  size="large"
                  rowKey="id"
                  pagination={paginationProps}
                  dataSource={publishedInfo && publishedInfo.data}
                  renderItem={(item: any) => (
                    <NoticeItem
                      className="noticeText"
                      dataSource={item}
                      operating={operatingType}
                      reloadDraft={this.draftNotice}
                      isFollow={(state: string, type: string) => this.showNoticeFollow(state, type)}
                      isTop={(state: string, type: string) => this.showNoticeTop(state, type)}
                      currentActiveColumn={this.state.currentActiveColumn}
                      history={this.props.history}
                      draftList={this.draftListInfo}
                      currentColumnInfo={
                        this.state.currentActiveColumn === 'column' ?
                          {
                            column_id: this.state.column_id,
                            now_page: this.state.now_page,
                            page_size: this.state.page_size,
                          } : {
                            now_page: this.state.now_page,
                            page_size: this.state.page_size
                          }
                      }
                    />
                  )}
                />
              </Spin>
            </div>
          </div>
        </Content >
      </Layout>
    );
  }
}
export default Form.create()(NoticeList);
