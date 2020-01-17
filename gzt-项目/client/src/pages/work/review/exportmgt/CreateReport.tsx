import * as React from 'react';
import { Icon, Form, Layout, Affix, Input, Divider, message, Button, Select, Tooltip } from 'antd';
import { FormComponentProps } from 'antd/lib/form';
import { History, Location } from 'history';
import { connect } from 'dva';
import { SelectedPersonnelInfo } from '../../../../components/selectPersonnelModal';
import TextLabel from '../../../../components/textLabel';
import BraftEditor from '../../../../components/braftEditor';
import { EditorState, ControlType } from 'braft-editor';
import SearchTable from '../../../../components/searchTable';
import AddSelect from '../../../../components/addSelect';
import SelectCc from '../../../../components/selectCc';
import './createReport.scss'
import { Dispatch } from 'redux';
import decryptId from '../../../../utils/decryptId';
import request from '../../../../utils/request';

const { Option } = Select;


const row = '{"blocks":[{"key":"41qqr","text":"清源水净化有限公司双电源厂区工程","type":"header-three","depth":0,"inlineStyleRanges":[{"offset":0,"length":16,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textAlign":"center"}},{"key":"eh3ab","text":"审核报告","type":"header-three","depth":0,"inlineStyleRanges":[{"offset":0,"length":4,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textAlign":"center"}},{"key":"fpl53","text":"{{文号}}","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":6,"style":"COLOR-F32784"},{"offset":0,"length":6,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textAlign":"center"}},{"key":"7clhc","text":"长葛市财政局投资评审中心:","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":13,"style":"BOLD"},{"offset":0,"length":13,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{}},{"key":"68kkd","text":"我单位接受贵单位委托，对清源水净化有限公司双电源厂区工程进行了审核，上述工程项目相关资料由贵单位提供，我们的责任是根据《河南省通用安装工程预算定额》(HA02-31-2016)及相关配套文件的规定，按照客观、公正、公平、合理的原则，组织有关专业技术人员对此项工程造价进行审核，并发表审核意见，出具审核报告。在审核过程中，我们根据贵单位提供的资料，专业技术人员会同相关单位及相关人员，认真地分析、认真计算，对工程量的计算、定额的套用、材料分析、工程取费、材料价格的调整等必要的审核程序严格审核，现已审核结束，并将审核结果报告如下：","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":264,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textIndent":1}},{"key":"412jg","text":"    一、工程概况：","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":11,"style":"LINEHEIGHT-1.5"},{"offset":0,"length":11,"style":"BOLD"}],"entityRanges":[],"data":{}},{"key":"fm63v","text":"    本工程为清源水净化有限公司双电源厂区工程，工程内容含高压开闭所安装、户外高压计量箱、顶管和电缆线路工程等。","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":57,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textIndent":1}},{"key":"9dq9e","text":"    二、审核范围：","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":11,"style":"LINEHEIGHT-1.5"},{"offset":0,"length":11,"style":"BOLD"}],"entityRanges":[],"data":{}},{"key":"1rln0","text":"   清源水净化有限公司双电源厂区工程提供施工图及预算内的全部内容。","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":34,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textIndent":1}},{"key":"e5nj4","text":"    三、审核依据：","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":11,"style":"LINEHEIGHT-1.5"},{"offset":0,"length":11,"style":"BOLD"}],"entityRanges":[],"data":{}},{"key":"8jevs","text":"1、依据建设单位提供的图纸及预算；","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":17,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textIndent":1}},{"key":"fhtqh","text":"2、{{水利审核依据}}；","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":13,"style":"LINEHEIGHT-1.5"},{"offset":2,"length":10,"style":"COLOR-F32784"}],"entityRanges":[],"data":{"textIndent":1}},{"key":"7iecg","text":"3、《河南省通用安装工程预算定额》(HA02-31-2016)及配套的定额综合解释和现行的有关造价文件","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":51,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textIndent":1}},{"key":"5kvfp","text":"4、人工费价格执行豫建标定【2018】40号文；","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":24,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textIndent":1}},{"key":"60bu6","text":"5、税金根据豫建设标【2018】22号文，按10%计入；","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":28,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textIndent":1}},{"key":"1dbte","text":"6、材料价格依据《许昌工程造价信息》2018年第六期，信息价中没有的材料，其价格参考市场价进行调整；","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":50,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textIndent":1}},{"key":"b76qj","text":"7、现行的法律法规、标准图集、规范、工艺标准、材料做法等。","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":29,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textIndent":1}},{"key":"625k5","text":"    四、审核原则：","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":1,"length":10,"style":"LINEHEIGHT-1.5"},{"offset":1,"length":10,"style":"BOLD"}],"entityRanges":[],"data":{}},{"key":"dr4qi","text":"    客观、公平、公正、实事求是。","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":18,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textIndent":1}},{"key":"c83es","text":"    五、审核方法：","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":11,"style":"LINEHEIGHT-1.5"},{"offset":0,"length":11,"style":"BOLD"}],"entityRanges":[],"data":{}},{"key":"dnls5","text":"    根据该工程实际情况，我们采取了普查的方法对该工程招标控制价进行了审核。","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":39,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textIndent":1}},{"key":"9h98v","text":" 六、审核结果：","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":1,"length":7,"style":"LINEHEIGHT-1.5"},{"offset":1,"length":7,"style":"BOLD"}],"entityRanges":[],"data":{}},{"key":"81d4j","text":"    清源水净化有限公司双电源厂区工程审核结果为：原报送审金额{{送审金额}}元，审定金额：{{审定金额}}元，审减金额  元。","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":65,"style":"LINEHEIGHT-1.5"},{"offset":32,"length":8,"style":"COLOR-F32784"},{"offset":47,"length":8,"style":"COLOR-F32784"}],"entityRanges":[],"data":{"textIndent":1}},{"key":"dkior","text":" ","type":"unstyled","depth":0,"inlineStyleRanges":[],"entityRanges":[],"data":{}},{"key":"d6tt7","text":"","type":"unstyled","depth":0,"inlineStyleRanges":[],"entityRanges":[],"data":{}},{"key":"7o9kq","text":"编制人 ：                                                 审核人:","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":58,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{}},{"key":"b56ka","text":"                       河南英华咨询有限公司","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":33,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textAlign":"right"}},{"key":"8kiqc","text":" ","type":"unstyled","depth":0,"inlineStyleRanges":[],"entityRanges":[],"data":{"textAlign":"right"}},{"key":"26o6m","text":"2019年 2月12日","type":"unstyled","depth":0,"inlineStyleRanges":[{"offset":0,"length":11,"style":"LINEHEIGHT-1.5"}],"entityRanges":[],"data":{"textAlign":"right"}}],"entityMap":{}}';
const data = [{
  key: '1',
  parameter: '{{文号}}',
  description: '文号自定义设置'
}, {
  key: '2',
  parameter: '{{送审金额}}',
  description: '项目送审金额',
}, {
  key: '3',
  parameter: '{{审定金额}}',
  description: '项目审定金额'
}];

