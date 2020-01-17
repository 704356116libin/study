<?php

namespace App\Http\Middleware;

use App\Tools\CompanyBasisLimitTool;
use Closure;
use Illuminate\Support\Facades\DB;

class FunModel
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
        $id=$request->id;//功能模块id
        $company_has_fun=DB::table('company_has_fun')->find($id);
        if($company_has_fun->is_enable==1){
            return $next($request);
        }else{
            return ['status'=>'fail','message'=>'该功能模块未启用'];
        }
    }
}
