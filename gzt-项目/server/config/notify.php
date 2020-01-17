<?php

return [
    'email_type'=>'notify',//邮件通知的标识
    'sms_templet'=>'',//短信通知的模板
    /*
     * client发送数据,的标识,前端根据不同的标识进行相应事件操作
     */
    'notify_way'=>[
        'active'=>'active',//标识业务代码主动推送
        'dynamic'=>[
            'dynamic_refresh'=>'dynamic_refresh',//工作动态数据刷新标识
            'dynamic_single'=>'dynamic_single',//工作动态数据刷新标识
        ],
        'company'=>[
            'current_company_alter'=>'current_company_alter',//当前企业变更标识
        ],
    ],
    /*
     * ws服务器推送数据标识
     */
    'ws_push_type'=>[
        'notify'=>'notify',//标识推送的数据为通知类型
        'message'=>'message',//标识推送的数据为消息类型
    ],
    /*
     * 通知模块---通知的大类包含type信息--主要用于进行动态组装数据↓type，type_key，class_type中的数据必须对应
     */
    'type'=>[
        //动态模块包含的类型
        'work_dynamic'=>[
            'c_notice',//企业公告标识
            'c_approval',//审批通知
            'c_collaborative',//协作通知
            'c_pst',//企业评审通标识
        ],
        //网站通知模块包含的类型
        'web_notice'=>[
            'notice',//网站公告标识
            'invite_partner',//合作伙伴邀请标识
            'invite_user',//员工邀请标识
//            'invite_partner',//联系人邀请标识
        ],
        //个人工作动态相关的类型
        'personal_work_dynamic'=>[
            'pst_beside',//个人评审通标识(主要是针对外部联系人)
        ],
        //
    ],
    /*
     *通知模块---各功能模块对应的type键名(及动态列表共又几个大类型)↑type，type_key，class_type中的数据必须对应
     */
    'type_key'=>[
        'work_dynamic'=>'work_dynamic',//工作动态模块对应的type键名
        'web_notice'=>'web_notice',//网站公告对应的type键名
        'personal_work_dynamic'=>'personal_work_dynamic',//个人工作动态对应的键名
    ],
    /*npm
     * 各功能模块通知type类型对应部---type(即通知表中的type字段)，type_key，class_type中的数据必须对应
     */
    'class_type'=>[
        \App\Models\CompanyNotice::class=>'c_notice',//企业公告对应的通知标识
        \App\Models\Approval::class=>'c_approval',//审批对应的通知标识
        \App\Models\CollaborativeTask::class=>'c_collaborative',//协助对应的通知标识
        \App\Models\CompanyPartnerRecord::class=>'invite_partner',//企业合作伙伴对应的通知标识
        \App\Models\Pst::class=>'c_pst',//评审通对应的通知标识(企业动态)
        \App\Models\UserCompany::class=>'invite_user',//企业邀请员工对应的通知标识
        /*
         * special_class--特殊的一些类(主要是动态里矛盾展示问题)
         */
        \App\Models\Pst::class.'pst_beside'=>'pst_beside',//评审通对应的通知标识(外部联系人--个人工作动态)
    ],
    /*
     *ws服务器推送数据格式--修改
     */
    'push_data_format'=>[
        'type'=>'',
        'data'=>[
            'type'=>'',
            'data'=>[]
        ],
    ],
];
