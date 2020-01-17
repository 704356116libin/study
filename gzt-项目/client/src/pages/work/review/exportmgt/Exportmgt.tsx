import * as React from 'react';
import { Layout, Button, Modal, Input, Row, Col, Spin, message, Radio } from 'antd';
import { Link } from 'react-router-dom';
import GroupTypeItem from '../common/GroupTypeItem';
import GroupNameItem from '../common/GroupNameItem';
import { connect } from 'dva';
import request from '../../../../utils/request';
import { RadioChangeEvent } from 'antd/lib/radio';
import classnames from 'classnames';
import S from './exportmgt.module.scss'
import decryptId from '../../../../utils/decryptId';
import TextLabel from '../../../../components/textLabel';
import Templatelist from '../common/TemplateList';
import DocNumber from './docNumber';

const { Header, Content } = Layout;
const RadioGroup = Radio.Group;
const RadioButton = Radio.Button;

const NAMESPACE = 'Review';
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    loading: state.loading.effects[`${NAMESPACE}/queryReports`]
  }
}

const mapDispatchToProps = (dispatch: any) => {
  return {
    /** 获取模板列表信息 */
    queryReports: () => {
      dispatch({
        type: `${NAMESPACE}/queryReports`
      });
    },
    /** 获取推荐模板信息 */
    queryClassicReports: () => {
      dispatch({
        type: `${NAMESPACE}/queryClassicReports`
      });
    },
    /** 获取分组信息 */
    queryReportGroup: (cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/queryReportGroup`,
        payload: cb
      });
    },
  }
}

export interface ExportmgtProps {
  /** 获取模板列表信息 */
  queryReports: Function;
  /** 已有模板列表 */
  reports: any;
  /** 模板列表loading */
  loading: boolean;
  /** 获取经典模板信息 */
  queryClassicReports: Function;
  /** 经典模板 */
  classicReports: any[];
  /** 获取模板分组信息 */
  queryReportGroup: Function;
  /** 模板分组信息 */
  reportGroup: any;
}

@connect(mapStateToProps, mapDispatchToProps)
export default class Exportmgt extends React.Component<ExportmgtProps>{

  state = {
    addGroupVisible: false,
    addGroupValue: '',
    renameGroupVisible: false,
    renameGroup: null as any,
    reportCreateVisible: false,
    moveToOtherGroupVisible: false,
    afterMovingGroup: null as any,
    beforeMovingReport: null as any,
    reportType: 'classic',
    docNumberVisible: false
  }

  componentDidMount() {
    this.props.queryReports();
  }
  /** 模板禁用启用状态改变后更新模板列表信息 */
  handleEnableStateChange = () => {
    this.props.queryReports();
  }
  /** 显示添加分组模态框 */
  showAddGroupModal = () => {
    this.setState({
      addGroupVisible: true,
      addGroupValue: ''
    })
  }
  /** 发送添加分组请求 */
  addGroup = async () => {

    this.setState({
      addGroupVisible: false
    })

    const result = await request('/api/c_pst_createExportType', {
      method: 'POST',
      body: {
        name: this.state.addGroupValue
      }
    });

    if (result.status === 'success') {
      message.success('新建成功');
      this.props.queryReports();
    } else {
      message.error('服务器异常，请稍后再试')
    }
  }
  /** 取消添加分组 关闭模态框 */
  cancelGroup = () => {
    this.setState({
      addGroupVisible: false
    })
  }
  /** 新增分组 内容改变回调 */
  addGroupChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    this.setState({
      addGroupValue: e.target.value
    })
  }

  /** 显示重命名分组模态框 */
  handleRenameGroup = (id: string, name: string) => {
    this.setState({
      renameGroupVisible: true,
      renameGroup: {
        id,
        name,
        oldName: name,
        disabled: true
      }
    })
  }
  /** 发送重命名分组请求 */
  renameGroup = async () => {

    this.setState({
      renameGroupVisible: false
    })

    const result = await request('/api/c_pst_editExportType', {
      method: 'POST',
      body: {
        id: this.state.renameGroup.id,
        name: this.state.renameGroup.name
      }
    });

    if (result.status === 'success') {
      message.success('重命名成功');
      this.props.queryReports();
    } else {
      message.error('服务器异常，请稍后再试')
    }
  }
  /** 取消重命名分组 关闭模态框 */
  cancelRenameGroup = () => {
    this.setState({
      renameGroupVisible: false
    })
  }
  /** 重命名分组 内容改变回调 */
  renameGroupChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    this.setState({
      renameGroup: {
        id: this.state.renameGroup.id,
        name: e.target.value,
        oldName: this.state.renameGroup.oldName,
        disabled: e.target.value === this.state.renameGroup.oldName
      }
    })
  }

  /** 显示流程列表模态框 */
  showReportCreateModal = () => {
    this.props.queryClassicReports();
    this.setState({
      reportCreateVisible: true
    })
  }
  /** 取消新建模板 关闭模态框 */
  cancelReportList = () => {
    this.setState({
      reportCreateVisible: false
    })
  }
  /** 删除分组 */
  handleDeleteGroup = async (id: string) => {
    const result = await request('/api/c_pst_deleteExportType', {
      method: "POST",
      body: {
        id
      }
    });
    if (result.status === 'success') {
      message.success('删除成功');
      this.props.queryReports();
    } else {
      message.error('服务器异常，请稍后再试')
    }
  }
  /** 点击 `移动到` 触发事件 */
  handleGroupMove = (groupId: string, id: string, name: string) => {
    this.props.queryReportGroup();
    this.setState({
      moveToOtherGroupVisible: true,
      beforeMovingReport: {
        groupId,
        id,
        name
      }
    })
  }
  /** 移动至其他分组 */
  moveToOtherGroup = async () => {
    this.setState({
      moveToOtherGroupVisible: false
    });
    const result = await request('/api/c_pst_exportTemplateMove', {
      method: "POST",
      body: {
        id: this.state.beforeMovingReport.id,
        typeId: this.state.afterMovingGroup.id
      }
    });
    if (result.status === 'success') {
      message.success('移动成功');
      this.props.queryReports();
    } else {
      message.error('服务器异常，请稍后再试')
    }

  }
  /** 取消移动分组 */
  cancelMove = () => {
    this.setState({
      moveToOtherGroupVisible: false
    })
  }
  /** 选中待移动的分组 */
  handleMoveGroupChecked = (id: string) => {
    // 当前所在组 返回
    if (decryptId(this.state.beforeMovingReport.groupId) === decryptId(id)) {
      return
    }
    this.setState({
      afterMovingGroup: {
        id
      }
    })
  }

  /** 模板类型切换 */
  handleTempTypeChange = (e: RadioChangeEvent) => {
    if (e.target.value === "classicReport") {
      this.setState({
        reportType: 'classic'
      })
    } else {
      this.setState({
        reportType: 'exist'
      })
    }
  }
  /** 显示设置文号模态框 */
  showDocNumberModal = () => {
    this.setState({
      docNumberVisible: true
    })
  }
  /** 关闭设置文号模态框 */
  closeDocNumberModal = () => {
    this.setState({
      docNumberVisible: false
    })
  }
  /** 更新文号规则 */
  handleDocNumberOk(rules: any) {
    (async () => {
      const result = await request('/api/c_pst_make_report_number', {
        method: 'POST',
        body: {
          rule_data: rules
        }
      })
      if (result.status === 'success') {
        message.success('文号规则设置成功')
      }
    })()
  }

  render() {

    const { reports, loading, reportGroup } = this.props;

    const {
      addGroupVisible,
      addGroupValue,
      reportCreateVisible,
      renameGroupVisible,
      renameGroup,
      moveToOtherGroupVisible,
      beforeMovingReport,
      afterMovingGroup,
      reportType,
      docNumberVisible
    } = this.state;

    const reportList = reportType === 'classic' ? null : reports && reports.enable;



    return (
      <Layout className="review-reportmgt">
        <Header className={S.header}>
          <Button
            className={S.groupBtn}
            type="primary"
            ghost
            icon="folder-add"
            onClick={this.showAddGroupModal}
          >添加分组</Button>
          <Link to="/work/review/reportGroupSort">
            <Button className={S.groupBtn} type="primary" ghost icon="sort-ascending" >
              分组排序
            </Button>
          </Link>
          <Button
            className={S.groupBtn}
            type="primary"
            ghost
            icon="folder-add"
            onClick={this.showDocNumberModal}
          >文号设置</Button>

          <Button
            type="primary"
            className={S.create}
            icon="plus"
            onClick={this.showReportCreateModal}
          >
            新建报告模板
            </Button>

        </Header>
        <Content className={S.reportList}>
          <Spin spinning={loading}>
            {
              reports && reports.enable.map((item: any, index: any) => {
                const { id, name, count, data } = item;
                return (
                  <div key={index}>
                    <GroupNameItem
                      type={name}
                      count={count}
                      onRenameGroup={() => this.handleRenameGroup(id, name)}
                      onDeleteGroup={() => this.handleDeleteGroup(id)}
                    />
                    {
                      data && data.map((params: any, key: any) => (
                        <GroupTypeItem
                          key={key}
                          type="export"
                          pathname="/work/review/createReport"
                          params={params}
                          changeEnableStateUrl="/api/c_pst_exportTemplateEnable"
                          onEnableStateChange={this.handleEnableStateChange}
                          onGroupMove={() => this.handleGroupMove(id, params.id, params.name)}
                        />
                      ))
                    }
                  </div>
                )
              })
            }
            <Row type="flex" style={{ marginTop: '10px', padding: '0 8px', lineHeight: '40px', background: '#f9f9f9' }}>
              <Col span={4}>
                <span style={{ color: '#222' }}>已禁用</span>
                {/* <span>（{count}）</span> */}
              </Col>
            </Row>
            {
              reports && reports.disable.map((params: any, key: any) => (
                <GroupTypeItem
                  key={key}
                  type="export"
                  params={params}
                  changeEnableStateUrl="/api/c_pst_exportTemplateEnable"
                  onEnableStateChange={this.handleEnableStateChange}
                />
              ))
            }
          </Spin>
        </Content>
        <Modal
          visible={addGroupVisible}
          title="添加分组"
          onOk={this.addGroup}
          onCancel={this.cancelGroup}
        >
          <Input onChange={this.addGroupChange} value={addGroupValue} maxLength={12} placeholder="最多10个字" />
        </Modal>
        <Modal
          visible={renameGroupVisible}
          title="重命名分组"
          onOk={this.renameGroup}
          onCancel={this.cancelRenameGroup}
          okButtonProps={{
            disabled: renameGroup ? renameGroup.disabled : true
          }}
        >
          <Input onChange={this.renameGroupChange} value={renameGroup && renameGroup.name} maxLength={12} placeholder="最多10个字" />
        </Modal>
        <Modal
          visible={docNumberVisible}
          title="文号设置"
          onCancel={this.closeDocNumberModal}
          width={850}
          footer={null}
        >
          <DocNumber
            onOk={this.handleDocNumberOk}
          />

        </Modal>
        <Modal
          closable={false}
          visible={moveToOtherGroupVisible}
          title={`移动 "${beforeMovingReport && beforeMovingReport.name}" 至`}
          onOk={this.moveToOtherGroup}
          onCancel={this.cancelMove}
          bodyStyle={{
            padding: 0,
            maxHeight: 250,
            overflowY: 'auto'
          }}
        >
          {
            reportGroup && reportGroup.map(({ id, name }: any) => (
              <div
                key={id}
                className={classnames('clearfix', S.reportGroupItem, (afterMovingGroup && afterMovingGroup.id === id) ? S.active : '')}
                onClick={() => this.handleMoveGroupChecked(id)}
              >
                <TextLabel text={name} colon={false} />
                <span className="pull-right">{beforeMovingReport && decryptId(beforeMovingReport.groupId) === decryptId(id) ? '当前所在组' : ''}</span>
              </div>
            ))
          }
        </Modal>
        <Modal
          visible={reportCreateVisible}
          title="创建评审模板"
          onCancel={this.cancelReportList}
          footer={null}
          width={800}
          getContainer={() => document.getElementsByClassName('work-review')[0] as any}
          wrapClassName="modal-review"
        >
          <Row className="modal-review-sectitle">
            <Col span={18} >
              <RadioGroup
                onChange={this.handleTempTypeChange}
                defaultValue="classicReport"
                buttonStyle="solid"
              >
                <RadioButton value="classicReport">使用推荐模板</RadioButton>
                <RadioButton value="existingReport">使用已有模板</RadioButton>
              </RadioGroup>
            </Col>
            <Col span={6} style={{ textAlign: 'right' }}>
              <Link to="/work/review/createReport">
                <Button onClick={this.cancelReportList} type="primary" ghost icon="plus" >
                  自定义模板
                </Button>
              </Link>
            </Col>
          </Row>
          <Templatelist
            className="modal-review-list"
            datasource={reportList}
            pathname="/work/review/createReport"
          />
        </Modal>
      </Layout >
    )
  }
}
