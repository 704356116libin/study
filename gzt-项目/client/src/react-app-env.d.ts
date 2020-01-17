/// <reference types="react-scripts" />

/**
 * 第三方包
 */
declare module 'dva-loading'
declare module 'react-dragula'
declare module 'react-router-cache-route'
declare module 'react-pdf-js'
declare module '@antv/data-set'


/**
 * 内部全局声明
 */
/**
 * 用户个人信息
 */
interface UserInfo{
  avatar: string;
  username: string;
  tel: string;
}
/**
* 用户在企业内的个人信息
*/
interface CorpprateInfo{
  avatar: string;
  username: string;
  department: string;
  position: string;
  companyname: string;
}