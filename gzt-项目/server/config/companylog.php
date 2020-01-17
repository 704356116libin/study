<?php

return [
    /*
        模块类型
    */
    'module'=>[//$key 为对应模型的表名
//      'business_management'=>'系统管理',
      'roles'=>['name'=>'职务/角色','model'=>\App\Models\Role::class,'table'=>'roles'],
      'approval_template'=>['name'=>'审批模板','model'=>\App\Models\ApprovalTemplate::class,'table'=>'approval_template'],
      'company_notice'=>['name'=>'公告','model'=>\App\Models\CompanyNotice::class,'table'=>'company_notice'],
    ],
    'module_type'=>[
        //系统管理
        'business_management'=>[
            'edit_business_data'=>'修改企业信息',
            'up_enterprise_file'=>'上传企业认证文件',
            'add_staff'=>'添加员工',
//            ''=>'冻结员工',
            'add_role'=>'创建职务',
            'edit_role'=>'修改职务',
        ],
        //审批模块
        'approval'=>[
            'add'=>'添加审批模板',
            'edit'=>'编辑审批模板'
        ],
        //公告模块
        'notice'=>[
            'add_notice'=>'发布公告',
        ],
    ],
    /*
        终端设备
    */
    'terminal_equipment'=>[
        'PC'=>'PC端',
        'iPhone'=>'iPhone端',
        'iPad'=>'iPad端',
        'Android'=>'Android端',
        'other'=>'其他',
    ],

    'model'=>[
        'role'=>[
            'type'=>'职务/角色',
            'name'=>'职务/角色名称',
            'description'=>'职务/角色描述',
            'guard_name'=>'警卫对象',
            'sort'=>'分类',
            'deleted_at'=>'删除时间',
        ]
    ]
];