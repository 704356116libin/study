import React, { useState, useEffect } from 'react';
import { Draggable, Droppable, DragDropContext, DropResult, ResponderProvided } from 'react-beautiful-dnd';
import { Modal, Button, Checkbox } from 'antd';
import { CheckboxChangeEvent } from 'antd/lib/checkbox';
import Preview from '../formLibrary/preview';
import classnames from 'classnames'
import './index.scss';

export interface ReviewFormProps {
  mode?: 'delete' | 'move';
  visible?: boolean;
  onCancel?: () => void;
  /** 点击确定回调，提供拖拽完成后的formData */
  onOk?: (formData: any[]) => void;
  /** 排除掉一些公共数据 */
  deleteData?: any;
  /** 拿到一些数据，展示一些控件 */
  defaultData?: any;
}

const BaseItems = [
  '分类',
  '工程分类',
  '项目名称',
  '送审金额',
  '审定金额',
  '送审时间',
  '建设单位',
  '业务科室',
  '标签',
  '完成时间'
]
/** 兼容自定义拖拽表单数据格式 */
const BaseItemMap = {
  分类: (required: boolean) => ({
    field: {
      label: '分类',
      name: 'category',
      required
    },
    type: 'SELECT'
  }),
  工程分类: (required: boolean) => ({
    field: {
      label: '工程分类',
      name: 'project_category',
      required
    },
    type: 'SELECT'
  }),
  项目名称: (required: boolean) => ({
    field: {
      label: '项目名称',
      name: 'project_name',
      required
    },
    type: 'INPUT'
  }),
  送审金额: (required: boolean) => ({
    field: {
      label: '送审金额',
      name: 'amount_of_review',
      required
    },
    type: 'INPUT'
  }),
  审定金额: (required: boolean) => ({
    field: {
      label: '审定金额',
      name: 'approved_amount',
      required
    },
    type: 'INPUT'
  }),
  送审时间: (required: boolean) => ({
    field: {
      label: '送审时间',
      name: 'submit_time',
      required
    },
    type: 'DATEPICKER'
  }),
  建设单位: (required: boolean) => ({
    field: {
      label: '建设单位',
      name: 'project_construction',
      required
    },
    type: 'SELECT'
  }),
  业务科室: (required: boolean) => ({
    field: {
      label: '业务科室',
      name: 'service_department',
      required
    },
    type: 'SELECT'
  }),
  标签: (required: boolean) => ({
    field: {
      label: '标签',
      name: 'action_label',
      required
    },
    type: 'SELECT'
  }),
  完成时间: (required: boolean) => ({
    field: {
      label: '完成时间',
      name: 'limit_time',
      required
    },
    type: 'RADIO'
  }),
}
const baseItemss = BaseItems.map((item) => ({
  name: item,
  required: false
}))


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

