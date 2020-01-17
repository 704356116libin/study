<?php
/**
 * 动态模块配置信息
 */
return [
   /*
    * 动态列表基础数据格式
    */
   'list_data'=>[
        'status'=>'',
        'unread_count'=>0,
        'data'=>[],
   ],
    /*
     * list_data中data单个记录的数据格式
     */
    'list_single_data'=>[
        'type'=>'',
        'unread_count'=>0,
        'data'=>[
            'company_id'=>0,//这个键值都是动态的,聊天,群组聊天可能为user_id / group_id
            'title'=>'',
            'content'=>'',
            'time'=>'',
        ],
    ],
    /*
     * 动态详情基础数据格式
     */
   'detail_data'=>[
        'status'=>'',
        'data'=>[],
   ],
    /*
     *动态详情单个元素的数据格式
     */
    'detail_singal_data'=>[
      'type'=>'',//每条数据的类型,企业公告，协助等等的模块标识
      'data'=>'',//单条记录的数据
    ],
    /*
     * 不同功能类所对应的(动态模块)资源类名
     */
    'class_resource'=>[
        \App\Models\CompanyNotice::class=>\App\Http\Resources\dynamic\CompanyNotice::class,//企业公告对应的动态资源展示模块
        \App\Models\Approval::class=>\App\Http\Resources\dynamic\Approval::class,//审批对应的动态资源展示模块
        \App\Models\CollaborativeTask::class=>\App\Http\Resources\dynamic\CollaborativeTask::class,//审批对应的动态资源展示模块
        \App\Models\CompanyPartner::class=>\App\Http\Resources\company\CompanyPartnerRecordResource::class,//企业合作伙伴对应的动态资源展示模块
        \App\Models\Pst::class=>\App\Http\Resources\pst\PstDetailResource::class,//评审通动态展示资源文件
    ],
    /*
     * 动态列表中每次加载的数据条数
     */
    'detail_load_list_count'=>8,
    /**
     * 动态列表json数据操作类型
     */
    'operate_type'=>[
        'update'=>'update',//代表更新列表数据
        'delete'=>'delete',//代表删除指定节点
        'reset_unread'=>'reset_unread',//代表重置指定节点的未读数
//        'get_single_data_index_info'=>'et_single_data_index_info',//获取单条数据的唯一标识company_id/user_id/group_id
    ],
    /*
     * 假数据
     */
    'chat_list'=>'{"status":"success","unread_count":4,"data":[{"type":"群组聊天","unread_count":3,"data":{"群组_id":5,"title":"xxxx","content":"xxxxx:你还没到啊？","time":"2019-01-14 15:41:50"}},{"type":"单人聊天","unread_count":1,"data":{"user_id":0,"title":"xxxx","content":"已经开好房了","time":"2019-01-14 15:41:50"}}]}',
];
