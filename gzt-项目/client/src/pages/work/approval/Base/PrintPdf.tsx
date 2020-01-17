import * as React from 'react';
import { Modal, Icon, Spin } from 'antd';
import PDF from 'react-pdf-js';
import './printPdf.scss'
interface PrintPdfProps {
  visible: boolean,
  onCancel: any,
  file: any,
  exportPdf: any,
  exportExcel: any,
  loading: boolean
}
export default class PrintPdf extends React.Component<PrintPdfProps, any> {

  handleOk = () => {

  }
  /**
   * 打印
   */
  onPrint = () => {
    window.print()
  }
  onExportPdf = () => {
    this.props.exportPdf()
  }
  onExportExcel = () => {
    this.props.exportExcel()
  }

  render() {
    const { visible, onCancel, file, loading } = this.props;

    return (

      <Modal
        visible={visible}
        footer={null}
        width='800px'
        closable={false}
        wrapClassName="print-pdf"
      >
        <Spin spinning={loading}>
          {
            file ? <div className="pdf-wrapper">
              <div className="nprint">
                <span onClick={this.onPrint} title="打印">
                  <Icon type="printer" className='pdf-icon' />
                </span>
                <span onClick={this.onExportExcel} title="导出Excel">
                  <Icon type="file-excel" className='pdf-icon' />
                </span>
                <span onClick={this.onExportPdf} title="导出PDF" >
                  <Icon type="file-pdf" className='pdf-icon' />
                </span>
                <span className="inline-block" style={{ float: 'right' }} onClick={() => onCancel()}><Icon type="close" style={{ fontSize: '20px' }} /></span>
              </div>
              <PDF
                className="print"
                file={file}
              />
            </div> : null
          }
        </Spin>
      </Modal>
    )
  }
}
