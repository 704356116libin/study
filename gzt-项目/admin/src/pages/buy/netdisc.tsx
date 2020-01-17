
import * as React from 'react';
import { Layout, Row, Col, Tabs, Button } from 'antd';
import "./sms.scss";
import { get } from '../../utils/request';
import QRCode from "qrcode.react";
import classnames from "classnames";
import { connect } from 'dva';
import { Modal } from 'antd';

const { Content } = Layout;
const { TabPane } = Tabs;
const mapAmout = {
  "5000条": 5000,
  "1万条": 10000,
  "2万条": 200000,
}
const NAMESPACE = 'Basis';
const mapStateToProps = (state: any) => {
  return {
    userInfo: state[NAMESPACE].userInfo
  }
}
@connect(mapStateToProps)
export default class Sms extends React.Component<any, any> {
  state = {
    data: [],
    sku_id: 0,
    amount: 0,
    length: 0,
    current_sku: 0,
    current_length: 0,
    current_price:0,
    visible: false,

  }
  componentDidMount() {
    (async () => {
      const result = await get('/api/getProduct?product_id=2')
      this.setState({
        data: result.skus

      })
    })()
  }
  handleSkuChange = (id: any, title: any, price:any) => {
    console.log(title)
    this.setState({
      sku_id: id,
      amount: mapAmout[title],
      current_sku: id,
      current_price: price
    })
  }
  footSkuChange = (length: any) => {
    this.setState({
      length,
      current_length: length
    })
  }
  showModal = () => {
    this.setState({
      visible: true,
    });
  };

  handleOk = (e:any) => {
    console.log(e);
    this.setState({
      visible: false,
    });
  };

