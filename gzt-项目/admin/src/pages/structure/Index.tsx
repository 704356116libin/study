
import * as React from 'react';
import { Button, Layout, Table, Select, Input, Divider, Spin, Form, message } from 'antd';
import { ColumnProps } from 'antd/lib/table';
import AddStaffDrawer from './AddStaffDrawer';
import DepartmentTree from '../../components/departmentTree';
import DepartmentModal from '../../components/departmentModal';
import { connect } from 'dva';
import EditDepModal from './EditDepModal';
import { FormComponentProps } from 'antd/lib/form';
import BatchEditDepartment from './BatchEditDepartment';
import BatchEditPosition from './BatchEditPosition';
import DisableStaff from './DisableStaff';
import { Link } from 'react-router-dom';
import './index.scss';
import generateRandomString from '../../utils/generateRandomString';
import decryptId from '../../utils/decryptId';

const { Content } = Layout;
const Option = Select.Option;
const NAMESPACE = 'Structure';
const Search = Input.Search;
/**
 * 组织结构
 */
interface IUser {
  key: number;
  name: string;
  is_enable: number;
  id: string
}
interface StructureProps extends FormComponentProps {
  showCompanyInfo: any;
  showDepartmentStaffInfo: any;
  companyInfo: any;
  companyDepStaffInfo: any;
  listLoading: boolean;
  staffListLoading: boolean;
  inviteStaffByTel: any;
  editDepartmentName: any;
  searchStaffList: any;
  enableStaff: Function;
  disableStaff: Function;
  managementRolesInfo: any;
  chooseManagementRoles: Function;
  editPosition: Function;
  addDepartment: Function;
  addStaffInfo: Function;
  batchEditDepartment: Function;
  staffDetailInfo: Function;
  staffInfo: any;

}
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    listLoading: state.loading.effects[`${NAMESPACE}/queryCompanyInfo`],
    staffListLoading: state.loading.effects[`${NAMESPACE}/queryCompanyInfo`]
  };
}
const mapDispatchToProps = (dispatch: any) => {
  return {
    /**公司信息 */
    showCompanyInfo: (cb: any) => {
      dispatch({
        type: `${NAMESPACE}/queryCompanyInfo`,
        payload: { cb }
      });
    },
    /**部门下的人员信息 */
    showDepartmentStaffInfo: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryDepartmentInfo`,
        payload: { params }
      });
    },
    /**公司邀请人员 */
    inviteStaffByTel: () => {
      dispatch({
        type: `${NAMESPACE}/inviteStaffByTel`
      });
    },
    editDepartmentName: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/queryDepartmentName`,
        payload: { params, cb }
      });
    },
    /** 查询用户*/
    searchStaffList: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryStaffListByTel`,
        payload: { params }
      });
    },
    /**禁用员工 */
    disableStaff: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/disableStaff`,
        payload: { params, cb }
      });
    },
    /**启用员工 */
    enableStaff: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/enableStaff`,
        payload: { params, cb }
      });
    },
    /**选择职务 */
    chooseManagementRoles: () => {
      dispatch({
        type: `${NAMESPACE}/queryManagementRoles`,
      });
    },
    /**批量修改职务 */
    editPosition: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/editStaffPosition`,
        payload: { params, cb }
      });
    },
    /**批量修改员工部门 */
    batchEditDepartment: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/batchEditDepartment`,
        payload: { params, cb }
      });
    },
    /**新增部门 */
    addDepartment: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/addDepartment`,
        payload: { params, cb }
      });
    },
    /**编辑时获取员工详细信息 */
    staffDetailInfo: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryStaffDetailInfo`,
        payload: { params }
      });
    },

  }
}

@connect(mapStateToProps, mapDispatchToProps)
class Structure extends React.Component<StructureProps, any> {
  state = {
    selectedRowKeys: [],//选择的员工
    loading: false,
    searchValue: '',
    pageSize: 10,// 每页的条数
    nowPage: 1,// 当前页数
    ifEnable: 1,//启用状态
    drawerVisible: false,
    addDepModalVisible: false,
    // inviteStaffModalVisible: false,
    editDepModalVisible: false,
    batchEditDepartmentVisible: false,
    batchPositionVisibleVisible: false,
    disableStaffVisible: false,
    currentDeptId: '',/**当前点击部门树的部门名称的id */
    currentDepName: '',/**当前点击部门树的部门名称 */
    editSelected: true,
    selectValue: 'batch',
    entryWay: 'add' /** 进入方式 编辑/新增员工 */
  };
  /**右侧用户内容信息 */
  showDepContent = (node_id: string | number, page_size: number, now_page: number, is_enable: number) => {
    this.props.showDepartmentStaffInfo({
      node_id,
      page_size,
      now_page,
      is_enable
    })
  }
  componentDidMount() {
    /**公司左侧 树结构 */
    this.props.showCompanyInfo((id: string, name: string) => {
      this.showDepContent(id, this.state.pageSize, this.state.nowPage, this.state.ifEnable);

      this.setState({
        currentDeptId: decryptId(id),
        currentDepName: name
      })
    });
    this.props.chooseManagementRoles();

  }

  /**禁用员工 */
  disableStaff = (current: any) => {
    this.setState({
      disableStaffVisible: false
    })
    // const selectKeys: any = this.state.selectedRowKeys;
    // if (selectKeys.length === 0) {
    //   message.info('请选择要操作的选项~')
    // } else {
    this.props.disableStaff({
      data: [current]
    }, () => {
      message.success('禁用成功');
      this.showDepContent(this.state.currentDeptId, this.state.pageSize, this.state.nowPage, this.state.ifEnable);
    })
    // }
  }

  /**启用员工 */
  enableStaff = (current: any) => {

    // const selectKeys: any = this.state.selectedRowKeys;
    // if (selectKeys.length === 0) {
    //   message.info('请选择要操作的选项~')
    // } else {
    this.props.enableStaff({
      data: [current]
    }, () => {
      message.success('启用成功');
    })
    // }
  }

  onSelectChange = (selectedRowKeys: any) => {
    console.log('selectedRowKeys changed: ', selectedRowKeys);
    this.setState({ selectedRowKeys });
  }

  onChangeSearchValue = (e: any) => {
    const val = e.target.value;
    this.setState({
      searchValue: val
    })
  }
  /**  像后台发送search请求 */
  handlePressEnter = (value: any) => {
    if (!value) {
      this.props.searchStaffList({ tel: this.state.searchValue })
    }
  }

  handleChange = (value: string) => {
    console.log(`selected ${value}`);
    if (value === 'disable') {
      this.setState({
        ifEnable: 0
      }, () => {
        this.showDepContent(this.state.currentDeptId, this.state.pageSize, this.state.nowPage, this.state.ifEnable);
      })
    } else {
      this.setState({
        ifEnable: 1
      }, () => {
        this.showDepContent(this.state.currentDeptId, this.state.pageSize, this.state.nowPage, this.state.ifEnable);
      })
    }
  }
  /** 批量操作   */
  batchAction = (value: string) => {
    this.setState({
      selectValue: "batch"
    })
    if (value === 'editDepartment') {
      this.setState({
        batchEditDepartmentVisible: true
      })
    } else if (value === 'editPosition') {
      this.setState({
        batchPositionVisibleVisible: true
      })
    } else if (value === 'disableStaff') {
      this.setState({
        disableStaffVisible: true
      })
    }
  }
  /**批量修改职务 */
  onEditPosition = (form: any) => {
    form.validateFieldsAndScroll((err: any, values: any) => {
      if (!err) {
        this.setState({
          batchPositionVisibleVisible: false
        });
        this.props.editPosition({
          user_ids: this.state.selectedRowKeys,
          role_id: values.role_id
        }, () => {
          message.success('修改成功');
        })
      }
    });
  }
  /**批量修改部门员工 */
  handleBatchEditDep = (form: any) => {
    form.validateFieldsAndScroll((err: any, values: any) => {
      if (!err) {
        this.setState({
          batchEditDepartmentVisible: false
        });
        this.props.batchEditDepartment({
          user_ids: this.state.selectedRowKeys,
          department_id: values.department_id
        }, () => {
          message.success('修改成功');
        })
      }
    });
  }
  /**
   * 分页
   */
  paginationChange = (pageNumber: any, pageSizes: any) => {
    this.showDepContent(this.state.currentDeptId, pageSizes, pageNumber, this.state.ifEnable);
  }
  /**改变 pageSize */
  onShowSizeChange = (pageNumber: any, pageSizes: any) => {
    this.setState({
      pageSize: pageSizes,
      nowPage: pageNumber
    })
    this.showDepContent(this.state.currentDeptId, pageSizes, pageNumber, this.state.ifEnable);
  }
  /**新增员工 */
  addStaff = () => {
    this.setState({
      drawerVisible: true,
      entryWay: 'add'
    })
  }
  /**操作部门树发生的行为 */
  departmentTreeInfo = (params: any) => {
    this.setState({
      currentDeptId: params.selectedKeys[0],
      currentDepName: params.departmentName,
      selectedRowKeys: []
    })
    /**改变'编辑按钮'状态 */
    this.setState({
      editSelected: params.selectedKeys[0] === this.props.companyInfo.data.id
    })
    /**展示该部门下的员工信息*/
    this.showDepContent(generateRandomString(6) + params.selectedKeys[0], this.state.pageSize, this.state.nowPage, this.state.ifEnable);
  }
  /**新增部门 */
  addDepartment = () => {
    this.setState({
      addDepModalVisible: true
    })
  }
  /**提交新增部门 */
  handleAddDep = (form: any) => {
    form.validateFieldsAndScroll((err: any, values: any) => {
      console.log(values, "values");
      values.node_id = generateRandomString(6) + values.node_id;
      if (!err) {
        this.setState({
          addDepModalVisible: false
        })
        this.props.addDepartment(values, () => {
          message.info("新增成功");
          this.props.showCompanyInfo();
        })
      }
    })
  }
  /**编辑部门 */
  editDepartment = () => {
    this.setState({
      editDepModalVisible: true
    })
  }

  /**提交编辑部门 */
  editSubmit = (form: any) => {
    form.validateFieldsAndScroll((err: any, values: any) => {
      if (!err) {
        this.setState({
          editDepModalVisible: false
        });
        this.props.editDepartmentName({
          node_id: this.state.currentDeptId,
          name: values.names
        }, () => {
          message.success('修改成功');
        })
      }
    });
  }
  // 编辑员工信息
  editStaffInfo = (user_id: number | string) => {
    this.setState({
      drawerVisible: true,
      entryWay: 'edit'
    })
    this.props.staffDetailInfo({
      user_id
    })
  }

  render() {
    const columns: ColumnProps<IUser>[] = [
      {
        title: '姓名',
        key: 'name',
        dataIndex: 'name',
      },
      {
        title: '性别',
        key: 'sex',
        dataIndex: 'sex',
      },
      {
        title: '邮箱',
        key: 'email',
        dataIndex: 'email',
      },
      {
        title: '房间号',
        key: 'roomNumber',
        dataIndex: 'roomNumber',
      },
      {
        title: '手机',
        key: 'tel',
        dataIndex: 'tel',
      },
      {
        title: '操作',
        key: 'operate',
        dataIndex: 'operate',
        width: 360,
        render: (text, record) => (
          < span >
            <a style={{ cursor: 'pointer' }} onClick={() => this.editStaffInfo(record.id)}>编辑</a>
            <Divider type="vertical" />
            {/* 1 表示 启用用状态，显示启用 */}
            {record.is_enable === 1 ?
              (<a style={{ cursor: 'pointer' }} onClick={() => this.disableStaff(record.id)}>禁用</a>)
              :
              (<a style={{ cursor: 'pointer' }} onClick={() => this.enableStaff(record.id)}>启用</a>)
            }
          </span >
        )
      },
    ];

    const { batchAction, onChangeSearchValue } = this;
    const { selectedRowKeys,
      searchValue,
      drawerVisible,
      addDepModalVisible,
      editDepModalVisible,
      batchEditDepartmentVisible,
      editSelected,
      selectValue,
      batchPositionVisibleVisible,
      disableStaffVisible,
      entryWay
    } = this.state;
    const { companyInfo, companyDepStaffInfo, listLoading, staffListLoading, managementRolesInfo, staffInfo } = this.props;

    const paginationProps = {
      showSizeChanger: true,
      showQuickJumper: true,
      pageSize: this.state.pageSize,
      pageSizeOptions: ['5', '10', '15'],
      // total: dataSource && dataSource.all_count,
      onChange: this.paginationChange,
      defaultCurrent: this.state.nowPage,
      onShowSizeChange: this.onShowSizeChange,
      showTotal: (total: any) => `共 ${total} 条数据`
    }
    const rowSelection = {
      selectedRowKeys,
      onChange: this.onSelectChange,
    };
    const hasSelected = selectedRowKeys.length > 0;

    return (
      <Content className="structure-wrapper wrapper">
        <div className="siderDepartment" >
          <div className="add-department" >
            <Button type="primary" icon="plus" block onClick={this.addDepartment}>新增部门</Button>
          </div>
          <Spin spinning={listLoading}>
            {
              companyInfo &&
              <DepartmentTree
                dataSource={companyInfo}
                onChange={this.departmentTreeInfo}
                depVisible={true}
                defaultSelectedKeys={companyInfo && [companyInfo.data.id]}
              />
            }
          </Spin>
          {/*  父组件向子组件传一个函数设置状态，同时子组件给父组件返回一个状态 */}
        </div>
        <div style={{ marginLeft: '260px', width: '100%' }}>
          <Spin spinning={staffListLoading}>
            <div className="top">
              <div style={{ float: 'left' }}>
                <Button style={{ marginLeft: '20px' }} icon="edit" disabled={editSelected} onClick={this.editDepartment} >编辑部门</Button>
                <Select
                  style={{ width: 170, marginLeft: '20px' }}
                  value={selectValue}
                  disabled={!hasSelected}
                  onChange={batchAction}>
                  <Option value="batch">批量操作</Option>
                  <Option value="editDepartment">批量修改部门</Option>
                  <Option value="editPosition">批量修改职务</Option>
                  <Option value="disableStaff">批量禁用员工</Option>
                </Select>
                <Button style={{ marginLeft: '20px' }} icon="plus" onClick={this.addStaff}>新增员工</Button>

                <Link to="/inviteStaff">
                  <Button style={{ marginLeft: '20px' }} icon="plus">邀请员工</Button>
                </Link>

              </div>
              <div style={{ float: 'right', marginRight: '10px' }}>
                <Select defaultValue="enable" className="structure-select" onChange={this.handleChange}>
                  <Option value="enable">启用账户</Option>
                  <Option value="disable">禁用账户</Option>
                </Select>
                <Search placeholder="请输入手机号进行查询"
                  onSearch={value => this.handlePressEnter(value)}
                  onChange={onChangeSearchValue}
                  value={searchValue}
                  style={{ width: '240px', marginRight: '10px' }}
                  allowClear={true}
                />
              </div>
            </div>
            <Table<IUser>
              rowSelection={rowSelection}
              columns={columns}
              dataSource={companyDepStaffInfo && companyDepStaffInfo.data.users}
              pagination={paginationProps}
              rowKey={record => record.id}
            />
          </Spin>
        </div>

        <AddStaffDrawer
          visible={drawerVisible}
          onClose={() => this.setState({ drawerVisible: false })}
          dataSource={companyInfo && companyInfo}
          staffDetail={staffInfo}
          entryWay={entryWay}
          currentDataInfo={{
            name: this.state.currentDepName,
            node_id: this.state.currentDeptId
          }}
          refreshStaffInfo={() => this.showDepContent(generateRandomString(6) + this.state.currentDeptId, this.state.pageSize, this.state.nowPage, this.state.ifEnable)}
          positionInfo={managementRolesInfo}
        />
        {/* 新增部门 */}
        <DepartmentModal
          visible={addDepModalVisible}
          onCancel={() => this.setState({ addDepModalVisible: false })}
          dataSource={companyInfo && companyInfo}
          onSubmit={this.handleAddDep}
          width={580}
          currentDataInfo={{
            name: this.state.currentDepName,
            node_id: this.state.currentDeptId
          }}

        />
        {/* 修改部门 */}
        <EditDepModal
          visible={editDepModalVisible}
          onCancel={() => this.setState({ editDepModalVisible: false })}
          currentDataInfo={{
            name: this.state.currentDepName,
            id: this.state.currentDeptId
          }}
          onSubmit={this.editSubmit}
        />
        {/* 邀请员工 */}
        {/* <InviteStaff
          visible={inviteStaffModalVisible}
          onCancel={() => this.setState({ inviteStaffModalVisible: false })}
          width={600}
          // inviteInfo={companyInviteInfo}
        /> */}
        {/* 批量修改部门 */}
        <BatchEditDepartment
          visible={batchEditDepartmentVisible}
          onHandleCancel={() => this.setState({ batchEditDepartmentVisible: false })}
          choosePersonNumber={selectedRowKeys.length}
          dataSource={companyInfo && companyInfo}
          currentDataInfo={{
            name: this.state.currentDepName,
            id: this.state.currentDeptId
          }}
          onHandleOk={this.handleBatchEditDep}
        />
        {/* 批量修改职务 */}
        <BatchEditPosition
          visible={batchPositionVisibleVisible}
          onHandleCancel={() => this.setState({ batchPositionVisibleVisible: false })}
          choosePersonNumber={selectedRowKeys.length}
          rolesInfo={managementRolesInfo}
          onHandleOk={this.onEditPosition}
        />
        {/* 批量停用员工 */}
        <DisableStaff
          visible={disableStaffVisible}
          onHandleCancel={() => this.setState({ disableStaffVisible: false })}
          choosePersonNumber={selectedRowKeys.length}
          onHandleOk={this.disableStaff}
        />

      </Content >

    )
  }
}
export default Form.create()(Structure);
