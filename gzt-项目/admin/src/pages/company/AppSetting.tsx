
import * as React from 'react';
import { Tabs, Row, Col, Switch, message } from 'antd';
import { connect } from 'dva';

import './appSetting.scss';

const { TabPane } = Tabs;

interface AppSettingProps {
  /** 应用列表 */
  apps: any[];
  /** 获取应用列表 */
  queryApps: Function;
  /** 启用 / 禁用 app */
  toggleApp: Function;
}

const NAMESPACE = 'AppSetting';

const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    listLoading: state.loading.effects[`${NAMESPACE}/queryApps`],
  }
}

const mapDispatchToProps = (dispatch: any) => {
  return {
    queryApps() {
      dispatch({
        type: `${NAMESPACE}/queryApps`
      })
    },
    toggleApp(body: any, cb:Function) {
      dispatch({
        type: `${NAMESPACE}/toggleApp`,
        payload: {
          body,
          cb
        }
      })
    }
  }
}

@connect(mapStateToProps, mapDispatchToProps)
export default class AppSetting extends React.Component<AppSettingProps, any> {

  componentDidMount() {
    this.props.queryApps();
  }

  handleSwitch = (checked: boolean, id: any, per_id: any) => {

    const messageText = checked?'启用成功':'禁用成功';
    this.props.toggleApp({
      id,
      per_id,
      is_enable: checked ? 1 : 0
    }, (status: string) => {
      if(status === 'success') {
        message.success(messageText);
      }else {
        message.error('服务器异常，请稍后再试')
      }
    })
  }

  render() {

    const { apps } = this.props;

    return (
      <div className="app-setting wrapper">
        <Tabs>
          <TabPane tab="应用管理" key="1">
            <div>
              <Row className="app-setting-title">
                <Col span={4}>应用</Col>
                <Col span={16}>简要说明</Col>
                <Col span={4}>操作</Col>
              </Row>
              {
                apps && apps.map(({ id, per_sort: { id: per_id, name, description }, is_enable }) => (
                  <Row className="app-setting-item" key={id}>
                    <Col span={4}>{name}</Col>
                    <Col span={16}>{description}</Col>
                    <Col span={4}>
                      <Switch
                        defaultChecked={is_enable === 1}
                        onChange={(checked) => this.handleSwitch(checked, id, per_id)}
                        checkedChildren="启用"
                        unCheckedChildren="禁用"
                      />
                    </Col>
                  </Row>
                ))
              }
            </div>

          </TabPane>
          <TabPane tab="参数设置" key="2">参数设置</TabPane>
          <TabPane tab="企业认证" key="3">企业认证</TabPane>
        </Tabs>

      </div>
    )
  }

}