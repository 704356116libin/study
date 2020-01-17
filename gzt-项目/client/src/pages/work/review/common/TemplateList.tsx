import * as React from 'react';
import { Card, Avatar } from 'antd';
import { Link } from 'react-router-dom';

interface TemplatelistProps extends React.HTMLAttributes<HTMLDivElement> {
  datasource: {
    count: number;
    data: any[]
    id: string;
    name: string;
    type: string;
  }[],
  pathname: string;
  onItemClick?: (event: React.MouseEvent<HTMLDivElement, MouseEvent>) => void;
}

export default function Templatelist({
  datasource,
  pathname,
  onItemClick,
  ...restProps
}: TemplatelistProps) {

  return (
    <div {...restProps}>
      {
        datasource && datasource.map(({ id, name, count, data }) => (
          count !== 0 && (
            <div key={id}>
              <div style={{ marginBottom: 16 }}>
                {name}（{count}）
                </div>
              <div className="clearfix">
                {data.map(({ id, name, description }) => (
                  <Link
                    key={id}
                    to={{
                      pathname,
                      state: {
                        type: 'INSERT',
                        data: {
                          id,
                          name
                        }
                      }
                    }}
                  >
                    <Card
                      size="small"
                      hoverable
                      bordered
                      style={{ float: 'left', margin: '0 16px 16px 0', width: 220 }}
                      onClick={onItemClick}
                    >
                      <Card.Meta
                        className="review-tempitem"
                        avatar={<Avatar shape="square" size={42} style={{ background: '#1890ff', fontSize: 14 }}>{name && name.substr(0, 2)}</Avatar>}
                        title={<span style={{ fontSize: 14 }}>{name}</span>}
                        description={<div className="overflow-ellipsis" style={{ fontSize: 12 }}>{description}</div>}
                      />
                    </Card>
                  </Link>
                ))}
              </div>
            </div>
          )))
      }
    </div>
  )
}
