import * as React from 'react';
import { Layout, Input, Select, Spin, Icon, message } from 'antd';
import { connect } from 'dva';
import Templatelist from './Base/Templatelist';
import './approval.scss';

const { Option } = Select;
const { Header } = Layout;
// const Search = Input.Search;

interface CreateApprovalProps {
  showApplyTemplate: any;
  applyTemplateList: any;
  listLoading: boolean;
  showApprovalTypeList: any;
  templateSelectInfo: any;
  showSearchTypeInfo:Function
}
const NAMESPACE = 'Approval'; // dva model 命名空间

const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    listLoading: state.loading.effects[`${NAMESPACE}/queryApplyTemplateList`]
  }
};

const mapDispatchToProps = (dispatch: any) => {
  return {
    showApplyTemplate: (id: number) => {
      dispatch({
        type: `${NAMESPACE}/queryApplyTemplateList`,
        payload: id
      });
    },
    showApprovalTypeList: () => {
      dispatch({
        type: `${NAMESPACE}/queryTemplateSelectList`,
        payload: {}
      });
    },
    showSearchTypeInfo: (param:any) => {
      dispatch({
        type: `${NAMESPACE}/queryTypeTemplateInfo`,
        payload: param
      });
    }
  }
}

@connect(mapStateToProps, mapDispatchToProps)
export default class CreateApproval extends React.Component<CreateApprovalProps, any> {
  state = {
    focus: false,
    searchValue: "",
    typeId: -1
  }
  emitEmpty = () => {
    this.setState({
      focus: false,
      searchValue: ""
    })
  }
  onFocus = () => {
    this.setState({
      focus: true,
    })
  }
  handlePressEnter = (e: any) => {

    const value = e.target.value;
    if (value === "") {
      message.info("请输入类型命名称进行搜索~")
    } else {
      this.props.showSearchTypeInfo({
        keyWords: value
      })
    }
  }
  handleChange = (id: number | string) => {
    if (id === -1) {
      id = 'all'
    }
    this.props.showApplyTemplate({
      type_id: id
    })
  }

  componentDidMount() {
    this.props.showApplyTemplate({ type_id: this.state.typeId === -1 ? 'all' : '' });
  }

  render() {
    const { applyTemplateList, templateSelectInfo, listLoading } = this.props;
    console.log(templateSelectInfo, "templateSelectInfo")
    const { typeId } = this.state;
    return (
      <Layout>
        <Header className="white" style={{ lineHeight: '73px', height: '73px' }}>
          <Input placeholder="请输入项目进行查询"
            onPressEnter={(e) => this.handlePressEnter(e)}
            prefix={<Icon type="search" style={{ color: 'rgba(0,0,0,.25)' }} />}
            style={{ width: '240px', marginLeft: '30px' }}
            allowClear={true}
          />
          <Select style={{ marginLeft: '30px', width: 150 }} defaultValue={typeId} onChange={this.handleChange}>
            <Option value={typeId}>全部</Option>
            {
              templateSelectInfo && templateSelectInfo.map((item: any, index: any) => {
                const { type_id, name } = item;
                return (
                  <Option value={type_id} key={index}>{name}</Option>
                )
              })
            }
          </Select>
        </Header>
        <Spin spinning={listLoading} delay={300}>
          <div style={{ padding: '10px 10px 20px 30px' }}>
            <Templatelist datasource={applyTemplateList} link="/work/approval/template" />
          </div>
        </Spin>
      </Layout>
    )
  }
}