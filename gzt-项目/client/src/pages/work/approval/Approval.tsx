import * as React from 'react';
import { Layout, Spin } from 'antd';
import Approvaldrawer from '../../../components/workmessage/approval/drawer';
import SearchInfo from './Base/SearchInfo';
import SelectInfo from './Base/SelectInfo';
import ButtonInfo from './Base/ButtonInfo';
import ApprovalItem from './Base/ApprovalItem';
import ListHeader from './Base/ListHeader';
import { connect } from 'dva';
import './approval.scss';

/**
 * 待审批/我已审批/我发起的/抄送给我的/归档
 */
const { Content, Header } = Layout;
interface ApprovalProps {
  approvalList: any;
  listLoading: boolean;
  drawerLoading: boolean;
  showApprovalList: any;
  showApprovalTypeList: any;
  showApprovalDetails: any;
  showApprovalDetailInfo: any;
  approvalDetails: any;
  templateSelectInfo: any;
  showPaginationInfo: any;
  match: any

}
const NAMESPACE = 'Approval'; // dva model 命名空间

const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    listLoading: state.loading.effects[`${NAMESPACE}/queryApprovalList`],
    drawerLoading: state.loading.effects[`${NAMESPACE}/queryApprovalDetail`]
  }
};
const mapDispatchToProps = (dispatch: any) => {
  return {
    showApprovalList: (value: any) => {
      dispatch({
        type: `${NAMESPACE}/queryApprovalList`,
        payload: value
      });
    },
    showApprovalDetailInfo: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryApprovalDetail`,
        payload: { params }
      });
    },
    showApprovalTypeList: () => {
      dispatch({
        type: `${NAMESPACE}/queryTemplateSelectList`,
        payload: {}
      });
    }
  }
}
@connect(mapStateToProps, mapDispatchToProps)
export default class Pending extends React.Component<ApprovalProps, any> {
  state = {
    visible: false,
    page_size: 10,  // 每页的条数
    now_page: 1, // 当前页数
    status: "all",
    type_id: 'all',
    version: -1 // 设置版本号，用于判断更新时刷新选择的status,type_id
  }

  componentDidMount() {
    this.showApprovalListInfo();
    this.props.showApprovalTypeList();
  }
  componentDidUpdate(prevPropsType: any) {
    if (this.props.match.params.approvalType !== prevPropsType.match.params.approvalType) {// 判断是否点击左侧标签
      this.showApprovalListInfo();
      this.setState({//设置版本号
        version: Date.now()
      })
    }

  }
  showApprovalListInfo = () => {
    this.props.showApprovalList({
      type: this.props.match.params.approvalType,//根据路由获取类型
      status: "all",
      type_id: "all",
      page: this.state.now_page,
      page_size: this.state.page_size
    });
  }
  showPaginationInfo = ({ now_page, page_size, type }: any) => {
    this.props.showApprovalList({
      type,
      status: "all",
      type_id: "all",
      page: now_page,
      page_size
    });
  }
  showApprovalSelectedList = (type_id: string) => {
    this.props.showApprovalList({
      type: this.props.match.params.approvalType,
      status: this.state.status,
      type_id,
      page: this.state.now_page,
      page_size: this.state.page_size
    });
    this.setState({
      type_id
    })
  }
  showApprovalInfoList = (status: any) => {
    this.props.showApprovalList({
      type: this.props.match.params.approvalType,
      status,
      type_id: this.state.type_id,
      page: this.state.now_page,
      page_size: this.state.page_size
    });
    this.setState({
      status
    })
  }

  showApprovalDetails = (params: any) => {

    this.props.showApprovalDetailInfo({ ...params });
    this.setState({
      visible: true
    })
  }
  render() {

    const approvalType = this.props.match.params.approvalType;
    const { visible, version } = this.state;
    const { approvalDetails, listLoading, drawerLoading, templateSelectInfo, approvalList } = this.props;
    return (
      <Layout className="white">
        <Header className="white">
          <div>
            <SearchInfo />
            <SelectInfo version={version} dataSource={templateSelectInfo} onChangeSelected={this.showApprovalSelectedList} />
            {
              approvalType === "approved" || approvalType === "initiate" || approvalType === "ccApproval"
                ?
                <ButtonInfo version={version} onChangeApprovedInfo={this.showApprovalInfoList} />
                : null
            }

          </div>
        </Header>
        <Content style={{ paddingBottom: '10px' }}>
          <ListHeader headerState={approvalType} />{/* 区分已归档 */}
          <Spin spinning={listLoading} delay={300}>
            <ApprovalItem
              dataSource={approvalList}
              headerState="approvalType"
              ApprovalListInfo={this.showPaginationInfo}
              type={approvalType}
              ApprovalDetailInfo={this.showApprovalDetails}
            />
            {/* 向子组件传值 并接受子组件的值  */}
          </Spin>

          {
            <Approvaldrawer
              visible={visible}
              loading={drawerLoading}
              getContainer="#work-approval"
              onClose={() => this.setState({ visible: false })}
              details={approvalDetails}
              onUpdate={this.props.showApprovalDetailInfo}
              updateList={this.showApprovalListInfo}
            />
          }
        </Content>
      </Layout>
    )
  }
}