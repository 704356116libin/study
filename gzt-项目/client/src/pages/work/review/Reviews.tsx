import * as React from 'react';
import { Layout, List, Row, Col, Select, Input, Tag } from 'antd';
import { Link } from 'react-router-dom';
import { match } from 'react-router-dom'
import { connect } from 'dva';
import TextLabel from '../../../components/textLabel';
import { SelectedValue } from 'antd/lib/select';

const { Header, Content } = Layout;
const { Option } = Select;
const { Search } = Input;

const NAMESPACE = 'Review';

interface ReviewsProps {
  /** 手动补充路由声明 */
  match: match<{ reviewType: string }>;
  /** 评审列表信息 */
  reviews: any[];
  /** 获取评审列表信息 */
  queryReviewsByState: Function;
  /** 获取评审列表信息 增加角色条件 */
  queryReviewsByStateAndRole: Function;
  /** 获取评审列表信息loading */
  loading: boolean;
}

const typeMap = {
  'all': 'all',
  'reviewing': '评审中',
  'pendingReceive': '待接收', //合作伙伴 or 外部联系人待接收状态
  'pendingAssign': '待指派',
  'pendingApproval': '待审核',
  'reviewed': '已完成',
  'archived': '已归档',
  'cancled': '已作废',
  'retracted': '已撤回',
}

const mapStatusToColor = {
  "已完成": "#1890ff",
  "已拒绝": "#f5222d",
  "评审中": '#34d058',
  "待审核": '#dea584',
  "已撤销": '#f00',
  "已作废": '#f00',
  "已撤回": '#1a64ff',
}

const mapStateToProps = (state: any) => {
  return {
    reviews: state[NAMESPACE].reviews,
    loading: state.loading.effects[`${NAMESPACE}/queryReviewsByState`]
  }
}

const mapDispatchToProps = (dispatch: any) => {
  return {
    queryReviewsByState(params: any) {
      dispatch({
        type: `${NAMESPACE}/queryReviewsByState`,
        payload: {
          params
        }
      })
    },
    queryReviewsByStateAndRole(params: any) {
      dispatch({
        type: `${NAMESPACE}/queryReviewsByStateAndRole`,
        payload: {
          params
        }
      })
    }
  }
}


/**
 * 我在评审的
 */
@connect(mapStateToProps, mapDispatchToProps)
export default class Reviews extends React.Component<ReviewsProps, any>{
  state = {
    page: this.props.match.params.reviewType,
    defaultType: 'all'
  }
  // constructor(props: any, ...args: any) {
    // super(props, ...args);
    // props.cacheLifecycles.didCache(this.componentDidCache.bind(this));
    // props.cacheLifecycles.didRecover(this.componentDidRecover.bind(this));
  // }
  componentDidMount() {
    this.props.queryReviewsByState({
      now_page: 1,
      page_size: 10,
      state_type: typeMap[this.props.match.params.reviewType]
    })
  }

  componentDidUpdate(prevProps: any) {
    if (prevProps.match.params.reviewType !== this.props.match.params.reviewType) {

      this.props.queryReviewsByState({
        now_page: 1,
        page_size: 10,
        state_type: typeMap[this.props.match.params.reviewType]
      });
      this.setState({
        defaultType: 'all'
      })
    }
  }

  componentDidCache() {
    this.setState({
      page: this.props.match.params.reviewType
    })

  }
  componentDidRecover() {
    // this.props.location.search = '?type=' + this.state.page;
  }
  /** 按我在当前评审中的角色筛选 */
  handleSelect = (value: SelectedValue) => {
    this.props.queryReviewsByStateAndRole({
      now_page: 1,
      page_size: 10,
      state_type: typeMap[this.props.match.params.reviewType],
      search_type: value
    });
    this.setState({
      defaultType: value
    })
  }

  render() {

    const { reviews, loading } = this.props;
    const { defaultType } = this.state;

    return (
      <Layout className="review-review">

        <Header className="white">
          <Search
            placeholder="请输入协助标题"
            enterButton="搜索"
            style={{ margin: '14px 0 0 30px', width: 240 }}
          />
          <Select style={{ marginLeft: '30px', width: 100 }} value={defaultType} onSelect={this.handleSelect}>
            <Option value="all">全部</Option>
            <Option value="my_publish">我发起的</Option>
            <Option value="my_duty">我负责的</Option>
            <Option value="my_join">我参与的</Option>
            <Option value="cc_my">抄送我的</Option>
          </Select>
        </Header>
        <Content className="white" style={{ overflowY: 'auto', paddingBottom: '10px' }}>
          <header className="review-header">
            <Row style={{ padding: '0 30px' }}>
              <Col span={6}>
                评审概览
              </Col>
              <Col span={2}>
                负责人
              </Col>
              <Col span={4}>
                协作单位
              </Col>
              <Col span={3}>
                当前处理人
              </Col>
              <Col span={3}>
                时长（工作日）
              </Col>
              <Col span={3}>
                发起时间
              </Col>
              {/*<Col span={3}>*/}
              {/*  完成时间*/}
              {/*</Col>*/}
              <Col span={3}>
                状态
              </Col>
            </Row>
          </header>
          <List
            loading={loading}
            dataSource={reviews}
            size="small"
            pagination={{
              style: { textAlign: 'center' },
              pageSizeOptions: ['10', '20', '40'],
              showQuickJumper: true,
              showSizeChanger: true,
              showTotal: total => `共 ${total} 条数据`
            }}
            renderItem={
              ({ id, form_template,duty_user_data,join_form_data,workingDays, created_at, state }: any, k: number) => (
                <Link to={{
                  pathname: `/work/review/detail/${id}`,
                  state: {
                    currentPosition: {
                      now_page: 1,
                      page_size: 10,
                      state_type: typeMap[this.props.match.params.reviewType],
                      search_type: defaultType
                    }
                  }
                }}>
                  <Row className="review-list" type="flex" align="middle" style={{ color: '#333' }}>
                    <Col span={6} className="review-list-title">
                      {
                        // 处理评审通概览
                        (() => {
                          let k = 0;
                          const overview = form_template.map(({ field, value }: any, index: number) => {
                            if (!value) {
                              return null
                            }
                            if (field.name === 'action_label') {
                              return (
                                <div key={field.name}>
                                  <Tag color="#f50">{value}</Tag>
                                </div>
                              )
                            }
                            if (field.label === '送审金额') {
                              return (
                                  <div key={field.name}>
                                    <TextLabel text={field.label} />
                                    <span>{value}元</span>
                                  </div>
                              )
                            }
                            if (k >= 3) {
                              return null
                            }
                            k++;

                            return (
                              <div key={field.name}>
                                <TextLabel text={field.label} />
                                <span>{value}</span>
                              </div>
                            )
                          })
                          return overview
                        })()
                      }
                    </Col>
                    <Col span={2}>
                      {JSON.parse(duty_user_data.duty_form_data).title}
                    </Col>
                    <Col span={4}>
                      协作单位
                    </Col>
                    <Col span={3}>
                      王工
                    </Col>
                    <Col span={3}>
                      {workingDays}天
                    </Col>
                    <Col span={3}>
                      {created_at}
                    </Col>
                    {/*<Col span={3}>*/}
                    {/*  {}*/}
                    {/*</Col>*/}
                    <Col span={3}>
                      {
                        (() => {
                          return <span style={{ color: mapStatusToColor[state] }}>{state}</span>
                        })()
                      }
                    </Col>
                  </Row>
                </Link>
              )
            }
          />
        </Content>
      </Layout>
    );
  }
}
