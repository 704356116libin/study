<?php
/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/11/14
 * Time: 14:01
 */

namespace App\Interfaces;
use App\Models\Company;

/**
 * 企业公告栏目信息接口
 */
interface CompanyNoticeColumnInterface
{
    public function addColumn(array $data);//添加公告栏目
    public function removeColumn(array $data);//移除公告栏目
    public function sortColumn(array $data);//公告栏目排序
    public function getAllColumn($company_id);//拿到某企业的公告栏目信息
    public function alterColumn(array $data);//更改栏目信息
    public function initCompanyColumn(int $company_id);//初始化企业公告栏目信息
}