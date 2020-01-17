import React from 'react';
import { Table } from "antd";
import { ColumnProps } from "antd/lib/table";

const columns: ColumnProps<IUser>[] = [
  {
    title: '联系人',
    key: 'name',
    dataIndex: 'name',
  },
  {
    title: '邮箱',
    key: 'email',
    dataIndex: 'email',
  },
  {
    title: '地址',
    key: 'address',
    dataIndex: 'address',
  },
  {
    title: '手机',
    key: 'tel',
    dataIndex: 'tel',
  }
];

export interface IUser {
  key: number;
  name: string;
  id: string
}


export default function ExternalContactTable({ dataSource }: any) {

  return (
    <Table<IUser>
      size="middle"
      columns={columns}
      dataSource={dataSource}
      rowKey={record => record.id}
    />
  )
}


