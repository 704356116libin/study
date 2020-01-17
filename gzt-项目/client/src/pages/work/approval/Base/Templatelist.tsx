import React, { useEffect } from 'react';
import { Card, Col, Row, Avatar, Layout } from 'antd';
import { Link } from 'react-router-dom';
const { Meta } = Card;
const { Content } = Layout;
interface TemplatelistProps extends React.Props<{}> {
  datasource: any;
  link: any;
  onCloseModal?: any;
}


export default function Templatelist({
  datasource,
  link,
  onCloseModal
}: TemplatelistProps) {

  useEffect(() => {
    // todo ...
  })
  return (
    <Content>
      {
        datasource && datasource.map(({ type_name, data }: any, index: any) => {
          if (data.length === 0) {
            return null
          }
          return (
            <div key={index}>
              <Row className="typeTitle" style={{ margin: '5px 0', background: '#f8f8f8', borderLeft: '3px solid #1890ff' }}>
                <Col style={{ padding: '10px 5px', color: '#000', fontSize: '16px' }} className="overflow-ellipsis">{type_name}</Col>
              </Row>
              <div className="clearfix" onClick={() => onCloseModal && onCloseModal()}>
                {
                  data && data.map(({ name, id, desc }: any, key: number) => (
                    <Link
                      key={key}
                      to={{
                        pathname: link,
                        state: {
                          type: 'insert',
                          id,
                          name,
                          desc
                        }
                      }}
                      className="templateBox">
                      <Card
                        size="small"
                        hoverable
                        bordered
                        style={{ float: 'left', margin: '0 16px 16px 0', width: 220 }}
                      >
                        <Meta
                          avatar={<Avatar shape="square" icon="user" style={{ background: '#3497FA' }} size={50} />}
                          title={<span style={{ fontSize: 14 }}>{name}</span>}
                          description={<div className="overflow-ellipsis" title={desc}>{desc}</div>}
                        />
                      </Card>
                    </Link>
                  )
                  )
                }
              </div>
            </div>
          )
        })
      }
    </Content>
  )
}
