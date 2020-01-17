import * as React from 'react';
import { Layout, Button, Row, Col, Radio, Form, Modal, Input, Icon, Spin, message } from 'antd';
import { connect } from 'dva';
import { Link } from 'react-router-dom';
import Templatelist from '../Base/Templatelist';
import TextLabel from '../../../../components/textLabel';
import { History } from 'history';
import './index.scss';
import GroupNameItem from './GroupNameItem';
import GroupTypeItem from './GroupTypeItem';


const { Header } = Layout;
const FormItem = Form.Item;
const RadioGroup = Radio.Group;
const RadioButton = Radio.Button;

interface ManagementProps {
  templateListLoading: any;
  showManagementTemList: any;
  dataSource: any;
  managementTemList: any;
  show: boolean;
  okText: string;
  maskClosable: boolean;
  form: any;
  editCreateTemName: any;
  removeTemplate: any;
  removeTemplateType: any;
  currenGrouptValue: any;
  handleSwitchChange: any;
  defaultTemplateList: any;
  existTemplateList: any;
  showDefaultTemplate: any;
  showExistTemplate: any;
  showApprovalGroupType: any;
  showWhetherToEnable: any;
  switchLoading: any;
  templateTypeLoading: any;
  history: History
}
const NAMESPACE = 'Approval'; // dva model 命名空间
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    templateListLoading: state.loading.effects[`${NAMESPACE}/queryManagementTemList`],
    // switchLoading: state.loading.effects[`${NAMESPACE}/queryTemWhetherToEnable`],
    templateTypeLoading: state.loading.effects[`${NAMESPACE}/queryDefaultTemList`]
  }
};
const mapDispatchToProps = (dispatch: any) => {
  return {
    /**
     * 默认模板列表
     */
    showDefaultTemplate: () => {
      dispatch({
        type: `${NAMESPACE}/queryDefaultTemList`,
      });
    },
    /**
     * 已有模板列表
     */
    showExistTemplate: () => {
      dispatch({
        type: `${NAMESPACE}/queryExistTemList`,
      });
    },

    /**
     * 模板列表
     */
    showManagementTemList: () => {
      dispatch({
        type: `${NAMESPACE}/queryManagementTemList`,
      });
    },
    /**
     * 编辑模板
     */
    editCreateTemName: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/queryCreateTemName`,
        payload: {
          params,
          cb
        }
      });
    },
    /**
     * 删除模板
     */
    removeTemplate: (params: any, cb: any) => {
      dispatch({
        type: `${NAMESPACE}/removeTemplate`,
        payload: {
          params,
          cb
        }
      });
    },
    /**
     * 删除类型
     */
    removeTemplateType: (params: any) => {
      console.log(params, 88888);
      dispatch({
        type: `${NAMESPACE}/removeTemplateType`,
        payload: {
          params
        }
      });
    },
    showApprovalGroupType: (params: any, cb: any) => {// 传递回调
      dispatch({
        type: `${NAMESPACE}/queryApprovalGroupType`,
        payload: {
          params,
          cb
        }
      });
    },
    showWhetherToEnable: (params: any) => {
      console.log(params)
      dispatch({
        type: `${NAMESPACE}/queryTemWhetherToEnable`,
        payload: params
      });
    }
  }
}

@connect(mapStateToProps, mapDispatchToProps)
class Management extends React.Component<ManagementProps, any> {
  state = {
    show: false,
    showGroup: false,
    showApproval: false,
    okText: '保存',
    maskClosable: false,
    currentValue: '',
    temTypeId: null,
    currenGrouptValue: '',
    templateType: 'default',
    currentPosition: '' // 当前点击模板 switch 位置

  }
  componentDidMount() {
    this.props.showManagementTemList();
  }


  // handleSort = (id: number) => {
  //   console.log(id);
  // }

  removeTemList = (id: number) => {
    this.props.removeTemplate({ id }, () => {
      message.info('删除成功');
    })
  }

  removeTemType = (typeId: number) => {
    this.props.removeTemplateType({ id: typeId })
  }
  showEditModal = (type: string, typeId: number) => {
    this.setState({
      show: true,
      maskClosable: false,
      currentValue: type,
      temTypeId: typeId
    })
  }
  showGroupModal = () => {
    this.setState({
      showGroup: true,
      maskClosable: false,
    })
  }
  /**
   * 创建审批模态框
   */
  showApprovalModal = () => {
    this.setState({
      showApproval: true,
    })
    this.props.showDefaultTemplate();// 默认模板
    this.props.showExistTemplate(); // 已有模板
  }
  groupSortInfo = () => {
    this.props.history.push('/work/approval/groupSort');
  }
  /**
   * 编辑成功
   */
  editHandleOk = (e: React.MouseEvent<any>) => {
    e.preventDefault();
    const { form } = this.props;
    form.validateFieldsAndScroll((err: any, values: any) => {
      if (!err) {
        this.setState({
          show: false,
        });
        this.props.editCreateTemName({ name: values.name, id: this.state.temTypeId }, () => {
          message.success('修改成功');
        });
      }
    });
  }
  handleGroupOk = () => {
    const { form } = this.props;
    form.validateFieldsAndScroll((err: any, values: any) => {
      if (!err) {
        this.setState({
          showGroup: false,
          currenGrouptValue: ''
        });
        this.props.showApprovalGroupType({ name: values.name }, () => {//传递回调
          message.success('创建成功');
        })
      }
    })

  }
  handleCancel = () => {
    this.setState({
      show: false
    });
  }
  handleChange = (e: any) => {
    this.props.form.setFieldsValue({
      currentValue: e.target.value
    })
  }
  handleGrorpChange = (e: any) => {
    this.props.form.setFieldsValue({
      currenGrouptValue: e.target.value
    })
  }
  /**
   * 是否启用模板
   */
  handleSwitchChange = (id: number, isShow: number) => {
    // this.setState({
    //   currentPosition: `${index}-${key}`
    // })
    this.enableTemplate(id, isShow);
  }
  // dragulaDecorator = (componentBackingInstance: any) => {
  //   if (componentBackingInstance) {
  //     Dragula([componentBackingInstance]).on('drop', (el: any, target: any, source: any, sibling: any) => {
  //       console.log(el, target, source, sibling, 562652653);
  //     });
  //   }
  // };
  enableTemplate = (id: number, isShow: number) => {
    this.props.showWhetherToEnable({
      id,
      is_show: isShow
    })
  }
  /**
   * 切换默认模板，已有模板
   */
  chooseTemplate = (e: any) => {
    if (e.target.value === "defaultTemplate") {
      this.setState({
        templateType: 'default'
      })
    } else {
      this.setState({
        templateType: 'exist'
      })
    }
  }
  render() {
    const { managementTemList, templateListLoading, defaultTemplateList, existTemplateList, templateTypeLoading } = this.props;
    const { show, maskClosable, okText, currentValue, showGroup, showApproval, currenGrouptValue, templateType } = this.state;
    const { getFieldDecorator } = this.props.form;
    const templateList = templateType === 'default' ? defaultTemplateList : existTemplateList

    return (
      <Layout className="management">
        <Header className="white" style={{ paddingLeft: "30px" }}>
          <Button icon="file" type="primary" ghost className="type-btn" onClick={() => this.showGroupModal()} >添加分组</Button>
          <Button icon="sort-ascending" type="primary" ghost className="type-btn" onClick={() => this.groupSortInfo()} >分组排序</Button>
          <Button icon="file" type="primary" className="type-btn" onClick={() => this.showApprovalModal()} >创建审批模板</Button>
        </Header>
        <div className="white" >
          <Spin spinning={templateListLoading} delay={300}>
            {
              managementTemList && managementTemList.enable.map((item: any, index: any) => {

                const { type, typeId, count, data } = item;
                const params = { type, typeId, count };
                return (
                  <div key={index} style={{ marginBottom: '10px' }}>
                    <GroupNameItem
                      contents={params}
                      onRemoveTemType={this.removeTemType}
                      onShowEditModal={this.showEditModal}
                    />
                    {
                      data && data.map((params: any, key: any) => {
                        // const loading = currentPosition === `${index}-${key}` && switchLoading
                        return (
                          <GroupTypeItem
                            key={key}
                            // index={index}
                            dataSource={params}
                            // loading={loading}
                            onRemoveTemList={this.removeTemList}
                            onHandleSwitchChange={this.handleSwitchChange}
                          />
                        )
                      })
                    }
                  </div>
                )
              })
            }
            <header style={{ height: '40px', lineHeight: '40px', background: '#f5f5f5' }}>
              <Row>
                <Col span={4} style={{ paddingLeft: '30px' }}>
                  <TextLabel text="类型名称" /><span>已禁用</span>
                </Col>
              </Row>
            </header>
            {
              managementTemList && managementTemList.disable.map((item: any, index: any) => {
                // const loading = currentPosition === `${index}-${key}` && switchLoading
                // const loading = currentPosition === `${index}-D` && switchLoading
                return (
                  <GroupTypeItem
                    // key="D"
                    key={index}
                    dataSource={item}
                    // loading={loading}
                    onRemoveTemList={this.removeTemList}
                    onHandleSwitchChange={this.handleSwitchChange}
                  />
                )
              })
            }
          </Spin>
          <Modal
            title="新建分组"
            visible={showGroup}
            centered
            mask
            maskClosable={maskClosable}
            onOk={this.handleGroupOk}
            onCancel={() => this.setState({ showGroup: false })}
            okText="新增"
            cancelText="取消"
            destroyOnClose={true}
          >
            <FormItem
              wrapperCol={{ span: 15, offset: 1 }}
              label=''
            >   {getFieldDecorator('name', {
              rules: [
                { required: true, message: '请输入名称' },
              ],
              initialValue: currenGrouptValue
            })(
              <Input placeholder="请输入名称(限10个字符)" maxLength={10} onChange={this.handleGrorpChange} className="categoryInput" />
            )}
            </FormItem>
          </Modal>
          <Modal
            title="创建审批模板"
            visible={showApproval}
            centered
            mask
            maskClosable={maskClosable}
            onCancel={() => this.setState({ showApproval: false })}
            destroyOnClose={true}
            width="800px"
          >
            <Spin spinning={templateTypeLoading}>
              <div className="management beautiful-scroll-bar-hover" style={{ height: 360, overflowX: 'hidden' }}>
                <Row style={{ marginBottom: '10px' }}>
                  <Col span={19} >
                    <RadioGroup defaultValue="defaultTemplate" buttonStyle="solid" onChange={this.chooseTemplate}>
                      <RadioButton value="defaultTemplate">使用推荐模板</RadioButton>
                      <RadioButton value="existingTemplate">使用已有模板</RadioButton>
                    </RadioGroup>
                  </Col>
                  <Col span={5}>
                    <Link to="/work/approval/newCreate" className="operate-btn" onClick={() => this.setState({ showApproval: false })}>
                      <Icon type="plus" />
                      自定义模板
                    </Link>
                  </Col>
                </Row>
                <div className="templateWrapper">
                  <Templatelist datasource={templateList} link="/work/approval/newCreate" onCloseModal={() => this.setState({ showApproval: false })} />
                </div>
              </div>
            </Spin>
          </Modal>
          <Modal
            title="编辑"
            visible={show}
            centered
            mask
            maskClosable={maskClosable}
            onOk={this.editHandleOk}
            onCancel={this.handleCancel}
            cancelText="取消"
            okText={okText}
            destroyOnClose={true}
          >
            <FormItem
              wrapperCol={{ span: 15, offset: 1 }}
            >   {getFieldDecorator('name', {
              rules: [
                { required: true, message: '请输入名称' },
              ],
              initialValue: currentValue
            })(
              <Input placeholder="请输入名称(限10个字符)" max="10" onChange={this.handleChange} className="categoryInput" />
            )}
            </FormItem>
          </Modal>
        </div>
      </Layout >
    )
  }
}
export default Form.create()(Management);
