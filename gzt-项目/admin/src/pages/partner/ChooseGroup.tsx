import React, { useState } from 'react';
import { Modal, List, Icon, Input } from 'antd';
import classNames from 'classnames';
import { ModalProps } from 'antd/lib/modal';
import './index.scss';
const ListItem = List.Item;
export interface ChooseGroupProps extends ModalProps {
  visible: boolean;
  onChooseOk: any,
  onCancel: () => void,
  groupList: any
  onAdd: any
}

export default function ChooseGroup(props: ChooseGroupProps) {
  const { visible, onChooseOk, onCancel, groupList, onAdd } = props;
  const [currentKey, setCurrentKey] = useState(-1);
  const [currentActive, setCurrentActive] = useState(true);
  const [inputSort, setInputSort]: any = useState(false);

  const [groupId, setGroupId] = useState(-1);
  const [addOptionValue, setAddOptionValue]: any = useState('');

  function onCurrentInfo(item: any, k: number) {
    setCurrentKey(k);
    setCurrentActive(false);
    setGroupId(item.id)
  }
  function allGroup() {
    setCurrentKey(-1);
    setCurrentActive(true);
  }
  function onOk() {
    onChooseOk(groupId)
  }

  function clearAddOption() {
    setInputSort(false);
    setAddOptionValue('');
  }

  function addOption() {
    setInputSort(false);
    setAddOptionValue('');
    onAdd && onAdd(addOptionValue);
  }
  return (
    <Modal
      visible={visible}
      onOk={onOk}
      onCancel={onCancel}
      className="grouplist-wrapper"
      width={400}
    >
      <div className='beautiful-scroll-bar-hover' style={{ height: 360, overflowX: 'hidden' }}>
        <div onClick={allGroup} style={{ height: '36px', lineHeight: '36px', color: 'rgba(0, 0, 0, 0.65)' }} className={classNames("partner-left-list", { active: currentActive })}>
          <span style={{ display: 'inline-block', paddingLeft: '20px' }}>全部公告</span>
        </div>
        <List
          dataSource={groupList}
          size="small"
          renderItem={
            (item: any, k: number) => (
              <div onClick={() => onCurrentInfo(item, k)} className="cursor-pointer">
                <ListItem key={item.name} className={classNames("partner-left-list", { active: currentKey === k })} >
                  <List.Item.Meta
                    style={{ width: '100%', paddingLeft: '20px' }}
                    title={
                      <div>
                        <span style={{ color: '#222' }}>{item.name}</span>
                      </div>
                    }
                  />
                </ListItem>
              </div>
            )
          }
        />
        {
          (() => {
            if (!inputSort) {
              return (
                <div
                  style={{ padding: '5px 0', cursor: 'pointer', textAlign: 'center', border: '1px solid rgb(217, 217, 217)' }}
                  onClick={() => setInputSort(true)}
                >
                  <Icon type="plus" /> 新建分类
            </div>)
            } else {
              return (
                <div>
                  <Input
                    style={{ width: 'calc(100% - 52px)', height: '28px' }}
                    value={addOptionValue}
                    onChange={(e) => setAddOptionValue(e.target.value)}
                  />
                  <Icon
                    type="close-circle"
                    style={{ marginLeft: '6px', fontSize: '20px', color: "#1890ff" }}
                    onClick={clearAddOption}
                  />
                  <Icon
                    type="check-circle"
                    theme="filled"
                    style={{ marginLeft: '6px', fontSize: '20px', color: '#1890ff' }}
                    onClick={addOption}
                  />
                </div>
              )
            }
          })()
        }


      </div>

    </Modal >
  )

}