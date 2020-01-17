import { hasLogged } from './utils/getAuthorization';
import dva from 'dva';
import * as History from 'history';
import * as serviceWorker from './serviceWorker';
import createLoading from 'dva-loading';
import route from './utils/routeConfig';
import models from './models'; // 初始化引入model
import { message } from 'antd';
import './index.scss';

if (!hasLogged) { // 未登录跳转
    if (process.env.NODE_ENV === 'development') {// 如果是开发期间
        window.location.href = `http://${process.env.REACT_APP_PROXY_TARGET}/login`
    } else {
        window.location.href = '/login'
    }
}

const app = dva({ // 1.创建 dva 实例
    // 创建 浏览器路由
    history: History.createBrowserHistory(),
    onError(e) {
        message.error(e.message, /* duration */3);
    }
}); // 创建应用  使用browserHistory/默认为hashHistory

app.use(createLoading()); // 使用loading插件 2.装载插件(可选)

models(app); // 注册所有modal
// app.model(UserNav); // 3.注册model 首页初始化需要的数据直接加载，剩下的按需加载

app.router(route); // 4.注册路由

app.start('#root'); // 5.启动应用

serviceWorker.unregister(); // 在生产环境中为用户在本地创建一个service worker 来缓存资源到本地