  handleCancel = (e:any) => {
    console.log(e);
    this.setState({
      visible: false,
    });
  };
  render() {
    const { userInfo } = this.props;
    const { data, sku_id, amount, length, current_sku, current_length,current_price} = this.state;
    const qrcodeurl = `https://pst.pingshentong.com/pay?sku_id=${sku_id}&amount=${amount}&length=${length}&user_id=${userInfo && userInfo.id}`;
    const pay = current_length/6*current_price;   
    return (
      <Content className="buy-sms" style={{padding:'30px 20px', background: 'white' }}>

        <div className="title">1、购买条数</div>
        <div className="gutter-example">
          <Row gutter={16}>
            {
              data.map((item: any, index: any) => {
                const { title, id, price} = item;
                return (
                  <Col key={index} span={6} onClick={() => this.handleSkuChange(id, title, price)} >
                    <div className={classnames("gutter-box", current_sku === id ? "active" : "")}>
                      <div className="number">{title}</div>
                      <div className="money">{price}元</div>
                      <div className="picture picture-1"></div>
                    </div>
                  </Col>
                )
              })
            }
          </Row>
        </div>
        <div className="title">2、购买时长</div>
        <div className="duration">
          <div className={classnames("btn-month", current_length === 6 ? "active" : "")} onClick={() => this.footSkuChange(6)}>6个月</div>
          <div className={classnames("btn-month", current_length === 12 ? "active" : "")} onClick={() => this.footSkuChange(12)}>12个月</div>
        </div>
        <div className="title">3、支付方式</div>
        <div className="pay-name">
          <Tabs defaultActiveKey="1">
            <TabPane tab="快捷支付" key="1" style={{ padding: '24px' }}>
            <div className="code">
                <div className="ewm" style={{ marginLeft:'12px'}}>
                <QRCode value={qrcodeurl}/>
                </div>
                <div style={{ display:'block',marginTop:'10px'}}>
                
                </div>
                
              </div>
              <div className="pay">
                <div className="introduce">
                应付金额：<a style={{textDecoration:'line-through'}}>280元</a>
              </div>
              <div className="introduce">
                实付金额：<a>{pay}元</a>
              </div>
              <div className="zhifu"></div>
              <div className="weixin"></div> 
              <p style={{fontSize:'14px',paddingTop:'20px'}}>使用支付宝/微信扫码付款</p>
              </div>
            </TabPane>
            <TabPane tab="对公转账" key="2">
              <div style={{ padding: '24px' }}>
                <div>
                  <span className="heading">收款公司名:</span>
                  <span className="content">长葛市探知科技有限公司</span>
                </div>
                <div>
                  <span className="heading">收款账户名:</span>
                  <span className="content">1708326009100002420</span>
                </div>
                <div>
                  <span className="heading">账户号名称:</span>
                  <span className="content">长葛市探知科技有限公司</span>
                </div>
                <div>
                  <span className="heading">应付金额：</span>
                  <span className="price">
                    <a>280</a>元 原价：
               <span>280元</span>
                  </span>
                </div>
                <div>
                  <span className="heading">
                    <Button type="primary">确认对公转账</Button>
                  </span>
                  <span className="date">
                    到账时间：广发1~2天，跨行3~5天
              </span>
                </div>
                <div>
                  <p className="prompt">
                    温馨提示：线下汇款请直接向您在评审通的专属账户汇款，系统会将汇款直接匹配到您的评审通订单。
                  </p>
                  <p className="prompt">如果您有任何疑问、请及时与客服沟通处理。</p>
                  <p className="prompt">邮箱：pingshentong@163.com</p>
                  <p className="prompt">联系电话：0374-6700996</p>
                </div>
              </div>
            </TabPane>
          </Tabs>
        </div>
        <p style={{ marginTop: '12px', marginLeft: '28px' }}>同意为该企业购买，已阅读并确认《工作通补充协议》</p>
        <div>
          <Button type="primary" onClick={this.showModal} >
          支付成功</Button>
        </div>
        <Modal
          title="转账后提交凭证>5天内为用户充值>用户自行购买服务"
          visible={this.state.visible}
          onOk={this.handleOk}
          onCancel={this.handleCancel}
          footer={null}
        ><div>
          <span className='project' style={{display: 'inline-block',width: '120px',verticalAlign:' middle',marginBottom: '15px'}}>转账金额：</span>
          <span className='detail' style={{display: 'inline-block',width: '220px',verticalAlign: 'middle',marginBottom: '15px'}}>280元</span>
          </div>
          <div>
          <span className='project' style={{display: 'inline-block',width: '120px',verticalAlign:' middle',marginBottom: '15px'}}>收款公司名：</span>
          <span className='detail' style={{display: 'inline-block',width: '220px',verticalAlign: 'middle',marginBottom: '15px'}}>长葛市探知科技有限公司</span>
          </div>
          <div>
          <span className='project' style={{display: 'inline-block',width: '120px',verticalAlign:' middle',marginBottom: '15px'}}>收款账户号：</span>
          <span className='detail'  style={{display: 'inline-block',width: '220px',verticalAlign: 'middle',marginBottom: '15px'}}>1708326009100002420</span>
          </div>
          <div>
          <span className='project' style={{display: 'inline-block',width: '120px',verticalAlign:' middle',marginBottom: '15px'}} >账户开户行：</span>
          <span className='detail'  style={{display: 'inline-block',width: '220px',verticalAlign: 'middle',marginBottom: '15px'}}>工商银行长葛支行金桥路分理处</span>
          </div>
          <div>
          <span className='project' style={{display: 'inline-block',width: '120px',verticalAlign:' middle',marginBottom: '15px'}}>付款凭证：</span>
          <span className='detail'  style={{display: 'inline-block',width: '220px',verticalAlign: 'middle',marginBottom: '15px'}}>上传凭证</span>
          </div>
          <div>
          <span className='project' style={{display: 'inline-block',width: '120px',verticalAlign:' middle',marginBottom: '15px'}}>付款人户名：</span>
          <input type="text" placeholder="请输入户名" style={{ border:'1px solid #cccccc'}}/>
          </div>
          <div>
          <span className='project' style={{display: 'inline-block',width: '120px',verticalAlign:' middle',marginBottom: '15px'}}>联系手机号：</span>
          <input type="text" placeholder="请输入联系手机号" style={{ border:'1px solid #cccccc'}}/>
          </div>
          <div>
          <span className='project' style={{display: 'inline-block',width: '120px',verticalAlign:' middle',marginBottom: '15px'}}>备注说明：</span>
          <input type="text" placeholder="请输入备注说明" style={{ width:'280px' ,height:"100px",border:'1px solid #cccccc'}}/>
          </div>
        
        </Modal>
        
      </Content>

    )
  }

}