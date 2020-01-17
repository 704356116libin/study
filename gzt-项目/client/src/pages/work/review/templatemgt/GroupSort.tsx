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
  queryTemplateGroup: Function;
  queryTemplates: Function;
  /** 模板分组列表 */
  templateGroup: any[];
}

const NAMESPACE = 'Review';
const mapStateToProps = (state: any) => {
  return {
    templateGroup: state[NAMESPACE].templateGroup
  }
};
const mapDispatchToProps = (dispatch: any) => {
  return {
    queryTemplateGroup: (cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/queryTemplateGroup`,
        payload: cb
      });
    },
    /** 获取模板列表信息 (用于刷新) */
    queryTemplates: () => {
      dispatch({
        type: `${NAMESPACE}/queryTemplates`
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
    if (!this.props.templateGroup) {
      /** 请求数据时loading */
      this.setState({
        queryLoading: true
      })
      this.props.queryTemplateGroup(() => {
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
      const result = await request('/api/c_pst_sort_pst_template_type', {
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
        this.props.history.push('/work/review/templatemgt');
        this.props.queryTemplates();
        this.props.queryTemplateGroup();
      } else {
        message.error('服务器错误，请稍后再试');
      }
    })()
  }

  render() {
    const { templateGroup } = this.props;
    const { loading, queryLoading } = this.state;

    return (
      <Layout className="review-group-sort" >
        <Row className="group-sort-header">
          <Col span={4}>
            <span className="group-sort-title">评审流程分组排序</span>
          </Col>
          <Col span={20} className="text-right">
            <Button onClick={() => this.props.history.replace('/work/review/templatemgt')}>取消</Button>
            <Button loading={loading} type="primary" style={{ marginLeft: '30px' }} onClick={this.saveGroupSort}>保存排序</Button>
          </Col>
        </Row>
        <Spin wrapperClassName="spin-scroll-wrapper" spinning={queryLoading}>
          <div ref={this.dragulaDecorator} className="group-sort-list">
            {
              templateGroup && templateGroup.map((item: any, index: any) => {
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