export interface DispatchToCreateReportProps {
  /** 获取分组信息 */
  queryReportGroup: (cb?: Function) => void;
  /** 获取单个报告模板详情 */
  queryReportById: (id: string, cb: Function) => void;
  /** 获取流程列表信息 (用于刷新) */
  queryReports: Function;
}
export interface StateToCreateReportProps {
  /** 分组信息 */
  reportGroup: any;
}

export interface CreateReportProps extends FormComponentProps, DispatchToCreateReportProps, StateToCreateReportProps {
  location: Location;
  history: History;
}

const NAMESPACE = 'Review';
const mapStateToProps = (state: any): StateToCreateReportProps => ({
  reportGroup: state[NAMESPACE].reportGroup
});
const mapDispatchToProps = (dispatch: Dispatch): DispatchToCreateReportProps => ({
  queryReportGroup: (cb) => {
    dispatch({
      type: `${NAMESPACE}/queryReportGroup`,
      payload: cb
    });
  },
  queryReportById: (id, cb) => {
    dispatch({
      type: `${NAMESPACE}/queryReportById`,
      payload: {
        id, cb
      }
    });
  },
  queryReports: () => {
    dispatch({
      type: `${NAMESPACE}/queryReports`
    });
  }

})
@connect(mapStateToProps, mapDispatchToProps)
class CreateReport extends React.Component<CreateReportProps, any>{

