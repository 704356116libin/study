import React, { useEffect, useState } from 'react';
import { Layout } from 'antd';
import { get } from '../../utils/request';
import handleTime from '../../utils/handleTime';
import getFileTypeBySuffix from '../../utils/getFileTypeBySuffix';

const { Content } = Layout;

const typeMapText = {
  '删除文件': '删除了',
  '上传文件': '上传了'
}

function DynamicDoc(props: any) {

  const [dynamics, setDynamics] = useState([])

  useEffect(() => {
    (async () => {
      const result = await get('/api/oss_fileDynamics');
      setDynamics(result);
    })()
  }, [])

  return (
    <Content style={{ padding: '24px', height: 'calc(100vh - 61px)' }}>
      <div style={{ padding: '24px', background: '#fff' }}>
        {
          dynamics && dynamics.map(({ id, created_at, type, dir, file_name }, index) => {

            const dynamicText = type === '删除目录'
              ? `我 删除了文件夹 "${dir}"`
              : type === '创建目录'
                ? `我 创建了文件夹 "${dir}"`
                : `我 在 "${dir ? dir : '我的文件'}" ${typeMapText[type]} 1 ${getFileTypeBySuffix(file_name) === 'img' ? '张图片' : '个文件'}`

            return (
              <div key={index} style={{ display: 'flex', padding: '12px 0' }}>
                <div key={id} style={{ flex: 'auto' }}>
                  {dynamicText}
                </div>
                <div style={{ flex: '0 0 auto', width: 100 }}>{handleTime(created_at)}</div>
              </div>
            )
          })
        }
      </div>
    </Content>
  )
}

export default DynamicDoc
