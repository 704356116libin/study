import * as React from 'react';
import { Layout, Tabs, Button, List, Divider, Modal, Input, message, Tooltip, Popconfirm } from 'antd';
import { DragDropContext, Draggable, Droppable, ResponderProvided, DropResult } from 'react-beautiful-dnd';
import request, { get } from '../../../utils/request';
import update from 'immutability-helper';
import classnames from 'classnames';
import '.././othersmgt.scss'

const { TabPane } = Tabs;

interface Item {
  value: string;
  index: number;
}

/**
 * 处理 交换后的数据
 * @param list 
 * @param startIndex 
 * @param endIndex 
 */
const reorder = (list: any[], startIndex: number, endIndex: number) => {
  const result = Array.from(list);
  const [removed] = result.splice(startIndex, 1);
  result.splice(endIndex, 0, removed);
  return result;
};

export default class Othersmgt extends React.Component<any, any>{

  state = {
    /** 送审业务负责科室: 当前是否处于编辑状态（新增除外） */
    customizeDeptEditState: false,
    customizeDepts: [],
    nextCustomizeDepts: [] as string[],
    addCustomizeDeptVisible: false,
    addCustomizeDeptValue: {} as Item,
    /** 标签: 当前是否处于编辑状态（新增除外） */
    labelEditState: false,
    labels: [],
    nextLabels: [] as string[],
    addLabelVisible: false,
    addLabelValue: {} as Item,
    labelType: 'add',
    customizeDeptType: 'add'
  }

  async componentDidMount() {
    const result = await get('/api/c_pst_get_basic_form_data');
    if (result.status === 'success') {
      this.setState({
        customizeDepts: result.data.service_department,
        nextCustomizeDepts: result.data.service_department,
        labels: result.data.action_label,
        nextLabels: result.data.action_label
      })
    }
  }

