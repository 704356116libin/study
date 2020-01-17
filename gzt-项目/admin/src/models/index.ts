// 引入所有model文件,汇总在这里统一处理
import { DvaInstance } from 'dva';
import company from './Company';
import structure from './Structure';
import basis from './Basis';
import partner from './Partner';
import contact from './Contact';
import appSetting from './AppSetting';



// 引入所有models 循环调用
const models = {
  contact,
  partner,
  company,
  structure,
  basis,
  appSetting

}

/**
 * 导出包括  所有app.model() 执行的函数
 * @param { DvaInstance } app  dva 实例
 */
export default (app: DvaInstance) => {

  for (const model in models) {
    if (models.hasOwnProperty(model)) {
      app.model((models as any)[model]);
    }
  }
}









