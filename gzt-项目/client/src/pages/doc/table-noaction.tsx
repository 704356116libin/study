import React from 'react';
import { Table, Avatar } from "antd";
import handleSize from "../../utils/handleSize";
import getFileTypeBySuffix from "../../utils/getFileTypeBySuffix";
import IconFile from "./iconfile";

export interface NoactionTableProps {
  dataSource: any[],
  /** 文件夹改变 */
  onDirectoryChange: Function;
  /** 切换公司 */
  onCompanyChange: Function;
}

/** 没有操作的表格 */
export default function NoactionTable(props: NoactionTableProps) {

  const { dataSource, onDirectoryChange, onCompanyChange } = props;

  const columns = [{
    title: '名称',
    dataIndex: 'name',
    render: (text: string, record: any, index: number) => {

      const fileType = getFileTypeBySuffix(text);

      const icon = record.type === 'folder'
        ? <IconFile type="folder" />
        : record.type === 'company'
          ? <IconFile type="company" />
          : fileType === 'img'
            ? <Avatar src={record.oss_path} />
            : <IconFile type={fileType} />

      return (
        <div
          className="filename"
          onClick={() => record.type === 'folder'
            ? onDirectoryChange(text)
            : record.type === 'company'
              ? onCompanyChange(text, record.id)
              : null
          }
        >
          {icon}
          <span className="filename-text">{text}</span>
        </div>
      )
    }
  }, {
    title: '大小',
    dataIndex: 'size',
    width: 100,
    align: 'center' as const,
    render: (text: number) => text && handleSize(text)
  }];

  return (
    <Table
      showHeader={false}
      pagination={false}
      columns={columns}
      dataSource={dataSource}
      className="beautiful-scroll-bar"
      style={{ height: 230 }}
      rowKey={(_, index) => `${index}`}
    />
  )
}