import React from 'react'; // , { useState } 
import { List, Icon } from 'antd';
import last from 'lodash/last'

/**
 * 根据文件后缀判断文件类型
 * @param suffixName 文件后缀
 */
function fileType(suffixName: string | undefined) {
  if (!suffixName) { return 'unknown' }
  const word = ['doc', 'docx'];
  const excel = ['xls', 'xlsx'];
  const ppt = ['ppt', 'pptx'];
  if (word.includes(suffixName)) {
    return 'word'
  } else if (excel.includes(suffixName)) {
    return 'excel'
  } else if (ppt.includes(suffixName)) {
    return 'ppt'
  } else if (suffixName === 'pdf') {
    return 'pdf'
  } else if (suffixName === 'txt') {
    return 'text'
  } else {
    return 'unknown'
  }
}

/**
 * 文件类型映射图标
 */
const typeMapIcon = {
  word: <Icon type="file-word" theme="filled" style={{ padding: '4px 0', fontSize: '36px', color: '#30a1e8' }} />,
  excel: <Icon type="file-excel" theme="filled" style={{ padding: '4px 0', fontSize: '36px', color: '#28a56f' }} />,
  ppt: <Icon type="file-ppt" theme="filled" style={{ padding: '4px 0', fontSize: '36px', color: '#f34e19' }} />,
  pdf: <Icon type="file-pdf" theme="filled" style={{ padding: '4px 0', fontSize: '36px', color: '#ff5562' }} />,
  text: <Icon type="file-text" theme="filled" style={{ padding: '4px 0', fontSize: '36px' }} />,
  unknown: <Icon type="file-unknown" theme="filled" style={{ padding: '4px 0', fontSize: '36px' }} />,
}

export interface AnnexListProps {
  /**
   * 附件信息列表
   */
  dataSource: any[]
}

export default function AnnexList({
  dataSource,
}: AnnexListProps) {

  return (
    <List
      split={false}
      size="small"
      itemLayout="horizontal"
      dataSource={dataSource}
      renderItem={(item: any) => (
        <List.Item>
          <List.Item.Meta
            avatar={typeMapIcon[fileType(last(item.name.split('.')))]}
            title={<a target="_blank" rel="noopener noreferrer" title="点击预览" href={item.oss_path}>{item.name}</a>}
            description={
              <span style={{ color: '#00a0ea' }}>
                <span style={{ marginRight: '12px', cursor: 'pointer' }}>下载</span>
                {/* <span style={{ marginRight: '12px', cursor: 'pointer' }}>转发</span> */}
                <span style={{ marginRight: '12px', cursor: 'pointer' }}>存网盘</span>
                <span style={{ marginRight: '12px', cursor: 'pointer' }}>访问记录</span>
              </span>
            }
          />
        </List.Item>
      )}
    />
  )
}