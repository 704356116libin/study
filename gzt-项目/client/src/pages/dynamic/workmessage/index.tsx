/**
 * 动态 -> 工作通知模块
 *      
 */
import * as React from 'react'
import Reviewmessage from '../../../components/workmessage/review'
import Approvalmessage from '../../../components/workmessage/approval'
import Noticemessage from '../../../components/workmessage/notice'
import AssistWindow from '../../../components/workmessage/assist'
import Communicationmessage from '../../../components/workmessage/communication'
import Reviewdrawer from '../../../components/workmessage/review/drawer'
import Approvaldrawer from '../../../components/workmessage/approval/drawer'
import Noticedrawer from '../../../components/workmessage/notice/drawer'
import Assistdrawer from '../../../components/workmessage/assist/drawer'
import Communicationdrawer from '../../../components/workmessage/communication/drawer'
import { Avatar, Spin, Icon } from 'antd'
import { connect } from 'dva'
import S from './index.module.scss'

interface WorkmessageProps {
  title: string;
  type: string;
  companyId: number | string | undefined;
  companyname: string;
  showDynamicDetail?: any;
  loading?: boolean;
  workmessage?: any,
  /**
   * 是否需要重新获取数据
   */
  needRefresh?: boolean;
}

const NAMESPACE = 'Workdynamics'; // dva model 命名空间

const mapStateToProps = (state: any) => {
  return {
    workmessage: state[NAMESPACE].workmessage,
    loading: state.loading.effects[`${NAMESPACE}/queryDynamicDetail`],
  }
};

const mapDispatchToProps = (dispatch: any) => {
  return {
    showDynamicDetail: (payload: any) => {
      dispatch({
        type: `${NAMESPACE}/queryDynamicDetail`,
        payload
      });
    }
  }
}

@connect(mapStateToProps, mapDispatchToProps)
export default class Workmessage extends React.Component<WorkmessageProps, any>{

  scrollWrapperEl: any = React.createRef();

  state = {
    workType: '',
    details: {},
    approvalDetails: null,
    detailLoading: false,
    messageList: [],
    scrolled: false,
    assistNeedUpdate: false,
    approvalNeedUpdate: false,
    prevScrollHeight: 0,
    canScroll: false,
    currentNoticeCompanyId: ''
  }

  componentDidMount() {

    this.scrollWrapperEl.current.scrollTop = this.scrollWrapperEl.current.scrollHeight;
    // 首次或者有新通知的时候请求
    this.props.showDynamicDetail({
      type: this.props.type,
      company_id: this.props.companyId,
      now_page: 1,
    });

  }

  componentDidUpdate(prevProps: any) {

    if (!this.state.scrolled && !this.props.loading) {  // 滚动条直接滑到最下面
      this.scrollWrapperEl.current.scrollTop = this.scrollWrapperEl.current.scrollHeight;
      this.setState({
        scrolled: true
      })
    }

    if (
      this.props.workmessage.noMore !== true &&
      this.state.canScroll &&
      this.scrollWrapperEl.current.scrollHeight !== this.state.prevScrollHeight
    ) {
      // 处理滚动条位置
      this.scrollWrapperEl.current.scrollTop = this.scrollWrapperEl.current.scrollHeight - this.state.prevScrollHeight;
      this.setState({
        canScroll: false
      })
    }

    // 重新请求
    if (this.props.title !== prevProps.title) {
      this.scrollWrapperEl.current.scrollTop = this.scrollWrapperEl.current.scrollHeight;
      this.props.showDynamicDetail({
        type: this.props.type,
        company_id: this.props.companyId,
        now_page: 1,
      });
    }
  }

  showWorkDetail = (details: any) => {
    this.setState({
      details,
      detailLoading: false
    })
  }

  showWorkDrawer = (workType: string) => {
    this.setState({
      workType,
      detailLoading: true,
      assistNeedUpdate: false
    })
  }
  assistUpdate = (id: number) => {
    this.setState({
      assistNeedUpdate: true
    })
  }
  //审批展示抽屉
  showApprovalDrawer = (workType: string) => {
    this.setState({
      workType,
      detailLoading: true,
      approvalNeedUpdate: false

    })
  }
  //审批显示详情
  showApprovalWorkDetail = (approvalDetails: any) => {
    this.setState({
      approvalDetails,
      detailLoading: false
    })
  }

