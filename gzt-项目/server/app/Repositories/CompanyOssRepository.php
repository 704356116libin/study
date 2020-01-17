<?php
namespace App\Repositories;
use App\Models\CompanyOss;
use App\Models\User;
use App\Models\UserOss;
use Faker\Provider\Company;
use Illuminate\Support\Facades\DB;

/**
 * 公司云存储仓库
 * Class UserOssRepository
 * @package App\Repositories
 */
class CompanyOssRepository
{
    static private $companyOssRepository;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {

    }
    /**
     * 单例模式
     */
    static public function getCompanyOssRepository(){
        if(self::$companyOssRepository instanceof self)
        {
            return self::$companyOssRepository;
        }else{
            return self::$companyOssRepository = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 更新企业oss相关信息--静态方法
     * @param $id
     * @param array $data
     * @return mixed
     */
    public static function updateOss(CompanyOss $oss,array $data){
        $oss->fill($data);
        $oss->save();
        return true;
    }
    /**
     * 拿到某企业的oss空间记录
     */
    public static function getRecord(int $company_id){
        return CompanyOss::where('company_id',$company_id)
                          ->first();
    }
}