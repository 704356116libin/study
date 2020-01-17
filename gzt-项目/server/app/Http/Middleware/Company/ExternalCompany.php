<?php

namespace App\Http\Middleware\Company;

use App\Tools\CompanyBasisLimitTool;
use Closure;

class ExternalCompany
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
        if(CompanyBasisLimitTool::externalCompanyLimit($user->id)){
            return $next($request);
        }else{
            return ['status'=>'fail','message'=>'外部公司达到上限'];
        }
    }
}
