import React, { useEffect, useState } from 'react';
import { Layout, Icon, Form, Button, Input, Upload, Row, Col, message } from 'antd';
import { Authorization } from '../../utils/getAuthorization';
import { get } from '../../utils/request';
import { connect } from 'dva';
import { FormComponentProps } from 'antd/lib/form';
import { Dispatch } from 'redux';
import './setting.scss'

const { Content } = Layout;
const FormItem = Form.Item;
const NAMESPACE = 'UserInfo';

interface StateToProfileProps {
  userInfo: any;
}
interface DispatchProfileProps {
  queryUserInfo: Function;
  /** 更新用户个人资料 */
  updateUserInfo: (body: any, cb: () => void) => void;
}

const mapStateToProps = (state: any): StateToProfileProps => ({
  userInfo: state[NAMESPACE].userInfo
})
const mapDispatchToProps = (dispatch: Dispatch): DispatchProfileProps => ({
  queryUserInfo() {
    dispatch({
      type: `${NAMESPACE}/queryUserInfo`
    })
  },
  updateUserInfo(body, cb) {
    dispatch({
      type: `${NAMESPACE}/updateUserInfo`,
      payload: { body, cb }
    })
  }
})

interface ProfileProps extends FormComponentProps, StateToProfileProps, DispatchProfileProps {
}

function Profile(props: ProfileProps) {

  const [avatarSrc, setAvatarSrc] = useState('');
  const [loading, setLoading] = useState(false);

  // 获取用户头像
  useEffect(() => {
    (async () => {
      const result = await get('/api/u_get_getPersonalAvatar');
      if (result.status === 'success') {
        setAvatarSrc(result.avatar.oss_path);
      }
    })()
  }, [])
  /** 设置用户资料 */
  useEffect(() => {
    if (props.userInfo) {
      const { name, signature } = props.userInfo;
      props.form.setFieldsValue({
        name,
        signature
      })
    }
  }, [props.userInfo])

  const { getFieldDecorator } = props.form;
  /** 更新用户资料 */
  function handleSubmit(e: any) {
    e.preventDefault();
    props.form.validateFieldsAndScroll((err: any, values: any) => {
      if (!err) {
        props.updateUserInfo(values, () => {
          message.success('更新成功');
        });
      }
    })
  }
  /** 上传限制 */
  function beforeUpload(file: any) {

    const isJPG = ['image/png', 'image/jpeg'].includes(file.type);
    if (!isJPG) {
      message.error('只支持jpg和png格式的头像!');
    }
    const isLt2M = file.size / 1024 / 1024 < 2;
    if (!isLt2M) {
      message.error('最大支持2M图片!');
    }
    return isJPG && isLt2M;
  }
  /** 头像上传 */
  function handleAvatarChange({ file, fileList, event }: any) {
    if (file.status === 'uploading') {
      setLoading(true);
      return;
    }
    if (file.status === 'done') {
      setLoading(false);
      setAvatarSrc(file.response.avatar.avatar.oss_path);
    }
  }
  /** loading 按钮 */
  const uploadButton = (
    <div>
      <Icon type={loading ? 'loading' : 'plus'} />
      <div className="ant-upload-text">{loading ? '上传中' : '上传头像'}</div>
    </div>
  );

  return (
    <Content className="settings-con">
      <h2 style={{ padding: '5px 0', lineHeight: '40px', fontSize: '18px', borderBottom: '1px solid #e1e4e8' }}>基本资料</h2>
      <Row>
        <Col span={16}>
          <Form
            onSubmit={handleSubmit}
            hideRequiredMark={true}
            layout="vertical"
            style={{ marginTop: '10px', width: '360px' }}
          >
            {/* <FormItem
              label="邮箱"
            >
              {getFieldDecorator('email', {
                rules: [{
                  required: true, message: '请输入邮箱!',
                }],
              })(
                <Input />
              )}
            </FormItem> */}
            <FormItem
              label="昵称"
            >
              {getFieldDecorator('name', {
                rules: [{
                  required: true, message: '请输入昵称!',
                }],
              })(
                <Input />
              )}
            </FormItem>
            <FormItem
              label="个性签名"
            >
              {getFieldDecorator('signature')(
                <Input.TextArea placeholder="向别人展示一下自己" />
              )}
            </FormItem>
            <FormItem>
              <Button type="primary" htmlType="submit" style={{ marginRight: '20px' }}>更新个人信息</Button>
            </FormItem>
          </Form>
        </Col>
        <Col span={8}>
          <FormItem
            colon={false}
            label="头像"
          >
            <Upload
              name="avatar"
              listType="picture-card"
              className="avatar-uploader"
              beforeUpload={beforeUpload}
              onChange={handleAvatarChange}
              action="/api/u_get_editPersonalAvatar"
              showUploadList={false}
              headers={{
                authorization: Authorization,
              }}
            >
              {loading ? uploadButton : avatarSrc ? <img src={avatarSrc} alt="avatar" /> : uploadButton}
            </Upload>
          </FormItem>
        </Col>
      </Row>
    </Content>
  )
}
export default Form.create()(connect(mapStateToProps, mapDispatchToProps)(Profile))
