import * as React from 'react';
import TextLabel from '../../../components/textLabel';
import { Divider } from 'antd';
import AnnexList from '../../../components/annexList';
import './contents.scss';
export interface ContentProps {
  detailInfo: any,
  browseHistory?: any,
  style?: any
}

export default function Contents(props: ContentProps) {

  const { style, detailInfo } = props;
  const { files, notice } = detailInfo
  const { title, type, organiser, created_at, content, browse_user_count } = notice;

  function browseHistory() {
    props.browseHistory();
  }
  return (
    <div style={style}>
      <div className="noticeDetail">
        <h1>{title}</h1>
        <div className="noticeAll">
          <TextLabel text="发布人" /><span className="header-info">{organiser}</span>
          <TextLabel text="所属栏目" /><span className="header-info">{type}</span>
          <TextLabel text="发布时间" /><span className="header-info">{created_at}</span>
          <span className="cursor-pointer" onClick={browseHistory}><TextLabel text="浏览人数" /><span style={{ color: '#00A0EA' }} >{browse_user_count}</span></span>
        </div>
      </div>
      <div className="noticeContent" dangerouslySetInnerHTML={{ __html: content }} />
      {
        files.length !== 0 && (
          <>
            <Divider />
            <div style={{ padding: '10px 0' }}>
              <AnnexList dataSource={files} />
            </div>
          </>
        )
      }
    </div>
  )
}