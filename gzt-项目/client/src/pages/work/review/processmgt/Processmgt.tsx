import * as React from 'react';
import { Layout, Button, Modal, Input, Row, Col, Spin, message } from 'antd';
import { Link } from 'react-router-dom';
import GroupTypeItem from '../common/GroupTypeItem';
import GroupNameItem from '../common/GroupNameItem';
import { connect } from 'dva';
import request from '../../../../utils/request';
import Templatelist from '../common/TemplateList';
import decryptId from '../../../../utils/decryptId';
import TextLabel from '../../../../components/textLabel';
import classnames from 'classnames';
import S from './processmgt.module.scss'

const { Header, Content } = Layout;
const NAMESPACE = 'Review';
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    loading: state.loading.effects[`${NAMESPACE}/queryProcesses`]
  }
}

const mapDispatchToProps = (dispatch: any) => {
  return {
    /**  获取流程列表信息 */
    queryProcesses: () => {
      dispatch({
        type: `${NAMESPACE}/queryProcesses`
      });
    },
    /** 获取流程分组信息 */
    queryProcessGroup: (cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/queryProcessGroup`,
        payload: cb
      });
    },
  }
}
export interface ProcessmgtProps {
  /** 获取流程列表信息 */
  queryProcesses: Function;
  /** 流程列表 */
  processes: any;
  /** 流程列表loading */
  loading: boolean;
  /** 获取流程分组信息 */
  queryProcessGroup: Function;
  /** 流程分组信息 */
  processGroup: any;
}
@connect(mapStateToProps, mapDispatchToProps)
export default class Processmgt extends React.Component<ProcessmgtProps>{

  state = {
    addGroupVisible: false,
    processListVisible: false,
    processCreateVisible: false,
    addGroupValue: '',
    renameGroup: null as any,
    renameGroupVisible: false,
    moveToOtherGroupVisible: false,
    afterMovingGroup: null as any,
    beforeMovingProcess: null as any
  }

  componentDidMount() {
    this.props.queryProcesses();
  }
  /** 流程禁用启用状态改变后更新流程列表信息 */
  handleEnableStateChange = () => {
    this.props.queryProcesses();
  }
  /** 流程删除后更新流程列表信息 */
  handleDeleteChange = () => {
    this.props.queryProcesses();
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

    const result = await request('/api/c_pst_add_process_template_type', {
      method: 'POST',
      body: {
        name: this.state.addGroupValue
      }
    });

    if (result.status === 'success') {
      message.success('新建成功');
      this.props.queryProcesses();
    } else {
      message.error('服务器异常，请稍后再试')
    }
  }

  cancelGroup = () => {
    this.setState({
      addGroupVisible: false
    })
  }

  addGroupChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    this.setState({
      addGroupValue: e.target.value
    })
  }

  /**
   * 显示流程列表模态框
   */
  showProcessCreateModal = () => {
    this.setState({
      processCreateVisible: true
    })
  }

  cancelProcessList = () => {
    this.setState({
      processCreateVisible: false
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

    const result = await request('/api/c_pst_alter_process_template_name', {
      method: 'POST',
      body: {
        id: this.state.renameGroup.id,
        name: this.state.renameGroup.name
      }
    });

    if (result.status === 'success') {
      message.success('重命名成功');
      this.props.queryProcesses();
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

  /** 删除分组 */
  handleDeleteGroup = async (id: string) => {
    const result = await request('/api/c_pst_delete_process_template_name', {
      method: "POST",
      body: {
        id
      }
    });
    if (result.status === 'success') {
      message.success('删除成功');
      this.props.queryProcesses();
    } else {
      message.error('服务器异常，请稍后再试')
    }
  }

  /** 点击 `移动到` 触发事件 */
  handleGroupMove = (groupId: string, id: string, name: string) => {
    this.props.queryProcessGroup();
    this.setState({
      moveToOtherGroupVisible: true,
      beforeMovingProcess: {
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
    const result = await request('/api/c_pst_move_process_template_type', {
      method: "POST",
      body: {
        template_id: this.state.beforeMovingProcess.id,
        type_id: this.state.afterMovingGroup.id
      }
    });
    if (result.status === 'success') {
      message.success('移动成功');
      this.props.queryProcesses();
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
    if (decryptId(this.state.beforeMovingProcess.groupId) === decryptId(id)) {
      return
    }
    this.setState({
      afterMovingGroup: {
        id
      }
    })
  }

  render() {

    const { processes, loading, processGroup } = this.props;

    const {
      addGroupVisible,
      addGroupValue,
      processCreateVisible,
      renameGroupVisible,
      renameGroup,
      moveToOtherGroupVisible,
      beforeMovingProcess,
      afterMovingGroup
    } = this.state;

    return (
      <Layout className="review-processmgt">
        <Header className={S.header}>
          <Button
            className={S.groupBtn}
            type="primary"
            ghost
            icon="folder-add"
            onClick={this.showAddGroupModal}
          >添加分组</Button>
          <Link to="/work/review/processGroupSort">
            <Button className={S.groupBtn} type="primary" ghost icon="sort-ascending" >
              分组排序
            </Button>
          </Link>
          {/* <Link to="/work/review/createprocess"> */}
          <Button
            type="primary"
            className={S.create}
            icon="plus"
            onClick={this.showProcessCreateModal}
          >
            新建审批流程
            </Button>
          {/* </Link> */}
        </Header>
        <Content className={S.processList}>
          <Spin spinning={loading}>
            {
              processes && processes.enable.map(({ id, name, count, data }: any) => {
                return (
                  <div key={id}>
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
                          type="process"
                          params={params}
                          pathname="/work/review/createProcess"
                          changeEnableStateUrl="/api/c_pst_switch_process_template_show"
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
              processes && processes.disable.map((params: any, key: any) => (
                <GroupTypeItem
                  key={key}
                  type="process"
                  params={params}
                  changeEnableStateUrl="/api/c_pst_switch_process_template_show"
                  onEnableStateChange={this.handleEnableStateChange}
                  deleteUrl="/api/c_pst_delete_process_template"
                  onDeleteChange={this.handleDeleteChange}
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
          closable={false}
          visible={moveToOtherGroupVisible}
          title={`移动 "${beforeMovingProcess && beforeMovingProcess.name}" 至`}
          onOk={this.moveToOtherGroup}
          onCancel={this.cancelMove}
          bodyStyle={{
            padding: 0,
            maxHeight: 250,
            overflowY: 'auto'
          }}
        >
          {
            processGroup && processGroup.map(({ id, name }: any) => (
              <div
                key={id}
                className={classnames('clearfix', S.processGroupItem, (afterMovingGroup && afterMovingGroup.id === id) ? S.active : '')}
                onClick={() => this.handleMoveGroupChecked(id)}
              >
                <TextLabel text={name} colon={false} />
                <span className="pull-right">{beforeMovingProcess && decryptId(beforeMovingProcess.groupId) === decryptId(id) ? '当前所在组' : ''}</span>
              </div>
            ))
          }
        </Modal>

        <Modal
          visible={processCreateVisible}
          title="新建审批流程"
          onCancel={this.cancelProcessList}
          footer={null}
          width={800}
          wrapClassName="modal-review"
          getContainer={() => document.getElementsByClassName('work-review')[0] as any}
        >
          <Row className="modal-review-sectitle">
            <Col span={18} >
              <Button type="primary" value="existingProcess">从已有流程新建</Button>
            </Col>
            <Col span={6} style={{ textAlign: 'right' }}>
              <Link to="/work/review/createProcess">
                <Button onClick={this.cancelProcessList} type="primary" ghost icon="plus" >
                  自定义流程
                </Button>
              </Link>
            </Col>
          </Row>
          <Templatelist
            className="modal-review-list"
            datasource={processes && processes.enable}
            pathname="/work/review/createProcess"
            onItemClick={this.cancelProcessList}
          />
        </Modal>
      </Layout>
    )
  }
}
