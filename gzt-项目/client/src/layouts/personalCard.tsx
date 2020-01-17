import React from 'react';
import { Modal, Divider, Spin, Avatar } from "antd";
import './personalCard.scss'

export default function PersonalCardModal(props: any) {

  const { visible, onCancel, dataSource } = props;

  const { avator, name, signature, company_data } = dataSource || {} as any;

  return (
    <Modal
      wrapClassName="business-card"
      title={
        <div>
          <Avatar size={80} src={avator} style={{ verticalAlign: 'top', color: '#f56a00', backgroundColor: '#fde3cf' }}>{name ? name : 'XXX'}</Avatar>
          <div style={{ paddingLeft: '20px', display: 'inline-block', color: '#fff' }}>
            <p style={{ margin: 0, lineHeight: '40px', fontWeight: 'bold', fontSize: '20px' }}>{name ? name : 'XXX'}</p>
            <p style={{ margin: 0, lineHeight: '30px' }}>{signature}</p>
          </div>
        </div>
      }
      width="360px"
      closable={false}
      visible={visible}
      onCancel={onCancel}
      footer={null}
      mask={false}
    >
      <Spin spinning={!dataSource}>
        <div className="carte beautiful-scroll-bar-hover">
          <div>
            <div style={{ marginBottom: '10px', fontWeight: 400, color: '#000', fontSize: '16px' }}>个人信息</div>
            <div>昵称： <span>{name}</span> </div>
          </div>
          <Divider />
          {
            company_data && (company_data as any[]).map(({
              company_name,
              user_name,
              user_tel,
              department,
              user_sex
            }, index, arr) => (
                <React.Fragment key={index}>
                  <div>
                    <div style={{ marginBottom: '10px', fontWeight: 400, color: '#000', fontSize: '16px' }}>{company_name}</div>
                    {user_name && <div>姓名： <span>{user_name}</span> </div>}
                    {user_tel && <div>电话： <span>{user_tel}</span> </div>}
                    {department && <div>部门： <span>{department}</span> </div>}
                    {user_sex && <div>性别： <span>{user_sex}</span> </div>}
                  </div>
                  {index !== arr.length - 1 ? <Divider /> : null}
                </React.Fragment>
              ))
          }
        </div>
        <div style={{ height: '20px', background: '#fff' }} />
      </Spin>
    </Modal>
  )
}
