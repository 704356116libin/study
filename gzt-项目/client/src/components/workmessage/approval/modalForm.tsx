import * as React from 'react';
import { Modal, Input, Row, Col } from 'antd';
import NotifiMeds from '../../../components/notifiMeds';
import TextLabel from '../../../components/textLabel';

interface ModalFormProps {
  title?: string;
  visible?: boolean;
  onCancel?: any;
  width?: number;
  onOk?: any;
  placeholder?: string; 
}

export default class ModalForm extends React.Component<ModalFormProps, any> {

  state = {
    completeSummary: '',
    notificationWay: {}
  }
  onModalOk = () => {
    this.props.onOk({
      completeSummary: this.state.completeSummary,
      notificationWay: this.state.notificationWay
    })
  }
  closeModal = () => {
    this.props.onCancel();
  }
  notifiChange = (values: any) => {
    // console.log(values);
    this.setState({
      notificationWay: values
    })
  }
  render() {
    const { completeSummary } = this.state;
    const { title, width, visible, onCancel, placeholder } = this.props;
    const agreeModalProps = {
      title,
      visible,
      onCancel,
      width: width || 520,
      onOk: this.onModalOk,
      placeholder
    }
    return (
      <Modal
        {
        ...agreeModalProps
        }
      >
        <Input.TextArea
        style={{    margin: '0px 27px',maxWidth: '90%'}}
          autosize={{ minRows: 3, maxRows: 10 }}
          value={completeSummary}
          placeholder={placeholder}
          onChange={(e: any) => this.setState({ completeSummary: e.target.value })}
        />
        <Row type="flex" style={{ marginTop: '20px' }}>
          <Col span={5} style={{ paddingRight: '10px', textAlign: 'right' }}><TextLabel text="通知方式" /></Col>
          <Col span={19}>
            <NotifiMeds onChange={this.notifiChange} />
          </Col>
        </Row>

      </Modal>
    )
  }
}