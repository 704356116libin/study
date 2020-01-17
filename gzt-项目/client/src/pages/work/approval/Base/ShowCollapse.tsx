import * as React from 'react';
import { Collapse } from 'antd';
const Panel = Collapse.Panel;

interface ShowCollapseProps {
  type: string,
  render: any,
  title: any
}
export default function ShowCollapse({
  type,
  render,
  title
}: ShowCollapseProps) {
  if (type === 'normal') {
    return render
  } else {//或签 ,会签
    const desc = type === 'orSign' ? '以下成员一人审批即可' : '须所有审批人同意';
    return (
      <Collapse defaultActiveKey={['1']} bordered={false}>
        <Panel header={title} key="1" >
          <div>
            <p>{desc}</p>
            {render}
          </div>
        </Panel>
      </Collapse>
    )
  }
}


