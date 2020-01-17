import React from 'react';
import { Row, Col, Layout, Button, Icon, message, Spin } from 'antd';
import Dragula from 'react-dragula';
import { connect } from 'dva';
import './groupSort.scss'
import request from '../../../../utils/request';

export interface GroupSortProps {
  /**
   * 数据
   */
  params: any;
  history: any;
  /** 获取模板分组列表信息 */
  queryProcessGroup: Function;
  queryProcesses: Function;
  /** 模板分组列表 */
  processGroup: any[];
}

const NAMESPACE = 'Review';
const mapStateToProps = (state: any) => {
  return {
    processGroup: state[NAMESPACE].processGroup
  }
};
const mapDispatchToProps = (dispatch: any) => {
  return {
    queryProcessGroup: (cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/queryProcessGroup`,
        payload: cb
      });
    },
    /** 获取流程列表信息 (用于刷新) */
    queryProcesses: () => {
      dispatch({
        type: `${NAMESPACE}/queryProcesses`
      });
    }
  }
}
@connect(mapStateToProps, mapDispatchToProps)
/**
 * 分组下具体流程item
 */
export default class GroupSort extends React.Component<GroupSortProps, any>{

  sortedProcessWrapperEl: any = React.createRef();
  state = {
    loading: false,
    queryLoading: false
  }

  componentDidMount() {

    /** 首次请求 之后用缓存的数据 */
    if (!this.props.processGroup) {
      /** 请求数据时loading */
      this.setState({
        queryLoading: true
      })
      this.props.queryProcessGroup(() => {
        this.setState({
          queryLoading: false
        })
      });
    }

  }

  /**
   * 注册拖拽事件
   * @param sortedProcessWrapper 原生DOM
   */
  dragulaDecorator = (sortedProcessWrapper: any) => {
    if (sortedProcessWrapper) {
      Dragula([sortedProcessWrapper]);
      this.sortedProcessWrapperEl.current = sortedProcessWrapper;
    }
  }
  /**
   * 保存拖拽后的顺序
   */
  saveGroupSort = () => {
    this.setState({
      loading: true
    });
    const sortJson = {};
    const groupList = (this.sortedProcessWrapperEl.current as any).getElementsByClassName('group-item');
    for (let i = 0; i < groupList.length; i++) {
      sortJson[groupList[i].dataset.type] = i;
    }
    (async () => {
      const result = await request('/api/c_pst_sort_process_template_type', {
        method: 'POST',
        body: {
          sort_json: sortJson
        }
      });
      this.setState({
        loading: false
      });
      if (result.status === 'success') {
        message.success('保存成功');
        this.props.history.push('/work/review/processmgt');
        this.props.queryProcesses();
        this.props.queryProcessGroup();
      } else {
        message.error('服务器错误，请稍后再试');
      }
    })()
  }

  render() {
    const { processGroup } = this.props;
    const { loading, queryLoading } = this.state;

    return (
      <Layout className="review-group-sort" >
        <Row className="group-sort-header">
          <Col span={4}>
            <span className="group-sort-title">评审流程分组排序</span>
          </Col>
          <Col span={20} className="text-right">
            <Button onClick={() => this.props.history.replace('/work/review/processmgt')}>取消</Button>
            <Button loading={loading} type="primary" style={{ marginLeft: '30px' }} onClick={this.saveGroupSort}>保存排序</Button>
          </Col>
        </Row>
        <Spin wrapperClassName="spin-scroll-wrapper" spinning={queryLoading}>
          <div ref={this.dragulaDecorator} className="group-sort-list">
            {
              processGroup && processGroup.map((item: any, index: any) => {
                const { id, name, count } = item;
                return (
                  <Row className="group-item" key={index} data-type={id}>
                    <Icon type="ordered-list" style={{ padding: '0 10px' }} />
                    <span>{name}</span>
                    <span style={{ color: '#888' }}>（{count}）</span>
                  </Row>
                )
              })
            }

          </div>
        </Spin>
      </Layout>
    )
  }
}