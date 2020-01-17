import * as React from "react";
import Dragula from 'react-dragula';
import { Modal, Button } from 'antd'; //  Form, 
import Preview from './preview'; //  Form, 
import './index.scss'

import BaseItems from './baseItems';

export interface FormLibraryProps {
  visible: boolean;
  onCancel: () => void;
  /**
   * 点击确定回调，提供拖拽完成后的formData
   */
  onOk: (formData: any[]) => void;
  /**
   * 拿到一些数据，展示一些控件
   */
  defalutData: any;
}
/**
 * 支持的控件类型
 */
export type FormItemType = 'INPUT' | 'TEXTAREA' | 'RADIO' | 'CHECKBOX' | 'DATEPICKER' | 'DATERANGE' | 'NUMBER' | 'MONEY' | 'SELECT' | 'ANNEX';
/**
 * 组件处理出来的(和要用的)最终数据
 */
export interface FormItemData {
  type: FormItemType;
  field: {
    label: string;
    required: boolean;
    [propName: string]: any;
  };
  value?: string & string[];
}
export interface FormLibraryState {
  currentItem: any;
  droppedCount: number;
  formData?: FormItemData[];
  previewVisible: boolean;
  currentRightRef: React.RefObject<{}> | null
}

/**
 * 表单拖拽组件
 */
export default class FormLibrary extends React.Component<FormLibraryProps, FormLibraryState> {
  drake: any;
  state = {
    currentItem: null,
    droppedCount: 0,
    previewVisible: false,
    formData: [],
    currentRightRef: null
  }
  componentDidMount() {
    // todo....
  }

  componentDidUpdate(prevProps: FormLibraryProps, prevState: FormLibraryState) {
    // todo....
  }

  /**
   * 初始化拖拽
   */
  dragulaDecorator = (componentBackingInstance: any) => {
    if (!componentBackingInstance) {
      return
    }
    let timers: any;
    let prevActive: any;
    if (componentBackingInstance.id === 'left') {
      this.drake = Dragula([componentBackingInstance], {
        copy(el: any, source: any) {
          return source === componentBackingInstance
        },
        accepts(el: any, target: any) {
          return target !== componentBackingInstance
        },
        revertOnSpill: true,
        invalid: (el: any, handle: any) => {
          if (handle.classList.contains('user-form-placeholder')) {
            return true
          }
          if (handle.classList.contains('cf-cover')) {
            prevActive && prevActive.classList.remove('active');
            const realEl = handle.parentNode;
            realEl.classList.add('active');
            prevActive = realEl;
          }
          return false
        }
      }).on('cloned', (clone: any, original: any, type: any) => {
        if (type === 'mirror') {
          if (!original.classList.contains('base')) {
            clone.style.width = '140px';
            clone.style.height = '36px';
            clone.innerHTML = `<span>${BaseItems[original.querySelector('[data-item-type]').dataset.itemType].name}</span>`;
          } else {
            // clone.style.boxShadow = '0 2px 3px 0px #ccc';
          }
        } else {
          clone.classList.remove('base');
          clone.innerHTML = '';
          clone.appendChild(BaseItems[original.querySelector('[data-item-type]').dataset.itemType].component(
            null,
            this.setCurrentItemInfo,
            () => {
              this.setState((prevState) => {
                return {
                  droppedCount: prevState.droppedCount - 1
                }
              })
            }));
        }

      }).on('out', (el: any, container: any, source: any) => {

        const scrollToTop = () => {
          cancelAnimationFrame(timers);
          if (container.scrollTop > 0) {
            container.scrollTop = container.scrollTop - 2;
            timers = requestAnimationFrame(scrollToTop)
          } else {
            container.scrollTop = 0;
            cancelAnimationFrame(timers)
          }
        }

        const scrollToBottom = () => {
          cancelAnimationFrame(timers);
          if (container.scrollTop + 2 < container.scrollHeight - container.clientHeight) {
            container.scrollTop = container.scrollTop + 2;
            timers = requestAnimationFrame(scrollToBottom)
          } else {
            container.scrollTop = container.scrollHeight - container.clientHeight;
            cancelAnimationFrame(timers)
          }
        }

        if (el.offsetTop < 200 + container.scrollTop) {
          timers = requestAnimationFrame(scrollToTop)
        } else {
          timers = requestAnimationFrame(scrollToBottom);
        }

      }).on('dragend', () => {
        cancelAnimationFrame(timers)
      }).on('over', () => {
        cancelAnimationFrame(timers)
      }).on('drop', (el: any, target: any, source: any, sibling: any) => {
        this.setState((prevState) => {
          return {
            droppedCount: prevState.droppedCount + 1
          }
        })
      }).on('drag', () => {
        // console.log('drag');
      }).on('shadow', () => {
        // console.log('shadow');
      }).on('remove', () => {
        // console.log('remove');
      })
    } else {
      this.drake.containers.push(componentBackingInstance);
      this.setState({
        currentRightRef: componentBackingInstance
      });
    }
  }