  //审批更新
  approvalUpdate = (id: number) => {
    this.setState({
      approvalNeedUpdate: true
    })
  }

  // 监听滚动事件
  wrapperScroll = (e: any) => {
    // 必须是大盒子本身滚动条滚动
    if (e.target === this.scrollWrapperEl.current && e.target.scrollTop === 0 && !this.props.workmessage.noMore) {
      this.props.showDynamicDetail({
        type: this.props.type,
        company_id: this.props.companyId,
        now_page: this.props.workmessage.currentPage,
      });
      this.setState({
        canScroll: true,
        prevScrollHeight: this.scrollWrapperEl.current.scrollHeight
      })
    }
  }

  render() {

    const { showWorkDetail, showWorkDrawer, showApprovalWorkDetail, showApprovalDrawer, assistUpdate, approvalUpdate } = this;
    const { title, loading, workmessage } = this.props;
    const { workType, details, approvalDetails, detailLoading, assistNeedUpdate, approvalNeedUpdate, currentNoticeCompanyId } = this.state;
    const { data, noMore } = workmessage;
    const typeMap = {
      'c_pst': (section: any) => (
        <Reviewmessage
          message={section}
          onClick={() => this.setState({ workType: 'c_pst' })}
        />
      ),
      'c_approval': (section: any) => (
        <Approvalmessage
          message={section}
          // onClick={() => this.setState({ workType: 'c_approval' })}
          onApprovalStart={() => showApprovalDrawer('c_approval')} // 展示抽屉设置 loading 不需要更新
          onApprovalEnd={showApprovalWorkDetail}// 展示详情信息
          needUpdate={approvalNeedUpdate} //是否需要更新
        />
      ),
      'c_notice': (section: any) => (
        <Noticemessage message={section} onClick={() => this.setState({
          workType: 'c_notice',
          currentNoticeCompanyId: section.data.company_id
        })} />
      ),
      'c_collaborative': (section: any) => <AssistWindow
        message={section}
        needUpdate={assistNeedUpdate}
        onStart={() => showWorkDrawer('c_collaborative')}
        onEnd={showWorkDetail}
      />,
      '沟通': (section: any) => <Communicationmessage message={section} onClick={() => this.setState({ workType: '沟通' })} />
    }

    return (

      <div ref={this.scrollWrapperEl} style={{ height: '100%', overflowY: 'auto' }} onScroll={this.wrapperScroll}>
        <header className={S.header}>
          <Avatar icon="appstore" size={36} alt="工作" style={{ backgroundColor: '#7586f9' }} />
          <div className={S.des}>
            {title}
          </div>
        </header>
        <div className={S.content}>
          <div className="text-center" style={{ visibility: loading || noMore ? 'visible' : 'hidden' }}>
            {
              noMore
                ? '没有更多了~'
                : <Icon type="loading" />
            }
          </div>
          <Spin spinning={loading} >
            {
              (() => {
                if (data.length === 0) { return };
                const workmessages = [];
                for (let length = data.length - 1, i = length; i >= 0; i--) {
                  const section = data[i];
                  const { type } = section;                  
                  workmessages.push(<section className={S.section} key={i}>{typeMap[type](section)}</section>)
                }
                return workmessages
              })()
            }
          </Spin>
        </div>
        <Reviewdrawer
          visible={'c_pst' === workType}
          onClose={() => this.setState({ workType: '' })}
        />

        {/* 审批抽屉 */}
        <Approvaldrawer
          details={approvalDetails}
          loading={detailLoading}
          visible={'c_approval' === workType}
          onClose={() => this.setState({ workType: '' })}
          onUpdate={approvalUpdate}
        />

        <Noticedrawer companyId={currentNoticeCompanyId} visible={'c_notice' === workType} onClose={() => this.setState({ workType: '' })} />
        <Assistdrawer
          onUpdate={assistUpdate}
          details={details}
          loading={detailLoading}
          visible={'c_collaborative' === workType}
          drawerClose={() => this.setState({ workType: '' })}
        />
        <Communicationdrawer visible={'沟通' === workType} onClose={() => this.setState({ workType: '' })} />
      </div>
    )
  }
}