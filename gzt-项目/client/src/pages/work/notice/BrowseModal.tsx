import React, { useState } from 'react';
import { Modal, Tabs, Avatar, Spin } from 'antd';
import { get } from '../../../utils/request';
import PersonalCardModal from '../../../layouts/personalCard';
const TabPane = Tabs.TabPane;

export interface BrowseModalProps {
  visible: boolean;
  onCancel: any;
  browseRecord: any;
  browseUnRecord: any
  loading: boolean
}
/** 浏览人数模态框（已读未读） */
export default function BrowseModal(props: BrowseModalProps) {
  const { visible, onCancel, browseRecord, browseUnRecord, loading } = props;

  const [businessCardVisible, setBusinessCardVisible] = useState(false);
  const [cardInfo, setCardInfo] = useState();

  /** 展示用户名片 */
  async function showBusinessCard(user_id: string | number) {
    setBusinessCardVisible(true);
    const result = await get(`/api/u_get_card_info?user_id=${user_id}`);
    if (result.status === 'success') {
      setCardInfo(result.data)
    }
  }

  return (
    <Modal
      centered={true}
      visible={visible}
      onCancel={onCancel}
      maskClosable={false}
      footer={null}
    >
      <div style={{ minHeight: '300px' }}>
        <Spin spinning={loading}>
          <Tabs defaultActiveKey="record">
            <TabPane tab="已读列表" key="record">
              {
                browseRecord && browseRecord.data.map(({ user_info, name }: any, index: any) => {
                  return (
                    <p key={index} className="inline-block" style={{ marginRight: '25px' }}>
                      <span onClick={() => showBusinessCard(user_info.id)}>
                        <Avatar src={user_info.avatar} size={50} >{user_info.name}</Avatar>
                      </span>
                      <p className="text-center">{name.slice(0, 4)}</p>
                    </p>
                  )
                })
              }
            </TabPane>
            <TabPane tab="未读列表" key="unRecord">
              {
                browseUnRecord && browseUnRecord.data.map(({ user_info, name }: any, index: any) => {
                  return (
                    <p key={index} className="inline-block" style={{ marginRight: '25px' }}>
                      <span onClick={() => showBusinessCard(user_info.id)}>
                        <Avatar src={user_info.avatar} size={50} >{user_info.name}</Avatar>
                      </span>
                      <p className="text-center"> {name.slice(0, 4)}</p>
                    </p>
                  )
                })
              }
            </TabPane>
          </Tabs>,
        </Spin>
      </div>
      <PersonalCardModal
        visible={businessCardVisible}
        onCancel={() => setBusinessCardVisible(false)}
        dataSource={cardInfo}
      />
    </Modal>
  )
}
