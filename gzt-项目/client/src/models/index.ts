// 引入所有model文件,汇总在这里统一处理
import { DvaInstance } from 'dva';
import Notice from './Notice';
import Approval from './Approval';
import Assist from './Assist';
import Contact from './Contact';
import UserInfo from './UserInfo';
import Workbench from './Workbench';
import Workdynamics from './Workdynamics';
import Notification from './Notification';
import Review from './Review';
import Doc from './Doc';

// 引入所有models 循环调用
const models = {
    Approval,
    Notice,
    Assist,
    Contact,
    UserInfo,
    Workbench,
    Workdynamics,
    Notification,
    Review,
    Doc
}

/**
 * 导出包括  所有app.model() 执行的函数
 * @param { DvaInstance } app  dva 实例
 */
export default (app: DvaInstance) => {

    for (const model in models) {
        if (models.hasOwnProperty(model)) {
            app.model(models[model]);
        }
    }
}
