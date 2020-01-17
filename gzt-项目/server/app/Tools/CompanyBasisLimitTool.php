<?php

namespace App\Tools;

use App\Http\Middleware\Company\CompanyOss;
use App\Models\Company;
use App\Models\UserCompany;
use Illuminate\Support\Facades\DB;

class CompanyBasisLimitTool
{

    /**
     * 构造函数
     * CollaborativeTool constructor.
     */
    public function __construct()
    {
    }

    /**
     * 公司磁盘使用到期时间
     */
    public static function oss($company_id):bool
    {
        $count=Company::where('company_id',$company_id)->where('expire_date','>',time())->count();
        return $count===0?false:true;
    }
    /**
     * 短信条数限定
     */
    public static function smsLimit($company_id):bool
    {
        try{
            $count= Company::find($company_id)->companyBasisLimit
                ->where('type','sms')
                ->where('expire_date','>',time())
                ->first()
                ->type_number;
            return $count>0?true:false;
        }catch (\Exception $exception){
            return false;
        }
    }
    /**
     * 合作伙伴上限
     */
    public static function partnerLimit($company_id):bool
    {
        try{
            $partner=DB::table('company_partner')
                ->where('company_id',$company_id)
                ->orWhere('invite_company_id',$company_id)
                ->where('status',1);
            $partner_count=array_unique(array_merge($partner->pluck('company_id')->toArray(), $partner->pluck('invite_company_id')->toArray()));

            $count= Company::find($company_id)->companyBasisLimit
                ->where('type','partner')
                ->where('expire_date','>',time())
                ->first()
                ->type_number;
            return $count>(count($partner_count)-1)?true:false;
        }catch (\Exception $exception){
            return false;
        }
    }
    /**
     * 外部联系人上限
     */
    public static function externalContactLimit($company_id):bool
    {
        try{
            $external_contact=DB::table('company_external_contact')
                ->where('company_id',$company_id)
                ->where('status',1)
                ->count();

            $count= Company::find($company_id)->companyBasisLimit
                ->where('type','external_contact')
                ->where('expire_date','>',time())
                ->first()
                ->type_number;
            return $count>$external_contact?true:false;
        }catch (\Exception $exception){
            return false;
        }
    }
    /**
     * 外部合作公司上限
     */
    public static function externalCompanyLimit($user_id):bool
    {
        try{
        $external_contact=DB::table('company_external_contact')
            ->where('external_contact_id',$user_id)
            ->where('status',1)
            ->count();
        $count= DB::table('company_basis_limit')
            ->where('user_id',$user_id)
            ->where('type','external_company')
            ->first()
            ->type_number;
            return $count>$external_contact?true:false;
        }catch (\Exception $exception){
            return false;
        }
    }
    /**
     * 公司员工数上限
     */
    public static function staffLimit($company_id):bool
    {
        try{
            $staff=UserCompany::where('company_id',$company_id)->count();
            $count= Company::find($company_id)->companyBasisLimit
                ->where('type','staff_number')
                ->where('expire_date','>',time())
                ->first()
                ->type_number;
            return $count>$staff?true:false;
        }catch (\Exception $exception){
            return false;
        }
    }

    /**
     * 邮件条数上限
     * @param $company_id
     * @return bool
     */
    public static function emailLimit($company_id):bool
    {
        try{
            $count = Company::find($company_id)->companyBasisLimit
                ->where('type','e-mail')
                ->where('expire_date','>',time())
                ->first()
                ->type_number;
            return $count>0?true:false;
        }catch (\Exception $exception){
            return false;
        }
    }


    /**
     * 获取剩余条数或空间
     * @param $company_id 公司id
     * @param $type 类型
     * @return mixed
     */
    public function getRemainNum($company_id,$type)
    {
        return Company::find($company_id)->companyBasisLimit
            ->where('type',$type)
            ->where('expire_date','>',time())
            ->first()
            ->type_number;
    }

    /**
     * 更新公司短信上限/邮件上限/员工上限/外部合作伙伴上限/外部联系人上限
     * @param $company_id
     * @param $type
     * @param $num
     */
    public function updateAllNum($company_id,$type,$num)
    {
        switch ($type){
            case "sms":
                $count = $this->getRemainNum($company_id,$type);
                $newNum = $count - $num;
                if($newNum < 0){
                    return ['status'=>'fail','message'=>'短信条数不足'];
                }
                //更新短信剩余条数
                $bool = Company::where('company_id',$company_id)->where('type',$type)->update(['type_number'=>$newNum]);
                if($bool){
                    return ['status'=>'success','message'=>'短信条数更新成功'];
                }else{
                    return ['status'=>'error','message'=>'短信条数更新失败'];
                }
                break;
            case "e-mail":
                $count = $this->getRemainNum($company_id,$type);
                $newNum = $count - $num;
                if($newNum < 0){
                    return ['status'=>'fail','message'=>'邮件条数不足'];
                }
                //更新短信剩余条数
                $bool = Company::where('company_id',$company_id)->where('type',$type)->update(['type_number'=>$newNum]);
                if($bool){
                    return ['status'=>'success','message'=>'邮件条数更新成功'];
                }else{
                    return ['status'=>'error','message'=>'邮件条数更新失败'];
                }
                break;
            case "":
                break;

        }
    }



}
