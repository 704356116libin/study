import * as React from 'react';
import { Layout, Table, Tabs, Button, } from 'antd';
import "./people.scss";
import request, { get } from '../../utils/request';
import QRCode from 'qrcode.react';
import classnames from "classnames";
import AddInput from './addInput';
import { connect } from 'dva';
import { Modal } from 'antd';
const { Content } = Layout;
const { TabPane } = Tabs;

const NAMESPACE = 'Basis';
const mapStateToProps = (state: any) => {
  return {
    userInfo: state[NAMESPACE].userInfo
  }
}

const renderContent = (value: any, row: any, index: any) => {
  const obj = {
    children: value,
    props: {} as any,
  };
  if (index === 4) {
    obj.props.colSpan = 0;
  }
  return obj;
};
const columns = [{
  width: 150,
  align: "center" as "center",
  title: '使用人数',
  dataIndex: 'title',
  render: (text: any) => <a href="javascript:;">{text}</a>,
}, {
  width: 150,
  render: (text: any, row: any, index: any) => {
    if (index < 4) {
      return <a href="javascript:;">{text}</a>;
    }
    return {
      children: <a href="javascript:;">{text}</a>,
      props: {
        colSpan: 2,
      },
    };
  },

  align: "center" as "center",
  title: '人数价格',
  className: 'column-money',
  dataIndex: 'price',
}, {
  width: 150,
  align: "center" as "center",
  title: '描述',
  dataIndex: 'description',
  render: renderContent
},
]
@connect(mapStateToProps)
export default class People extends React.Component<any, any> {
  state = {
    data: [],
    current_length: 0,
    count: 20,
    visible: false,
    order_no: '',
    sku_id: 1,
  };
  tmp_id = 1;
  timer = 1;
  componentDidMount() {
    (async () => {
      const result = await get('/api/getProduct?product_id=1');
      const order_no = await get('/api/getOrderNo', { resToJson: false });
      this.setState({
        data: result.skus,
        order_no: order_no
      })
    })();
    this.timer = window.setInterval(
        () => this.getOrderPaid(), 3000
    );
  }
  componentWillUnmount() {
    window.clearInterval(this.timer);
  }
  getOrderNo() {
    (async () => {
      const order_no = await get('/api/getOrderNo', { resToJson: false });
      this.setState({
        order_no: order_no
      })
    })()
  }
  getOrderPaid() {
    (async () => {
      const order_paid = await request('/api/getPaidByOrderNo', {
        method: 'POST',
        resToJson: false,
        body: {
          order_no: this.state.order_no,
        }
      });
      console.log(order_paid);
      if (order_paid.code == 1) {
        window.clearInterval(this.timer);
        this.showModal();
      } else if(order_paid.code == 2){
        window.clearInterval(this.timer);
        //已过期，二维码应该蒙上一层遮盖，不让扫描了，需要刷新
      }
    })()
  }
  footSkuChange = (length: any) => {
    this.getOrderNo();
    this.setState({
      length,
      current_length: length
    })
  }
  handleAmountChange = (count: any) => {
    this.getOrderNo();
    if(count <=100){
    this.tmp_id = 1;
    }else if(count <=200){
    this.tmp_id = 2;
    }
    else if(count <=300){
    this.tmp_id = 3;
    }
    else if(count <=400){
    this.tmp_id = 4;
    }
    else if(count <=500){
    this.tmp_id = 5;
    }
    this.setState({
      count,
      sku_id: this.tmp_id
    })
  }
  showModal = () => {
    this.setState({
      visible: true,
    });
  };

  handleOk = (e: any) => {
    console.log(e);
    this.setState({
      visible: false,
    });
  };

  handleCancel = (e: any) => {
    console.log(e);
    this.setState({
      visible: false,
    });
  };
  render() {
    const { userInfo } = this.props;    
    const { data, current_length, count, order_no,sku_id } = this.state;
    const qrcodeurl = `https://pst.pingshentong.com/pay?sku_id=${sku_id}&length=${current_length}&amount=${count}&user_id=${userInfo && userInfo.id}&order_no=${order_no}$t=1111`;
    const price = 299;
    const amount = current_length / 12 * price * count;

    return (
      <Content className="buy-people" style={{ background: "white", padding: '30px 20px' }}>
        <div className="title">
          1、人数标准价格表（超过使用人数<a>请联系我们</a>）
       </div>
        <Table
          size="middle"
          pagination={false}
          columns={columns}
          dataSource={data}
          bordered
        />
        <div className="title">
          3、选购服务

       </div>
        <AddInput onChange={this.handleAmountChange} count={count}> <s>20人起售，以10的倍数递增或递减</s></AddInput>
        <div className="month">
          <Button type="default" className={classnames("btn-month", current_length === 1 ? "active" : "")} onClick={() => this.footSkuChange(1)} >1年</Button>
          <Button type="default" className={classnames("btn-month", current_length === 2 ? "active" : "")} onClick={() => this.footSkuChange(2)} >2年</Button>
          <Button type="default" className={classnames("btn-month", current_length === 3 ? "active" : "")} onClick={() => this.footSkuChange(3)} >3年</Button>
        </div>
        <div className="title">4、支付方式</div>
        <div className="pay-name">
          <Tabs defaultActiveKey="1">
            <TabPane tab="快捷支付" key="1" style={{ padding: '24px' }}>
              <div style={{ overflow: 'hidden' }}>
                <div className="code">
                  <div className="ewm" style={{ marginLeft: '12px' }}>
                    <QRCode value={qrcodeurl} />
                  </div>
                  <div style={{ display: 'block', marginTop: '10px' }}>

                  </div>

                </div>
                <div className="pay">
                  <div className="introduce">
                    应付金额：<a style={{ textDecoration: 'line-through' }}>280元</a>
                  </div>
                  <div className="introduce">
                    实付金额：<a>{amount}元</a>
                  </div>
                  <div className="zhifu"></div>
                  <div className="weixin"></div>
                  <p style={{ fontSize: '14px', paddingTop: '20px' }}>使用支付宝/微信扫码付款</p>
                </div>
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
        <p style={{ margin: '12px 0px 200px 28px' }}>同意为该企业购买，已阅读并确认《工作通补充协议》</p>
        <div>
          <Button type="primary" onClick={this.showModal} >
            支付失败</Button>
        </div>
        <Modal
          title="支付页面提醒"
          visible={this.state.visible}
          onOk={this.handleOk}
          onCancel={this.handleCancel}
          footer={null}
        >
          <p style={{ textAlign: 'center', marginBottom: '60px' }}>扫码支付成功</p>
          <div style={{ textAlign: "center" }}>
            <a href="#" style={{ marginRight: '20px' }}>查看帮助</a>
            <a href="#">返回首页</a>
          </div>
        </Modal>

      </Content>

    )

  }
}
