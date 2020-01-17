<?php
namespace App\Repositories;
use App\Models\CompanyNotice;
use App\Models\CompanyNoticeColumn;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * 企业公告栏目仓库类
 * Class CompanyNoticeColumnRepository
 * @package App\Repositories
 */
class CompanyNoticeColumnRepository
{
    static private $companyNoticeColumnRepository;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {

    }
    /**
     * 单例模式
     */
    static public function getCompanyNoticeRepository(){
        if(self::$companyNoticeColumnRepository instanceof self)
        {
            return self::$companyNoticeColumnRepository;
        }else{
            return self::$companyNoticeColumnRepository = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }

    /**
     * 新建企业公告
     * @param array $data
     * @return mixed
     */
    public function add(array $data){
        return CompanyNoticeColumn::create($data);
    }
    /**
     * 查询指定名称的栏目是否已在某公司存在
     * @param $company_id:公司id
     * @param $name:
     * @return bool:true存在,不可创建  false不存在,可用
     */
    public function checkExistByName($company_id,$name){
        $record=CompanyNoticeColumn::where('company_id',$company_id)
                            ->where('name',$name)
                            ->first();
        return is_null($record)?false:true;
    }
    /**
     *更新信息
     */
    public function update($id,$data){
      return CompanyNoticeColumn::find($id)
                                   ->update($data);
    }
    /**
     * 获取某企业栏目的当前条数(用于自增排序)
     */
    public function getCNoticeColumnCount($company_id){
        return CompanyNoticeColumn::where('company_id',$company_id)
                                    ->count();
    }
    /**
     * 获取某企业的公告信息
     */
    public function getAllColumn($company_id){
        return CompanyNoticeColumn::where('company_id',$company_id)
                                    ->ordered()
                                    ->get();
    }
    public function checkColumnExsit($company_id,$name){
        $count= CompanyNoticeColumn::where('company_id',$company_id)
            ->where('name',$name)
            ->count();
        return $count==0?false:true;
    }
}