import * as React from 'react';
import { Layout, Menu, Icon, Button, List, Row, Col, Select, Input, Spin } from 'antd';
import { Link } from 'react-router-dom';
import Assistdrawer from '../../../components/workmessage/assist/drawer';
import { connect } from 'dva';
import './assist.scss';

const { Sider, Content } = Layout;
const { Option } = Select;
const { Search } = Input;

interface MyAssistProps {
  assistList: any;
  assistDetails: any;
  currentStatus: string;
  listLoading: boolean;
  detailLoading: boolean;
  showAssistList: (params: any) => void;
  showAssistDetails: (id: number) => void;
  showAssistSearch: (params: any) => void;
}
const NAMESPACE = 'Assist'; // dva model 命名空间

const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    listLoading: state.loading.effects[`${NAMESPACE}/queryAssistList`],
    detailLoading: state.loading.effects[`${NAMESPACE}/queryAssistDetails`]
  }
};

const mapDispatchToProps = (dispatch: any) => {
  return {
    showAssistList: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryAssistList`,
        payload: params
      });
    },
    showAssistDetails: (id: number) => {
      dispatch({
        type: `${NAMESPACE}/queryAssistDetails`,
        payload: id
      });
    },
    showAssistSearch: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/searchAssistList`,
        payload: params
      });
    }
  }
}

const mapStatusToColor = {
  "待接收": "#1890ff",
  "已拒绝": "#f5222d",
  "进行中": '#34d058',
  "待审核": '#dea584',
  "已撤销": '#f00'
}

@connect(mapStateToProps, mapDispatchToProps)
export default class AssistTemplate extends React.Component<MyAssistProps, any> {

  state = {
    visible: false,
    current: 1,
    pageSize: 10,
    currentType: 'all',
    currentRange: 'all',
  }

  componentDidMount() {
    this.props.showAssistList({
      status: 'all',
      internalOrExternal: 'all',
      type: 'all',
      offset: 0,
      limit: 10
    })
  }

  showAssistDetails = (id: number) => {
    this.props.showAssistDetails(id);
    this.setState({
      visible: true
    })
  }

  handleRangeChange = (value: string) => {
    this.props.showAssistList({
      status: this.props.currentStatus,
      internalOrExternal: value,
      type: this.state.currentType,
      offset: 0,
      limit: 10
    })
    this.setState({
      currentRange: value
    })
  }
  handleTypeChange = (value: string) => {
    this.props.showAssistList({
      status: this.props.currentStatus,
      internalOrExternal: this.state.currentRange,
      type: value,
      offset: 0,
      limit: 10
    })
    this.setState({
      currentType: value
    })
  }
  /** 左侧状态切换(进行中的。。等等) */
  onStatusSelect = ({ key }: any) => {
    this.props.showAssistList({
      status: key,
      internalOrExternal: 'all',
      type: 'all',
      offset: 0,
      limit: 10
    })
    this.setState({
      currentType: 'all',
      currentRange: 'all',
      current: 1,
      pageSize: 10,
      visible: false
    })
  }
  onSearchList = (title: string) => {
    this.props.showAssistSearch({
      status: this.props.currentStatus,
      offset: 0,
      limit: 10,
      title
    })
    this.setState({
      currentType: 'all',
      currentRange: 'all'
    })
  }

  onShowSizeChange = (current: number, pageSiz: number) => {
    this.props.showAssistList({
      status: this.props.currentStatus,
      type: this.state.currentType,
      offset: (current - 1) * pageSiz,
      limit: pageSiz
    })
    this.setState({
      current,
      pageSize: pageSiz
    })
  }

