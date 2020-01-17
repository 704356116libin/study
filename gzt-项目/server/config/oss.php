<?php
/**
 * 云存储配置文件,包含企业云/个人云的基本参数配置
 */
return [
    'company'=>[
        'default_size'=>5,//G
        'path'=>'company/',
    ],
    'user'=>[
        'default_size'=>2,//G
        'path'=>'user/',
        'userDir'=>[
          'toxiang'=>'toxiang/',//头像目录



        ],
    ],
    'root_path'=>'https://gzts.oss-cn-beijing.aliyuncs.com/',
];
