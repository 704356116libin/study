
import React, { useEffect } from 'react';
import { Layout, Row, Col, Avatar, Tooltip, Icon, Button, Progress } from 'antd';
import { Link } from 'react-router-dom';
import { connect } from 'dva';
import { Dispatch } from 'redux';
import SmsData from './sms';
import PersonnelData from './personnel';
import DiskData from './disk';
import './index.scss';

const { Header, Content } = Layout;
const IconFont = Icon.createFromIconfontCN({
  scriptUrl: '//at.alicdn.com/t/font_1176129_kmrrudd78js.js',
  extraCommonProps: {
    color: 'in'
  }
});
interface StateToHomeProps {
  basisInfo: any;
}
interface DispatchToHomeProps {
  queryBasisInfo: Function;
}
interface HomeProps extends StateToHomeProps, DispatchToHomeProps {
}

const NAMESPACE = 'Basis';
const mapStateToProps = (state: any): StateToHomeProps => ({
  basisInfo: state[NAMESPACE].basisInfo
})
const mapDispatchToProps = (dispatch: Dispatch<any>): DispatchToHomeProps => ({
  queryBasisInfo() {
    dispatch({
      type: `${NAMESPACE}/queryBasisInfo`
    })
  }
})

function Home(props: HomeProps) {

  const { basisInfo, queryBasisInfo } = props;

  useEffect(() => {
    queryBasisInfo()
  }, [])

  return (
    <Layout style={{ marginTop: '64px', overflow: 'hidden' }}>
      <Header className="index-header">
        <Row type="flex" align="middle">
          <Col span={6} className="index-name">
            <Avatar size={64} src={basisInfo && basisInfo.base_info.logo && basisInfo.base_info.logo.url} >{basisInfo && basisInfo.base_info.abbreviation}</Avatar>
            <div>
              {basisInfo && basisInfo.base_info.name}
              <Link to="/information" style={{ marginLeft: 12 }} >编辑</Link>
            </div>
          </Col>
          <Col span={18} className="index-cert">
            {
              basisInfo
                ? basisInfo.base_info.verified === 1
                  ? (
                    <>
                      <Tooltip title="认证后可以自定义企业logo">
                        <span className="index-cert-has">
                          <IconFont style={{ fontSize: 24 }} type="icon-dingzhijiaju" />
                        </span>
                      </Tooltip>
                      <Tooltip title="认证后最高增加20人">
                        <Avatar size={40} style={{ background: '#1890ff' }} icon="team" />
                      </Tooltip>
                      <Tooltip title="认证后最高增加30条">
                        <span className="index-cert-has">
                          <IconFont style={{ fontSize: 24 }} type="icon-message" />
                        </span>
                      </Tooltip>
                      <Tooltip title="认证后最高扩容 3G">
                        <span className="index-cert-has">
                          <IconFont style={{ fontSize: 24 }} type="icon-wangpan" />
                        </span>
                      </Tooltip>
                    </>
                  )
                  : (
                    <>
                      <Tooltip title="可以自定义企业logo">
                        <span className="index-cert-no">
                          <IconFont style={{ fontSize: 24 }} type="icon-dingzhijiaju" />
                        </span>
                      </Tooltip>
                      <Tooltip title="认证后最高增加20人">
                        <Avatar size={40} icon="team" />
                      </Tooltip>
                      <Tooltip title="认证后最高增加30条">
                        <span className="index-cert-no">
                          <IconFont style={{ fontSize: 24 }} type="icon-message" />
                        </span>
                      </Tooltip>
                      <Tooltip title="认证后最高扩容 3G">
                        <span className="index-cert-no">
                          <IconFont style={{ fontSize: 24 }} type="icon-wangpan" />
                        </span>
                      </Tooltip>
                      <Link to="/license">
                        <Button type="primary" style={{ background: '#00AEB7', borderColor: '#00AEB7' }}>申请企业认证</Button>
                      </Link>
                    </>
                  )
                : null
            }
          </Col>
        </Row>
      </Header>
      <Content className="index-con">
        <Row className="index-sec" type="flex" align="middle" style={{ padding: '24px 0' }}>
          <Col span={6} style={{ textAlign: 'center', borderRight: '1px solid #ddd' }}>
            <Progress type="circle" percent={basisInfo ? Math.round(basisInfo.base_limit.staff_number.use_number / basisInfo.base_limit.staff_number.type_number * 100) : 0} />
          </Col>
          <Col span={12} style={{ padding: '0 24px' }}>
            <div style={{ display: 'inline-block', marginRight: '24px' }}>
              <p>当前可用人数</p>
              <span className="index-per">{basisInfo && basisInfo.base_limit.staff_number.type_number}人</span>
            </div>
            <div style={{ display: 'inline-block' }}>
              <p>剩余可用人数</p>
              <span className="index-per">{basisInfo && basisInfo.base_limit.staff_number.type_number - basisInfo.base_limit.staff_number.use_number}人</span>
            </div>
          </Col>
          <Col span={6} style={{ textAlign: 'center' }}>
            <Link to="/buy/people"><Button type="primary" ghost>购买名额</Button></Link>
          </Col>
        </Row>
        <Row className="index-sec text-center" style={{ paddingBottom: '12px' }}>
          <Col span={8}>
            <PersonnelData dataSource={basisInfo && {
              staff_number: basisInfo.base_limit.staff_number,
              partner: basisInfo.base_limit.partner,
              external_contact: basisInfo.base_limit.external_contact
            }} />
            <Link to="/buy/people"><Button type="primary" ghost>扩容</Button></Link>
          </Col>
          <Col span={8}>
            <DiskData dataSource={basisInfo && basisInfo.base_limit.disk} />
            <Link to="/buy/netdisc"><Button type="primary" ghost>扩容</Button></Link>
          </Col>
          <Col span={8}>
            <SmsData dataSource={basisInfo && basisInfo.base_limit.sms} />
            <Link to="/buy/sms"><Button type="primary" ghost>充值</Button></Link>
          </Col>
        </Row>
      </Content>
    </Layout>
  )
}
export default connect(mapStateToProps, mapDispatchToProps)(Home)
