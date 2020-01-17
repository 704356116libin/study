import React from 'react';
import { Table } from "antd";
import { ColumnProps } from "antd/lib/table";

const columns: ColumnProps<IUser>[] = [
  {
    title: '合作伙伴名称',
    key: 'name',
    dataIndex: 'name',
  },
  {
    title: '联系人',
    key: 'user_name',
    dataIndex: 'user_name',
  },
  {
    title: '邮箱',
    key: 'user_email',
    dataIndex: 'user_email',
  },
  {
    title: '地址',
    key: 'address',
    dataIndex: 'address',
  },
  {
    title: '手机',
    key: 'user_tel',
    dataIndex: 'user_tel',
  }
];

export interface IUser {
  key: number;
  name: string;
  id: string
}


export default function PartnerTable({ dataSource }: any) {

  return (
    <Table<IUser>
      size="middle"
      columns={columns}
      dataSource={dataSource}
      rowKey={record => record.id}
    />
  )
}


