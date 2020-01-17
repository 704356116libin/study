const fs = require('fs');
const path = require('path');

// 复制打包后的 index.html 到 server/resources/views/app.blade.php
fs.copyFileSync(
    path.resolve(__dirname, '../server/public/client/index.html'),
    path.resolve(__dirname, '../server/resources/views/app.blade.php')
);