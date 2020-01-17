<?php

namespace App\Http\Controllers\Api;

use App\Events\InstationNotifiyEvent;
use App\Notifications\InstationNotifiy;
use App\Tools\CompanyNoticeTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 企业公告控制器
 * Class DepartmentController
 * @package App\Http\Controllers\Api
 */
class CompanyNoticeController extends Controller
{
    private $companyNoticeTool;//公司企业公告工具类
    /**
     * DepartmentController constructor.
     */
    public function __construct()
    {
        $this->companyNoticeTool=CompanyNoticeTool::getCompanyNoticeTool();
    }
    /**
     * 新建公告
     * @param array $data:公告数据
     */
    public function add(Request $request)
    {
        return $this->companyNoticeTool->add($request);
    }
    /**
     * 移除公告
     * @param array $data
     */
    public function remove(Request $request)
    {
       return $this->companyNoticeTool->remove($request->notice_id);
    }
    /**
     * 置顶公告
     * @param array $data
     */
    public function topRemark(Request $request)
    {
        return $this->companyNoticeTool->topRemark($request->notice_id);
    }
    /**
     * 取消置顶公告
     * @param array $data
     */
    public function topCancle(Request $request)
    {
        return $this->companyNoticeTool->topCancle($request->notice_id);
    }
    /**
     * 返回某用户所有有权看到的公告(应用Laravel资源类)
     */
    public function getShowNotice(Request $request)
    {
        $user=auth('api')->user();
        return $this->companyNoticeTool->getShowNotice($request->all(),$user);
    }
    /**
     * 返回某用户所有有权看到的公告--通过栏目(应用Laravel资源类)
     */
    public function getShowNoticeByColumn(Request $request)
    {
        $user=auth('api')->user();
        return $this->companyNoticeTool->getShowNoticeByColumn($request->all(),$user);
    }
    /**
     * 返回某用户所有有权看到的公告--搜索标题(应用Laravel资源类)
     */
    public function searchNoticeByTitle(Request $request)
    {
        $user=auth('api')->user();
        return $this->companyNoticeTool->searchNoticeByTitle($request->all(),$user);
    }
    /**
     * 通过id获取公告
     * @param $notice_id
     */
    public function getNoticeById(Request $request)
    {
        return CompanyNoticeTool::getCompanyNoticeTool()->getNoticeById($request->notice_id);
    }
    /**
     * 用户关注某条公告
     * @param $notice_id
     */
    public function userFollowNotice(Request $request)
    {
       return $this->companyNoticeTool->userFollowNotice($request->notice_id);
    }
    /**
     * 用户取关注某条公告
     * @param $notice_id
     */
    public function userDeFollowNotice(Request $request)
    {
        return $this->companyNoticeTool->userDeFollowNotice($request->notice_id);
    }
    /**
     * 拿到某用户在某公司关注的公告列表--分页
     */
    public function getUserFollowNoticeList(Request $request){
        return $this->companyNoticeTool->getUserFollowNoticeList($request) ;
    }
    /**
     * 获取某条公告的浏览记录(已浏览的&未浏览的)
     * @param $notice_id
     */
    public function getNoticeLookRecord(Request $request){
        return $this->companyNoticeTool->getNoticeLookRecord($request->notice_id,'get_browse_user',$request);
    }
    /**
     * 获取某条公告的浏览记录(已浏览的)
     * @param $notice_id
     */
    public function getNoticeUnLookRecord(Request $request){
        return $this->companyNoticeTool->getNoticeUnLookRecord($request);
    }
    /**
     * 撤销某个公告
     * @param $notice_id
     */
    public function cancelNotice(Request $request)
    {
        return $this->companyNoticeTool->cancelNotice($request->notice_id);
    }
    /**
     * 获取所有撤销(未展示的公告)
     * @param $company_id
     */
    public function getCancelNotice(Request $request)
    {
        return $this->companyNoticeTool->getCancelNotice($request->all());
    }
    /**
     * 更新某个公告信息
     * @param $company_id
     */
    public function updateNotice(Request $request)
    {
        return $this->companyNoticeTool->updateNotice($request);
    }
    /**
     *发布某条公告
     */
    public function publish(Request $request)
    {
        return $this->companyNoticeTool->publish($request);
    }
    /**
     * 获取我的外部公告(合作伙伴)
     */
    public function getPartnerNotice(Request $request)
    {
        return $this->companyNoticeTool->getPartnerNotice($request->all());
    }
    /**
     * 获取我的外部公告(外部联系公司)
     */
    public function getExternalNotice(Request $request)
    {
        return $this->companyNoticeTool->getExternalNotice($request->all());
    }

    /**
     * 下载附件
     * @param Request $request
     */
    public function downloadFile(Request $request)
    {
        return $this->companyNoticeTool->downloadFile($request->all());
    }

    /**
     * 存网盘（从公司网盘到个人网盘）
     * @param Request $request
     */
    public function transferFile(Request $request)
    {
        return $this->companyNoticeTool->transferFile($request->all());
    }

    /**
     * 访问记录
     * @param Request $request
     */
    public function accessLog(Request $request)
    {
        return $this->companyNoticeTool->getFileAccessLog($request->all());
    }
}
