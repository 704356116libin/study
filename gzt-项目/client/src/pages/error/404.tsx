import * as React from 'react';
import Exception from '../../components/exception'; // 引入 404组件

export default class Err extends React.Component {

  public render() {
    return (
      <div>
        <Exception type="404" redirect="/" backText="返回首页" />
      </div>
    );
  }
}
