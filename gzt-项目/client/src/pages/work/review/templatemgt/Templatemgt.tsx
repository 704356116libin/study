import * as React from 'react';
import { Layout, Button, Modal, Input, Row, Col, Spin, message, Radio } from 'antd';
import { Link } from 'react-router-dom';
import GroupTypeItem from '../common/GroupTypeItem';
import GroupNameItem from '../common/GroupNameItem';
import { connect } from 'dva';
import request from '../../../../utils/request';
import Templatelist from '../common/TemplateList';
import classnames from 'classnames';
import decryptId from '../../../../utils/decryptId'
import TextLabel from '../../../../components/textLabel';
import S from './templatemgt.module.scss'
import { RadioChangeEvent } from 'antd/lib/radio';

const { Header, Content } = Layout;
const RadioGroup = Radio.Group;
const RadioButton = Radio.Button;

const NAMESPACE = 'Review';
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    loading: state.loading.effects[`${NAMESPACE}/queryTemplates`]
  }
}

const mapDispatchToProps = (dispatch: any) => {
  return {
    /** 获取模板列表信息 */
    queryTemplates: () => {
      dispatch({
        type: `${NAMESPACE}/queryTemplates`
      });
    },
    /** 获取推荐模板信息 */
    queryClassicTemplates: () => {
      dispatch({
        type: `${NAMESPACE}/queryClassicTemplates`
      });
    },
    /** 获取分组信息 */
    queryTemplateGroup: (cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/queryTemplateGroup`,
        payload: cb
      });
    },
  }
}

export interface TemplatemgtProps {
  /** 获取模板列表信息 */
  queryTemplates: Function;
  /** 已有模板列表 */
  templates: any;
  /** 模板列表loading */
  loading: boolean;
  /** 获取经典模板信息 */
  queryClassicTemplates: Function;
  /** 经典模板 */
  classicTemplates: any[];
  /** 获取模板分组信息 */
  queryTemplateGroup: Function;
  /** 模板分组信息 */
  templateGroup: any;
}

@connect(mapStateToProps, mapDispatchToProps)
export default class Templatemgt extends React.Component<TemplatemgtProps>{

  state = {
    addGroupVisible: false,
    addGroupValue: '',
    renameGroupVisible: false,
    renameGroup: null as any,
    templateListVisible: false,
    templateCreateVisible: false,
    moveToOtherGroupVisible: false,
    beforeMovingTemp: null as any,
    afterMovingGroup: null as any,
    templateType: 'classic'
  }

  componentDidMount() {
    this.props.queryTemplates();
  }
  /** 模板禁用启用状态改变后更新模板列表信息 */
  handleEnableStateChange = () => {
    this.props.queryTemplates();
  }
  /** 模板删除后更新模板列表信息 */
  handleDeleteChange = () => {
    this.props.queryTemplates();
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

    const result = await request('/api/c_pst_add_pst_template_type', {
      method: 'POST',
      body: {
        name: this.state.addGroupValue
      }
    });

    if (result.status === 'success') {
      message.success('新建成功');
      this.props.queryTemplates();
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

    const result = await request('/api/c_pst_alter_pst_template_name', {
      method: 'POST',
      body: {
        id: this.state.renameGroup.id,
        name: this.state.renameGroup.name
      }
    });

    if (result.status === 'success') {
      message.success('重命名成功');
      this.props.queryTemplates();
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
  showTemplateCreateModal = () => {
    this.props.queryClassicTemplates();
    this.setState({
      templateCreateVisible: true
    })
  }
  /** 取消新建模板 关闭模态框 */
  cancelTemplateList = () => {
    this.setState({
      templateCreateVisible: false
    })
  }
  /** 删除分组 */
  handleDeleteGroup = async (id: string) => {
    const result = await request('/api/c_pst_delete_pst_template_name', {
      method: "POST",
      body: {
        id
      }
    });
    if (result.status === 'success') {
      message.success('删除成功');
      this.props.queryTemplates();
    } else {
      message.error('服务器异常，请稍后再试')
    }
  }

  /** 点击 `移动到` 触发事件 */
  handleGroupMove = (groupId: string, id: string, name: string) => {
    this.props.queryTemplateGroup();
    this.setState({
      moveToOtherGroupVisible: true,
      beforeMovingTemp: {
        groupId,
        id,
        name
      },
      afterMovingGroup: null
    })
  }
  /** 移动至其他分组 */
  moveToOtherGroup = async () => {
    this.setState({
      moveToOtherGroupVisible: false
    });
    const result = await request('/api/c_pst_move_pst_template_type', {
      method: "POST",
      body: {
        template_id: this.state.beforeMovingTemp.id,
        type_id: this.state.afterMovingGroup.id
      }
    });
    if (result.status === 'success') {
      message.success('移动成功');
      this.props.queryTemplates();
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
    if (decryptId(this.state.beforeMovingTemp.groupId) === decryptId(id)) {
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
    if (e.target.value === "classicTemplate") {
      this.setState({
        templateType: 'classic'
      })
    } else {
      this.setState({
        templateType: 'exist'
      })
    }
  }
  render() {

    const { classicTemplates, templates, loading, templateGroup } = this.props;

    const {
      addGroupVisible,
      addGroupValue,
      templateCreateVisible,
      moveToOtherGroupVisible,
      beforeMovingTemp,
      afterMovingGroup,
      renameGroupVisible,
      renameGroup,
      templateType
    } = this.state;

    const templateList = templateType === 'classic' ? classicTemplates : templates && templates.enable;
    return (
      <Layout className="review-templatemgt">
        <Header className={S.header}>
          <Button
            className={S.groupBtn}
            type="primary"
            ghost
            icon="folder-add"
            onClick={this.showAddGroupModal}
          >添加分组</Button>
          <Link to="/work/review/tempGroupSort">
            <Button className={S.groupBtn} type="primary" ghost icon="sort-ascending" >
              分组排序
            </Button>
          </Link>
          {/* <Link to="/work/review/createtemplate"> */}
          <Button
            type="primary"
            className={S.create}
            icon="plus"
            onClick={this.showTemplateCreateModal}
          >
            新建评审模板
            </Button>
          {/* </Link> */}
        </Header>
        <Content className={S.templateList}>
          <Spin spinning={loading}>
            {
              templates && templates.enable.map((item: any, index: any) => {
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
                          pathname="/work/review/createTemplate"
                          type="template"
                          params={params}
                          changeEnableStateUrl="/api/c_pst_switch_pst_template_show"
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
              templates && templates.disable.map((params: any, key: any) => (
                <GroupTypeItem
                  key={key}
                  params={params}
                  type="template"
                  changeEnableStateUrl="/api/c_pst_switch_pst_template_show"
                  deleteUrl="/api/c_pst_delete_pst_template"
                  onEnableStateChange={this.handleEnableStateChange}
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
          title={`移动 "${beforeMovingTemp && beforeMovingTemp.name}" 至`}
          onOk={this.moveToOtherGroup}
          onCancel={this.cancelMove}
          bodyStyle={{
            padding: 0,
            maxHeight: 250,
            overflowY: 'auto'
          }}
        >
          {
            templateGroup && templateGroup.map(({ id, name }: any) => (
              <div
                key={id}
                className={classnames('clearfix', S.tempGroupItem, (afterMovingGroup && decryptId(afterMovingGroup.id) === decryptId(id)) ? S.active : '')}
                onClick={() => this.handleMoveGroupChecked(id)}
              >
                <TextLabel text={name} colon={false} />
                <span className="pull-right">{beforeMovingTemp && decryptId(beforeMovingTemp.groupId) === decryptId(id) ? '当前所在组' : ''}</span>
              </div>
            ))
          }
        </Modal>
        <Modal
          visible={templateCreateVisible}
          title="创建评审模板"
          onCancel={this.cancelTemplateList}
          footer={null}
          width={800}
          getContainer={() => document.getElementsByClassName('work-review')[0] as any}
          wrapClassName="modal-review"
        >
          <Row className="modal-review-sectitle">
            <Col span={18} >
              <RadioGroup
                onChange={this.handleTempTypeChange}
                defaultValue="classicTemplate"
                buttonStyle="solid"
              >
                <RadioButton value="classicTemplate">使用推荐模板</RadioButton>
                <RadioButton value="existingTemplate">使用已有模板</RadioButton>
              </RadioGroup>
            </Col>
            <Col span={6} style={{ textAlign: 'right' }}>
              <Link to="/work/review/createTemplate">
                <Button onClick={this.cancelTemplateList} type="primary" ghost icon="plus" >
                  自定义模板
                </Button>
              </Link>
            </Col>
          </Row>
          <div className="templateWrapper">
            <Templatelist
              className="modal-review-list"
              datasource={templateList}
              pathname="/work/review/createTemplate"
              onItemClick={this.cancelTemplateList}
            />
          </div>
        </Modal>
      </Layout>
    )
  }
}
