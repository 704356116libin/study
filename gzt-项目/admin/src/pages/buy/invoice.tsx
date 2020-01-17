import * as React from 'react';
import {Layout, Tabs, Table ,Button, Form, Input,Radio} from 'antd';
import "./invoice.scss";
const TabPane = Tabs.TabPane;
const { Content } = Layout;
// const RadioGroup = Radio.Group;

const columns = [
  { title: '申请时间', dataIndex: 'name', key: 'name', },
  { title: '发票抬头', dataIndex: 'age', key: 'age' },
  { title: '商品名称', dataIndex: 'address', key: 'address' },
  { title: '发票金额', dataIndex: '', key: 'x', render: () => <a href="javascript:;">Delete</a>,},
  { title: '发票性质', dataIndex: 'nature', key: 'nature' },
  { title: '发票状态', dataIndex: 'status', key: 'status' }
];
const data = [
  {
    key: 1, name: '2018-9-13 18:21:36', age: '某某神探科技有限公司', address: '2019-1-12至2020-1-12', description: '280',status:'处理中',nature:'电子',amount:''
  },
  {
    key: 2, name: '2018-9-13 18:21:36', age: '某某神探科技有限公司', address: '2019-1-12至2020-1-12', description: '400',status:'已开票',nature:'电子（下载）',
  },
  {
    key: 3, name: '2018-9-13 18:21:36', age: '某某神探科技有限公司', address: '2019-1-12至2020-1-12', description: '350',status:'处理中',nature:'电子',
  },
];
 class Sms extends React.Component<any, any> {
  render() {
    const { getFieldDecorator } = this.props.form;
    const formItemLayout = {
      labelCol: {
        xs: { span: 24 },
        sm: { span: 6 },
      },
      wrapperCol: {
        xs: { span: 24 },
        sm: { span: 16 },
      },
    };
    const tailFormItemLayout = {
      wrapperCol: {
        xs: {
          span: 24,
          offset: 0,
        },
        sm: {
          span: 16,
          offset: 6,
        },
      }
    }
    const rowSelection = {
      onChange: (selectedRowKeys:any, selectedRows:any) => {
        console.log(`selectedRowKeys: ${selectedRowKeys}`, 'selectedRows: ', selectedRows);
      },
      getCheckboxProps: (record:any) => ({
        disabled: record.name === 'Disabled User', // Column configuration not to be checked
        name: record.name,
      }),
    };
    const renderTitle =()=>{
      return(
        <div style={{marginLeft:'20px'}}>当前有3个订单可申请发票，可开票总额：¥535.00</div>
        )
      }
    const renderFooter =()=>{
      return(
        <div>待开票金额:￥0.00(已选金额:￥0.00) <button style={{width:'100px',height:'35px',border:'1px solid lightgray',color: 'gray',marginLeft:'21px'}}>申请发票</button></div>
      )
    } 
    
    return (
      <Content style={{background:'white',padding:'30px 20px'}}>
      <Tabs defaultActiveKey="1">
        <TabPane tab="申请记录" key="1">
          <Table
            columns={columns}
            pagination={false}
            dataSource={data}
            
          />
        </TabPane>
        <TabPane tab="发票申请" key="2">
        <Table
            title={renderTitle}
            rowSelection={rowSelection}
            columns={columns}
            pagination={false}
            dataSource={data}
            footer={renderFooter}
            
          />
        </TabPane>
        <TabPane tab="发票抬头设置" key="3">
        <Form {...formItemLayout} style={{ width:'500px'}}>
        <Form.Item label="开具类型：">
          {getFieldDecorator('radio-group')(
            <Radio.Group>
              <Radio value="a">个人</Radio>
              <Radio value="b">企业</Radio>
            </Radio.Group>
          )}
        </Form.Item>
        <Form.Item label="发票抬头：">
          {getFieldDecorator('email', {
            rules: [{
              required: true, message: '请填写您纳税登记证上的编号', 
            }],
          })(
            <Input placeholder="请填写您纳税登记证上的编号"/>
          )}
        </Form.Item>
        <Form.Item
          label="纳税识别号："
        >
          {getFieldDecorator('email', {
            rules: [{
              required: true, message: '请填写您纳税登记证上的编号',

            }],
          })(
            <Input placeholder="请填写您纳税登记证上的编号"/>
          )}
        </Form.Item>
        <Form.Item
          label="手机号："
        >
          {getFieldDecorator('email', {
            rules: [{
              required: true, message: '请填写您用来接收电子发票的手机号',
            }],
          })(
            <Input placeholder="请填写您用来接收电子发票的手机号"/>
          )}
        </Form.Item>
        <Form.Item
          label="发票类型："
        >
          {getFieldDecorator('email', {
            
            rules: [{
              required: true, message: '请输入发票类型',
            }],
          })(
            <Input className="genre" style={{ background:'rgba(234,234,227,1)',}}  placeholder="请填写您纳税登记证上的编号" />
          )}
        </Form.Item>
        <Form.Item
          label="注册地址："
        >
          {getFieldDecorator('email', {
            rules: [{
              required: true, message: '请填写营业执照上的注册地址', 
            }],
          })(
            <Input placeholder="请填写营业执照上的注册地址"/>
          )}
        </Form.Item>
        <Form.Item
          label="注册电话："
        >
          {getFieldDecorator('email', {
            rules: [{
              required: true, message: '请填写您公司有效联系电话',
            }],
          })(
            <Input placeholder="请填写您公司有效联系电话"/>
          )}
        </Form.Item>
        <Form.Item
          label="开户银行："
        >
          {getFieldDecorator('email', {
            rules: [{
              required: true, message: '请填写您开户许可证上的开户银行',
            }],
          })(
            <Input placeholder="请填写您开户许可证上的开户银行"/>
          )}
        </Form.Item>
        <Form.Item
          label="银行账户："
                  >
          {getFieldDecorator('email', {
            rules: [{
              required: true, message: '请填写您开户许可证上的银行账户',
            }],
          })(
            <Input placeholder="请填写您开户许可证上的银行账户"/>
          )}
        </Form.Item>
        <Form.Item {...tailFormItemLayout}>
          <Button type="primary" htmlType="submit">提交</Button>
        </Form.Item>
      </Form>
        </TabPane>
      </Tabs>
      </Content>
    
    )
  }
}
export  default Form.create()( Sms );