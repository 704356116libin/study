<?php

namespace App\Http\Middleware\Company;

use App\Tools\CompanyBasisLimitTool;
use App\Tools\FunctionTool;
use Closure;

class StaffNumber
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
        $user=auth('api')->user();
        $company_id=array_get($request->all(),'company_id');
        $company_id=$company_id===null?$user->current_company_id:FunctionTool::decrypt_id($company_id);
        if(CompanyBasisLimitTool::staffLimit($company_id)){
            return $next($request);
        }else{
            return ['status'=>'fail','message'=>'公司员工达到上限'];
        }
    }
}
