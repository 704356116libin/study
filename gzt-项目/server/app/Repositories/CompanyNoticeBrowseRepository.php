<?php
namespace App\Repositories;
use App\Models\CompanyNotice;
use App\Models\CompanyNoticeColumn;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * 企业公告浏览记录仓库类
 * Class CompanyNoticeColumnRepository
 * @package App\Repositories
 */
class CompanyNoticeBrowseRepository
{
    static private $companyNoticeBrowseRepository;
    private $table='company_notice_browse_record';
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {

    }
    /**
     * 单例模式
     */
    static public function getCompanyNoticeBrowseRepository(){
        if(self::$companyNoticeBrowseRepository instanceof self)
        {
            return self::$companyNoticeBrowseRepository;
        }else{
            return self::$companyNoticeBrowseRepository = new self;
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
        $record=DB::table($this->table)
                    ->where('user_id',$user_id)
                    ->where('notice_id',$notice_id)
                    ->first();
        return is_null($record)?false:true;
    }
    /**
     * 添加某用户某公告的浏览记录
     * @param array $data
     */
    public function addUserRecord(array $data)
    {
        return DB::table($this->table)
            ->insert($data);
    }
    /**
     * 删除某用户的某公告记录
     * @param int $user_id
     * @param int $notice_id
     */
    public function deleteUserRecord(int $user_id, int $notice_id)
    {
    }
    public static function getNoticeBrowseCount($notice_id){
        return DB::table('company_notice_browse_record')
            ->where('notice_id', $notice_id)
            ->count();;
    }
}