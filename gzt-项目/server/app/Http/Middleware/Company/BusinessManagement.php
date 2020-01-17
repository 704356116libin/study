<?php

namespace App\Http\Middleware\Company;

use App\Tools\RoleAndPerTool;
use Closure;

class BusinessManagement
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
        if(RoleAndPerTool::user_has_c_per($user->id,$user->current_company_id,['c_super_manage_per'],"any")){
            return $next($request);
        }else{
            return ['status'=>'fail','message'=>'短信达到上限'];
        }
    }
}
