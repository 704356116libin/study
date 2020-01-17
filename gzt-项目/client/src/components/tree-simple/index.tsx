import React, { useState, useEffect } from 'react';
import { Tree, Input, Avatar } from 'antd';
import { TreeProps } from 'antd/lib/tree';
import { camelCaseToUnderline } from '../../utils/camelCaseToUnderline';

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
function generateList(data: any[], childrenPropName: string) {
  for (const node of data) {
    const id = node.id;
    const name = node.name;
    dataList.push({ key: id, title: name });
    if (node[childrenPropName]) {
      generateList(node[childrenPropName], childrenPropName);
    }
  }
};

/**
 * 获取父节点的key
 * @param key 传入的key值 
 * @param tree 整个树
 */
function getParentKey(key: string, tree: any[], childrenPropName: string): string | undefined {
  let parentKey;
  for (const node of tree) {
    if (node[childrenPropName]) {
      // 传进来的key在子节点中找到了，那自身就是父节点
      if (node[childrenPropName].some((item: any) => item.id === key)) {
        parentKey = `${node.id}`;
      } else if (getParentKey(key, node[childrenPropName], childrenPropName)) { // 继续找
        parentKey = getParentKey(key, node[childrenPropName], childrenPropName);
      }
    }
  }
  return parentKey;
};

export interface SimpleTreeProps extends TreeProps {
  /** 源数据 */
  dataSource: {
    category: any[],
    [propName: string]: any[]
  };
  /** 一些基本信息 */
  treeInfo: {
    /** 标题 */
    title: string;
    /** 可以选中到右侧的 type 属性名 */
    typePropName: string;
  };
  // 容器宽度
  width?: string | number;
  /** 展示叶子节点 */
  showLeaf?: boolean;
}

/**
 * 组织结构树组件
 */
export default function SimpleTree(props: SimpleTreeProps) {

  const [expandedKeys, setExpandedKeys] = useState([`${props.treeInfo.typePropName}s`]);
  const [searchValue, setSearchValue] = useState('');
  const [dataSource, setDataSource] = useState();

  useEffect(() => {
    if (!props.dataSource) {
      return
    }
    // 把源数据处理成前端可用的数据
    let usableData = [
      ...props.dataSource.category
    ];
    if (props.showLeaf !== false) {
      usableData = usableData.concat(props.dataSource[camelCaseToUnderline(props.treeInfo.typePropName)].map((item: any) => ({ ...item, type: props.treeInfo.typePropName })));
    }
    // 合作伙伴数据
    setDataSource(usableData);
    generateList(usableData, props.treeInfo.typePropName);
  }, [props.dataSource, props.showLeaf, props.treeInfo.typePropName]);
  /** 展开关闭节点 */
  function onExpand(expandedKey: any) {
    setExpandedKeys(expandedKey);
  }
  /** 根据输入的内容进行搜索  */
  function onSearchChange(e: any) {
    const value = e.target.value;
    const nextExpandedKeys = dataList.map((item) => {
      if (item.title.indexOf(value) > -1) {
        return getParentKey(item.key, dataSource, props.treeInfo.typePropName);
      }
      return null;
    }).filter((item, i, self) => item && self.indexOf(item) === i) as string[];
    setSearchValue(value);
    // 设置展开节点keys 自动展开父节点
    setExpandedKeys([`${props.treeInfo.typePropName}s`, ...nextExpandedKeys]);

  }
  /** 递归渲染 搜索到的值高亮显示 */
  function loop(data: any[], label: Array<{ key: any, title: any }>) {
    return data.map(({ id, name, [camelCaseToUnderline(props.treeInfo.typePropName)]: children, type }: any) => {
      const index = name.indexOf(searchValue);
      const beforeStr = name.substr(0, index);
      const afterStr = name.substr(index + searchValue.length);
      const titleText = index > -1 ? (
        <span style={{ display: 'inline-block', verticalAlign: 'top' }}>
          {beforeStr}
          <span style={{ color: '#f50' }}>{searchValue}</span>
          {afterStr}
        </span>
      ) : <span>{name}</span>;
      // 合作伙伴的话展示头像
      const title = type === props.treeInfo.typePropName ? (
        <span style={{ display: 'inline-block', height: '20px', lineHeight: '20px' }}>
          <Avatar size={20} style={{ marginRight: '6px', verticalAlign: 'top', backgroundColor: '#87d068' }}>
            {name.substr(name.length - 1, name.length)}
          </Avatar>
          {titleText}
        </span>
      ) : titleText;
      // 当前节点的一些信息
      const keyInfo = {
        type: children ? 'category' : props.treeInfo.typePropName,
        key: `${id}`,
        title: name
      };
      // 从父节点到当前节点整个相关数据
      const linKey = [
        ...label,
        keyInfo
      ];
      if (children && children.length !== 0) {
        return (
          <TreeNode
            linKey={linKey}
            keyInfo={keyInfo}
            key={`${id}`}
            title={title}
          >
            {loop(children, linKey)}
          </TreeNode>
        )
      }
      return (
        <TreeNode
          linKey={linKey}
          keyInfo={keyInfo}
          key={`${id}`}
          title={title}
        />
      )
    })
  }

  return (
    <div style={{ float: 'left', width: props.width || 280, height: '100%', padding: '10px', background: '#fafafa', overflow: 'auto', boxShadow: '0px 0px 6px 0px #cccccc' }}>
      <Search placeholder="搜索" onChange={onSearchChange} />
      {dataSource && (
        <Tree
          expandedKeys={expandedKeys}
          onExpand={onExpand}
          selectedKeys={props.selectedKeys}
          onSelect={props.onSelect}
          checkable={props.checkable}
          onCheck={props.onCheck}
          checkedKeys={props.checkedKeys}
        >
          <TreeNode
            linKey={[
              {
                key: `${props.treeInfo.typePropName}s`,
                title: props.treeInfo.title
              }
            ]}
            keyInfo={{
              type: `${props.treeInfo.typePropName}s`,
              key: `${props.treeInfo.typePropName}s`,
              title: props.treeInfo.title
            }}
            key={`${props.treeInfo.typePropName}s`}
            title={props.treeInfo.title}
          >
            {
              loop(dataSource, [
                {
                  key: `${props.treeInfo.typePropName}s`,
                  title: props.treeInfo.title
                }
              ])
            }
          </TreeNode>
        </Tree>
      )}
    </div>
  )
}