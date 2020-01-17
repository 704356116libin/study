
import * as React from 'react';
import { Layout, Icon, Form, Upload, Button, message } from 'antd';
import { FormComponentProps } from 'antd/lib/form';
import Credentials from '../../assets/img/credentials.png'
import request from '../../utils/request';
import './index.scss';
import { connect } from 'dva';
import { Dispatch } from 'redux';

const { Content } = Layout;
const FormItem = Form.Item;

interface StateToLicenseProps {
  /** 认证信息 */
  certInfo: any;
}
interface DispatchToLicenseProps {
  /** 认证信息 */
  queryCertInfo: Function;
}

interface LicenseProps extends FormComponentProps, StateToLicenseProps, DispatchToLicenseProps {

}
/** 执照上传 */
// const alertMessage = <span> <TextLabel text="认证状态" className="red" /><span className="red">未认证</span> 认证免费获得10G存储空间，10G邮箱空间，专属短信签名等特权 <Link to='/'>点击认证</Link></span>
const formItemLayout = {
  labelCol: {
    xs: { span: 24 },
    sm: { span: 3 },
  },
  wrapperCol: {
    xs: { span: 24 },
    sm: { span: 8 },
  },
}
const formItemsLayout = {
  wrapperCol: {
    xs: {
      span: 24,
      offset: 0,
    },
    sm: {
      span: 8,
      offset: 3,
    },
  },
}

const NAMESPACE = 'Company';
const mapStateToProps = (state: any): StateToLicenseProps => ({
  certInfo: state[NAMESPACE].certInfo
})
const mapDispatchToProps = (dispatch: Dispatch<any>): DispatchToLicenseProps => ({
  queryCertInfo() {
    dispatch({
      type: `${NAMESPACE}/queryCertInfo`
    })
  }
})
@connect(mapStateToProps, mapDispatchToProps)
class License extends React.Component<LicenseProps, any> {
  componentDidMount() {
    this.props.queryCertInfo();
  }
  handleSubmit = (e: any) => {

    e.preventDefault();
    this.props.form.validateFieldsAndScroll(async (err, values) => {
      if (!err) {
        const result = await request('/api/management_company_enterprise_file', {
          method: 'POST',
          body: values
        })
        if (result.status === 'success') {
          message.success(result.message)
        }

      }
    })
  }
  handleChange = (value: any) => {
    console.log(`selected ${value}`);
  }
  normFile = (e: any, s: any) => {
    // 限制单个文件
    const isLt20M = e.file.size / 1024 / 1024 < 10;

    if (Array.isArray(e)) {
      return e;
    }

    if (isLt20M || e.file.status === 'removed') {
      return e && e.fileList
    } else {
      message.error('支持10M以下的文件！');
      return e && e.fileList.filter((file: any) => file.uid !== e.file.uid);
    }
  }
  // 阻止上传
  beforeUpload = (file: any) => {

    // const fileList = this.props.form.getFieldValue("updatingfiles");
    // console.log(fileList, file, 666666666);

    // this.props.form.setFieldsValue({
    //   "updatingfiles": [...fileList, file]
    // })
    return false;
  }
  render() {
    const { certInfo } = this.props;
    const { getFieldDecorator } = this.props.form;

    return (
      <Content className="company-wrapper wrapper">
        {certInfo && certInfo.verified == 1
          ?
          (
            <div style={{ textAlign: 'center' }}>
              <img src={certInfo.url} alt="" />
              <div>当前企业已认证</div>
            </div>
          )
          :
          (
            <Form onSubmit={this.handleSubmit} className="form">
              <FormItem  {...formItemLayout}
                label="资质证明">
                {getFieldDecorator('updatingfiles', {
                  valuePropName: 'fileList',
                  getValueFromEvent: this.normFile,
                  rules: [
                    { required: true, message: '请上传资质证明' },
                  ]
                })(
                  <Upload
                    listType="picture"
                    beforeUpload={this.beforeUpload}
                  >
                    <Button>
                      <Icon type="upload" /> 上传文件
                </Button>
                  </Upload>
                )}
              </FormItem>
              <FormItem  {...formItemsLayout}>
                <div>
                  <div>1. 企、事业单位可直接上传营业执照副本、组织机构代码、税务登记证等任意一种 </div>
                  <div>2. 其他公共机构可上传盖有公章的介绍信下载介绍信</div>
                  <div>3. 上传资质证明为扫描件或电子照片，请确保信息清晰可辨</div>
                  <div>4. 支持jpg、jpeg、png、gif、bmp格式，且照片小于5M</div>
                  <div>5. 10工作日内完成审核，我们承诺仅用于企业认证使用，不会用于其他商业用途</div>
                </div>
                <img src={Credentials} alt="营业执照" />
              </FormItem>
              <FormItem  {...formItemsLayout}>
                <Button htmlType="submit" type="primary">提交认证</Button>
              </FormItem>
            </Form>
          )
        }
      </Content>
    )
  }
}
export default Form.create()(License)