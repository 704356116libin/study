import * as React from 'react';
import { Link } from 'react-router-dom';
import { History } from 'history';
import { Layout, Input, Select, Card, Avatar, Spin } from 'antd';
import { FormComponentProps } from 'antd/lib/form';
import { connect } from 'dva';

interface ReviewTemplatesProps extends FormComponentProps {
  history: History;
  location: any;
  /** 获取模板列表信息loading */
  loading: boolean;
  /** 模板列表信息 */
  templates: any;
  /** 获取模板列表信息 */
  queryTemplates: Function;
  /** 模板分组信息 */
  templateGroup: any[];
  /** 获取模板分组信息 */
  queryTemplateGroup: Function;
}

const { Header, Content } = Layout;
const Option = Select.Option;
const NAMESPACE = 'Review';
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    loading: state.loading.effects[`${NAMESPACE}/queryTemplates`]
  }
}

const mapDispatchToProps = (dispatch: any) => {
  return {
    /** 获取模板列表信息 */
    queryTemplates: (cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/queryTemplates`,
        payload: cb
      });
    },
    /** 获取模板分组信息 */
    queryTemplateGroup: (cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/queryTemplateGroup`,
        payload: cb
      });
    }
  }
}

@connect(mapStateToProps, mapDispatchToProps)
class ReviewTemplates extends React.Component<ReviewTemplatesProps, any> {

  state = {
    enableTemplates: []
  }

  componentDidMount() {

    this.props.queryTemplates((enableTemplates: string[]) => {
      this.setState({
        enableTemplates
      })
    });

    this.props.queryTemplateGroup();
  }

  componentDidUpdate(prevProps: any) {
    // 在别的地方更新模板信息的时候 这里要跟着改变
    if (this.props.templates && prevProps.templates !== this.props.templates) {
      this.setState({
        enableTemplates: this.props.templates.enable
      })
    }
  }
  /** 搜索 */
  onTypeSearch = (value: string) => {
    if (value === '') {
      this.setState({
        enableTemplates: this.props.templates.enable
      })
    }

    // 二层拷贝原始数据
    const cloneEnableTemplates = this.props.templates.enable.map(({ data, ...others }: any) => ({
      ...others,
      data: [...data]
    }))
    // 根据搜索结果设置state
    this.setState({
      enableTemplates: cloneEnableTemplates.filter(({ name, data }: any, k: number) => {
        if (data.length === 0) {
          return false
        }
        if (name.includes(value)) {
          return true
        } else {
          const nextTemps = data.filter(({ name, description }: any) => name.includes(value) || (description && description.includes(value)));
          if (nextTemps.length === 0) {
            return false
          } else {
            cloneEnableTemplates[k].data = nextTemps;
            return true
          }
        }
      })
    })
  }

  onTypeSelect = (value: string) => {
    if (value === '全部') {
      this.setState({
        enableTemplates: this.props.templates.enable
      })
    } else {
      this.setState({
        enableTemplates: this.props.templates.enable.filter(({ name }: any) => name === value)
      })
    }
  }

  render() {

    const { templateGroup, loading } = this.props;
    const { enableTemplates } = this.state;

    return (
      <Layout className="review-Processes">
        <Header style={{ background: '#fff' }}>
          <Input.Search
            placeholder="请输入关键字进行搜索"
            onSearch={this.onTypeSearch}
            style={{ marginLeft: '30px', width: 200 }}
          />
          <Select defaultValue="全部" style={{ marginLeft: '30px', width: 120 }} onChange={this.onTypeSelect}>
            <Option value="全部">全部</Option>
            {templateGroup && templateGroup.map(({ count, name }, i: number) => {
              if (count === 0) {
                return null
              }
              return <Option key={`${i}`} value={name}>{name}</Option>
            })}
          </Select>
        </Header>
        <Content style={{ margin: '20px 0 0 30px' }}>
          <Spin spinning={loading}>
            {
              enableTemplates.map(({ name, count, data }: any, k) => (
                count !== 0 && (
                  <div key={k}>
                    <div style={{ marginBottom: 16 }}>
                      {name}（{count}）
                    </div>
                    <div className="clearfix">
                      {data.map(({ id, name, description }: any, k: number) => (
                        <Link
                          key={k}
                          to={{
                            pathname: '/work/review/initiate',
                            state: {
                              id,
                              name
                            }
                          }}
                        >
                          <Card
                            size="small"
                            hoverable
                            bordered
                            style={{ float: 'left', margin: '0 16px 16px 0', width: 220 }}>
                            <Card.Meta
                              className="review-tempitem"
                              avatar={<Avatar shape="square" size={42} style={{ background: '#1890ff', fontSize: 14 }}>{name.substr(0, 2)}</Avatar>}
                              title={<span style={{ fontSize: 14 }}>{name}</span>}
                              description={<div className="overflow-ellipsis" style={{ fontSize: 12 }}>{description}</div>}
                            />
                          </Card>
                        </Link>
                      ))}
                    </div>
                  </div>
                )))
            }
          </Spin>
        </Content>
      </Layout>
    )
  }
}
export default ReviewTemplates
