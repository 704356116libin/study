
import * as React from 'react';
import { Link } from 'react-router-dom';
import { Icon, Layout } from 'antd';
import classnames from "classnames";
import "./index.scss";

const { Sider } = Layout;
const IconFont = Icon.createFromIconfontCN({
  scriptUrl: '//at.alicdn.com/t/font_1169688_vdsda4jxono.js',
  extraCommonProps: {
    style: { fontSize: '24px', margin: '0 12px', verticalAlign: 'middle' }
  }
});
export default class Buy extends React.Component<any, any> {
  state = { current: 'people' }
  handleChange(current: String) {
    this.setState({
      current
    })
  }
  render() {
    const { current } = this.state;
    return (
      <Layout style={{ marginTop: '64px'}}>
        <Sider theme="light" style={{ padding:'30px' }}>
          <ul>
            <li
              onClick={() => this.handleChange('people')}
              className={classnames("buy-item first", current === 'people' ? "active" : "")}
            >
              <IconFont type="icon-huiyuandengji" />
              <Link to="/buy/people" className="headline"> 人数版</Link>
            </li>
            <li
              onClick={() => this.handleChange('sms')}
              className={classnames("buy-item", current === 'sms' ? "active" : "")}
            >
              <IconFont type="icon-duanxin" />
              <Link to="/buy/sms" className="headline"> 短信服务</Link>
            </li>
            <li
              onClick={() => this.handleChange('netdisc')}
              className={classnames("buy-item", current === 'netdisc' ? "active" : "")}
            >
              <IconFont type="icon-wangpan" />
              <Link to="/buy/netdisc" className="headline"> 网盘扩容</Link>
            </li>
            <li
              onClick={() => this.handleChange('order')}
              className={classnames("buy-item", current === 'order' ? "active" : "")}
            >
              <IconFont type="icon-dingdan" />
              <Link to="/buy/order" className="headline"> 我的订单</Link></li>
              <li
              onClick={() => this.handleChange('invoice')}
              className={classnames("buy-item", current === 'invoice' ? "active" : "")}
            >
              <IconFont type="icon-fapiao" />
              <Link to="/buy/invoice" className="headline"> 我的发票</Link></li>
          </ul>
        </Sider>
        {this.props.children}
      </Layout>

    )
  }

}