  state = {
    selectPersonnelVisible: false,
    selectedPersonnelInfo: 'all' as SelectedPersonnelInfo | 'all',
    reportGroup: undefined as any,
    reportGroupLoading: false,
    reportValue: BraftEditor.createEditorState(row) as EditorState
  }

  componentDidMount() {
    this.setState({
      reportGroupLoading: true
    })
    if (this.props.location.state) {
      this.props.queryReportById(this.props.location.state.data.id, ({
        name,
        type,
        header,
        footer,
        per
      }: any) => {
        this.props.form.setFieldsValue({
          name,
          type_id: type.type_id,
          header,
          footer,
          per: per === 'all' ? null : per.selectedPersonnelInfo
        })
      });
    }
    this.props.queryReportGroup((reportGroup: any) => {
      this.setState({
        reportGroupLoading: false,
        reportGroup
      })
    })
  }

  /** 保存修改后的报告模板 */
  saveReport = () => {

    console.log(this.state.reportValue.toHTML());

    message.success('保存成功');
  }

  handleSubmit = (e: React.FormEvent<any>) => {
    e.preventDefault();
    this.props.form.validateFieldsAndScroll(async (err, values) => {
      if (!err) {
        let isNull = true;
        if (values.per !== undefined) {
          for (const key in values.per.checkedPersonnels) {
            if (values.per.checkedPersonnels[key].length !== 0) {
              isNull = false;
              break;
            }
          }
        }
        if (isNull) {
          values.per = 'all'
        } else {
          const staffId = values.per.checkedPersonnels.map((item: any) => item.key);
          const departmentId = values.per.checkedKeys.filter((item: any) => !staffId.includes(item));
          values.per = { staffId, departmentId, selectedPersonnelInfo: values.per }
        }

        values.text = this.state.reportValue.toHTML();

        console.log(values);

        let url = '/api/c_pst_createExportTemplate';
        let messageText = '创建成功';
        if (this.props.location.state && this.props.location.state.type === 'UPDATE') {
          url = '/api/c_pst_exportTemplateSaveEdit';
          messageText = '修改成功';
          values.id = this.props.location.state.data.id;
        }

        const result = await request(url, {
          method: 'POST',
          body: values
        })
        console.log(result);

        if (result.status === 'success') {
          message.success(messageText);
          this.props.queryReports();
          this.props.history.replace('/work/review/exportmgt');
        } else {
          message.error('服务器错误，请稍后再试')
        }


      }
    })
  }

  handleReportAdd = async (addOptionValue: any) => {
    if (addOptionValue === '') {
      message.info('分组名称不能为空哦~');
      return
    }
    const result = await request('/api/c_pst_createExportType', {
      method: 'POST',
      body: {
        name: addOptionValue
      }
    });

    if (result.status === 'success') {
      message.success('新建成功');
      this.setState({
        reportGroup: [...this.state.reportGroup, {
          count: 0,
          id: result.data.id,
          name: addOptionValue,
          type: 'type'
        }]
      })
      this.props.queryReports();
    } else {
      message.error('服务器异常，请稍后再试')
    }
  }

  render() {

    const formItemLayout = {
      labelCol: {
        xs: { span: 24 },
        sm: { span: 8 },
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
          span: 18,
          offset: 8,
        },
      },
    };

    const { reportValue } = this.state;
    const controls: ControlType[] = [
      'undo', 'redo', 'remove-styles',
      {
        key: 'custom-button',
        type: 'button',
        title: '保存',
        text: <Icon type="save" theme="filled" />,
        onClick: this.saveReport
      },
      'separator',
      'headings', 'font-size', 'font-family', 'separator',
      'bold', 'italic', 'underline', 'text-color', 'separator',
      'list-ul', 'list-ol', 'text-indent', 'text-align', 'separator',
      'superscript', 'subscript', 'media', 'fullscreen'
    ];


