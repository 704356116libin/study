
import * as React from 'react';
import { FormComponentProps } from 'antd/lib/form';
import { Form, Input, Button, Icon, message, Layout } from 'antd';
import TextLabel from '../../components/textLabel';
// import { CopyToClipboard } from 'react-copy-to-clipboard';
import { connect } from 'dva';
import './index.scss';
const { Content } = Layout;
const FormItem = Form.Item;
const NAMESPACE = 'Structure';
interface InviteStaffProps extends FormComponentProps {
  inviteStaffByTel: Function;
  staffInvitationUrl: Function;
  invitationUrl: any;
  urlInvalid: Function;
}
let inviteStaffOptions = [
  {
    key: 1,
    label: '账号',
    value: '请输入手机号'
  },
  {
    key: 2,
    label: '账号',
    value: '请输入手机号'
  },
  {
    key: 3,
    label: '账号',
    value: '请输入手机号'
  },
]
// 
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE]
  };
}
const mapDispatchToProps = (dispatch: any) => {
  return {
    inviteStaffByTel: (body: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/inviteStaffByTel`,
        payload: { body, cb }
      });
    },
    /**生成链接 */
    staffInvitationUrl: (cb: any) => {
      dispatch({
        type: `${NAMESPACE}/queryInvitationUrl`,
        payload: { cb }
      });
    },
    urlInvalid: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryUrlInvalid`,
        payload: { params }
      });
    }
  }
}
@connect(mapStateToProps, mapDispatchToProps)
class InviteStaff extends React.Component<InviteStaffProps, any> {
  state = {
    inviteStaffOptions,
    copyValue: '',
    copied: false,
  }

  componentDidMount() {
    // todo。。。
    this.props.staffInvitationUrl();

  }
  searchUrl = (url: any) => {
    let search = url.split('?')[1];
    let params = {};
    if (search != "") {
      search.slice(0).split("&").forEach(
        function (val: any) {
          var arr = val.split("=");
          params[arr[0]] = arr[1];
        }
      );
    }
    return params;

  }

  // componentDidUpdate(prevProps: any) {
  //   if (this.props.invitationUrl !== prevProps.invitationUrl) { // 判断是否变化
  //     this.setState({
  //       copyValue: this.props.invitationUrl
  //     })
  //   }
  // }

  handleSubmit = (e: any) => {
    e.preventDefault();
    this.props.form.validateFieldsAndScroll((err, values) => {
      if (!err) {
        this.props.inviteStaffByTel(values, () => { message.info('已发送邀请信息') })
      }
    });
  }
  add = () => {
    let key: number = Number(this.state.inviteStaffOptions.length) + 1;
    if (key <= 15) {
      const newIninviteStaffOptions = this.state.inviteStaffOptions.concat({
        key,
        label: '账号',
        value: `请输入手机号`
      })
      this.setState({
        inviteStaffOptions: newIninviteStaffOptions
      })
    } else {
      message.info('最多可以邀请15个~');
      return;
    }
  }
  // onHandleClose = () => {
  //   this.props.onCancel();
  // }
  /**
   * 删除
   */
  remove = (k: number) => {
    if (inviteStaffOptions.length === 1) { return; }
    const newIninviteStaffOptions = this.state.inviteStaffOptions.filter(({ key }: any) => key !== k);
    this.setState({
      inviteStaffOptions: newIninviteStaffOptions
    })

  }

  render() {
    const { getFieldDecorator } = this.props.form;
    const { inviteStaffOptions } = this.state;

    const { invitationUrl } = this.props;
    const formItemLayoutWithOutLabel = {
      wrapperCol: {
        xs: { span: 24, offset: 0 },
        sm: { span: 12, offset: 4 },
      },
    };
    const formItemButtonLayoutLabel = {
      wrapperCol: {
        xs: { span: 24, offset: 0 },
        sm: { span: 6, offset: 4 },
      },
    };
    const formItemLayout = {
      labelCol: {
        xs: { span: 24 },
        sm: { span: 4 },
      },
      wrapperCol: {
        xs: { span: 24 },
        sm: { span: 8 },
      },
    };

    return (
      <Content className="invite-wrapper wrapper">
        <Form onSubmit={this.handleSubmit} style={{ width: '100%' }}>
          {
            inviteStaffOptions.map((item: any) => (
              <div key={item.key} >
                <FormItem
                  {...formItemLayout}
                  label={item.label}
                  required={false}
                >
                  {getFieldDecorator(`telOrEmails[${item.key - 1}]`, {
                    validateTrigger: ['onChange', 'onBlur'],
                    rules: [
                      {
                        required: true,
                        whitespace: true,
                        message: "请填写手机号",
                      },
                      {
                        pattern: /^1[3456789][0-9]{9}$/, message: '请输入正确的手机号'
                      }
                    ],
                  })(
                    <Input placeholder={item.value} style={{ width: '60%', marginRight: 8 }} />
                  )}
                  {
                    inviteStaffOptions.length > 1 ? (
                      <Icon
                        className="dynamic-delete-button"
                        type="minus-circle-o"
                        onClick={() => this.remove(item.key)}
                      />
                    ) : null
                  }
                </FormItem>
              </div>
            ))
          }
          <FormItem {...formItemLayoutWithOutLabel}>
            <Button type="dashed" onClick={this.add} style={{ width: '40%' }}>
              <Icon type="plus" />添加
            </Button>
          </FormItem>
          <FormItem {...formItemLayoutWithOutLabel}>
            <div style={{ padding: '0 24px', border: '1px solid #ddd' }}>
              <div><TextLabel text='其他邀请方式' /> </div>
              {/* <CopyToClipboard text={copyValue} onCopy={() => this.setState({ copied: true })}><span className="cursor-pointer">复制内容</span> </CopyToClipboard> */}
              <div>
                <TextLabel text="注册地址" /><a target="_blank" href={invitationUrl && `${window.location.origin}/invite-staff${invitationUrl.url}`} style={{ color: '#00a0ea' }}>{invitationUrl && `${window.location.origin}/invite-staff${invitationUrl.url}`}</a>
              </div>
              <p>
                <TextLabel text="温馨提示" />
                您可以用QQ、微信等方式把邀请码和企业号发送给同事，对方完成认证后就可以和您一起使用评审通了。
                {invitationUrl && invitationUrl.messgae}
              </p>
            </div>
          </FormItem>
          <FormItem
            {...formItemButtonLayoutLabel} className="text-right">
            <div>
              <Button type="primary" htmlType="submit">确定邀请</Button>
            </div>
          </FormItem>
        </Form>
      </Content>
    )
  }
}
export default Form.create()(InviteStaff)
