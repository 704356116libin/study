<?php

namespace App\Http\Controllers\Api;

use App\Tools\CompanyTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 企业/组织控制器
 * Class CompanyControoler
 * @package App\Http\Controllers\Api
 */
class CompanyController extends Controller
{
    private $companyTool;//企业or组织工具类

    /**
     * CompanyController constructor.
     */
    public function __construct()
    {
        $this->companyTool = CompanyTool::getCompanyTool();
    }
    /**
     * 创建一个公司
     * @param \Illuminate\Http\Request $request
     */
    public function createCompany(Request $request)
    {
        return $this->companyTool->createCompany($request);
    }
    /**
     * 检查该名称是否已经有公司注册并认证
     * @param $name
     * @return string:false代表不存在已认证的企业反之亦然
     */
    public function checkNameVerified(Request $request)
    {
        return $this->companyTool->checkNameVerified($request->name);
    }
    /**
     * 通过名称来搜索公司
     * @param $name
     */
    public function searchCompanyByName(Request $request)
    {
        return $this->companyTool->searchCompanyByName($request);
    }
    /**
     * 发送合作伙伴邀请
     * @param array $data
     */
    public function sendCompanyPartner(Request $request){
        return $this->companyTool->sendCompanyPartner($request->all());
    }
    /**
     * 处理合作伙伴邀请
     * @param array $data
     */
    public function dealCompanyPartner(Request $request){
        return $this->companyTool->dealCompanyPartner($request->all());
    }
    /**'
     * 获取某个企业的合作伙伴信息
     * @param $company_id:目标企业
     */
    public function getCompanyPartner(Request $request)
    {
       return $this->companyTool->getCompanyPartner($request->get('company_id'));
    }
    /**
     * 获取某个企业的合作伙伴的邀请记录
     * @param int $company_id
     */
    public function getCompanyPartnerRecord(Request $request)
    {
        return $this->companyTool->getCompanyPartnerRecord($request->get('company_id'));
    }

    /**
     * @param Request $request
     * 获取公司列表所在
     */
    public function getCompanyList()
    {
        return $this->companyTool->getCompanyList();
    }
    /**
     * 用户切换公司
     */
    public function changeCompany(Request $request)
    {
        return $this->companyTool->changeCompany($request->company_id);
    }
}
