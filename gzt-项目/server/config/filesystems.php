<?php

return [



    'default' => env('oss','oss'),


    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],
        'oss' => [
                'driver'        => 'oss',
                'access_id'     => env('Aliyun_OSS_AccessKeyId'),
                'access_key'    => env('Aliyun_OSS_AccessKeySecret'),
                'bucket'        => env('Aliyun_OSS_bucket_name'),
                'endpoint'      => env('Aliyun_OSS_endpoint'), // OSS 外网节点或自定义外部域名
                //'endpoint_internal' => '<internal endpoint [OSS内网节点] 如：oss-cn-shenzhen-internal.aliyuncs.com>', // v2.0.4 新增配置属性，如果为空，则默认使用 endpoint 配置(由于内网上传有点小问题未解决，请大家暂时不要使用内网节点上传，正在与阿里技术沟通中)
                'cdnDomain'     => '<CDN domain, cdn域名>', // 如果isCName为true, getUrl会判断cdnDomain是否设定来决定返回的url，如果cdnDomain未设置，则使用endpoint来生成url，否则使用cdn
                'ssl'           => true, // true to use 'https://' and false to use 'http://'. default is false,
                'isCName'       => false,// 是否使用自定义域名,true: 则Storage.url()会使用自定义的cdn或域名生成文件url， false: 则使用外部节点生成url
                'debug'         => false,
        ],
    ],
    /*
     * 文件上传时与前端上传框架基础配置(若前端框架改变需要的数据也要相应的调整)---不能轻易改动相关名称!!!!!!!
     */
    'upload'=>[
        'allow_file_type'=>'/(txt|doc|docx|doct|png|jpg|jpeg|pdf|xls|xlsx|zip|svg)$/',//允许上传的文件格式
        'allow_file_message'=>'文件格式不合法,只允许 txt,doc,docx,doct,png,jpg,jpeg,pdf,xls,xlsx,zip,svg格式的文件!',//允许上传的文件格式报错信息
        'max_size'=>100,//单文件上传最高大小/m
        'up_files_name'=>'updatingfiles',//长传文件被包裹的属性名称
        'file'=>[
            'real_path'=>'thumbUrl',//上传文件的临时路径
            'name'=>'name',//上传文件的文件名
            'size'=>'size',//上传文件的大小/
            'type'=>'type',//上传文件的mini type
            'uid' => 'uid',//uid
            'lastModified' => 'lastModified',//最后一次更改的时间戳/毫秒级
            'lastModifiedDate' => 'lastModifiedDate'//最后一次更改的时间
        ]
    ]
];
