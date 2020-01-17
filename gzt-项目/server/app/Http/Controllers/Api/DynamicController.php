<?php

namespace App\Http\Controllers\Api;

use App\Tools\DynamicTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 动态模块控制器
 * Class DynamicController
 * @package App\Http\Controllers\Api
 */
class DynamicController extends Controller
{
    private $dynamicTool;
    /**
     * DynamicController constructor.
     */
    public function __construct()
    {
        $this->dynamicTool=DynamicTool::getInstance();
    }
    /**
     * 获取动态列表的详情信息--分页加载
     */
    public function getListDetailInfo(Request $request)
    {
        return $this->dynamicTool->getListDetailInfo($request);
    }
    /**
     *获取动态列表信息
     */
    public function getListInfo(Request $request){
        return $this->dynamicTool->getListInfo();
    }
    /**
     *获取动态列表未读数
     */
    public function getListUnreadCount(Request $request){
        return $this->dynamicTool->getListUnreadCount();
    }
    /**
     * 删除列表信息中的某个节点数据
     */
    public function deleteListNode(Request $request)
    {
        return $this->dynamicTool->deleteListNode($request);
    }
}
