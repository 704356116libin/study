<?php
namespace App\Repositories;
use App\Models\Company;
use Illuminate\Support\Facades\DB;


/**
 * 公司仓库类
 * Class UserOssRepository
 * @package App\Repositories
 */
class CompanyRepository
{
    static private $companyRepository;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {

    }
    /**
     * 单例模式
     */
    static public function getCompanyRepository(){
        if(self::$companyRepository instanceof self)
        {
            return self::$companyRepository;
        }else{
            return self::$companyRepository = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 创建一个公司
     */
    public function createCompany(array $data){
        return Company::create($data);
    }
    /**
     * 检查该名称是否已经有公司注册并认证
     * @param $name
     */
    public function checkNameVerified($name)
    {
        $record=Company::where('name',$name)
                       ->where('verified',1)
                       ->get();
        return count($record)==0?false:true;
    }
    /**
     * 通过名称来搜索公司
     * @param $name
     */
    public function searchCompanyByName($name)
    {
        return Company::where('name','like','%'.$name.'%')
                        ->get();
    }
    /**
     * 校验某个企业是否存在
     */
    public function checkCompanyExist(int $company_id){
        $record=Company::find($company_id);
        return is_null($record)?false:true;
    }
    /**
     * 拿到某用户所加入的company_id  array
     */
    public static function getUserCompanyIds($user_id){
        return  DB::table('user_company')
                   ->where('user_id',$user_id)
                   ->get()
                   ->pluck('company_id')
                   ->toArray();
    }
    /**
     * 获取指定id数组的企业
     */
    public function getCompanyByIds(array $ids){
        return Company::whereIn('id',$ids)
                ->get();
    }
    /**
     * 检查某企业下是否有某个用户
     * @param int $company_id:目标企业
     * @param int $user_id:目标用户
     */
    public static function companyHaveUser(int $company_id,int $user_id ){
        $count=DB::table('user_company')
                    ->where([
                        ['company_id','=',$company_id],
                        ['user_id','=',$user_id],
                    ])
                    ->count();
        return  $count==0?false:true;
    }
}