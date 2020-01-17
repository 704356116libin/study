
import * as serviceWorker from './serviceWorker';
import * as history from 'history';
import route from './utils/routeConfig';
import dva from 'dva'; // 引入dva
import createLoading from 'dva-loading';
import { message } from 'antd';
import models from './models'; // 初始化引入model
import './index.scss';

const app = dva({ // 1.创建 dva 实例
  history: history.createBrowserHistory({ basename: "/useradmins" }),//配置路由前缀
  onError(e) {
    message.error(e.message, /* duration */3);
  }
}); // 创建应用  使用browserHistory/默认为hashHistory
app.use(createLoading()); // 使用loading插件 2.装载插件(可选)

models(app); // 注册所有modal
// app.model(UserNav); // 3.注册model 首页初始化需要的数据直接加载，剩下的按需加载
app.router(route); // 4.注册路由

app.start('#root'); // 5.启动应用
// If you want your app to work offline and load faster, you can change
// unregister() to register() below. Note this comes with some pitfalls.
// Learn more about service workers: http://bit.ly/CRA-PWA
serviceWorker.unregister();// 在生产环境中为用户在本地创建一个service worker 来缓存资源到本地
