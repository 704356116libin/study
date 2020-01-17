<?php

namespace App\Http\Middleware\Company;
use App\Tools\CompanyBasisLimitTool;
use Closure;

class Email
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
        $use = auth('api')->user();
        if(CompanyBasisLimitTool::emailLimit($use->current_company_id)){
            return $next($request);
        }else{
            return ['status'=>'fail','message'=>'邮件条数已到达上限'];
        }
    }
}