  onDragEnd = (result: DropResult, provided: ResponderProvided) => {
    // 如果没有放到指定区域，不做处理
    if (!result.destination) {
      return;
    }
    const { droppableId, index: destinationIndex } = result.destination;
    const { index: sourceIndex } = result.source;
    if (destinationIndex === sourceIndex) {
      return;
    }
    /** 处理拖拽后的列表 */
    const nextBaseItems = reorder(
      droppableId === 'droppable-dept' ? this.state.nextCustomizeDepts : this.state.nextLabels,
      sourceIndex,
      destinationIndex
    );
    // 设置state
    if (droppableId === 'droppable-dept') {
      this.setState({
        nextCustomizeDepts: nextBaseItems
      })
      this.update({
        service_department: nextBaseItems
      }, '排序成功')
    } else {
      this.setState({
        nextLabels: nextBaseItems
      })
      this.update({
        action_label: nextBaseItems
      }, '排序成功')
    }
  }
  /** 展示送审业务负责科室 */
  customizeDeptModalShow = () => {
    this.setState({
      addCustomizeDeptVisible: true,
      addCustomizeDeptValue: '',
      customizeDeptType: 'add'
    })
  }
  /** 删除送审业务负责科室 */
  customizeDeptClose = (item: string) => {
    const nextCustomizeDepts = this.state.nextCustomizeDepts.filter((dept: string) => dept !== item);
    this.setState({
      nextCustomizeDepts
    });
    this.update({
      service_department: nextCustomizeDepts
    }, '删除成功')
  }
  /** 修改送审业务负责科室 */
  customizeDeptEdit = (item: any) => {
    this.setState({
      addCustomizeDeptVisible: true,
      addCustomizeDeptValue: item,
      customizeDeptType: 'edit'
    })
  }
  /** 添加送审业务负责科室 */
  addCustomizeDept = async () => {
    if (this.state.nextCustomizeDepts.includes(this.state.addCustomizeDeptValue.value)) {
      message.error('送审业务负责科室已存在！')
    } else {
      const { value, index } = this.state.addCustomizeDeptValue;
      let newNextCustomizeDepts: string[];
      if (this.state.customizeDeptType === 'add') {
        newNextCustomizeDepts = [value, ...this.state.nextCustomizeDepts];
      } else {
        newNextCustomizeDepts = update(this.state.nextCustomizeDepts, {
          $splice: [[index, 1, value]]
        })
      }

      this.setState({
        nextCustomizeDepts: newNextCustomizeDepts,
        addCustomizeDeptVisible: false
      });
      this.update({
        service_department: newNextCustomizeDepts
      }, this.state.customizeDeptType === 'add' ? '添加成功' : '修改成功')

    }
  }
  addCustomizeDeptChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    this.setState({
      addCustomizeDeptValue: {
        value: e.target.value,
        index: this.state.addCustomizeDeptValue.index
      }
    })
  }
  /** 展示模态框 */
  labelModalShow = () => {
    this.setState({
      addLabelVisible: true,
      addLabelValue: '',
      labelType: 'add'
    })
  }
  /** 修改 label */
  labelEdit = (label: any) => {
    this.setState({
      addLabelVisible: true,
      addLabelValue: label,
      labelType: 'edit'
    })
  }
  /** 关闭label */
  labelClose = (label: string) => {
    const nextLabels = this.state.nextLabels.filter((labelL: string) => labelL !== label);
    this.setState({
      nextLabels
    });
    this.update({
      action_label: nextLabels
    }, '删除成功')
  }
  addLabel = async () => {
    if (this.state.nextLabels.includes(this.state.addLabelValue.value)) {
      message.error('标签名已存在！')
    } else {
      const { value, index } = this.state.addLabelValue;
      let newNextLabels: string[];
      if (this.state.labelType === 'add') {
        newNextLabels = [value, ...this.state.nextLabels];
      } else {
        newNextLabels = update(this.state.nextLabels, {
          $splice: [[index, 1, value]]
        })
      }
      this.setState({
        nextLabels: newNextLabels,
        addLabelVisible: false
      });
      this.update({
        action_label: newNextLabels
      }, this.state.labelType === 'add' ? '添加成功' : '修改成功')
    }
  }
  /**
   * 更新自定义科室和标签
   * @param body 
   */
  async update(body: any, messageText: string) {

    const keyMap = {
      service_department: ['customizeDepts', 'nextCustomizeDepts'],
      action_label: ['labels', 'nextLabels'],
    }
    const result = await request('/api/c_pst_update_basic_form_data', {
      method: 'POST',
      body
    })
    if (result.status === 'success') {
      message.success(messageText);
      this.setState({
        [keyMap[Object.keys(body)[0]][0]]: body[Object.keys(body)[0]]
      })
    } else {
      message.error('服务器异常，请稍后再试');
      this.setState({
        [keyMap[Object.keys(body)[0]][1]]: this.state[keyMap[Object.keys(body)[0]][0]]
      })
    }
  }
  addLabelChange = (e: React.ChangeEvent<HTMLInputElement>) => {

    this.setState({
      addLabelValue: {
        value: e.target.value,
        index: this.state.addLabelValue.index
      }
    })
  }
  render() {

    const {
      customizeDeptEditState,
      nextCustomizeDepts,
      addCustomizeDeptVisible,
      addCustomizeDeptValue,
      labelEditState,
      nextLabels,
      addLabelVisible,
      addLabelValue
    } = this.state;

    return (
      <Layout className="review-othersmgt white">
        <DragDropContext
          onDragEnd={this.onDragEnd}
        >
          <Tabs defaultActiveKey="1" >
            <TabPane tab="送审科室" key="1">
              <div style={{ padding: '0 24px' }}>
                <Button
                  style={{ display: customizeDeptEditState ? 'none' : 'inline-block', marginRight: '24px' }}
                  type={"primary"}
                  onClick={this.customizeDeptModalShow}
                >
                  新增送审科室
              </Button>
              </div>
              <Divider />
              <Droppable droppableId="droppable-dept">
                {(provided, snapshot) => {
                  return (
                    <div
                      ref={provided.innerRef}
                      {...provided.droppableProps}
                      className="droppable-wrapper"
                    >
                      {
                        nextCustomizeDepts.map((value: string, index: number) => (
                          <Draggable key={value} draggableId={value} index={index}>
                            {(provided, snapshot) => (
                              <div ref={provided.innerRef}>
                                <List.Item
                                  {...provided.draggableProps}
                                  {...provided.dragHandleProps}
                                  className={classnames('draggable-item', snapshot.isDragging ? 'gu-mirror' : '')}
                                  actions={
                                    [
                                      <Tooltip title="编辑">
                                        <Button onClick={() => this.customizeDeptEdit({ value, index })} ghost type="primary" size="small" shape="circle" icon="edit" />
                                      </Tooltip>,
                                      index !== 0
                                        ?
                                        <Popconfirm title="确定删除吗?" onConfirm={() => this.customizeDeptClose(value)} okText="确定" cancelText="取消">
                                          <Tooltip title="删除">
                                            <Button type="danger" size="small" shape="circle" icon="close" />
                                          </Tooltip>
                                        </Popconfirm>
                                        : <div style={{ width: '24px' }}></div>
                                    ]
                                  }>
                                  <List.Item.Meta
                                    title={value}
                                  />
                                </List.Item>
                              </div>
                            )}
                          </Draggable>
                        ))
                      }
                      {provided.placeholder}
                    </div>
                  )
                }}
              </Droppable>
            </TabPane>
            <TabPane tab="标签管理" key="2">
              <div style={{ padding: '0 24px' }}>
                <Button
                  style={{ display: labelEditState ? 'none' : 'inline-block', marginRight: '24px' }}
                  type={"primary"}
                  onClick={() => this.setState({ addLabelVisible: true })}
                >
                  新增标签
              </Button>
              </div>
              <Divider />
              <Droppable droppableId="droppable-label">
                {(provided, snapshot) => {
                  return (
                    <div
                      ref={provided.innerRef}
                      {...provided.droppableProps}
                      className="droppable-wrapper"
                    >
                      {
                        nextLabels.map((value: string, index: number) => (
                          <Draggable key={value} draggableId={value} index={index}>
                            {(provided, snapshot) => (
                              <div ref={provided.innerRef}>
                                <List.Item
                                  {...provided.draggableProps}
                                  {...provided.dragHandleProps}
                                  className={classnames('draggable-item', snapshot.isDragging ? 'gu-mirror' : '')}
                                  actions={
                                    [
                                      <Tooltip title="编辑">
                                        <Button onClick={() => this.labelEdit({ value, index })} ghost type="primary" size="small" shape="circle" icon="edit" />
                                      </Tooltip>,
                                      index !== 0
                                        ?
                                        <Popconfirm title="确定删除吗?" onConfirm={() => this.labelClose(value)} okText="确定" cancelText="取消">
                                          <Tooltip title="删除">
                                            <Button type="danger" size="small" shape="circle" icon="close" />
                                          </Tooltip>
                                        </Popconfirm>
                                        : <div style={{ width: '24px' }}></div>
                                    ]
                                  }>
                                  <List.Item.Meta
                                    title={value}
                                  />
                                </List.Item>
                              </div>
                            )}
                          </Draggable>
                        ))
                      }
                      {provided.placeholder}
                    </div>
                  )
                }}
              </Droppable>
            </TabPane>
          </Tabs>
        </DragDropContext>
        <Modal
          visible={addCustomizeDeptVisible}
          title="新增送审科室"
          onOk={this.addCustomizeDept}
          onCancel={() => this.setState({ addCustomizeDeptVisible: false })}
        >
          <Input onChange={this.addCustomizeDeptChange} value={addCustomizeDeptValue.value} maxLength={12} placeholder="最多10个字" />
        </Modal>
        <Modal
          visible={addLabelVisible}
          title="新增标签"
          onOk={this.addLabel}
          onCancel={() => this.setState({ addLabelVisible: false })}
        >
          <Input onChange={this.addLabelChange} value={addLabelValue.value} maxLength={12} placeholder="最多10个字" />
        </Modal>
      </Layout>
    )
  }
}