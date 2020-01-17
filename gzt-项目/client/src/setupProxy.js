const proxy = require('http-proxy-middleware'); // devServer
const mockParams = require('../mock'); // 引入用于生成数据需要的参数

module.exports = function (app) {
  app.use(proxy('/api', {
    target: `http://${process.env.REACT_APP_PROXY_TARGET}`,
    changeOrigin: true
  }));

  for (const key in mockParams) {
    for (const url in mockParams[key]) {
      if (typeof mockParams[key][url] === 'function') { // 执行自定义函数
        app.all(url, mockParams[key][url])
      } else {// 拦截请求,返回mock数据
        app.all(url, (req, res) => {
          res.json(mockParams[key][url]);
        })
      }
    }
  }
};