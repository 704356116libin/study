<?php
namespace App\Http\Controllers\Api;

use App\Tools\BusinessManagementTool;
use Illuminate\Http\Request;

class BusinessManagementController extends Controller
{
    private $getBusinessManagementTool;
    /**
     * 构造函数,获取企业认证工具类
     */
    public function __construct()
    {
        $this->getBusinessManagementTool = BusinessManagementTool::getBusinessManagementTool();
    }
    /**
     * 企业后台首页
     */
    public function index()
    {
        return $this->getBusinessManagementTool->index();
    }
    /**
     * 获取公司信息
     */
    public function companyData()
    {
        return $this->getBusinessManagementTool->companyData();
    }
    /**
     * 保存企业信息
     */
    public function enterpriseInfoSave(Request $request)
    {
        return $this->getBusinessManagementTool->enterpriseInfoSave($request->all());
    }
    /**
     * 上传企业认证文件
     */
    public function companyEnterpriseFile(Request $request)
    {
        return $this->getBusinessManagementTool->companyEnterpriseFile($request->all());
    }
    /**
     * 企业认证文件
     */
    public function enterpriseFile()
    {
        return $this->getBusinessManagementTool->enterpriseFile();
    }
    /**
     * 企业认证文件
     */
    public function getEnterpriseFile()
    {
        return $this->getBusinessManagementTool->getEnterpriseFile();
    }
    /**
     * 职务列表
     */
    public function allRoles(Request $request)
    {
        return $this->getBusinessManagementTool->allRoles($request->all());
    }
    /**
     * 搜索职务
     */
    public function searchRole(Request $request)
    {
        return $this->getBusinessManagementTool->searchRole($request->role_name);
    }
    /**
     * 添加职务
     */
    public function addRole(Request $request)
    {
        return $this->getBusinessManagementTool->addRole($request->all());
    }
    /**
     * 为职务/角色添加用户
     */
    public function giveRoleUsers(Request $request)
    {
        return $this->getBusinessManagementTool->giveRoleUsers($request->all());
    }

    /**
     * 编辑职务
     */
    public function editRole(Request $request)
    {
        return $this->getBusinessManagementTool->editRole($request->role_id);
    }
    /**
     * 保存编辑职务
     */
    public function saveEditRole(Request $request)
    {
        return $this->getBusinessManagementTool->saveEditRole($request->all());
    }

    /**
     * 删除职务
     */
    public function deleteRole(Request $request)
    {
        return $this->getBusinessManagementTool->deleteRole($request->role_id);
    }
    /**
     * 公司基础权限
     */
    public function c_per()
    {
        return $this->getBusinessManagementTool->c_per();
    }
    /**
     * 1生成邀请码
     */
    public function generateInvitationCode(Request $request)
    {
        return $this->getBusinessManagementTool->generateInvitationCode();
    }

    /**
     * 2兑换邀请码
     */
    public function redeemInvitationCode(Request $request)
    {
        return $this->getBusinessManagementTool->redeemInvitationCode($request->all());
    }

    /**
     * 3设置用户名和密码或验证用户名和密码(请求数据同用户注册一样)
     */
    public function setUser(Request $request)
    {
        return $this->getBusinessManagementTool->setUser($request);
    }
    /**
     * 邀请链接邀请(生成邀请连接)
     */
    public function invitationUrl()//管理员生成邀请连接
    {
        return $this->getBusinessManagementTool->invitationUrl();
    }

    /**
     * 合作伙伴组列表
     */
    public function companyPartnerTypes(Request $request)
    {
        return $this->getBusinessManagementTool->companyPartnerTypes();
    }

    /**
     * @param Request $request
     * @return mixed
     * 增删该合作伙伴类型
     */
    public function companyPartnerTypesOperating(Request $request)
    {
        return $this->getBusinessManagementTool->companyPartnerTypesOperating($request->all());
    }
    /**
     * 公司合作伙伴信息
     * @param Request $request
     */
    public function companyPartner(Request $request)
    {
        return $this->getBusinessManagementTool->companyPartner($request->all());
    }
    /**
     * 按公司名模糊查询本公司合作伙伴
     * @param Request $request
     */
    public function companyPartnerByName(Request $request)
    {
        return $this->getBusinessManagementTool->companyPartnerByName($request->all());
    }
    /**
     * 搜索公司合作伙伴
     * @param Request $request
     */
    public function searchCompanyPartner(Request $request)
    {
        return $this->getBusinessManagementTool->searchCompanyPartner($request->name);
    }
    /**
     * 合作伙伴申请列表
     * @param Request $request
     */
    public function companyPartnerApply()
    {
        return $this->getBusinessManagementTool->companyPartnerApply();
    }

