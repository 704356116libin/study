import * as React from 'react';
import { Layout, Row, Col, Button, Icon, message, Spin } from 'antd';
import Dragula from 'react-dragula';
import { History } from 'history';
import './index.scss';
import { connect } from 'dva';
import req from '../../../../utils/request';

const { Content, Header } = Layout;
interface GroupProps {
  showTemSortList: any,
  temSortList: any,
  history: History,
  saveSortInfo: any,
  listLoading: boolean
}
const NAMESPACE = 'Approval'; // dva model 命名空间
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    listLoading: state.loading.effects[`${NAMESPACE}/queryTemSortList`],
  }
};

const mapDispatchToProps = (dispatch: any) => {
  return {
    showTemSortList: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryTemSortList`,
        payload: params
      });
    },
  }
}

@connect(mapStateToProps, mapDispatchToProps)
export default class Group extends React.Component<GroupProps, any> {
  state = {
    /**
     * 保存排序loading
     */
    sortLoading: false
  }
  componentDidMount() {
    this.props.showTemSortList({
      company_id: '1'
    })
  }
  saveSortInfo = () => {
    this.setState({
      sortLoading: true
    })
    const sortlist = document.body.querySelectorAll('.temSortList .sort-list');
    const sortJson = {}
    for (let index = 0; index < sortlist.length; index++) {
      sortJson[(sortlist[index] as any).dataset.type] = index;
    }
    (async () => {
      const result = await req('/api/c_approval_type_sequence_save', {
        method: 'POST',
        body: {
          sort_json: sortJson
        }
      })
      this.setState({
        sortLoading: false
      })
      if (result.status === 'success') {
        message.success('提交成功');
        this.props.history.push('/work/approval/management');
      } else {
        message.info('服务器繁忙，请稍后再试~')
      }
    })()
  }
  /**
   * 激活拖拽插件
   */
  dragulaDecorator = (componentBackingInstance: any) => {
    if (componentBackingInstance) {

      Dragula([componentBackingInstance]);
    }
  };
  render() {
    const { temSortList, history, listLoading } = this.props;
    const { sortLoading } = this.state;

    return (
      <Layout className="management white">
        <Header className="white">
          <Row className="sort-wrapper">
            <Col span={4}>
              审批类型分组排序
              </Col>
            <Col span={20} className="text-right">
              <Button className="type-btn" onClick={() => { history.push('/work/approval/management') }}>取消排序</Button>
              <Button loading={sortLoading} type="primary" onClick={this.saveSortInfo} style={{ marginRight: '20px' }} >保存排序</Button>
            </Col>
          </Row>
        </Header>
        <Content >
          <Spin spinning={listLoading} delay={300}>
            <div ref={this.dragulaDecorator} className="temSortList">
              {
                temSortList && temSortList.map((item: any, index: any) => {
                  const { type_id, name, all_count } = item;
                  return (
                    <Row className="sort-list cursor-pointer" key={index} data-type={type_id}>
                      <Col span={5}>
                        <Icon type="ordered-list" style={{ paddingRight: '15px' }} className='parmary-color' />
                        类型名称：{name}
                      </Col>
                      <Col span={5}>
                        审批数量：{all_count}
                      </Col>
                    </Row>
                  )
                })
              }
            </div>
          </Spin>
        </Content>
      </Layout>
    )
  }
}