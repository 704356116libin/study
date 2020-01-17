const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
// 覆盖webpack配置
mix.webpackConfig({
  module: {
    rules: [
      {
        test: /\.(m?js|jsx|tsx|ts)$/,
        exclude: /(node_modules|vendor)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-react'],
            plugins: [
              ['import', {
                libraryName: 'antd',
                libraryDirectory: 'es',
                style: true,
              }],
              ["@babel/plugin-proposal-class-properties", { "loose": true }]
            ],
          }
        }
      },
      {
        test: /\.less$/,
        exclude: /vendor/,
        use: {
          loader: 'less-loader',
          options: {
            javascriptEnabled: true,
            modifyVars: {
              "@border-radius-base": "3px"
            },
          }
        }
      }
    ]
  }
});


mix.js('resources/js/app.js', 'public/js')
  .react('resources/js/inviteStaff.js', 'public/js')
  .sass('resources/sass/app.scss', 'public/css');
