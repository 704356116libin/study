<?php

namespace App\Http\Controllers\Api;

use App\Tools\CompanyNoticeColumnTool;
use App\Tools\CompanyNoticeTool;
use App\Tools\DepartmentTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 企业公告栏目控制器
 * Class DepartmentController
 * @package App\Http\Controllers\Api
 */
class CompanyNoticeColumnController extends Controller
{
    private $cNoticeColumnTool;//企业公告栏目工具类
    /**
     * DepartmentController constructor.
     */
    public function __construct()
    {
        $this->cNoticeColumnTool=CompanyNoticeColumnTool::getCompanyNoticeColumnTool();
    }
    /**
     * 添加栏目
     * @param $company_id
     * @param $data
     */
    public function addColumn(Request $request)
    {
        return $this->cNoticeColumnTool->addColumn($request->all());
    }
    /**
     * 移除栏目
     * @param array $data
     */
    public function removeColumn(Request $request)
    {
        return $this->cNoticeColumnTool->removeColumn($request->all());
    }
    /**
     * 栏目排序(前端传参)
     * @param $data
     */
    public function sortColumn(Request $request)
    {
        return $this->cNoticeColumnTool->sortColumn($request->all());
    }
    /**
     * 获取企业公告栏目信息
     */
    public function getAllColumn(Request $request)
    {
        return $this->cNoticeColumnTool->getAllColumn($request);
    }
    /**
     * 更改栏目
     * @param array $data
     */
    public function alterColumn(Request $request)
    {
        return $this->cNoticeColumnTool->alterColumn($request->all());
    }
}
