<?php

namespace App\Http\Middleware\Company;

use App\Tools\CompanyBasisLimitTool;
use Closure;

class CompanyOss
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
        if(CompanyBasisLimitTool::oss($user->current_company_id)){
            return $next($request);
        }else{
            return ['status'=>'fail','message'=>'磁盘使用到期'];
        }
    }
}
