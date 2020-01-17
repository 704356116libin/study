import React, { Component, forwardRef, useImperativeHandle } from 'react';
import { Form, Button, Input, Layout, Divider, message } from 'antd';
import './index.scss'

const { Content } = Layout;
/** 图形验证码 */
const Captcha = forwardRef(({ onChange, value, imgSrc, changeCaptcha }, ref) => {

  useImperativeHandle(ref, () => ({}));

  return (
    <>
      <Input
        value={value}
        onChange={onChange}
        placeholder="请输入验证码"
        className="re-input invite-input captcha"
        style={{ verticalAlign: 'bottom' }}
      />
      <span style={{ float: 'right' }}>
        <img src={imgSrc} onClick={changeCaptcha} />
      </span>
    </>
  )
})
/** 手机验证码 */
const TelCode = forwardRef(({ onChange, value, getTelCode, disabled, buttonText }, ref) => {

  useImperativeHandle(ref, () => ({}));

  return (
    <>
      <Input
        placeholder="请输入短信验证码"
        className="re-input invite-input code-mess"
        onChange={onChange}
        value={value}
      />
      <Button disabled={disabled} style={{ float: 'right' }} type='primary' className='code-btn' onClick={getTelCode}>{buttonText}</Button>
    </>
  )
})
class InviteStaff extends Component {

  constructor(props) {
    super(props);
    this.state = {
      inviteInfo: JSON.parse(window.inviteInfo),
      hasRegistered: true,
      captcha_image_content: '',
      captcha_key: '',
      tel_key: '',
      disabled: false,
      buttonText: '获取验证码',
      timer: 0
    }
  }
  componentDidMount() {
    this.getCaptcha();
  }
  /** 获取图片验证码 */
  getCaptcha = async () => {
    const res = await fetch('/api/captchas', {
      headers: {
        "Content-Type": "application/json"
      }
    });
    const result = await res.json();
    this.setState({
      captcha_image_content: result.captcha_image_content,
      captcha_key: result.captcha_key
    })
  }

  handleSubmit = (e) => {
    e.preventDefault();
    this.props.form.validateFieldsAndScroll(async (err, values) => {
      if (!err) {
        const res = await fetch('/setUser', {
          headers: {
            "Content-Type": "application/json"
          },
          method: 'POST',
          body: JSON.stringify({
            ...values,
            tel_key: this.state.tel_key,
            company_id: this.state.inviteInfo.company_id
          })
        });
        const result = await res.json();
        if (result.status === 'success') {
          message.success('加入成功');
          window.location.href = '/login';
        }

      }
    })
  }

  handleInputBlur = async (e) => {
    const tel = e.target.value;
    if (/^1[3456789][0-9]{9}$/.test(tel)) {

      const res = await fetch('/checkTelExsit', {
        headers: {
          "Content-Type": "application/json"
        },
        method: 'POST',
        body: JSON.stringify({
          tel
        })
      });
      const result = await res.json();
      if (result.message === '手机号不存在') {
        message.info('当前手机号还没有注册, 需设置密码');
        this.setState({
          hasRegistered: false
        })
      } else if (result.message === '手机号已存在') {
        message.info('当前手机号已注册, 可以直接加入');
        this.setState({
          hasRegistered: true
        })
      }
    }
  }
  /** 更新图片验证码 */
  handleCaptchaChange = () => {
    this.getCaptcha();
  }
  /** 获取验证码 */
  handleTelCodeChange = () => {
    this.props.form.validateFieldsAndScroll(['tel', 'captcha'], (err, values) => {
      if (!err) {
        const { tel, captcha } = values;
        (async () => {
          const res = await fetch('/api/getTelCode', {
            method: 'POST',
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify({
              tel,
              tel_time: Date.now(), //当前时间戳
              captcha_code: captcha,  //验证码
              tel_type: 'register',
              captcha_key: this.state.captcha_key
            })
          });
          const result = await res.json();
          if (result.status === 'fail') {
            this.props.form.setFields({
              captcha: {
                errors: [{
                  message: result.message,
                }]
              }
            })
          } else {
            message.success('验证码发送成功');
            this.setState({
              disabled: true,
              tel_key: result.tel_key
            })
            let k = 60;
            this.setState({
              timer: setInterval(() => {
                k--;
                this.setState({
                  buttonText: k + '秒'
                })
                if (k <= 0) {
                  window.clearInterval(this.state.timer);
                  this.setState({
                    buttonText: '获取验证码',
                    disabled: false
                  })
                }
              }, 1000)
            })
          }
        })();
      }
    })
  }
  handleTelCodevalidator = (e) => {
    if (e.target.value === '') {
      this.setState({
        validateStatus: 'error',
        help: '请输入短信验证码'
      })
    } else {
      this.setState({
        validateStatus: 'success',
        help: ''
      })
    }
  }
  render() {
    const { inviteInfo: { user_name, company_name }, hasRegistered, captcha_image_content, disabled, buttonText } = this.state;

    const { getFieldDecorator } = this.props.form;

    return (
      <Content className="invite-wrapper">
        <div className='invite-form'>
          <div className="invite-info" style={{ textAlign: 'center' }}>
            <span className='name'>{user_name}</span> 邀请你加入 <span className='name'>{company_name}</span>
          </div>
          <Divider />
          <Form
            onSubmit={this.handleSubmit}
            style={{ padding: '0 50px' }}
          >
            <Form.Item>
              {getFieldDecorator('name', {
                rules: [{ required: true, message: '请输入真实姓名' }],
              })(
                <Input placeholder="请输入真实姓名" className="re-input invite-input" />
              )}
            </Form.Item>
            <Form.Item>
              {getFieldDecorator('tel', {
                rules: [{
                  required: true,
                  message: '请输入手机号'
                }, {
                  pattern: /^1[3456789][0-9]{9}$/, message: '请输入正确的手机号'
                }],
              })(
                <Input type="tel" placeholder="请输入手机号" onBlur={this.handleInputBlur} className="re-input invite-input" />
              )}
            </Form.Item>
            <Form.Item>
              {getFieldDecorator('captcha', {
                rules: [{ required: true, message: '请输入验证码' }],
              })(
                <Captcha imgSrc={captcha_image_content} changeCaptcha={this.handleCaptchaChange} />
              )}
            </Form.Item>
            <Form.Item>
              {getFieldDecorator('tel_code', {
                rules: [{ required: true, message: '请输入短信验证码' }],
              })(
                <TelCode
                  buttonText={buttonText}
                  disabled={disabled}
                  validator={this.handleTelCodevalidator}
                  getTelCode={this.handleTelCodeChange}
                />
              )}
            </Form.Item>
            {
              hasRegistered ? null : (
                <Form.Item>
                  {getFieldDecorator('password', {
                    rules: [{ required: true, message: '设置密码' }],
                  })(
                    <Input type="password" placeholder="请设置密码" className="re-input invite-input" />
                  )}
                </Form.Item>
              )
            }
            <Form.Item>
              <Button type="primary" htmlType="submit" size="large" block className="login-form-button">
                提交申请
              </Button>
            </Form.Item>
          </Form>
        </div>
      </Content >
    );
  }
}
export default Form.create()(InviteStaff)
