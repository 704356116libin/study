<?php
/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/11/14
 * Time: 14:01
 */

namespace App\Interfaces;
use App\Models\CompanyNotice;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * 企业公告接口
 * Interface ValidateInterface
 * @package App\Interfaces
 */
interface CompanyNoticeInterface
{
    public function add(Request $request);//新建一个公告
    public function remove($notice_id);//移除某个公告
    public function topRemark($notice_id);//将某个公告置顶
    public function getShowNotice($data,User $user);//返回某用户在某企业中有权可见的公告
    public function getShowNoticeByColumn($data,User $user);//返回某用户在某企业中有权可见的公告--分栏目
    public function getNoticeLookRecord($notice_id,string $type,Request $request=null);//返回某条公告的浏览记录(已浏览)
    public function getNoticeUnLookRecord(Request $request);//返回某条公告的浏览记录(未浏览)
    public function addNoticeLookRecord(array $data);//添加某条公告的浏览记录
    public function userFollowNotice($notice_id);//用户关注某条公告
    public function userDeFollowNotice($notice_id);//用户取关注某条公告
    public function getUserFollowNoticeList(Request $request);//获取某用户在某公司关注的公告分页展示
    public function checkUserFollow($notice_id);//检查用户是否关注某公告
    public function getNoticeById($notice_id);//拿到某个公告具体的信息
    public function cancelNotice($notice_id);//撤销某个公告
    public function getCancelNotice(array $data);//拿到某企业全部的撤销公告(只有管理员)
    public function updateNotice(Request $request);//更新公告信息
    public function searchNoticeByTitle(array $data,User $user);//通过title搜索公告信息
    public function publish(Request $request);//发布公告并通知该公告需要通知的人员
}