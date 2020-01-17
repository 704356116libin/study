const proxy = require('http-proxy-middleware'); // devServer

module.exports = function (app) {
  app.use(proxy('/api', {
    target: `http://${process.env.REACT_APP_PROXY_TARGET}`,
    changeOrigin: true
  }));
};