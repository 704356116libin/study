<?php
namespace App\Repositories;
use App\Models\Company;
use App\Models\CompanyPartner;
use App\Models\CompanyPartnerRecord;
use App\Tools\FunctionTool;
use function GuzzleHttp\Psr7\_parse_request_uri;
use Illuminate\Support\Facades\DB;


/**
 * 公司仓库类
 * Class UserOssRepository
 * @package App\Repositories
 */
class CompanyPartnerRepository
{
    /**
     * 更新合作伙伴邀请记录
     */
    public static function updatePartnerRecord($record_id,array $data){
        return CompanyPartnerRecord::where('id',$record_id)
                            ->update($data);
    }
    /**
     * 建立两个企业的合作关系
     */
    public static function makePartnerRelation(int $company_id,int $invite_company_id,$type_id){
        $partner_id=DB::table('company_partner')->insertGetId(['company_id'=>$company_id, 'invite_company_id'=>$invite_company_id,]);
        if($type_id==null){
            return true;
        }else{
            $type_id=FunctionTool::decrypt_id($type_id);
        }
        return DB::table('partner_sort')->insert(['partner_id'=>$partner_id,'sort_id'=>$type_id]);
    }
    /**
     * 检查两个企业是否时合作伙伴的关系
     * @param int $id1
     * @param int $id2
     * @return bool:
     */
    public static function checkPartnerRelation(int $id1,int $id2){
        $count1= DB::table('company_partner')
                    ->where('company_id',$id1)
                    ->where('invite_company_id',$id2)
                    ->count()
                    ;
        $count2= DB::table('company_partner')
            ->where('company_id',$id2)
            ->where('invite_company_id',$id1)
            ->count()
        ;
        return $count1+$count2>0?true:false;
    }
    /**'
     *获取某企业的合作伙伴的id数组
     * @param $company_id:目标企业
     */
    public static function getCompanyPartnerIds(int $company_id)
    {
        //调取发起方为自己的合作伙伴id
        $arr1=DB::table('company_partner')
            ->where('company_id',$company_id)
            ->get()
            ->pluck('invite_company_id')
            ->toArray();
        //调取发起方不是自己的合作伙伴的id
        $arr2=DB::table('company_partner')
            ->where('invite_company_id',$company_id)
            ->get()
            ->pluck('company_id')
            ->toArray();
        return array_unique(array_merge($arr1,$arr2));
    }
    /**'
     *获取某企业的合作伙伴发起记录
     * @param $company_id:目标企业
     */
    public static function getCompanyPartnerRecord($company_id)
    {
        return DB::table('company_partner')
            ->where('company_id',$company_id)
            ->orderBy('create_at','desc')
            ->get();
    }
}