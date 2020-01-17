import * as React from 'react';
import AppHeader from './layouts/header';
import { LocaleProvider } from 'antd';
import moment from 'moment';
import zh_CN from 'antd/lib/locale-provider/zh_CN';
import 'moment/locale/zh-cn';
moment.locale('zh-cn');

class App extends React.Component {

  render() {
    return (
      <LocaleProvider locale={zh_CN}>
        <>
          <AppHeader />
          {this.props.children}
        </>
      </LocaleProvider>
    );
  }
}
export default App; // 拖拽