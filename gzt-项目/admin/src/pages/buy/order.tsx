import * as React from 'react';
import { Layout,Tabs, Table, Pagination,} from 'antd';
import "./order.scss";
import {get} from '../../utils/request';
const TabPane = Tabs.TabPane;
const { Content } = Layout;
const columns = [
  { title: '订单编号', dataIndex: 'id', key: 'name' },
  { title: '产品信息', dataIndex: 'title', key: 'age' },
  { title: '服务时间', dataIndex: 'created_at', key: 'address' },
  {
    /*title: '交易金额（元）', dataIndex: '', key: 'x', render: () => <a href="javascript:;">Delete</a>,*/
    title: '交易金额（元）', dataIndex: 'price', key: 'amount'
  },
];
export default class Sms extends React.Component<any, any> {
  state={
    data:[]
  }
  componentDidMount(){
    (async () => {
      const result = await get('/api/getProduct?product_id=3')
      this.setState({
        data:result.skus
      })
    })()
  }
  render() {
    const {data} = this.state
    return (
      <Content style={{background:'white',padding:'30px 20px'}}>
      <Tabs defaultActiveKey="1">
        <TabPane tab="全部订单" key="1">
          <Table
            columns={columns}
            pagination={false}
            dataSource={data}              
          />
          <Pagination defaultCurrent={6} total={500} />
        </TabPane>
        <TabPane tab="待支付" key="2">
        <p style={{color:' #00A0EA',textAlign:'center'}}>抱歉，暂无数据</p>
        </TabPane>
        <TabPane tab="交易完成" key="3">
        <Table
            columns={columns}
            pagination={false}
            dataSource={data}
          />
          <Pagination defaultCurrent={6} total={500} / >
        </TabPane>
        
      </Tabs>
      </Content>
    )
  }
}