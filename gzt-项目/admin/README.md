## **目录结构**

```
admin
├── build              // 打包目录
├── mock               // 模拟数据
│   └─ index.js        // 每添加一个  mock  文件，需要在此文件夹中引用
├── node_modules
├── public
│   ├── favicon.ico
│   ├── index.html
│   └── manifest.json
├── src
│   ├── assets         // 资源目录
│   ├── components     // 组件目录(最好是无状态组件)
│   ├── layouts        // 布局组件
│   ├── models         // dva model目录 
│   │   └── index.ts   // 每添加一个 model, 需要在 index.ts 里引用一下
│   ├── pages          // 页面
│   ├── utils          // 工具函数
│   ├── App.module.scss  // cssModule 后缀名必须是 .module.scss
│   ├── App.test.tsx
│   ├── APP.tsx
│   ├── index.scss         // 全局 scss  
│   ├── index.tsx          // 入口文件
│   ├── react-app-env.d.ts      // ts 声明文件
│   ├── route.tsx          // 入路由配置文件
│   ├── serviceWorker.ts
│   └── setupProxy.js      // 配置代理
├── .env                   // 环境变量
├── .env.example           // 环境变量例子( git跟踪，环境变量配置示例  )
├── config-overrides.js    // 覆盖webpack配置
├── package.json
├── README.json            // 可以先阅读一下 😊
└── tsconfig.json          // ts 配置文件
```

## **安装依赖**(提前安装Node.js)

```js
npm install
```

## **启动服务**

```js
npm start
```

## **生产构建**

```js
npm run build
```

## **配置代理**

1.在 src/setupProxy.js 文件中设置，设置如下：

```js
 app.use(proxy('/api', {
    target: `http://${process.env.REACT_APP_PROXY_TARGET}`,
    changeOrigin: true
  }));
```

### create-react-app 官方文档

[https://facebook.github.io/create-react-app/docs/getting-started](https://facebook.github.io/create-react-app/docs/getting-started)

### antd 官网

[https://ant.design/docs/react/use-in-typescript-cn](https://ant.design/docs/react/use-in-typescript-cn)

### create-react-app 结合 antd 使用相关配置

[https://ant.design/docs/react/use-with-create-react-app-cn](https://ant.design/docs/react/use-with-create-react-app-cn)

### dva 官网

[https://dvajs.com/](https://dvajs.com/)

### mockjs 使用方式

[https://github.com/nuysoft/Mock/wiki/Getting-Started](https://github.com/nuysoft/Mock/wiki/Getting-Started)