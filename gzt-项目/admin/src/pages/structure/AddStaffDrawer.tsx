
import * as React from 'react';
import { FormComponentProps } from 'antd/lib/form';
import { Form, Input, Drawer, Button, message, Select } from 'antd';
import { connect } from 'dva';
import HigherDepartment from '../../components/HigherDepartment';
import generateRandomString from '../../utils/generateRandomString';
import decryptId from '../../utils/decryptId';

const FormItem = Form.Item;
const Option = Select.Option;
const NAMESPACE = 'Structure';
interface AddStaffDrawerProps extends FormComponentProps {
  visible: any;
  onClose: any;
  addStaffInfo?: any;
  editStaffInfo?: any;
  entryWay: string;
  staffDetail: any;
  currentDataInfo: any;
  dataSource: any;
  refreshStaffInfo: any;
  positionInfo: any;
}
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE]
  };
}
const mapDispatchToProps = (dispatch: any) => {
  return {
    addStaffInfo: (params: any, cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/addStaff`,
        payload: { params, cb }
      });
    },
    editStaffInfo: (params: any, cb: Function) => {
      dispatch({
        type: `${NAMESPACE}/editStaffInfo`,
        payload: { params, cb }
      });
    }
  }
}
const formItemLayout = {
  labelCol: {
    xs: { span: 24 },
    sm: { span: 4 },
  },
  wrapperCol: {
    xs: { span: 24 },
    sm: { span: 10 },
  },
};

@connect(mapStateToProps, mapDispatchToProps)
class AddStaffDrawer extends React.Component<AddStaffDrawerProps, any> {

  componentDidUpdate(prevProps: any) {
    if (this.props.entryWay === "edit") {
      if (this.props.staffDetail !== prevProps.staffDetail) { // 判断是否变化
        if (this.props.staffDetail) {
          const { email, is_enable, name, address, remarks, roomNumber, sex, tel, role_ids } = this.props.staffDetail
          this.props.form.setFieldsValue({
            email,
            is_enable,
            name,
            address,
            remarks,
            roomNumber,
            sex,
            tel,
            department_id: this.props.currentDataInfo.node_id,
            role_ids: role_ids ? role_ids.map((role_id: string) => decryptId(role_id)) : []
          })
        }
      }
    }
  }

  handleSubmit = (e: any) => {
    e.preventDefault();
    this.props.form.validateFieldsAndScroll((err, values) => {
      if (!err) {
        values.department_id = generateRandomString(6) + values.department_id;
        values.role_ids = values.role_ids.map((role_id: string) => generateRandomString(6) + role_id);
        if (this.props.entryWay === 'add') { // 新增员工
          this.props.addStaffInfo(values, () => {
            message.info('提交成功');
            this.props.refreshStaffInfo();
          });
        } else { // 编辑后提交
          const { user_id, company_id } = this.props.staffDetail;
          values.old_node_id = generateRandomString(6) + this.props.currentDataInfo.node_id;
          values.user_id = user_id;
          values.company_id = company_id;
          this.props.editStaffInfo(values, () => {
            message.info('提交成功');
            this.props.refreshStaffInfo();
          })
        }
        this.props.onClose();
      }
    });
  }

  onHandleClose = () => {
    this.props.onClose();
  }

  render() {
    const { getFieldDecorator } = this.props.form;
    const { visible, currentDataInfo, dataSource, entryWay, positionInfo, staffDetail } = this.props;
    const title = entryWay === 'add' ? ("新增员工") : ("编辑员工");
    // 判断用户是否可以输入手机号
    const canInputTel = staffDetail ? staffDetail.activation === 1 ? true : false : false;
    return (
      <Drawer
        title={title}
        width={520}
        onClose={this.onHandleClose}
        visible={visible}
        destroyOnClose={true}
      >
        <Form onSubmit={this.handleSubmit}>
          <FormItem
            {...formItemLayout}
            label="姓名">
            {getFieldDecorator('name', {
              rules: [{ required: true, message: '请输入姓名' }],
            })(<Input placeholder="请输入姓名" />)}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="性别">
            {getFieldDecorator('sex')(
              <Select placeholder="请选择性别" >
                <Option value="男">男</Option>
                <Option value="女">女</Option>
              </Select>
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="手机号">
            {getFieldDecorator('tel', {
              rules: [
                { required: true, message: '请输入手机号' },
                {
                  pattern: /^1[3456789][0-9]{9}$/, message: '请输入正确的手机号'
                }
              ],
            })(<Input placeholder="请输入手机号" disabled={canInputTel} maxLength={11} />)}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="邮箱">
            {getFieldDecorator('email', {
              rules: [{ required: true, message: '请输入邮箱' },
              { type: 'email', message: '请输入正确的邮箱' }
              ],
            })(<Input placeholder="请输入邮箱" />)}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label='上级部门'
          >   {getFieldDecorator('department_id', {
            rules: [
              { required: true, message: '上级部门' },
            ],
          })(
            <HigherDepartment
              currentDefaultInfo={currentDataInfo}
              dataSource={dataSource}
            />
          )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="职位">
            {getFieldDecorator('role_ids')(
              <Select
                mode="tags"
                placeholder="请选择职位"
                style={{ width: '100%' }}
              >
                {
                  positionInfo && positionInfo.data.map((option: any, index: any) => (
                    <Option value={decryptId(option.id)} key={index}>{option.name}</Option>
                  ))
                }
              </Select>
            )}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="备注">
            {getFieldDecorator('remarks')(<Input placeholder="请输入备注" />)}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="地址">
            {getFieldDecorator('address')(<Input placeholder="请输入地址" />)}
          </FormItem>
          <FormItem
            {...formItemLayout}
            label="房间号">
            {getFieldDecorator('roomNumber')(<Input placeholder="请输入房间号" />)}
          </FormItem>
          <div
            style={{
              position: 'absolute',
              left: 0,
              bottom: 0,
              width: '100%',
              borderTop: '1px solid #e9e9e9',
              padding: '10px 16px',
              background: '#fff',
              textAlign: 'right',
            }}
          >
            <Button htmlType="submit" type="primary" style={{ marginRight: 8 }}>
              保存
            </Button>
            <Button type="default" onClick={this.onHandleClose}>
              取消
            </Button>
          </div>
        </Form>
      </Drawer>
    )
  }
}
export default Form.create<AddStaffDrawerProps>()(AddStaffDrawer)
