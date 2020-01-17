
import * as React from 'react';
import { Row, Layout, Button, Col, Input, List, Divider, Spin, message } from 'antd';
import { connect } from 'dva';
import ListHeader from './ListHeader';
import { Link } from 'react-router-dom';
import SelectPersonnelModal from '../../components/selectPersonnelModal';
import './index.scss';
import request from '../../utils/request';

const { Content } = Layout;
const Search = Input.Search;

const NAMESPACE = 'Company';
const STRUCTURE = 'Structure';
interface PermissionProps {
  showPermissionList: any,
  permissionList: any,
  removePositoin: any,
  listLoading: boolean,
  showCompanyInfo: Function;
  companyInfo: any;
}
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    companyInfo: state[STRUCTURE].companyInfo,
    listLoading: state.loading.effects[`${NAMESPACE}/queryPermissionList`],
  }
}


const mapDispatchToProps = (dispatch: any) => {
  return {
    showPermissionList: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryPermissionList`,
        payload: params
      });
    },
    removePositoin: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/removePermissionById`,
        payload: { params, cb }
      });
    },
    /**公司信息 */
    showCompanyInfo: (cb: any) => {
      dispatch({
        type: `${STRUCTURE}/queryCompanyInfo`,
        payload: { cb }
      });
    },
  }
}
@connect(mapStateToProps, mapDispatchToProps)
export default class Permission extends React.Component<PermissionProps, any> {
  state = {
    pageSize: 10,
    nowPage: 1,
    visible: false,
    role_id: ''
  }
  /**
   * 分页
   * @param pageNumber 
   * @param pageSize 
   */
  paginationChange = (pageNumber: any, pageSizes: any) => {
    this.props.showPermissionList({
      page_size: pageSizes,
      now_page: pageNumber
    });
  }
  onShowSizeChange = (pageNumber: any, pageSizes: any) => {
    this.setState({
      pageSize: pageSizes,
      nowPage: pageNumber
    })
    this.props.showPermissionList({
      page_size: pageSizes,
      now_page: pageNumber
    });
  }
  componentDidMount() {
    this.props.showPermissionList({
      page_size: this.state.pageSize,
      now_page: this.state.nowPage
    });
  }

  /**
   * 详情
   */
  permissionDetails = (id: number) => {
    console.log(id);
  }
  /**
   * 删除职务
   */
  removePosition = (role_id: number) => {
    this.props.removePositoin({ role_id }, () => {
      message.info('删除成功');
    })
  }

  showDeptModal = (role_id: any) => {
    this.setState({
      visible: true,
      role_id
    })
  }
  closeModal = () => {
    this.setState({
      visible: false
    })
  }

  handleOk = (deptInfo: any) => {
    const { role_id } = this.state;
    const user_ids = deptInfo.checkedPersonnels.map(({ key }: any) => key);
    (async () => {
      const result = await request('/api/management_role_add_user', {
        method: 'POST',
        body: {
          role_id,
          user_ids
        }
      })
      if (result.status === 'success') {
        message.success('添加成功');
        this.props.showPermissionList({
          page_size: this.state.pageSize,
          now_page: this.state.nowPage
        });
      } else {
        message.error('服务器错误，请稍后再试')
      }
      this.setState({
        visible: false
      })
    })()
  }

  render() {
    const { permissionList, listLoading } = this.props;
    const paginationProps = {
      showSizeChanger: true,
      showQuickJumper: true,
      pageSize: this.state.pageSize,
      pageSizeOptions: ['5', '10', '15'],
      total: permissionList && permissionList.count,
      onChange: this.paginationChange,
      defaultCurrent: this.state.nowPage,
      onShowSizeChange: this.onShowSizeChange,
      showTotal: (total: any) => `共 ${total} 条数据`
    }

    const { visible } = this.state;

    return (
      <Content className="permission-wrapper wrapper">
        <Row className="permission-top">
          <Col span={4}>
            <Link to={
              {
                pathname: '/newPosition',
                state: { type: 'add' }
              }
            }>
              <Button icon="plus" type="primary">新增职务</Button>
            </Link>
          </Col>
          <Col span={20} className="text-right">
            <Search
              placeholder="input search text"
              onSearch={value => console.log(value)}
              style={{ width: 200 }}
            />
          </Col>
        </Row>
        <Spin spinning={listLoading} >
          <div>
            <ListHeader />
            {
              (() => {
                if (permissionList) {
                  return (
                    <List
                      dataSource={permissionList.data}
                      size="small"
                      pagination={paginationProps}
                      renderItem={
                        (item: any) => (
                          <Row className="list-item" onClick={() => this.permissionDetails(item.id)} type="flex">
                            <Col span={3}>
                              {item.name}
                            </Col>
                            <Col span={3}>
                              {item.user_counts}
                            </Col>
                            <Col span={14}>
                              {
                                item.user_names && item.user_names.join('，')
                              }
                            </Col>
                            <Col span={4}>
                              <Button type="link" onClick={() => this.showDeptModal(item.id)}>添加人员</Button>
                              <Divider type="vertical" />
                              <Link to={
                                {
                                  pathname: '/newPosition',
                                  state: { type: 'edit', roleId: item.id }
                                }
                              }
                              > 编辑</Link>
                              <Divider type="vertical" />
                              <a onClick={() => this.removePosition(item.id)}>删除</a>
                            </Col>
                          </Row>
                        )
                      }
                    />
                  )
                } else return null;
              })()
            }
          </div>
        </Spin>
        <SelectPersonnelModal
          visible={visible}
          onOk={this.handleOk}
          onCancel={this.closeModal}
        />
      </Content>
    )
  }
}