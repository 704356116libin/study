import React, { useEffect, useState } from 'react';
import { Layout, List, Avatar, message } from 'antd';
import request, { get } from '../../utils/request';
import getFileTypeBySuffix from '../../utils/getFileTypeBySuffix';
import IconFile from './iconfile';

const { Content } = Layout;

/** 最近使用 */
function RecentlyDoc(props: any) {

  const [list, setList] = useState([]);

  useEffect(() => {
    (async () => {
      const result = await get('/api/oss_recentlyUsed');
      setList(result.filter(({ name }: any) => name !== null));
    })()
  }, [])

  /** 下载文件 */
  async function downloadFile(type: string, file_id: string, company_id: string) {
    if (file_id === null) {
      message.error('该文件不存在！');
      return
    }
    const result = await request('/api/oss_single_file_upload', {
      method: 'POST',
      getFile: true,
      body: {
        type,
        fileIds: [file_id],
        company_id
      }
    })
    let blobUrl = window.URL.createObjectURL(result.blob);
    const a = document.createElement('a');
    a.download = decodeURI(result.headers.get('filename'));//获取文件名
    a.href = blobUrl;
    a.click();
    window.URL.revokeObjectURL(blobUrl);
    message.info('下载成功');
  }
  return (
    <Content style={{ height: 'calc(100vh - 61px)', padding: 24 }}>
      <div style={{ padding: 24, background: '#fff' }}>
        <List
          itemLayout="horizontal"
          dataSource={list}
          renderItem={({ name, path, type, file_id, company_id }: any) => (
            <List.Item actions={[
              <span
                className="primary-color"
                onClick={() => downloadFile(type, file_id, company_id)}
              >下载</span>
            ]}>
              <List.Item.Meta
                avatar={
                  getFileTypeBySuffix(name) === 'img'
                    ? <Avatar src={`https://gzts.oss-cn-beijing.aliyuncs.com/${path}`} />
                    : <IconFile type={getFileTypeBySuffix(name)} />}
                title={<a href="https://ant.design">{name}</a>}
                description={path}
              />
            </List.Item>
          )}
        />
      </div>
    </Content>
  )
}

export default RecentlyDoc