  onPageChange = (current: number, pageSiz: number = 10) => {
    this.props.showAssistList({
      status: this.props.currentStatus,
      type: this.state.currentType,
      offset: (current - 1) * pageSiz,
      limit: pageSiz
    })
    this.setState({
      visible: false,
      current
    })
  }
  onUpdate = (id: number) => {
    this.props.showAssistDetails(id);
  }
  updateList = () => {
    const pageSize = this.state.pageSize;
    this.props.showAssistList({
      status: this.props.currentStatus,
      type: this.state.currentType,
      offset: (this.state.current - 1) * pageSize,
      limit: pageSize
    })
  }
  render() {

    const { onShowSizeChange, onPageChange, showAssistDetails, handleTypeChange, handleRangeChange, onSearchList } = this;
    const { pageSize, visible, currentRange, currentType, current } = this.state;
    const { assistList, assistDetails, listLoading, detailLoading, } = this.props;

    return (
      <Layout id="assist-overview">
        <div style={{ top: '96px', position: 'sticky', background: '#fff', border: '1px solid #f0f2f5' }}>
          <Link to="/work/assist/template" style={{ borderRight: '1px solid rgb(232, 232, 232)' }}>
            <Button type="primary" style={{ margin: '10px 24px', width: '160px' }}>
              <Icon type="edit" />
              <span className="nav-text">发起协助</span>
            </Button>
          </Link>
          <Select style={{ marginLeft: '30px', width: 100 }} value={currentRange} onChange={handleRangeChange}>
            <Option value="all">全部</Option>
            <Option value="internal">内部的</Option>
            <Option value="cross">跨圈的</Option>
          </Select>
          <Select style={{ marginLeft: '30px', width: 100 }} value={currentType} onChange={handleTypeChange}>
            <Option value="all">全部</Option>
            <Option value="initiated">我发起的</Option>
            <Option value="responsible">我负责的</Option>
            <Option value="involved">我参与的</Option>
          </Select>
          <Search
            placeholder="请输入协助标题"
            enterButton="搜索"
            style={{ margin: '10px 0 0 30px', width: 240 }}
            onSearch={onSearchList}
          />
        </div>
        <Layout style={{ top: '150px', position: 'sticky', height: 'calc( 100vh - 151px )' }}>
          <Sider width="210" className="box" style={{ background: '#fff', overflow: 'auto', borderRight: '1px solid #e8e8e8' }}>
            <Menu
              mode="inline"
              defaultSelectedKeys={['all']}
              style={{ border: 'none' }}
              onSelect={this.onStatusSelect}
            >
              <Menu.Item key="all" className="assist-class">
                <Icon type="ordered-list" />
                <span className="nav-text">全部</span>
              </Menu.Item>
              <Menu.Item key="processing" className="assist-class">
                <Icon type="loading" />
                <span className="nav-text">进行中的</span>
              </Menu.Item>
              <Menu.Item key="pending" className="assist-class">
                <Icon type="smile" />
                <span className="nav-text">待接收的</span>
              </Menu.Item>
              <Menu.Item key="pendingReview" className="assist-class">
                <Icon type="meh" />
                <span className="nav-text">待审核的</span>
              </Menu.Item>
              <Menu.Item key="rejected" className="assist-class">
                <Icon type="frown" />
                <span className="nav-text">已拒绝的</span>
              </Menu.Item>
              <Menu.Item key="completed" className="assist-class">
                <Icon type="coffee" />
                <span className="nav-text">已完成的</span>
              </Menu.Item>
              <Menu.Item key="revoked" className="assist-class">
                <Icon type="delete" />
                <span className="nav-text">已撤销的</span>
              </Menu.Item>
            </Menu>
          </Sider>
          <Layout id="assist-con" style={{ background: '#fff' }}>
            <Content style={{ background: '#fff', minHeight: 280 }}>
              <header className="assist-header">
                <Row style={{ padding: '0 30px' }}>
                  <Col span={9}>
                    协助概览
                  </Col>
                  <Col span={5}>
                    发起时间
                  </Col>
                  <Col span={5}>
                    完成时间
                  </Col>
                  <Col span={5}>
                    状态
                  </Col>
                </Row>
              </header>
              <Spin spinning={listLoading} delay={300}>
                <List
                  dataSource={assistList.data}
                  size="small"
                  pagination={{
                    style: { textAlign: 'center' },
                    pageSize,
                    current,
                    pageSizeOptions: ['10', '20', '40'],
                    showQuickJumper: true,
                    showSizeChanger: true,
                    onShowSizeChange,
                    onChange: onPageChange,
                    total: assistList.all_count,
                    showTotal: total => `共 ${total} 条数据`
                  }}
                  renderItem={
                    (item: any, k: number) => (
                      <Row className="assist-list" onClick={() => showAssistDetails(item.id)}>
                        <Col span={9} className="assist-list-title">
                          {item.title}<br />
                        </Col>
                        <Col span={5}>
                          {item.created_at}
                        </Col>
                        <Col span={5}>
                          {item.limit_time ? item.limit_time : '不限时间'}
                        </Col>
                        <Col span={5}>
                          {
                            (() => {
                              const status = item.is_cancel === 1 ? "已撤销" : item.status
                              return <span style={{ color: mapStatusToColor[status] }} >{status}</span>
                            })()
                          }
                        </Col>
                      </Row>
                    )
                  }
                />
              </Spin>
            </Content>
            <Assistdrawer
              visible={visible}
              loading={detailLoading}
              getContainer="#assist-con"
              onUpdate={this.onUpdate}
              updateList={this.updateList}
              details={assistDetails}
              drawerClose={() => this.setState({ visible: false })}
            />
          </Layout>
        </Layout>
      </Layout>
    )
  }
}