  onPreview = () => {
    this.setState({
      formData: this.handleFormData(),
      previewVisible: true
    })
  }

  onOk = () => {
    this.setState({
      currentItem: null
    })
    this.props.onOk(this.handleFormData());
  }

  render() {

    const { visible, onCancel } = this.props;
    const { currentRightRef, currentItem } = this.state;
    const CurrentI = currentItem ? BaseItems[(currentItem as any).type].Customizer : () => null;

    return (

      <Modal
        title="表单库"
        centered
        visible={visible}
        onCancel={onCancel}
        width={1200}
        maskClosable={false}
        destroyOnClose
        bodyStyle={{ padding: '24px 0' }}
        footer={
          <div style={{ textAlign: 'left' }}>
            <Button type="primary" onClick={this.onOk}>确定</Button>
            <Button type="default" onClick={this.onPreview}>预览</Button>
            <Button type="dashed" onClick={onCancel}>取消</Button>
          </div>
        }
      >

        <div className="form-library">
          <div className="base-form" id='left' ref={this.dragulaDecorator}>
            {
              (() => {
                const oFragement = [];
                for (const key in BaseItems) {
                  if (BaseItems.hasOwnProperty(key)) {
                    oFragement.push(
                      <div key={key} className="base">
                        <span data-item-type={key}>{BaseItems[key].name}</span>
                      </div>
                    );
                  }
                }
                return oFragement
              })()
            }
          </div>
          <div className="user-form" id='right' ref={this.dragulaDecorator}>
            {
              (() => {
                if (visible && currentRightRef && this.props.defalutData && Array.isArray(this.props.defalutData)) {
                  this.setState({
                    currentRightRef: null
                  })
                  this.props.defalutData.forEach((item, k) => {
                    const oDiv = document.createElement('div');
                    oDiv.appendChild(
                      BaseItems[item.type].component(
                        item.field,
                        this.setCurrentItemInfo,
                        () => {
                          this.setState((prevState) => {
                            return {
                              droppedCount: prevState.droppedCount - 1
                            }
                          })
                        }
                      )
                    )
                    currentRightRef && (currentRightRef as any).appendChild(oDiv)

                  })
                  return null
                } else {
                  if (this.state.droppedCount === 0) {
                    return <p className="user-form-placeholder">请拖拽表单至此处</p>
                  } else {
                    return null
                  }
                }
              })()
            }
          </div>
          <div className="custom-form beautiful-scroll-bar">
            {
              currentItem ?
                <CurrentI field={(currentItem as any).field} setCurrentItemInfo={this.setCurrentItemInfo} />
                : null
            }
          </div>
        </div>
        <Preview visible={this.state.previewVisible} data={this.state.formData} onCancel={() => this.setState({ previewVisible: false })} />
      </Modal>
    )
  }

  /**
   * 设置当前处于激活状态的表单元素的信息, 右侧设置信息处任何一个onChange事件触发
   */
  private setCurrentItemInfo = (currentItem: any) => {
    this.setState({
      currentItem
    })
  }
  /**
   * 处理拖拽完成后的表单数据
   */
  private handleFormData() {
    const droppedItems: any = document.querySelectorAll('#right > div');
    const formData = [];
    for (const droppedItem of droppedItems) {
      const formItemInfo = droppedItem.querySelector('.cf-cover').formInfo;
      formData.push(formItemInfo)
    }

    return formData
  }
}