    const columns = [{
      title: '参数',
      dataIndex: 'parameter',
      key: 'parameter',
      width: '40%'
    }, {
      title: '说明',
      dataIndex: 'description',
      key: 'description',
      width: '60%'
    }];

    const { getFieldDecorator } = this.props.form;
    const { reportGroup, reportGroupLoading } = this.state;

    return (
      <Layout className="review-report-create white">
        <div className="review-report-header">
          <span className="goback" onClick={() => this.props.history.replace('/work/review/exportmgt')}>
            <Icon type="arrow-left" />返回
          </span>
          <TextLabel style={{ marginLeft: '20px' }} text={'1111'} colon={false} />
        </div>
        <div className="review-report-main">
          <Affix style={{ zIndex: 99, position: 'absolute', top: 126, left: 0 }} offsetTop={64} target={() => document.getElementsByClassName('review-report-create')[0] as HTMLDivElement}>
            <div style={{ width: 480, padding: '0 24px', background: '#fff', height: 'calc(100vh - 161px)', overflow: 'hidden auto' }}>

              <Form
                onSubmit={this.handleSubmit}
                {...formItemLayout}
              >
                <Form.Item
                  label='报告模板名称'
                >
                  {getFieldDecorator('name', {
                    rules: [
                      {
                        required: true,
                        message: '请输入报告模板名称',
                      }],
                  })(
                    <Input placeholder="请为这个报告模板起个名字" />
                  )}
                </Form.Item>
                <Form.Item
                  label='分组选择'
                >
                  {getFieldDecorator('type_id', {
                    rules: [
                      {
                        required: true,
                        message: '请选择',
                      }],
                  })(
                    <AddSelect placeholder="请选择分组" loading={reportGroupLoading} onAdd={this.handleReportAdd}>
                      {reportGroup && reportGroup.map((option: any, index: any) => (
                        <Option value={decryptId(option.id)} key={index}>{option.name}</Option>
                      ))}
                    </AddSelect>
                  )}
                </Form.Item>
                <Form.Item
                  {...formItemLayout}
                  label='报告模板说明'
                >
                  {getFieldDecorator('description', {
                    initialValue: ''
                  })(
                    <Input placeholder="可以为这个报告模板添加一个描述" />
                  )}
                </Form.Item>
                <Form.Item
                  {...formItemLayout}
                  label={(
                    <span>
                      参与人&nbsp;
                      <Tooltip title="默认是公司内部全体员工">
                        <Icon type="question-circle-o" />
                      </Tooltip>
                    </span>
                  )}
                >
                  {getFieldDecorator('per', {
                    valuePropName: 'ccInfo',
                  })(
                    <SelectCc />
                  )}
                </Form.Item>

                <Form.Item
                  label="表头标题设置"
                >
                  {getFieldDecorator('header', {
                    initialValue: ''
                  })(
                    <Input placeholder="设置导出报告的表头" />
                  )}

                </Form.Item>
                <Form.Item
                  label="表尾名称设置"
                >
                  {getFieldDecorator('footer', {
                    initialValue: ''
                  })(
                    <Input placeholder="设置导出报告的表尾" />
                  )}
                </Form.Item>
                <Form.Item
                  {...tailFormItemLayout}
                >
                  <Button type="primary" htmlType="submit">
                    确定
                  </Button>
                </Form.Item>
              </Form>

              <Divider />
              <SearchTable
                style={{ width: '320px', background: '#fff' }}
                title={() => '自定义参数说明'}
                bordered
                columns={columns}
                dataSource={data}
              />
              {/* <Divider />
              <div style={{ textAlign: 'right' }}>
                <Button
                  type="primary"
                  onClick={() => {
                    console.log(this.state.reportValue.toRAW());
                  }}>确定</Button>
                <Button style={{ marginLeft: '12px' }}>预览</Button>
              </div> */}
            </div>
          </Affix>
          <BraftEditor
            className="create-report"
            value={reportValue}
            controls={controls}
            onChange={(EditorState) => this.setState({ reportValue: EditorState })}
            onSave={this.saveReport}
          />
        </div>
      </Layout >
    )
  }
}
export default Form.create()(CreateReport)