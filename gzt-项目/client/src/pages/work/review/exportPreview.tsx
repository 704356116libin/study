import * as React from 'react';
import { History, Location } from 'history';
import { Layout, Icon, Row, Col, Button, message, Modal } from 'antd';
import request, { get } from '../../../utils/request';
import BraftEditor from '../../../components/braftEditor';
import { ControlType } from 'braft-editor';
import './exportPreview.scss';

export interface ExportPreviewProps {
  location: Location;
  history: History;
}

class ExportPreview extends React.Component<ExportPreviewProps, any> {

  state = {
    template: {},
    reportValue: undefined,
    visible: false
  }

  componentDidMount() {
    if (this.props.location.state) {
      const { pst_id, report_id: temId } = this.props.location.state;
      (async () => {
        const result = await get('/api/c_pst_getReplacedVarTemplate', {
          params: {
            pst_id,
            temId
          }
        })
        if (result.status === 'success') {
          this.setState({
            template: result.data,
            reportValue: BraftEditor.createEditorState(result.data.text)
          })
        }
      })()
    }
  }
  /** 导出 */
  handleExport = async () => {
    const result = await request('/api/c_pst_exportSingleTemplatePackage', {
      getFile: true,
      method: 'POST',
      body: {
        ...this.state.template,
        text: (this.state.reportValue as any).toHTML()
      }
    })
    let blobUrl = window.URL.createObjectURL(result.blob);
    const a = document.createElement('a');
    a.download = decodeURI(result.headers.get('filename'));//获取文件名
    a.href = blobUrl;
    a.click();
    window.URL.revokeObjectURL(blobUrl);
    message.info('导出成功');
  }
  /** 处理打印 */
  handlePrint = () => {
    //   <head>
    //   <style>
    //   p {page-break-after: always}
    //   </style>
    //    <head>
    const printf = (document.getElementById('print') as any);
    const html = `
    <body>${(this.state.reportValue as any).toHTML()}</body>
    `
    printf.contentDocument.write(html);
    printf.contentDocument.close();
    printf.contentWindow.print();
  }
  /** 预览 */
  preview = () => {
    this.setState({
      visible: true
    })
  }
  render() {

    const { reportValue, visible } = this.state;
    const controls: ControlType[] = [
      'undo', 'redo', 'remove-styles',
      'separator',
      'headings', 'font-size', 'font-family', 'separator',
      'bold', 'italic', 'underline', 'text-color', 'separator',
      'list-ul', 'list-ol', 'text-indent', 'text-align', 'separator',
      'superscript', 'subscript', 'media', 'fullscreen'
    ];

    return (
      <Layout className="review-export-preview" >
        <div style={{ marginBottom: 24, padding: '0 20px', height: '56px', lineHeight: '56px', border: '1px solid #eee' }}>
          <span className="goback" onClick={() => this.props.history.goBack()}>
            <Icon type="arrow-left" />返回
          </span>
        </div>
        <Row>
          <Col span={4} className="text-right" style={{ paddingRight: 36 }}>
            <div style={{ marginBottom: 12 }}>
              <Button type="primary" style={{ width: 160 }} onClick={this.handleExport}>下载</Button>
            </div>
            <div style={{ marginBottom: 12 }}><Button type="primary" style={{ width: 160 }} onClick={this.preview}>预览</Button></div>
            <div style={{ marginBottom: 12 }}>
              <Button type="primary" style={{ width: 160 }} onClick={this.handlePrint}>打印</Button>
            </div>
          </Col>
          <Col span={20}>
            <BraftEditor
              className="create-report"
              value={reportValue}
              controls={controls}
              onChange={(EditorState) => this.setState({ reportValue: EditorState })}
            />
          </Col>
        </Row>
        <Modal
          visible={visible}
          title="预览"
          width={790}
          footer={null}
          onCancel={() => this.setState({ visible: false })}
        >
          <div dangerouslySetInnerHTML={{ __html: this.state.reportValue ? (this.state.reportValue as any).toHTML() : '' }} />
        </Modal>
        <iframe id="print" src="" width="0" height="0" frameBorder="0" />
      </Layout>
    )
  }
}
export default ExportPreview
