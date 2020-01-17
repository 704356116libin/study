<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;
use App\Interfaces\CompanyNoticeBrowseInterface;
use App\Repositories\CompanyNoticeBrowseRepository;
/**
 * 企业公告浏览记录工具类
 */
class CompanyNoticeBrowseTool implements CompanyNoticeBrowseInterface
{
    static private $cNoticeBrowseTool;
    private $cNoticeBrowseRepository;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->cNoticeBrowseRepository=CompanyNoticeBrowseRepository::getCompanyNoticeBrowseRepository();
    }
    /**
     * 单例模式
     */
    static public function getCompanyNoticeBrowseTool(){
        if(self::$cNoticeBrowseTool instanceof self)
        {
            return self::$cNoticeBrowseTool;
        }else{
            return self::$cNoticeBrowseTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 检查某用户是否有浏览某个公告记录
     * @param int $user_id
     * @param int $notice_id
     */
    public function checkUserRecordExist(int $user_id, int $notice_id)
    {
        return  $this->cNoticeBrowseRepository->checkUserRecordExist($user_id,$notice_id);
    }
    /**
     * 添加某用户某公告的浏览记录
     * @param array $data
     */
    public function addUserRecord(array $data)
    {
        return  $this->cNoticeBrowseRepository->addUserRecord($data);
    }
    /**
     * 删除某用户的某公告记录
     * @param int $user_id
     * @param int $notice_id
     */
    public function deleteUserRecord(int $user_id, int $notice_id)
    {
        return  $this->cNoticeBrowseRepository->deleteUserRecord( $user_id,  $notice_id);
    }
}