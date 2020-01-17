
import * as React from 'react';
import { Button, Input } from 'antd';
export default class AddInput extends React.Component<any, any> {
  addChange = () => {
    this.props.onChange && this.props.onChange(
      this.props.count + 10
    )
  }
  lessChange = () => {
    this.props.onChange && this.props.onChange(
      this.props.count - 10 <= 0 ? 0 : this.props.count - 10
    )
  }
  render() {
    return (
      <div style={{ marginBottom: '30px' }}>
        <Button icon="minus" onClick={this.lessChange} style={{ width: '60px', height: '46px', verticalAlign: 'middle' }} />
        <Input style={{ width: '160px', height: '46px', textAlign: 'center', verticalAlign: 'middle', }} value={this.props.count} />
        <Button style={{ width: '60px', height: '46px', verticalAlign: 'middle' }} icon="plus" onClick={this.addChange} />

      </div>
    )
  }

}