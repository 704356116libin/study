import * as React from 'react'
import { Drawer, Row, Col, Spin, Modal } from 'antd'
import { connect } from 'dva';
import { dropByCacheKey, getCachingKeys } from 'react-router-cache-route';
import decryptId from '../../../utils/decryptId';
import { History, Location } from 'history';
import { withRouter, match } from 'react-router';
import './index.scss'

/**
 * 动态 -> 工作通知模块 -> 公告 -> 抽屉
 */
// 在动态模块中，应该先划出抽屉，然后给个loading状态，等数据拿到后展示，而不是等数据拿到后再滑出抽屉
const NAMESPACE = 'Notice';
const WORKBENCH = 'Workbench';
const USERINFO = 'UserInfo';
interface NoticeMessageProps {
  onClose: any,
  visible: any,
  showNoticeInfo?: any,
  noticeContent?: any,
  loading?: boolean,
  companyId?: string;
  queryCompanys?: Function;
  changeCompany?: Function;
  queryUserPermission?: Function;
  history: History;
  location: Location;
  match: match;
}
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    loading: state.loading.effects[`${NAMESPACE}/queryNoticeContent`],
  }
};
const mapDispatchToProps = (dispatch: any) => ({
  queryCompanys: (cb: Function) => {
    dispatch({
      type: `${WORKBENCH}/queryCompanys`,
      payload: { cb }
    });
  },
  changeCompany: (id: string | number, cb: Function) => {
    dispatch({
      type: `${WORKBENCH}/changeCompany`,
      payload: { id, cb }
    });
  },
  queryUserPermission: () => {
    dispatch({
      type: `${USERINFO}/queryUserPermission`
    });
  }
});

class Noticemessage extends React.Component<NoticeMessageProps, any>{

  /** 切换公司 */
  companyChange = (id: string, cb: any) => {
    this.props.changeCompany && this.props.changeCompany(id, () => {
      // 清除所有缓存页面
      for (const cacheKey of getCachingKeys()) {
        dropByCacheKey(cacheKey)
      }
      this.props.queryUserPermission && this.props.queryUserPermission();
      cb();
    })
  }

  /** 查看详情 */
  lookDetail = (id: string) => {
    this.props.queryCompanys && this.props.queryCompanys((data: any) => {
      if (this.props.companyId && data.current_company.id && decryptId(data.current_company.id) === decryptId(this.props.companyId)) {
        this.props.history.push({
          pathname: `/work/notice/details/${id}`,
          state: {
            type: 'fromDynamic'
          }
        });
      } else {
        Modal.confirm({
          title: '温馨提示',
          content: '你当前查看的公告所在的公司不是当前的默认公司，确定切换默认公司并查看吗？',
          onOk: () => {
            this.companyChange(this.props.companyId as any, () => {
              this.props.history.push(`/work/notice/details/${id}`);
            });
          }
        });
      }
    })
  }

  render() {
    const { onClose, visible, noticeContent, loading, companyId } = this.props;
    console.log(companyId, 555);
    return (
      <Drawer
        title="公告"
        placement="right"
        mask={false}
        onClose={onClose}
        visible={visible}
        width="660"
        getContainer=".dynamic-content"
        className="notice-drawer"
      >
        <Spin spinning={loading}>
          {
            (() => {
              if (noticeContent) {
                const { notice } = noticeContent;
                const { id, content, organiser, title, type, created_at } = notice;
                return (
                  <>
                    {/* 文档碎片 */}
                    <div style={{ borderBottom: '1px solid #eee' }}>
                      <div>
                        <div style={{ padding: '5px 0', fontSize: '20px' }}>{title}</div>
                        <div style={{ padding: '10px 0' }}>
                          <span style={{ display: 'incline-block', padding: '5px 10px', background: '#70BEEA', color: '#fff', borderRadius: '3px' }}>{type}</span>
                        </div>
                        <div style={{ padding: '5px 0' }} className="clearfix">
                          <Row>
                            <Col span={5}>{organiser}</Col>
                            <Col span={5} offset={2}><span style={{ color: '#00A0EA' }}>浏览人数: <span>6人</span></span></Col>
                          </Row>
                        </div>
                        <div style={{ padding: '5px 0', marginBottom: '10px' }} className="clearfix">
                          <Row>
                            <Col span={5}>{created_at}</Col>
                            <Col span={5} offset={2}>
                              <span className="primary-color" style={{ cursor: 'pointer' }} onClick={() => this.lookDetail(id)}>查看详情</span>
                            </Col>
                          </Row>
                        </div>
                      </div>
                    </div>
                    <div style={{ padding: '15px' }}>
                      <div dangerouslySetInnerHTML={{ __html: content }} />
                    </div>
                  </>
                )
              } else {
                return null;
              }
            })()
          }
        </Spin>
      </Drawer>
    )
  }
}

export default withRouter<NoticeMessageProps, any>(connect(mapStateToProps, mapDispatchToProps)(Noticemessage))