export default function ReviewFormLibrary(props: ReviewFormProps) {

  const {
    mode = 'move',
    visible,
    onCancel,
    onOk,
    deleteData,
    defaultData
  } = props;

  const [previewVisible, setPreviewVisible] = useState();
  const [baseItems, setBaseItems] = useState(baseItemss);
  const [userItems, setUserItems]: any = useState([]);
  const [currentItem, setCurrentItem] = useState();

  useEffect(() => {
    if (deleteData) {
      const deleteItems = deleteData.map((item: any) => ({
        name: item.field.label,
        required: item.field.required
      }))
      const defaultItems = defaultData.map((item: any) => ({
        name: item.field.label,
        required: item.field.required
      }));
      // 左侧需要排除掉的控件
      const nextUserItems = [...deleteItems, ...defaultItems];

      // 右侧需要展示的控件 'move' 代表移动模式
      mode === 'move'
        ? setUserItems(deleteItems)
        : setUserItems(defaultItems);

      setBaseItems(baseItems.filter((baseItem) => !nextUserItems.some((userItem: any) => baseItem.name === userItem.name)));
    }
  }, [deleteData, defaultData])

  function onDragEnd(result: DropResult, provided: ResponderProvided) {
    // 如果没有放到指定区域，不做处理
    if (!result.destination) {
      return;
    }
    const { droppableId: destinationDroppableId, index: destinationIndex } = result.destination;
    const { droppableId: sourceDroppableId, index: sourceIndex } = result.source;

    if (sourceDroppableId === 'droppable-base') {// 左边区域内拖拽
      if (destinationDroppableId === 'droppable-base') {
        /**
         * 处理拖拽后的列表
         */
        const nextBaseItems = reorder(
          baseItems,
          sourceIndex,
          destinationIndex
        );
        // 设置state
        setBaseItems(nextBaseItems);
      } else {// 左边 拖到 右边
        const nextBaseItems = Array.from(baseItems);
        const nextUserItems = Array.from(userItems);

        const [removed] = nextBaseItems.splice(sourceIndex, 1);
        nextUserItems.splice(destinationIndex, 0, removed);

        setBaseItems(nextBaseItems);
        setUserItems(nextUserItems);

      }
    } else {
      if (destinationDroppableId === 'droppable-user') {// 右边区域内拖拽
        /**
         * 处理拖拽后的列表
         */
        const nextUserItems = reorder(
          userItems,
          sourceIndex,
          destinationIndex
        );
        // 设置state
        setUserItems(nextUserItems);
      } else {// 右边 拖到 左边
        const nextUserItems: any[] = Array.from(userItems);
        const nextBaseItems = Array.from(baseItems);

        const [removed] = nextUserItems.splice(sourceIndex, 1);
        nextBaseItems.splice(destinationIndex, 0, removed);

        setUserItems(nextUserItems);
        setBaseItems(nextBaseItems);

      }
    }
  }

  /** 预览 */
  function onPreview() {
    // 显示模态框
    setPreviewVisible(true);
  }

  /** 用户区域 表单点击事件 */
  function handleClick(item: any) {
    setCurrentItem(item)
  }

  /** 处理是否必选对应状态 */
  function handleRequiredChange(e: CheckboxChangeEvent) {

    setCurrentItem({
      name: currentItem.name,
      required: e.target.checked
    });
    setUserItems(userItems.map((item: any) => {
      if (item.name === currentItem.name) {
        return {
          name: item.name,
          required: e.target.checked
        }
      } else {
        return item
      }
    }))

  }

  function handleOk() {

    onOk && onOk(userItems.map(({ name, required }: any) => BaseItemMap[name](required)));
  }

  return (
    <Modal
      title="评审表单"
      centered
      visible={visible}
      onCancel={onCancel}
      width={660}
      maskClosable={false}
      destroyOnClose
      bodyStyle={{ padding: '24px 0' }}
      getContainer={() => document.querySelector('.review-template-create') || document.body}
      footer={
        <div style={{ textAlign: 'left' }}>
          <Button type="primary" onClick={handleOk}>确定</Button>
          <Button type="default" onClick={onPreview}>预览</Button>
          <Button type="dashed" onClick={onCancel}>取消</Button>
        </div>
      }
    >
      <div className="review-form">
        <DragDropContext
          onDragEnd={onDragEnd}
        >
          {/* 左边 */}
          <Droppable droppableId="droppable-base" type="REVIEW_FORM">
            {(provided, snapshot) => {
              return (
                <div
                  ref={provided.innerRef}
                  {...provided.droppableProps}
                  className="base-form"
                >
                  {/* key 不能用index,否则就会卡顿 原因不明 插件机制问题 */}
                  {baseItems.map((item: any, index: number) => (
                    <Draggable key={item.name} draggableId={item.name} index={index}>
                      {(provided, snapshot) => (
                        <div
                          ref={provided.innerRef}
                          {...provided.draggableProps}
                          {...provided.dragHandleProps}
                          className={classnames('draggable-item', snapshot.isDragging ? 'gu-mirror' : '')}
                        >
                          {item.name}
                        </div>
                      )}
                    </Draggable>
                  ))}
                  {provided.placeholder}
                </div>
              )
            }}
          </Droppable>
          {/* 右边 */}
          <Droppable droppableId="droppable-user" type="REVIEW_FORM">
            {(provided, snapshot) => (
              <div
                ref={provided.innerRef}
                {...provided.droppableProps}
                className="user-form"
              >
                {userItems.length !== 0
                  ? userItems.map((item: any, index: number) => (
                    <Draggable key={item.name} draggableId={item.name} index={index}>
                      {(provided, snapshot) => (
                        <div
                          ref={provided.innerRef}
                          {...provided.draggableProps}
                          {...provided.dragHandleProps}
                          className={classnames(
                            'draggable-item',
                            snapshot.isDragging ? 'gu-mirror' : '',
                            currentItem ? currentItem.name === item.name ? 'active' : '' : '')}
                          onClick={() => handleClick(item)}
                        >
                          <span className={item.required ? 'required' : undefined}>{item.name}</span>
                        </div>
                      )}
                    </Draggable>
                  )) : '请拖拽控件至此处'
                }
                {provided.placeholder}
              </div>
            )}
          </Droppable>
        </DragDropContext>
        {
          <div className="custom-form">
            {
              currentItem
                ? (
                  <>
                    <h3 className="cf-customizer-type">
                      {currentItem.name}
                    </h3>
                    <div>
                      <div className="cf-customizer-field cf-customizer-required">
                        <div className="custom">
                          <Checkbox checked={currentItem.required} onChange={handleRequiredChange}>
                            设为必填
                        </Checkbox>
                        </div>
                      </div>
                    </div>
                  </>
                ) : null
            }
          </div>
        }
      </div>
      <Preview
        type="review"
        visible={previewVisible}
        data={userItems.map(({ name, required }: any) => BaseItemMap[name](required))}
        onCancel={() => setPreviewVisible(false)} />
    </Modal>
  )
}

ReviewFormLibrary.defaultProps = {
  defaultData: []
}
