
import * as React from 'react';
import { Layout, Radio, Button, Col, Row, Icon, Spin, message } from 'antd';
import { connect } from 'dva';
import Dragula from 'react-dragula';
import { History } from 'history';
import './index.scss';
import req from '../../utils/request';

const { Content } = Layout;
const RadioGroup = Radio.Group;
const RadioButton = Radio.Button;
const NAMESPACE = 'Company';
interface SortProps {
  history: History,
  showDepartmentList: any,
  showPositionList: any,
  departmentList: any,
  permissionList: any,
  saveSortInfo: any,
  listLoading: boolean
}
const mapStateToProps = (state: any) => {
  return {
    ...state[NAMESPACE],
    listLoading: state.loading.effects[`${NAMESPACE}/queryDepartmentList`],
  }
};
const mapDispatchToProps = (dispatch: any) => {
  return {
    //部门列表
    showDepartmentList: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryDepartmentList`,
        payload: params
      })
    },
    //职务列表
    showPositionList: (params: any) => {
      dispatch({
        type: `${NAMESPACE}/queryPermissionList`,
        payload: params
      });
    },
  }
}

@connect(mapStateToProps, mapDispatchToProps)
export default class Sort extends React.Component<SortProps, any> {
  state = {
    listType: 'department',
    sortLoading: false, //保存排序loading
    currentPostion: -1
  }
  componentDidMount() {
    this.props.showPositionList();
    this.props.showDepartmentList();
  }
  /**
   * 排序
   */
  saveSortInfo = () => {

    if (this.state.listType === 'department') {//部门
      message.success('提交成功');
    } else {//职务
      this.setState({
        sortLoading: true
      })
      const sortlist = document.body.querySelectorAll('.listWrapper .sortList');
      const sortJson = {};
      for (let index = 0; index < sortlist.length; index++) {
        sortJson[(sortlist[index] as any).dataset.type] = index;
      }
      (async () => {
        const result = await req('/api/management_job_ordering', {
          method: 'POST',
          body: {
            sort_data: sortJson
          }
        })
        this.setState({
          sortLoading: false
        })
        if (result.status === 'success') {
          message.success('提交成功');
          this.props.history.push('/work/approval/management');
        } else {
          message.info('服务器繁忙，请稍后再试~')
        }
      })()
    }
  }
  onRadionChange = (e: any) => {
    if (e.target.value === 'department') {
      this.setState({
        listType: 'department'
      })
    } else {
      this.setState({
        listType: 'position'
      })
    }
  }
  /**
   * 激活拖拽插件
   */
  dragulaDecorator = (componentBackingInstance: any) => {
    if (componentBackingInstance) {
      Dragula([componentBackingInstance]).on('drag', (el: any, target: any) => {
        this.setState({
          currentPostion: Array.from(target.children).findIndex((item) => item === el)
        })
      }).on('drop', (el: any, target: any, source: any, sibling: any) => {
        (async () => {
          const result = await req('/api/management_department_ordering', {
            method: 'POST',
            body: {
              oldOrder: this.state.currentPostion,
              newOrder: Array.from(target.children).findIndex((item) => item === el),
              node_id: el.dataset.type
            }
          })
          if (result.status !== 'success') {
            message.info('服务器繁忙，请稍后再试~')
          }
        })()
      })
    }
  }
  render() {
    const { departmentList, permissionList, listLoading } = this.props;
    const { listType, sortLoading } = this.state;
    const sortList = listType === 'department' ? departmentList : permissionList;

    return (
      <Content className="sort-wrapper wrapper">
        <div>
          <Row>
            <Col span={4}>
              <RadioGroup defaultValue="department" buttonStyle="solid" onChange={this.onRadionChange}>
                <RadioButton value="department" >部门</RadioButton>
                <RadioButton value="position"  >职务</RadioButton>
              </RadioGroup></Col>
            <Col span={20}>
              <div className="text-right">
                <Button type='primary' className="btn" onClick={this.saveSortInfo} loading={sortLoading}>保存</Button>
                <Button>取消</Button>
              </div>
            </Col>
          </Row>
          <Spin spinning={listLoading} delay={300}>
            <div className='listWrapper' ref={this.dragulaDecorator}>
              {
                sortList && sortList.data.map((item: any, index: any) => {
                  const { id, name, } = item;
                  return (
                    <Row className="sortList cursor-pointer" key={index} data-type={id}>
                      <Col>
                        <Icon type="ordered-list" className='sortIcon' />
                        名称：{name}
                      </Col>
                    </Row>
                  )
                })
              }
            </div>
          </Spin>
        </div>
      </Content>
    )
  }
}