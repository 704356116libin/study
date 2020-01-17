<?php
namespace App\Interfaces;


use Illuminate\Http\Request;

interface CompanyInterface
{
    public function createCompany(Request $request);    //创建公司
    public function checkNameVerified($name);//检查该公司or组织名称是否已经被注册
    public function searchCompanyByName(Request $request);//通过名称搜索某公司or组织
    public function checkCompanyExist(int $company_id);//校验某企业是否存在
    public function sendCompanyPartner(array $data);//发送企业邀请
    public function dealCompanyPartner(array $data);//处理企业邀请
    public function getCompanyPartnerRecord(int $company_id);//获取企业邀请记录
    public function getCompanyPartner($company_id);//查询某企业的合作伙伴
}