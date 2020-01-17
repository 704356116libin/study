import React, { useEffect, useState } from 'react';
import { Layout, Tabs, Button, message, List } from 'antd';
import req, { get } from '../../utils/request';
import './setting.scss'
import TextLabel from '../../components/textLabel';

const TabPane = Tabs.TabPane;
const { Content } = Layout;

function Invitation(props: any) {

  const [info, setInfo] = useState()

  /** 获取用户被邀请信息 */
  async function queryInvitationInfo() {
    const result = await get('/api/u_get_invitelist');
    if (result.status === 'success') {
      setInfo(result.data)
    }
  }

  useEffect(() => {
    queryInvitationInfo();
  }, []);

  async function handleExternalContact(agreeOrRefuse: 'agree' | 'refuse', company_id: any) {
    const result = await req('/api/management_deal_external_users', {
      method: 'POST',
      body: {
        company_id,
        agreeOrRefuse,
        type_id: -1
      }
    });
    if (result.status === 'success') {
      message.info('操作成功');
      queryInvitationInfo();
    }
  }
  async function handleCompany(agreeOrRefuse: 'agree' | 'refuse', company_id: any) {
    const result = await req('/api/management_dealStaffInvite', {
      method: 'POST',
      body: {
        company_id,
        agreeOrRefuse
      }
    });
    if (result.status === 'success') {
      message.info('操作成功');
      queryInvitationInfo();
    }
  }

  return (
    <Content className="settings-con">
      <h2 style={{ padding: '5px 0', lineHeight: '40px', fontSize: '18px', borderBottom: '1px solid #e1e4e8' }}>邀请信息</h2>
      <Tabs defaultActiveKey="staff">
        <TabPane tab="员工邀请" key="staff">
          <List
            dataSource={info && info.staff}
            renderItem={({ company_id, company_name }: any) => (
              <List.Item actions={[
                <Button type="primary" onClick={() => handleCompany('agree', company_id)}>同意</Button>,
                <Button type="danger" onClick={() => handleCompany('refuse', company_id)}>拒绝</Button>
              ]}>
                <List.Item.Meta
                  title={<><span className="primary-color">{company_name}</span>邀请你加入</>}
                />
              </List.Item>
            )}
          />
        </TabPane>
        <TabPane tab="外部联系人邀请" key="externalContact">
          <List
            dataSource={info && info.externalContact}
            renderItem={({ id, name, reason }: any) => (
              <List.Item actions={[
                <Button type="primary" onClick={() => handleExternalContact('agree', id)}>同意</Button>,
                <Button type="danger" onClick={() => handleExternalContact('refuse', id)}>拒绝</Button>
              ]}>
                <List.Item.Meta
                  title={<><span className="primary-color">{name}</span> 邀请你成为公司的外部联系人</>}
                  description={<><TextLabel text="邀请意见" />{reason}</>}
                />
              </List.Item>
            )}
          />
        </TabPane>
      </Tabs>
    </Content >
  )
}
export default Invitation
