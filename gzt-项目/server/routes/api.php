<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$api = app('Dingo\Api\Routing\Router');//Dingo接管整个路由
$api->version('v1',[ 'namespace' => 'App\Http\Controllers\Api'],function($api) {
    $api->post('version','DingoController@version'  );
    $api->post('version2','DingoController@version2'  );
    $api->post('/api_test','DingoController@api_test')->middleware('auth:api');//api接口测试
    /**
     *1分钟限制访问10次的路由组
     */
    $api->group(['middleware' => 'api.throttle', 'limit' => 10, 'expires' => 1,], function($api) {
        $api->any('captchas', 'CaptchaController@makeCaptcha');//图片验证码接口
    });
    /**
     *1分钟限制访问10次的路由组
     */
    $api->group(['middleware' => 'api.throttle', 'limit' => 10, 'expires' => 1,], function($api) {
        $api->any('/getTelCode','SmsController@getTelCode');//发送短信验证
    });
    /**
     * 需要身份验证的路由
     */
    $api->group(['middleware' => 'auth:api', 'limit' => 1, 'expires' => 2,], function($api) {
        /**
         * 用户信息调取模块
         */
        $api->get('/u_get_permissions','UserController@permissions'  );//获取用户权限数组
        $api->get('/u_get_card_info','UserController@getUserCardInfo'  );//获取用户的名片信息
        $api->post('/u_alter_company_id','UserController@alterCurrentCompany'  );//变更用户当前企业
        $api->get('/u_get_base_info','UserController@getLoginUserInfo'  );//拉取用户基础信息
        $api->post('/u_eidtPersonalData','UserController@eidtPersonalData'  );//修改用户基础信息
        $api->get('/u_get_getPersonalAvatar','UserController@getPersonalAvatar'  );//获取个人头像
        $api->post('/u_get_editPersonalAvatar','UserController@editPersonalAvatar'  );//更换个人头像
        $api->get('/u_get_invitelist','UserController@invitelist'  );//邀请列表
        $api->post('/management_deal_external_users','BusinessManagementController@dealExternalContactUsers')->middleware('externalCompany');//处理外部联系公司申请
        $api->post('/management_dealStaffInvite','BusinessManagementController@dealStaffInvite');//处理员工邀请
        /**
         * 企业/组织信息模块
         */
        $api->post('/c_create','CompanyController@createCompany'  );//创建一个企业
        $api->get('/c_company_list','CompanyController@getCompanyList'  );//公司列表,用于用户切换公司
        $api->post('/c_company_change','CompanyController@changeCompany'  );//用户切换公司
        $api->post('/c_check_name_verified','CompanyController@checkNameVerified'  );//检查某个企业名称是否已经被注册
        $api->post('/c_search_company_by_name','CompanyController@searchCompanyByName'  );//通过名称搜索企业信息
        /**
         * 企业合作伙伴
         */
        $api->post('/c_send_company_partner','CompanyController@sendCompanyPartner'  )->middleware('partner');//发起合作伙伴邀请
        $api->post('/c_deal_company_partner','CompanyController@dealCompanyPartner'  )->middleware('partner');//处理合作伙伴邀请
        $api->post('/c_get_company_partner','CompanyController@getCompanyPartner'  );//获取某企业的合作伙伴信息
        $api->get('/c_get_company_partner_record','CompanyController@getCompanyPartner'  );//获取某企业邀请合作伙伴的记录
        /**
         * 企业公告模块
         */
        $api->post('/c_notice_add','CompanyNoticeController@add'  );//新建一个公告
        $api->post('/c_notice_remove','CompanyNoticeController@remove'  );//移除一个或多个公告
        $api->post('/c_notice_top','CompanyNoticeController@topRemark'  );//置顶某个公告
        $api->post('/c_notice_topCancle','CompanyNoticeController@topCancle'  );//取消置顶某个公告
        $api->get('/c_notice_show','CompanyNoticeController@getShowNotice'  );//获取某个用户某个企业有权可见的公告信息
        $api->get('/c_notice_showByColumn','CompanyNoticeController@getShowNoticeByColumn'  );//获取某个用户某个企业有权可见的公告信息--通过栏目
        $api->get('/c_notice_searchNoticeByTitle','CompanyNoticeController@searchNoticeByTitle'  );//通过标题搜索可见公告搜索
        $api->get('/c_notice_getNoticeById','CompanyNoticeController@getNoticeById');//获取单个公告详情
        $api->post('/c_notice_followNotice','CompanyNoticeController@userFollowNotice');//关注某个公告
        $api->post('/c_notice_deFollowNotice','CompanyNoticeController@userDeFollowNotice');//取关注某个公告
        $api->get('/c_notice_user_follows','CompanyNoticeController@getUserFollowNoticeList');//获取某用户在某企业下所关注的公告列表信息
        $api->get('/c_notice_browse_record','CompanyNoticeController@getNoticeLookRecord');//获取某条公告的已读记录
        $api->get('/c_notice_unbrowse_record','CompanyNoticeController@getNoticeUnLookRecord');//获取某条公告的未读记录
        $api->post('/c_notice_cancelNotice','CompanyNoticeController@cancelNotice');//撤销某个公告
        $api->get('/c_notice_getCancelNotice','CompanyNoticeController@getCancelNotice');//获取所有撤销的公告
        $api->post('/c_notice_updateNotice','CompanyNoticeController@updateNotice');//更新公告信息*cccc()
        $api->post('/c_notice_publish','CompanyNoticeController@publish');//发布某条公告
        $api->post('/c_notice_getPartnerNotice','CompanyNoticeController@getPartnerNotice');//跨圈公告(合作伙伴)
        $api->post('/c_notice_getExternalNotice','CompanyNoticeController@getExternalNotice');//跨圈公告(外部联系公司)
        $api->post('/c_notice_getNoticeFile','CompanyNoticeController@downloadFile');//下载附件
        $api->post('/c_notice_transferFile','CompanyNoticeController@transferFile');//附件存网盘
        $api->post('/c_notice_accessLog','CompanyNoticeController@accessLog');//附件访问记录
        //公告栏目
        $api->post('/c_notice_addColumn','CompanyNoticeColumnController@addColumn'  );//新建一个公告栏目
        $api->post('/c_notice_removeColumn','CompanyNoticeColumnController@removeColumn'  );//移除一个公告栏目
        $api->post('/c_notice_sortColumn','CompanyNoticeColumnController@sortColumn');//移除一个公告栏目
        $api->get('/c_notice_getAllColumn','CompanyNoticeColumnController@getAllColumn');//获取公告栏目
        $api->post('/c_notice_alterColumn','CompanyNoticeColumnController@alterColumn');//更改栏目信息
        /**
         * 企业部门模块
         */
//        $api->post('/c_department_addRootNode','DepartmentController@add');//新建某个企业一个部门根节点
        $api->post('/c_department_appendNode','DepartmentController@appendNode');//新建某个企业一个部门根节点(后台创建分组)
        $api->post('/c_department_editDepartment','DepartmentController@editDepartment');//编辑部门
        $api->get('/c_department_deleteDepartment','DepartmentController@deleteDepartment');//删除部门
        $api->get('/c_department_getAllTree','DepartmentController@getAllTree');//拿到某个企业的根树(后台部门列表+员工列表)
        $api->get('/c_department_getCompanyAll','DepartmentController@getCompanyAll');//获取公司部门树和合作伙伴树和外部联系人树
        $api->post('/c_department_saveUserDate','DepartmentController@saveUserDate')->middleware('staff');//后台管理新增员工
        $api->post('/c_department_userDetail','DepartmentController@userDetail');//员工信息(抽屉展示)
        $api->post('/c_department_editUserDetail','DepartmentController@editUserDetail');//编辑员工信息
        $api->post('/c_department_addStallByTel','DepartmentController@addStallByTel')->middleware('staff');//手机号或邮箱添加员工(邀请员工)
        $api->post('/c_department_getDescendantsTree','DepartmentController@getNodeDescendantsTree');//拿到某个节点下的子树
        $api->get('/c_department_searchTree','DepartmentController@searchTree');//树内搜索
        $api->get('/c_department_departmentDetail','DepartmentController@departmentDetail');//拿到某个节点的一级详细信息
        $api->post('/c_department_batchEditDepartments','DepartmentController@batchEditDepartments');//批量修改员工部门
        $api->post('/c_department_batchEditRoles','DepartmentController@batchEditRoles');//批量修改员工职务
        $api->post('/c_department_batchDisable','DepartmentController@batchDisable');//批量禁用员工
        $api->post('/c_department_batchFreeze','DepartmentController@batchFreeze');//批量启用员工
        $api->post('/c_department_thaw','DepartmentController@thaw');//解冻员工
        $api->get('/c_department_searchTel','DepartmentController@searchTel');//手机号搜索员工
        $api->any('/c_department_departmentTest','DepartmentController@test');//部门相关方法测试
        /**
         *企业云存储模块(网盘)
         */
        $api->post('/c_oss_makeDir','CompanyOssController@makeDir');//新建目录企业云存储
        $api->post('/c_oss_deleteDir','CompanyOssController@deleteDir');//删除目录
        $api->post('/c_oss_uploadPublicFile','CompanyOssController@uploadPublicFile');//直接上传工共文件
        $api->post('/c_oss_copy_file_to_path','CompanyOssController@copyFileToPath');//复制文件至指定目录--主要是模块数据保存
        $api->post('/c_oss_copy_folder','CompanyOssController@copyFolder');//复制文件夹
        $api->get('/c_oss_get_directory_to_info','CompanyOssController@getTargetDirectoryInfo');//获取指定目录下的子目录&文件信息
        $api->post('/c_oss_update_file_name','CompanyOssController@updateFileName');//更改文件名
        $api->post('/c_oss_delete_file','CompanyOssController@deleteFile');//删除文件
        $api->post('/c_oss_move_file','CompanyOssController@moveFile');//移动文件
        $api->post('/c_oss_move_folder','CompanyOssController@moveFolder');//移动文件夹
        $api->post('/c_oss_batchDelete','CompanyOssController@batchDelete');//批量删除
        $api->post('/c_oss_batchCopy','CompanyOssController@batchCopy');//批量复制
        $api->post('/c_oss_batchMove','CompanyOssController@batchMove');//批量移动
        $api->post('/c_oss_add_file_browse_record','CompanyOssController@addFileBrowseRecord');//添加文件的浏览记录
        $api->post('/c_oss_get_file_browse_record','CompanyOssController@getFileBrowseRecord');//获取文件的浏览记录

        /**
         *个人云存储模块(网盘)
         */
        $api->post('/u_oss_makeDir','PersonalOssController@makeDir');//新建目录(个人)
        $api->post('/u_oss_uploadFile','PersonalOssController@uploadFile');//上传文件
        $api->get('/u_oss_get_directory_to_info','PersonalOssController@getTargetDirectoryInfo');//获取指定目录下的子目录&文件信息
        $api->post('/u_oss_copy_file_to_path','PersonalOssController@copyFileToPath');//复制文件至指定目录--主要是模块数据保存
        $api->post('/u_oss_copy_folder','PersonalOssController@copyFolder');//复制文件夹
        $api->post('u_oss_update_file_name','PersonalOssController@updateFileName');//更改文件名
        $api->post('/u_oss_deleteDir','PersonalOssController@deleteDir');//删除目录
        $api->post('/u_oss_delete_file','PersonalOssController@deleteFile');//删除文件
        $api->post('/u_oss_move_file','PersonalOssController@moveFile');//移动文件
        $api->post('/u_oss_move_folder','PersonalOssController@moveFolder');//移动文件夹
        $api->post('/u_oss_batchDelete','PersonalOssController@batchDelete');//批量删除
        $api->post('/u_oss_batchCopy','PersonalOssController@batchCopy');//批量复制
        $api->post('/u_oss_batchMove','PersonalOssController@batchMove');//批量移动
        //云盘操作
        $api->get('/oss_fileDynamics','PersonalOssController@fileDynamics');//文件动态
        $api->get('/oss_recentlyUsed','PersonalOssController@recentlyUsed');//最近使用
        $api->post('/oss_single_file_upload','PersonalOssController@singleFileUpload');//纯文件下载
        $api->post('/oss_download_package','PersonalOssController@downloadPackage');//打包下载
        /**
         * 协作模块
         */
        $api->post('/c_assist_sendInvite','CollaborationController@sendInvite');  //发起协作
        $api->post('/c_assist_partner','CollaborationController@partner_assistance');  //合作伙伴协助
        $api->post('/c_assist_editTask','CollaborationController@editTask');  //编辑协作
        $api->get('/c_assist_taskList','CollaborationController@taskList');  //协助列表
        $api->get('/c_assist_search','CollaborationController@search');  //搜索查询
        $api->get('/c_assist_taskDetail','CollaborationController@taskDetail');  //任务详情
        $api->post('/c_assist_receiveButton','CollaborationController@receiveButton');  //点击接受
        $api->post('/c_assist_transferButton','CollaborationController@transferButton');  //点击转交
        $api->get('/c_assist_transferList','CollaborationController@transferList');  //转交列表
        $api->post('/c_assist_transferOperating','CollaborationController@transferOperating');  //操作(接收转交任务,拒绝转交任务)
        $api->get('/c_assist_rejectButton','CollaborationController@rejectButton');  //点击拒绝
        $api->post('/c_assist_carryOutButton','CollaborationController@carryOutButton');  //点击完成
        $api->post('/c_assist_auditButton','CollaborationController@auditButton');  //发起人审核操作
        $api->post('/c_assist_cancel','CollaborationController@cancel');  //发起人撤销操作
        $api->get('/c_assist_recoveryTask','CollaborationController@recoveryTask');  //恢复任务
        $api->delete('/c_assist_deleteTask','CollaborationController@deleteTask');  //删除任务
        $api->delete('/c_assist_deleteFile','CollaborationController@deleteFile');  //删除附件
        $api->post('/c_assist_saveForm','CollaborationController@saveForm');  //保存表单
        $api->get('/testCollaborative','CollaborationController@testCollaborative');  //测试
        /**
         * 审批
         */
        $api->post('/c_approval_type_add','ApprovalController@addApprovalType');  //添加类型
        $api->get('/c_approval_type_delete','ApprovalController@deleteApprovalType');  //删除类型
        $api->delete('/c_approval_file_delete','ApprovalController@deleteFile');  //删除附件
        $api->post('/c_approval_type_sequence_save','ApprovalController@saveSequence');  //管理模板审批类型拖拽排序保存入库
        $api->post('/c_approval_type_edit','ApprovalController@editApprovalType');  //管理模板编辑审批类型
        $api->get('/c_approval_types','ApprovalController@approvalTypeList');  //审批类型

        $api->post('/c_approval_template_add','ApprovalController@addApprovalTemplate');  //添加流程模板
        $api->get('/c_approval_templates_all','ApprovalController@sysTemList');  //管理模板列表
        $api->get('/c_approval_template_edit','ApprovalController@editTemplate');  //管理模板编辑审批模板
        $api->get('/c_approval_template_delete','ApprovalController@deleteTemplate');  //管理模板删除审批模板
        $api->get('/c_approval_template_enable','ApprovalController@isShow');  //是否启用模板
        $api->post('/c_approval_template_save','ApprovalController@saveEditTemplate');  //管理模板保存编辑后的审批模板 /c_approval_saveEditTemplate
        $api->get('/c_approval_templates_able','ApprovalController@ablTemplateList');  //查找所有可用模板
        $api->get('/c_approval_templates_searchAbl','ApprovalController@searchAblTemplate');  //搜索框搜索可用模板(通过name)
        $api->get('/c_approval_template_select','ApprovalController@selectTem');  //选择模板
        $api->get('/c_approval_templates_classic','ApprovalController@approvalClassicTemplate');  //审批经典模板
        $api->get('/c_approval_templates_existing','ApprovalController@existingTemplate');  //已有模板

        $api->get('/c_approval_detail','ApprovalController@detail');    //审批详情
        $api->post('/c_approval_create','ApprovalController@createApproval');  //创建审批申请
        $api->get('/c_approval_list','ApprovalController@approvalList');  //审批列表
        $api->post('/c_approval_agree','ApprovalController@agree');  //同意
        $api->post('/c_approval_refuse','ApprovalController@refuse');  //拒绝
        $api->post('/c_approval_transfer','ApprovalController@transfer');  //转交
        $api->get('/c_approval_cancel','ApprovalController@cancel');  //撤销
        $api->get('/c_approval_archive','ApprovalController@archive');  //归档
        $api->post('/c_approval_urgent','ApprovalController@urgent');  //催办
        $api->get('/c_approval_again_apply','ApprovalController@againApply');  //再次申请
        $api->post('/c_approval_export','ApprovalController@export');    //导出数据
        $api->post('/c_approval_downTest','ApprovalController@downTest');    //导出测试

        /**
         * 动态模块
         */
        $api->get('/dynamic_get_list_info','DynamicController@getListInfo');//获取动态列表信息
        $api->get('/dynamic_get_list_unreadCount','DynamicController@getListUnReadCount');//获取动态列表未读数
        $api->get('/dynamic_get_list_detail','DynamicController@getListDetailInfo');//分页获取动态列表详情
        $api->post('/dynamic_delete_list_node','DynamicController@deleteListNode');//删除某个用户列表信息中某个节点
        /**
         * 企业后台管理
         */
        $api->group(['middleware' => 'businessManagement'], function($api) {

        $api->get('/management_enterprise_company_index','BusinessManagementController@index');//获取企业后台首页数据
        $api->get('/management_enterprise_company_data','BusinessManagementController@companyData');//获取企业信息
        $api->post('/management_enterprise_info_save','BusinessManagementController@enterpriseInfoSave');//保存企业信息
        $api->post('/management_company_enterprise_file','BusinessManagementController@companyEnterpriseFile');//上传企业认证文件
        $api->get('/management_enterprise_file','BusinessManagementController@enterpriseFile');//公司的认证文件
        $api->get('/management_get_enterprise_file','BusinessManagementController@getEnterpriseFile');//获取公司认证信息
        $api->get('/management_roles','BusinessManagementController@allRoles');//职务列表
        $api->get('/management_searchRole','BusinessManagementController@searchRole');//搜索职务
        $api->post('/management_role_add','BusinessManagementController@addRole');//添加职务
        $api->post('/management_role_add_user','BusinessManagementController@giveRoleUsers');//为职务/角色添加用户
        $api->get('/management_role_edit','BusinessManagementController@editRole');//编辑职务
        $api->post('/management_role_save_edit','BusinessManagementController@saveEditRole');//保存编辑职务
        $api->delete('/management_role_delete','BusinessManagementController@deleteRole');//删除职务
        $api->get('/management_c_per','BusinessManagementController@c_per');//公司基础权限
        $api->get('/management_generate_invitation_code','BusinessManagementController@generateInvitationCode');//生成邀请码
        $api->post('/management_redeem_invitation_code','BusinessManagementController@redeemInvitationCode')->middleware('staff');//2验证链接是否过期(验证邀请码)
        $api->post('/management_set_user','BusinessManagementController@setUser');//3加入企业填写个人信息(请求数据同用户注册一样)
        $api->get('/management_invitation_url','BusinessManagementController@invitationUrl');//邀请链接邀请(生成邀请连接)
        $api->get('/management_company_partner','BusinessManagementController@companyPartner');//公司合作伙伴信息
        $api->post('/management_company_partner_by_name','BusinessManagementController@companyPartnerByName');//按公司名模糊查询本公司合作伙伴
        $api->post('/management_search_company_partner','BusinessManagementController@searchCompanyPartner');//搜索公司合作伙伴
        $api->post('/management_company_partner_apply','BusinessManagementController@companyPartnerApply');//合作伙伴申请列表
        $api->post('/management_descendants','BusinessManagementController@descendants');//公司下的部门
        $api->post('/management_department_ordering','BusinessManagementController@departmentOrdering');//部门排序
        $api->post('/management_job_ordering','BusinessManagementController@jobOrdering');//职务排序


        /**
         * 合作伙伴
         */
        $api->get('/management_company_partner_types','BusinessManagementController@companyPartnerTypes');//合作伙伴列表
        $api->post('/management_company_partner_types_operating','BusinessManagementController@companyPartnerTypesOperating');//处理合作伙伴类型(增删改)
        $api->post('/management_company_partner','BusinessManagementController@companyPartner');//合作伙伴列表
        $api->post('/management_search_company_partner','BusinessManagementController@searchCompanyPartner');//搜索公司合作伙伴
//        $api->post('/management_company_partner','BusinessManagementController@companyPartner');//申请加合作伙伴(按钮)
        $api->get('/management_company_partner_apply','BusinessManagementController@companyPartnerApply');//合作伙伴申请列表
//        $api->post('/management_company_partner','BusinessManagementController@companyPartner');//处理合作伙伴申请
        $api->delete('/management_delete_company_partner','BusinessManagementController@deleteCompanyPartner');//解除合作伙伴
        $api->post('/management_partner_group_edit','BusinessManagementController@partnerGroupEdit');//批量操作合作伙伴分组
        /**
         * 外部联系人
         */
        $api->post('/management_search_external_users','BusinessManagementController@searchExternalContactUsers');//搜索外部联系人
        $api->post('/management_invite_external_users','BusinessManagementController@inviteExternalContactUsers')->middleware('externalContact');//邀请外部联系人
        $api->get('/management_apply_external_companys','BusinessManagementController@applyExternalContactCompanys');//外部联系公司邀请列表
        $api->post('/management_external_contact_types_operating','BusinessManagementController@externalContactTypesOperating');//类型(增删改)
        $api->get('/management_external_contact_types','BusinessManagementController@externalContactTypes');//外部联系人分组
        $api->get('/management_external_company_types','BusinessManagementController@externalCompanyTypes');//外部联系公司分组
        $api->get('/management_external_users','BusinessManagementController@externalContactUsers');//返回外部联系人数据
        $api->delete('/management_delete_external_user','BusinessManagementController@deleteExternalUser');//删除外部联系人,
        $api->delete('/management_delete_external_company','BusinessManagementController@deleteExternalCompany');//删除外部联系公司,
        $api->post('/management_external_companys','BusinessManagementController@externalContactCompanys');//返回外部联系公司数据
        $api->post('/management_external_group_edit','BusinessManagementController@externalGroupEdit');//批量操作分组(外部联系人或公司)
        $api->post('/management_external_user_by_name','BusinessManagementController@externalUserByName');//模糊查询外部联系人
        $api->any('/management_test','BusinessManagementController@test')->middleware('sms');//测试
        /**
         * 公司日志
         */
        $api->get('/management_log_module_type','BusinessManagementController@logModuleType');//日志模块列表(日志左侧列表)
        $api->post('/management_search_operation_log','BusinessManagementController@searchOperationLog');//日志搜索列表(日志右侧列表)
        /**
         * 应用设置
         */
        $api->get('/management_company_funs','BusinessManagementController@companyFuns');//公司已开启的功能模块
        $api->post('/management_set_company_fun','BusinessManagementController@setCompanyFun');//设置功能是否开启
        $api->get('/management_fun_show','BusinessManagementController@FunShow');//功能模块展示
        });
        /**
         * 企业--评审通功能
         */
        /*
        * ==============================评审通流程相关=======================================>
        */
        $api->post('/c_pst_add_process_template','PstController@addProcessTemplate');//添加评审流程信息
        $api->post('/c_pst_delete_process_template','PstController@deleteProcessTemplate');//移除某个评审通模板
        $api->post('/c_pst_switch_process_template_show','PstController@switchShowProcessTemplate');//转换模板的启用禁用状态
        $api->post('/c_pst_move_process_template_type','PstController@moveProcessTemplate');//移动模板到指定类型下
        $api->get('/c_pst_get_company_process_template','PstController@getCompanyProcessTemplate');//获取某企业所有的评审通流程模板信息
        $api->get('/c_pst_get_single_process_template','PstController@getProcessTemplateById');//通过id获取指定评审通流程模板的详细信息
        $api->post('/c_pst_update_process_template','PstController@updateProcessTemplate');//通过id获取更新指定评审通流程模板的详细信息

        $api->post('/c_pst_add_process_template_type','PstController@addProcessTemplateType');//添加评审流程分类
        $api->post('/c_pst_sort_process_template_type','PstController@sortProcessTemplateType');//评审流程分类排序
        $api->get('/c_pst_get_process_template_type','PstController@getProcessTemplateType');//按序获取所有的评审流程分类
        $api->post('/c_pst_alter_process_template_name','PstController@alterProcessTemplateTypeName');//更改评审通流程分类名称
        $api->post('/c_pst_delete_process_template_name','PstController@deleteProcessTemplateType');//删除评审通流程分类名称

        /*
         * ==============================评审通模板相关=======================================>
         */
        $api->post('/c_pst_add_pst_template','PstController@addPstTemplate');//添加评审通模板
        $api->post('/c_pst_delete_pst_template','PstController@deletePstTemplate');//移除某个评审通模板
        $api->post('/c_pst_switch_pst_template_show','PstController@switchShowPstTemplate');//转换模板的启用禁用状态
        $api->post('/c_pst_move_pst_template_type','PstController@moveTemplate');//移动模板到指定类型下
        $api->get('/c_pst_get_classic_pst_template','PstController@getClassicPstTemplate');//获取网站的经典评审通模板信息
        $api->get('/c_pst_get_company_pst_template','PstController@getCompanyPstTemplate');//获取某企业所有的评审通模板信息
        $api->get('/c_pst_get_single_pst_template','PstController@getPstTemplateById');//通过id获取指定评审通模板的详细信息
        $api->post('/c_pst_update_pst_template','PstController@updatePstTemplate');//通过id获取更新指定评审通模板的详细信息

        $api->post('/c_pst_add_pst_template_type','PstController@addPstTemplateType');//添加评审通分类
        $api->post('/c_pst_sort_pst_template_type','PstController@sortPstTemplateType');//评审分类排序
        $api->get('/c_pst_get_pst_template_type','PstController@getPstTemplateType');//按序获取所有的评审通分类
        $api->post('/c_pst_alter_pst_template_name','PstController@alterPstTemplateTypeName');//更改评审通分类名称
        $api->post('/c_pst_delete_pst_template_name','PstController@deletePstTemplateType');//删除评审通分类名称

        /*
         * ==============================评审通基础表单相关=======================================>
         */
        $api->get('/c_pst_get_basic_form_data','PstController@getFormBasicData');//拉取企业评审通基础表单数据
        $api->post('/c_pst_update_basic_form_data','PstController@updateCompanyFormBasicData');//变更企业评审通基础表单数据
        /*
         * ==============================评审通相关=======================================>
         */
        $api->post('/c_pst_create','PstController@createPst');//评审通的创建
        $api->get('/c_pst_search_by_my_type','PstController@searchMyPstByType');//评审通的按类型搜索与我相关的记录
        $api->get('/c_pst_search_by_state','PstController@searchPstByState');//评审通的按流程类型搜索评审通记录
        $api->get('/c_pst_union_search_by_state','PstController@unionSearchPstByState');//评审通联合分类分页查询
        $api->get('/c_pst_get_single_detail','PstController@getPstById');//通过id 获取指定的评审通详情记录
        //================================评审通附件相关=========================================>
        $api->get('/c_pst_get_single_files','PstController@getPstFiles');//通过id 获取指定的评审通的资料清单
        $api->post ('/c_pst_update_single_files','PstController@updatePstFiles');//更新指定的评审通的资料清单


        /*
        * ==============================评审通操作相关=======================================>
        */
        $api->post('/c_pst_receive','PstController@receive');//接收
        $api->post('/c_pst_refuse_receive','PstController@refuse_receive');//拒绝接收
        $api->post('/c_pst_editor','PstController@editor');//编辑
        $api->post('/c_pst_appoint','PstController@appoint');//指派
        $api->post('/c_pst_transfer_duty','PstController@transfer_duty');//转移负责人
        $api->post('/c_pst_transfer_join','PstController@transfer_join');//转移参与人
        $api->post('/c_pst_deliver','PstController@deliver');//递交
        $api->post('/c_pst_recall','PstController@recall');//召回
        $api->post('/c_pst_cancle','PstController@cancle');//作废
        $api->post('/c_pst_back','PstController@back');//打回
        $api->post('/c_pst_retract','PstController@retract');//撤回
        $api->post('/c_pst_finish','PstController@finish');//完成
        $api->post('/c_pst_archive','PstController@archive');//归档
        $api->get('/c_pst_get_merge_data','PstController@getMergeData');//获取需要合并的下级数据
        $api->post('/c_pst_update_join_form_data','PstController@updateInsideJoinForm');//更新目标评审通下的内部参与人表单数据
        $api->post('/c_pst_duty_agree_join','PstController@duty_agree_join');//确认参与人的信息
        /*
         * =============================评审通关联审批======================================>
         */
        $api->get('/c_pst_get_related_approval','PstController@getPstRelationApproval');//拉取评审通关联审批信息
        /*
       * =============================评审通关联评审通======================================>
       */
        $api->get('/c_pst_get_self_related','PstController@getPstSelfRelation');//拉取评审通关联评审通信息
        $api->get('/c_pst_remove_self_related','PstController@removePstSelfRelation');//移除评审通关联评审通
        $api->get('/c_pst_can_related','PstController@getCanRelationPst');//查询出某企业内能够让进行关联的评审通
        //===============================评审通操作记录相关==========================================================>
        $api->get('/c_pst_operate_record','PstController@getPstOperateRecord');//查询出某评审通的操作记录
        $api->get('/c_pst_current_handler','PstController@getPstCurrentHandler');//查询出某评审通的当前处理人

        //===============================评审通报告文号相关==========================================================>
        $api->post('/c_pst_make_report_number','PstController@makeReportNumber');//生成 or 更新企业
        $api->get('/c_pst_report_number_get','PstController@getReportNumber');// 获取企业文号规则

        //==============================产品 订单测试相关==========================================================>
        $api->get('getProducts', 'ProductsController@getProducts');//查询产品库
        $api->get('getProduct', 'ProductsController@getProduct');//查询产品详情
        $api->get('getOrders', 'OrdersController@getOrders');//查询订单
        $api->get('getOrder', 'OrdersController@getOrder');//查询订单详情
        $api->post('saveOrder','OrdersController@store');//

        $api->post('/pay/getSurplusDaysDeductedAmount','PayController@getSurplusDaysDeductedAmount');//获取抵扣金额
        $api->post('/pay/cancelMemberOrder','PayController@cancelMemberOrder');//取消订单
        $api->post('/pay/getMemberPayUrl','PayController@getMemberPayUrl');//获取订单支付链接
        $api->post('/getWxOpenIdAuthUrl','SocialiteController@getWxOpenIdAuthUrl');//获取订单支付链接

        $api->post('/getPaidByOrderNo','OrdersController@getPaidByOrderNo');
        $api->get('/getOrderNo','OrdersController@getOrderNo');
        //===========================================评审通导出模板设置=======================================================>
        $api->get('/c_pst_getExportTypeList','PstController@getExportTypeList');//获取导出模板类型列表//
        $api->post('/c_pst_createExportType','PstController@createExportType');//创建分组
        $api->post('/c_pst_deleteExportType','PstController@deleteExportType');//删除分组
        $api->post('/c_pst_editExportType','PstController@editExportType');//编辑分组
        $api->post('/c_pst_saveExportTypeSequence','PstController@saveExportTypeSequence');//保存分组排序
        $api->post('/c_pst_createExportTemplate','PstController@createExportTemplate');//创建评审通导出模板
        $api->get('/c_pst_exportTemplateList','PstController@exportTemplateList');//评审通导出模板列表
        $api->post('/c_pst_exportTemplateEdit','PstController@exportTemplateEdit');//评审通导出模板编辑
        $api->post('/c_pst_exportTemplateSaveEdit','PstController@exportTemplateSaveEdit');//评审通导出模板保存编辑
        $api->post('/c_pst_exportTemplateEnable','PstController@exportTemplateEnable');//评审通导出模板禁用(启用)
        $api->post('/c_pst_exportTemplateMove','PstController@exportTemplateMove');//评审通导出模板移动
        $api->post('/c_pst_createExportPackage','PstController@createExportPackage');//创建或编辑导出模板打包分组
        $api->post('/c_pst_deleteExportPackage','PstController@deleteExportPackage');//删除导出模板打包分组
        $api->post('/c_pst_editExportPackageName','PstController@editExportPackageName');//编辑导出模板打包分组名称
        $api->post('/c_pst_exportSingleTemplatePackage','PstController@exportSingleTemplatePackage');//个人选择导出单个模板进行导出
        $api->post('/c_pst_exportTemplatePackage','PstController@exportTemplatePackage');//个人选择导出模板进行打包导出(多个模板导出)
        $api->get('/c_pst_exportPackageLike','PstController@exportPackageLike');//个人导出模板包列表
        $api->get('/c_pst_getReplacedVarTemplate','PstController@getReplacedVarTemplate');//生成单个评审通报告(变量替换返回模板)
        $api->get('/c_pst_test','PstController@test');//生成单个评审通报告(变量替换返回模板)



        $api->post('/invoice_save_title','InvoicesController@saveInvoiceTitle'); //保存发票抬头
        $api->post('/invoice_save_message','InvoicesController@saveInvoice'); //保存发票信息
        $api->get('/getOrderList','InvoicesController@getOrderList'); //获取可开票的订单列表
        $api->post('/delInvoiceTitle','InvoicesController@delInvoiceTitle'); //删除发票抬头
        $api->post('/setDefaultTitle','InvoicesController@setDefaultTitle'); //设置发票默认抬头
        $api->get('/getInvoiceDetail','InvoicesController@getInvoiceDetail'); //发票详情
        $api->post('/setInvoiceState','InvoicesController@setInvoiceState'); //修改发票申请状态
        $api->get('/getDefaultInvoiceTitle','InvoicesController@getDefaultInvoiceTitle'); //获取用户发票默认抬头
        $api->get('/getAllInvoiceTitle','InvoicesController@getAllInvoiceTitle'); //发票抬头列表
        $api->get('/invoice_list','InvoicesController@getInvoices'); //发票记录


    });

});
$api->version('v2', function($api) {
    $api->post('version','App\Http\Controllers\Server\DemoController@version2');
});
