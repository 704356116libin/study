import React, { useState, useEffect } from 'react';
import { Tree, Input, Avatar, Spin } from 'antd';
import { get } from '../../utils/request';
import { AntTreeNodeSelectedEvent, AntTreeNodeCheckedEvent } from 'antd/lib/tree';

const TreeNode = Tree.TreeNode;
const Search = Input.Search;

export interface LinKeyItem {
  key: string;
  title: string;
}
export type LinKey = LinKeyItem[];

const dataList: any[] = [];

/**
 * 递归树结构转化为一维数组
 * @param data 树
 */
function generateList(data: any[]) {
  for (const node of data) {
    const id = node.id;
    const name = node.name;
    dataList.push({ key: id, title: name });
    if (node.children) {
      generateList(node.children);
    }
  }
};

/** 处理部门组织结构数，把user信息和部门都塞到children里 */
function handleDeptTree(tree?: any) {

  if (tree.children) {
    for (const node of tree.children) { // 循环 递归
      handleDeptTree(node)
    }
    if (tree.users) { // 用户信息塞进children
      tree.children = tree.children.concat(tree.users);
    }
  }
}

/**
 * 获取父节点的key
 * @param key 传入的key值 
 * @param tree 整个树
 */
function getParentKey(key: string, tree: any[]): any {
  let parentKey;
  for (const node of tree) {
    if (node.children) {
      // 传进来的key在子节点中找到了，那自身就是父节点
      if (node.children.some((item: any) => item.id === key)) {
        parentKey = node.id;
      } else if (getParentKey(key, node.children)) { // 继续找
        parentKey = getParentKey(key, node.children);
      }
    }
  }
  return parentKey;
}
/** 组织结构树组件props */
export interface OrganizationTreeProps {
  orgnizationDept?: any;
  width?: string | number;
  selectedKeys?: string[];
  onSelect?: (selectedKeys: string[], e: AntTreeNodeSelectedEvent) => void;
  checkable?: boolean;
  onCheck?: (checkedKeys: string[] | { checked: string[]; halfChecked: string[]; }, e: AntTreeNodeCheckedEvent) => void;
  checkedKeys?: string[];
  /** 展示叶子节点 */
  showLeaf?: boolean;
}
/** 组织结构树组件 */
export default function OrganizationTree(props: OrganizationTreeProps) {
  const [expandedKeys, setExpandedKeys] = useState([] as string[]);
  const [autoExpandParent, setAutoExpandParent] = useState(true);
  const [searchValue, setSearchValue] = useState('');
  const [orgnizationDept, setOrgnizationDept] = useState();
  const [loading, setLoading] = useState(false);

  useEffect(() => {

    (async () => {

      let orgnizationDept;
      if (props.orgnizationDept) {
        orgnizationDept = props.orgnizationDept;
      } else {
        setLoading(true);
        orgnizationDept = await get('/api/c_department_getAllTree', {
          params:{
            activation: 1
          }
        });
      }

      setOrgnizationDept(orgnizationDept);

      setExpandedKeys([orgnizationDept.data.id]);
      // 是否展示叶子节点 ( 最下级节点, 具体到这里指员工 )
      props.showLeaf !== false && handleDeptTree(orgnizationDept.data); // 调用

      generateList(orgnizationDept.data.children);

      setLoading(false);

    })();
  }, [props.orgnizationDept, props.showLeaf]);

  /**
   * 展开关闭节点
   * @param expandedKey 
   */
  function onExpand(expandedKey: any) {
    setExpandedKeys(expandedKey);
    setAutoExpandParent(false);
  }

  /** 根据输入的内容进行搜索 */
  function onSearchChange(e: any) {
    const value = e.target.value;
    const expandedKeyss = dataList.map((item) => {
      if (item.title.indexOf(value) > -1) {
        return getParentKey(item.key, orgnizationDept.data.children);
      }
      return null;
    }).filter((item, i, self) => item && self.indexOf(item) === i);
    setSearchValue(value);
    setExpandedKeys(expandedKeyss);
    setAutoExpandParent(true);
  }

  /**
   * 递归渲染 搜索到的值高亮显示
   * @param data 源数据
   * @param label 上级传递过来的linkey
   */
  function loop(data: any[], label: Array<{ key: any, title: any }>) {
    return data.map((item: any) => {
      // 处理搜索到的文字，高亮显示
      const index = item.name.indexOf(searchValue);
      const beforeStr = item.name.substr(0, index);
      const afterStr = item.name.substr(index + searchValue.length);
      const titleText = index > -1 ? (
        <span style={{ display: 'inline-block', verticalAlign: 'top' }}>
          {beforeStr}
          <span style={{ color: '#f50' }}>{searchValue}</span>
          {afterStr}
        </span>
      ) : <span>{item.name}</span>;
      // 员工的话展示头像
      const title = item.type === 'user' ? (
        <span style={{ display: 'inline-block', height: '20px', lineHeight: '20px' }}>
          <Avatar size={20} style={{ marginRight: '6px', verticalAlign: 'top', backgroundColor: '#87d068' }}>
            {item.name.substr(item.name.length - 1, item.name.length)}
          </Avatar>
          {titleText}
        </span>
      ) : titleText;
      // 详细一点的信息
      const keyInfo = {
        type: item.type === 'user' ? 'personnel' : 'department',
        key: `${item.id}`,
        title: item.name
      }
      // 面包屑导航需要用到的数据
      const linKey = [
        ...label,
        keyInfo
      ]
      // 部门且部门下有子部门或者员工
      if (item.children && item.children.length !== 0) {
        return (
          <TreeNode linKey={linKey} keyInfo={keyInfo} key={`${item.id}`} title={titleText}>
            {loop(item.children, linKey)}
          </TreeNode>
        );
      }

      return (
        <TreeNode
          linKey={linKey}
          keyInfo={keyInfo}
          key={`${item.id}`}
          title={title}
        />
      )
    })
  }

  return (

    <div style={{ float: 'left', width: props.width, height: '100%', padding: '10px', background: '#fafafa', overflow: 'auto', boxShadow: '0px 0px 6px 0px #cccccc' }}>
      <Search placeholder="搜索" onChange={onSearchChange} />
      <Spin spinning={loading}>
        {orgnizationDept && (
          <Tree
            autoExpandParent={autoExpandParent}
            selectedKeys={props.selectedKeys}
            expandedKeys={expandedKeys}
            onSelect={props.onSelect}
            onExpand={onExpand}
            checkable={props.checkable}
            onCheck={props.onCheck}
            checkedKeys={props.checkedKeys}
          >
            <TreeNode
              linKey={[
                {
                  key: `${orgnizationDept.data.id}`,
                  title: orgnizationDept.data.name
                }
              ]}
              keyInfo={{
                type: 'organization',
                key: `${orgnizationDept.data.id}`,
                title: orgnizationDept.data.name
              }}
              key={`${orgnizationDept.data.id}`}
              title={orgnizationDept.data.name}
              style={{ color: '#000' }}>
              {loop(orgnizationDept.data.children, [
                {
                  key: `${orgnizationDept.data.id}`,
                  title: orgnizationDept.data.name
                }
              ])}
            </TreeNode>
          </Tree>
        )}
      </Spin>
    </div>
  )
}