/**
 * 部门树
 */

import React, { useState, useEffect } from 'react';
import { Tree } from 'antd';
import { AntTreeNodeSelectedEvent } from 'antd/lib/tree';
import classnames from 'classnames'
import './index.scss'
import decryptId from '../../utils/decryptId';
const TreeNode = Tree.TreeNode;

export interface DepartmentTreeProps {
  onChange: any,
  dataSource: any,
  /** 控制树结构的显示隐藏 */
  depVisible?: boolean,
  loading?: any,
  /** 默认选中 */
  defaultSelectedKeys?: any[]
  selectedKeys?: any[];
}


export default function DepartmentTree(props: DepartmentTreeProps) {

  const { onChange, dataSource, depVisible, defaultSelectedKeys } = props;

  // const [autoExpandParent, setAutoExpandParent] = useState(true);

  const [selectedKeys, setSelectedKeys] = useState();

  const [expandedKeys, setExpandedKeys] = useState([decryptId(dataSource.data.id)]);

  useEffect(() => {
    setSelectedKeys(props.selectedKeys)
  }, [props.selectedKeys]);

  useEffect(() => {
    setSelectedKeys(defaultSelectedKeys)
  }, []);


  /**
  * 点击节点 设置选中状态的state selectedKeys
  * @param selecteKeys 
  * @param e 
  */
  function setBreadcrumb(selectedKeys: any[], e: AntTreeNodeSelectedEvent) {
    if (!e.selected) { return }

    setSelectedKeys(selectedKeys);

    onChange && onChange({ selectedKeys, departmentName: e.selectedNodes && e.selectedNodes[0].props.title });
  }
  /**
   * 展开/收起节点
   * @param expandedKey 
   */
  function onExpand(expandedKey: any) {
    setExpandedKeys(expandedKey);
    // setAutoExpandParent(false);
  }
  /**
   * 递归渲染
   */
  function loop(data: any[]) {
    return data.map((item: any) => {
      if (item.children && item.children.length !== 0) {
        return (
          <TreeNode key={decryptId(item.id)} title={item.name}>
            {loop(item.children)}
          </TreeNode>
        )
      } else {//下一级没有children 返回自己
        return (
          <TreeNode key={decryptId(item.id)} title={item.name} />
        )
      }
    })
  }
  return (
    <div className={classnames('deptree', depVisible ? 'show' : 'hide')} >
      <Tree
        // autoExpandParent={autoExpandParent}
        /** （受控）设置选中的树节点*/
        selectedKeys={selectedKeys}
        /** （受控）展开指定的树节点 */
        expandedKeys={expandedKeys}
        /** 点击树节点触发 */
        onSelect={setBreadcrumb}
        /** 展开/收起节点时触发 */
        onExpand={onExpand}

      > {
          dataSource ? (
            <TreeNode
              title={dataSource.data.name}
              key={decryptId(dataSource.data.id)}
            >
              {
                loop(dataSource.data.children)
              }
            </TreeNode >
          ) : null
        }
      </Tree>
    </div>
  )
}