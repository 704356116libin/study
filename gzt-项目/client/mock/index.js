/**
 * 所有mock文件需要在这里引入方能使用
 */
const notice = require('./notice');
const userInfo = require('./userInfo');
const assist = require('./assist');
const approval = require('./approval');
const dynamic = require('./dynamic');

module.exports = {
    notice,
    userInfo,
    assist,
    approval,
    dynamic
}