    /**
     * @param Request $request
     * @return array
     * 删除合作伙伴关系
     */
    public function deleteCompanyPartner(Request $request)
    {
        return $this->getBusinessManagementTool->deleteCompanyPartner($request->company_id);
    }
    /**
     * 批量操作合作伙伴分组
     */
    public function partnerGroupEdit(Request $request)
    {
        return $this->getBusinessManagementTool->partnerGroupEdit($request->relate_id,$request->type_id);
    }

    /**
     * 公司下的部门
     * @return mixed
     */
    public function descendants()
    {
        return $this->getBusinessManagementTool->descendants();
    }

    /**
     * @return array
     * 部门排序
     */
    public function departmentOrdering(Request $request)
    {
        return $this->getBusinessManagementTool->departmentOrdering($request->all());
    }
    /**
     * 职务排序
     */
    public function jobOrdering(Request $request)
    {
        return $this->getBusinessManagementTool->jobOrdering($request->all());
    }
    /*******************外部联系人****************************/
    /**
     * 搜索外部联系人
     */
    public function searchExternalContactUsers(Request $request)
    {
        return $this->getBusinessManagementTool->searchExternalContactUsers($request->condition);
    }
    /**
     * 邀请外部联系人
     */
    public function inviteExternalContactUsers(Request $request)
    {
        return $this->getBusinessManagementTool->inviteExternalContactUsers($request->user_id,$request->description);
    }
    /**
     * 外部联系公司邀请列表
     */
    public function applyExternalContactCompanys()
    {
        return $this->getBusinessManagementTool->applyExternalContactCompanys();
    }
    /**
     * 处理外部联系公司申请
     */
    public function dealExternalContactUsers(Request $request)
    {
        return $this->getBusinessManagementTool->dealExternalContactUsers($request->company_id,$request->agreeOrRefuse,$request->type_id);
    }
    /**
     * 处理员工邀请
     */
    public function dealStaffInvite(Request $request)
    {
        return $this->getBusinessManagementTool->dealStaffInvite($request->all());
    }
    /**
     * @param $data
     * 增删该外部联系人类型
     */
    public function externalContactTypesOperating(Request $request)
    {
        return $this->getBusinessManagementTool->externalContactTypesOperating($request->all());
    }
    /**
     * 外部联系人分类列表
     */
    public function externalContactTypes()
    {
        return $this->getBusinessManagementTool->externalContactTypes();
    }
    /**
     * 外部联系公司分类列表
     */
    public function externalCompanyTypes()
    {
        return $this->getBusinessManagementTool->externalCompanyTypes();
    }
    /**
     * @return mixed
     * 返回外部联系人数据
     */
    public function externalContactUsers(Request $request)
    {
        return $this->getBusinessManagementTool->externalContactUsers($request->all());
    }
    /**
     * 删除外部联系人
     */
    public function deleteExternalUser(Request $request)
    {
        return $this->getBusinessManagementTool->deleteExternalUser($request->user_id);
    }
    /**
     * 删除外部联系公司
     */
    public function deleteExternalCompany(Request $request)
    {
        return $this->getBusinessManagementTool->deleteExternalCompany($request->company_id);
    }
    /**
     * @return mixed
     * 返回外部联系公司数据
     */
    public function externalContactCompanys(Request $request)
    {
        return $this->getBusinessManagementTool->externalContactCompanys($request->all());
    }
    /**
     * 批量操作分组(外部联系人)
     */
    public function externalGroupEdit(Request $request)
    {
        return $this->getBusinessManagementTool->externalGroupEdit($request->relate_id,$request->type_id,$request->type);
    }
    /**
     * tel email name 模糊查询外部联系人
     */
    public function externalUserByName(Request $request)
    {
        return $this->getBusinessManagementTool->externalUserByName($request->condition);
    }
    /**
     * 日志模块列表(左侧列表)
     */
    public function logModuleType(Request $request)
    {
        return $this->getBusinessManagementTool->logModuleType();
    }
    /**
     * 日志搜索列表(右侧列表)
     */
    public function searchOperationLog(Request $request)
    {
        return $this->getBusinessManagementTool->searchOperationLog($request->all());
    }
    /**
     * @return array
     * 公司已开启的功能模块
     */
    public function companyFuns()
    {
        return $this->getBusinessManagementTool->companyFuns();
    }

    /**
     * @param $data
     * 设置功能是否开启
     */
    public function setCompanyFun(Request $request)
    {
        return $this->getBusinessManagementTool->setCompanyFun($request->all());
    }

    /**
     * 用户功能展示模块
     */
    public function FunShow()
    {
        return $this->getBusinessManagementTool->FunShow();
    }
    /**
     * 测试
     */
    public function test(Request $request)
    {
        return $this->getBusinessManagementTool->test($request);
    }

}