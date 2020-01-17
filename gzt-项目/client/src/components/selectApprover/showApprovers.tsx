import React, { forwardRef, Ref, useImperativeHandle, Fragment } from 'react';
import PersonnelAvatar from "../personnelAvatar";
import { Icon } from 'antd';

const signMap = {
  countersign: '会签',
  orSign: '或签'
}
/**
 * 仅仅展示审批人员组件
 * @param param0 
 */
function ShowApprovers({ approvers, onClick }: any, ref: Ref<any>) {

  useImperativeHandle(ref, () => ({}));

  return (
    <div>
      {
        approvers && approvers.map(({ type, checkedInfo: { checkedKeys, checkedPersonnels } }: any, index: number) => {
          const { title } = checkedPersonnels[0];
          const count = checkedPersonnels.length;
          return (
            <Fragment key={index}>
              <PersonnelAvatar
                avatarText={type === 'normal' ? title : signMap[type]}
                name={type === 'normal' ? title : `${count}名成员`}
                close={false}
                onClick={() => onClick && onClick({
                  type,
                  checkedInfo: {
                    checkedKeys,
                    checkedPersonnels
                  }
                })}
              />
              {index !== approvers.length - 1 ?
                <Icon type="arrow-right" style={{ padding: '0 10px', color: '#ccc', fontSize: "18px" }} />
                : null
              }
            </Fragment>
          )
        })
      }
    </div>
  )
}
export default forwardRef(ShowApprovers)