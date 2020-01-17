import React from 'react'
import { Button, Modal, Row, Col, Divider, Radio } from 'antd';
import SelectPersonnelModal from '../selectPersonnelModal';
import PersonnelAvatar from '../personnelAvatar';
import update from 'immutability-helper';
import './index.scss';

interface SelectApproverProps {
  peopleList?: any[];
  onChange?: any;
  approvers?: any[]
}

const confirm = Modal.confirm;
const RadioGroup = Radio.Group;
const signMap = {
  countersign: '会签',
  orSign: '或签'
}
/**
 * 选择审批人员组件
 */
export default class SelectApprover extends React.Component<SelectApproverProps, any> {

  state = {
    visible: false,
    selectPersonnelVisible: false,
    checkedInfo: {
      checkedKeys: [],
      checkedPersonnels: []
    },
    version: 0,
    signType: 'countersign',
    addType: 'UPDATE',
    currentProcess: -1
  }

  /**
   * 新增人员显示审批模态框,同时重置状态
   */
  showModal = () => {
    this.setState({
      visible: true,
      checkedInfo: {
        checkedKeys: [],
        checkedPersonnels: []
      },
      signType: 'countersign',
      currentProcess: -1
    })
  }
  /**
   * 已选环节显示审批模态框,同时赋予状态
   */
  showHasModal = (index: number, signType: string, checkedInfo: any) => {
    this.setState({
      visible: true,
      checkedInfo,
      signType,
      addType: 'UPDATE',
      currentProcess: index
    })
  }
  /**
   * 插入人员显示审批模态框,同时重置状态
   */
  showInsertModal = (index: number) => {
    this.setState({
      visible: true,
      checkedInfo: {
        checkedKeys: [],
        checkedPersonnels: []
      },
      signType: 'countersign',
      addType: 'INSERTAFTER',
      currentProcess: index
    })
  }
  /**
   * 弹出删除节点警告框
   */
  showDeleteConfirm = () => {
    confirm({
      title: '当前没有选择任何人员，点击确定将会删除对应审批节点，确定继续吗？',
      okText: '确定',
      okType: 'danger',
      cancelText: '取消',
      onOk: () => {
        this.setState({
          visible: false
        });
        this.removeApprovers(this.state.currentProcess)
      }
    });
  }
  /**
   * 模态框点击OK
   */
  okModal = () => {
    // 没有选择人员或者把人删完了
    if (this.state.checkedInfo.checkedPersonnels.length === 0) {
      if (this.state.currentProcess === -1) {
        this.setState({
          visible: false
        })
        return
      } else {
        this.showDeleteConfirm()
        return
      }
    }
    this.setState({
      visible: false
    })
    const onChange = this.props.onChange;
    const newCheckedInfo = {
      type: this.state.signType,
      checkedInfo: this.state.checkedInfo
    };
    let approvers;

    if (this.props.approvers) {
      if (this.state.currentProcess === -1) {// 新增
        approvers = [
          ...this.props.approvers,
          newCheckedInfo]
      } else {// 修改替换
        if (this.state.addType === 'UPDATE') { // 更新
          approvers = this.props.approvers.map((approver, i) => i === this.state.currentProcess ? newCheckedInfo : approver)
        } else if (this.state.addType === 'INSERTAFTER') {
          approvers = update(this.props.approvers, {// 插入
            $splice: [[this.state.currentProcess + 1, 0, newCheckedInfo]]
          })
        }
      }
    } else {// 创建模板或者自由审批首次
      approvers = [newCheckedInfo]
    }
    onChange && onChange(approvers);
  }
  /**
   * 模态框取消
   */
  cancelModal = () => {
    this.setState({
      visible: false
    })
  }
  // 展示人员选择Modal
  showSelectPersonnelModal = () => {
    this.setState({
      selectPersonnelVisible: true,
      version: Date.now()
    })
  }
  // 关闭人员选择Modal
  cancelSelectPersonnelModal = () => {
    this.setState({
      selectPersonnelVisible: false
    })
  }
  // OK人员选择Modal
  okSelectPersonnelModal = (checkedInfo: any) => {

    let signType = this.state.signType;
    // 如果人员减少到一人，普通签
    if (checkedInfo.checkedPersonnels.length <= 1) {
      signType = 'normal'
    } else if (signType === 'normal') {
      signType = 'countersign'
    }
    this.setState({
      selectPersonnelVisible: false,
      checkedInfo,
      signType
    })
  }

