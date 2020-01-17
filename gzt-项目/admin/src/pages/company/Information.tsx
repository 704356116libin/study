
import * as React from 'react';
import { Layout, Alert, Form, Input, Upload, Icon, Modal, Select, Button, message } from 'antd';
import TextLabel from '../../components/textLabel';
import { Link } from 'react-router-dom';
import { FormComponentProps } from 'antd/lib/form';
import { provinceData, cityData, countyData } from './province';
import SelectLinkage from '../../components/select';
import request, { get } from '../../utils/request';
import './index.scss';

const { Content } = Layout;
const FormItem = Form.Item;
const Option = Select.Option;
interface InformationProps extends FormComponentProps {

}
// import { connect } from 'dva';

// const NAMESPACE = 'company';
// const mapStateToProps = (state: any) => {
//   return state[NAMESPACE]
// };
// const mapDispatchToProps = (dispatch: any) => {
//   return {
//     showNavigation: (nav: any, k: number) => {
//       dispatch({
//         type: `${NAMESPACE}/setNavigation`,
//         payload: { nav }
//       });
//     }
//   }
// }


const alertMessage = <span> <TextLabel text="认证状态" className="red" /><span className="red">未认证</span> 免费获得4G存储空间，6G邮箱空间，专属短信签名等特权 <Link to='/useradmins/license'>点击认证</Link></span>
const formItemLayout = {
  labelCol: {
    xs: { span: 24 },
    sm: { span: 3 },
  },
  wrapperCol: {
    xs: { span: 24 },
    sm: { span: 4 },
  },
}
const formSelectLayout = {
  labelCol: {
    xs: { span: 24 },
    sm: { span: 3 },
  },
  wrapperCol: {
    xs: { span: 24 },
    sm: { span: 8 },
  },
}
const formButtonLayout = {
  wrapperCol: {
    xs: {
      span: 24,
      offset: 0,
    },
    sm: {
      span: 18,
      offset: 3,
    },
  },
};

// @connect(mapStateToProps, mapDispatchToProps)
class Information extends React.Component<InformationProps, any> {

  state = {
    previewVisible: false,
    previewImage: '',
    fileList: [],
    cities: cityData[provinceData[0]],
    certified: 0

  };

  async componentDidMount() {
    // 获取企业当前的基本信息
    const result = await get('/api/management_enterprise_company_data');
    if (result.status === 'success') {
      this.props.form.setFieldsValue({
        ...result.data
      })
      this.setState({
        certified: result.data.verified
      })
    }
  }
  // 更新企业信息
  handleSubmit = (e: any) => {
    e.preventDefault();
    this.props.form.validateFieldsAndScroll(async (err, values) => {
      if (!err) {
        const result = await request('/api/management_enterprise_info_save', {
          method: 'POST',
          body: values
        })
        if (result.status === 'success') {
          message.success('企业信息更新成功')
        } else {
          message.error(result.message)
        }
      }
    })
  }
  handlePreview = (file: any) => {
    this.setState({
      previewImage: file.url || file.thumbUrl,
      previewVisible: true,
    });
  }
  handleChange = ({ fileList }: any) => this.setState({ fileList })

  handleCancel = () => this.setState({ previewVisible: false });
  /** 省份*/
  handleProvinceChange = (value: any) => {
    this.setState({
      cities: cityData[value],

    });
  }
  handleSelectChange = (value: []) => {
    console.log(value);
  }
  normFile = (e: any, s: any) => {
    // 限制单个文件
    const isLt20M = e.file.size / 1024 / 1024 < 5;

    if (Array.isArray(e)) {
      return e;
    }

    if (isLt20M || e.file.status === 'removed') {
      return e && e.fileList
    } else {
      message.error('支持5M以下的图片！');
      return e && e.fileList.filter((file: any) => file.uid !== e.file.uid);
    }
  }
  render() {
    const { previewVisible, previewImage, fileList, certified } = this.state;
    const uploadButton = (
      <div>
        <Icon type="plus" />
        <div className="ant-upload-text">Upload</div>
      </div>
    );
    const { getFieldDecorator } = this.props.form;
    return (
      <Content className="company-wrapper wrapper">
        {certified == 0 && <Alert message={alertMessage} type="warning" showIcon />}
        {/* <div className="license-top">
          <span>企业信息</span><span > <Icon type="question-circle" className="show-info" />在哪展示</span>
        </div> */}
        <Form onSubmit={this.handleSubmit} className="form">
          <FormItem
            {...formItemLayout}
            label="公司logo"
          >
            {getFieldDecorator('upload', {
              valuePropName: 'fileList',
              getValueFromEvent: this.normFile,
            })(
              <Upload
                listType="picture-card"
                onPreview={this.handlePreview}
                onChange={this.handleChange}
                beforeUpload={() => false}
              >
                {fileList.length >= 1 ? null : uploadButton}
              </Upload>
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="企业名称"
          >
            {getFieldDecorator('name', {
              rules: [
                { required: true, message: '请填写企业名称' },
              ]
            })(
              <Input placeholder='请填写创建公司的名称' />
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="企业简称"
          >
            {getFieldDecorator('abbreviation', {
              rules: [
                { required: true, message: '请填写企业简称' },
              ]
            })(
              <Input placeholder='请填写企业简称' />
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="企业电话"
          >
            {getFieldDecorator('tel', {
              rules: [
                { required: true, message: '请填写企业电话' },
              ]
            })(
              <Input placeholder='请填写企业简称' />
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="企业类型"
          >
            {getFieldDecorator('type', {
              initialValue: 'private'
            })(
              <Select>
                <Option value="private">私营企业</Option>
                <Option value="stateOwned">国有企业</Option>
              </Select>
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="所属行业"
          >
            {getFieldDecorator('industry', {
              initialValue: 'private'
            })(
              <Select>
                <Option value="software">计算器软件</Option>
                <Option value="stateOwned">建筑行业</Option>
              </Select>
            )}
          </FormItem>
          <FormItem
            {...formSelectLayout}
            label="所属地区"
          >
            {getFieldDecorator('area', {
              // initialValue: 'private'
            })(
              <SelectLinkage
                provinceData={provinceData}
                cityData={cityData}
                countyData={countyData}
                onChange={this.handleSelectChange}
              />
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="企业地址"
          >
            {getFieldDecorator('address', {
            })(
              <Input placeholder="请输入企业地址" />
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="邮政编码"
          >
            {getFieldDecorator('postalCode', {
              rules: [
                {
                  len: 6,
                  message: '请输入正确的邮政编码',
                }
              ]
            })(
              <Input placeholder="请输入邮政编码" />
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="企业传真"
          >
            {getFieldDecorator('fax', {
            })(
              <Input placeholder="请输入传真号" />
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="企业网址"
          >
            {getFieldDecorator('internetSite', {
            })(
              <Input placeholder="请输入企业网址" />
            )}
          </FormItem>
          <FormItem
            {...formButtonLayout}

          >
            <Button type="primary" htmlType="submit" >保存</Button>
          </FormItem>

        </Form>
        {/* logo 预览 modal */}
        <Modal visible={previewVisible} footer={null} onCancel={this.handleCancel}>
          <img alt="example" style={{ width: '100%' }} src={previewImage} />
        </Modal>
      </Content>
    )
  }
}
export default Form.create()(Information)
