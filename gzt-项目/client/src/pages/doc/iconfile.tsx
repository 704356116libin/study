import React from 'react';
import { Icon } from "antd";

const IconFont = Icon.createFromIconfontCN({
  scriptUrl: '//at.alicdn.com/t/font_1167761_p8fd9c67ngq.js',
  extraCommonProps: {
    className: 'icon-folder'
  }
});

/** 文件类型映射图标 */
export default function IconFile({ type }: { type: string | undefined }) {
  switch (type) {
    case 'folder':
      return <IconFont type="icon-folder" />
    case 'company':
      return <IconFont type="icon-folder-company" />
    case 'txt':
      return <IconFont type="icon-txt" />
    default:
      return <Icon type="file-unknown" theme="filled" />
  }
}