  /**
   * 删除当前人员
   */
  removePersonnel = (key: number, linKey: any[]) => {

    const keys = linKey.map((item) => item.key);
    const checkedKeys = this.state.checkedInfo.checkedKeys.filter((iKey: any) => !keys.includes(iKey));
    const checkedPersonnels = this.state.checkedInfo.checkedPersonnels.filter((item: any) => item.key !== key);
    let signType = this.state.signType;
    // 如果人员减少到一人，普通签
    if (checkedPersonnels.length <= 1) {
      signType = 'normal'
    }
    this.setState({
      checkedInfo: {
        checkedKeys,
        checkedPersonnels
      },
      signType
    })
  }
  /**
   * 删除当前环节
   */
  removeApprovers = (index: number) => {
    const onChange = this.props.onChange;
    onChange && onChange((this.props.approvers as any[]).filter((approver, i) => i !== index));
  }

  handleRadioChange = (e: any) => {
    if (e.target.checked) {
      this.setState({
        signType: e.target.value
      })
    }
  }

  render() {

    const { approvers } = this.props;
    const { visible, selectPersonnelVisible, checkedInfo, version, signType } = this.state;
    const modalProps = {
      visible: selectPersonnelVisible,
      centered: true,
      onOk: this.okSelectPersonnelModal,
      onCancel: this.cancelSelectPersonnelModal,
      checkedKeys: checkedInfo.checkedKeys,
      checkedPersonnels: checkedInfo.checkedPersonnels,
      version
    }

    return (
      <div>
        {
          approvers && approvers.map(({ type, checkedInfo: { checkedKeys, checkedPersonnels } }: any, index: number) => {
            const { title } = checkedPersonnels[0];
            const count = checkedPersonnels.length;
            return (
              <React.Fragment key={index}>
                <PersonnelAvatar
                  avatarText={type === 'normal' ? title : signMap[type]}
                  name={type === 'normal' ? title : `${count}名成员`}
                  onClose={() => this.removeApprovers(index)}
                  onClick={() => this.showHasModal(index, type, { checkedKeys, checkedPersonnels })}
                />
                <div className="add-node">
                  {
                    approvers.length !== index + 1 ? (
                      <Button
                        style={{ boxShadow: '0 2px 4px 0 rgba(0, 0, 0, .1)' }}
                        type="primary"
                        shape="circle"
                        icon="plus"
                        onClick={() => this.showInsertModal(index)}
                      />
                    ) : null
                  }
                </div>
              </React.Fragment>
            )
          })
        }
        <Button type="primary" icon="plus" shape="circle" onClick={this.showModal} />
        <Modal
          visible={visible}
          onOk={this.okModal}
          onCancel={this.cancelModal}
          centered={true}
          width={600}
          bodyStyle={{ height: '400px' }}
        >
          <Row>
            <Col span={6} style={{ paddingRight: '16px', textAlign: 'right' }}>审批人类别：</Col>
            <Col span={18}>指定成员</Col>
          </Row>
          <Divider />
          <Row>
            <Col span={6} style={{ paddingRight: '16px', textAlign: 'right' }}>
              已选审批人员：
            </Col>
            <Col span={18}>
              {
                checkedInfo.checkedPersonnels.map(({ key, title, linKey }: any) => (
                  <React.Fragment key={key}>
                    <PersonnelAvatar
                      name={title}
                      onClose={() => this.removePersonnel(key, linKey)}
                    />
                    <div style={{
                      width: '10px',
                      display: 'inline-block'
                    }} />
                  </React.Fragment>
                ))
              }
              <Button type="primary" onClick={this.showSelectPersonnelModal}>添加人员</Button>
            </Col>
          </Row>
          <Divider />
          <Row style={{ display: checkedInfo.checkedPersonnels.length > 1 ? 'block' : 'none' }}>
            <Col span={6} style={{ paddingRight: '16px', textAlign: 'right' }}>多人审批方式：</Col>
            <Col span={18} style={{ wordBreak: "normal" }}>
              <RadioGroup onChange={this.handleRadioChange} value={signType}>
                <Radio value="countersign">
                  会签（所有审批人同意才为同意）
                </Radio>
                <Radio value="orSign" style={{ margin: "10px 0" }}>
                  或签（任何一人同意即为同意，任何一人拒绝即为拒绝）
                </Radio>
              </RadioGroup>
            </Col>
          </Row>
        </Modal>
        <SelectPersonnelModal {...modalProps} />
      </div>
    )
  }
}
