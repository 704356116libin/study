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
 * 企业公告浏览记录接口
 */
interface CompanyNoticeBrowseInterface
{
   public function checkUserRecordExist(int $user_id,int $notice_id);//检查某用户是否有浏览某个公告记录
   public function addUserRecord(array $data);//添加某用户某公告的浏览记录
   public function deleteUserRecord(int $user_id,int $notice_id);//删除某用户某公告的浏览记录

}