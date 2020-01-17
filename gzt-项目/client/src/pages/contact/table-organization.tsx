import React from 'react';
import { Table } from "antd";
import { ColumnProps } from "antd/lib/table";

const columns: ColumnProps<IUser>[] = [
  {
    title: '姓名',
    key: 'name',
    dataIndex: 'name',
  },
  {
    title: '性别',
    key: 'sex',
    dataIndex: 'sex',
  },
  {
    title: '邮箱',
    key: 'email',
    dataIndex: 'email',
  },
  {
    title: '房间号',
    key: 'roomNumber',
    dataIndex: 'roomNumber',
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
  is_enable: number;
  id: string
}


export default function OrganizationTable({ dataSource }: any) {

  return (
    <Table<IUser>
      size="middle"
      columns={columns}
      dataSource={dataSource}
      rowKey={record => record.id}
    />
  )
}


