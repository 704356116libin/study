/**
 * 配置antd 按需加载/覆盖less变量
 */
const path = require('path');
const { override, fixBabelImports, addLessLoader } = require('customize-cra');

module.exports = {
  webpack: override(
    // antd 组件按需加载
    fixBabelImports('import', {
      libraryName: 'antd',
      libraryDirectory: 'es',
      style: true,
    }),
    // less 配置
    addLessLoader({
      javascriptEnabled: true,
      modifyVars: {
        "@border-radius-base": "3px"
      },
    }),
  ),
  paths: (paths, env) => {
    // 修改打包后的路径， 直接打包到 server 目录下
    paths.appBuild = path.resolve(__dirname, '../server/public/client');
    return paths;
  }
}
