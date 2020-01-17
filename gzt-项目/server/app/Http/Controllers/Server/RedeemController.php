<?php


namespace App\Http\Controllers\Server;


use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Tools\BusinessManagementTool;
use App\Tools\FunctionTool;
use Clarkeash\Doorman\Facades\Doorman;
use Illuminate\Http\Request;

class RedeemController extends Controller
{
    private $getBusinessManagementTool;
    /**
     * RedeemController constructor.
     */
    public function __construct()
    {
        $this->getBusinessManagementTool = BusinessManagementTool::getBusinessManagementTool();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     * 兑换邀请码(判断邀请链接是否已失效)
     */
    public function redeem(Request $request)
    {
        try{
            $data=$request->all();
            $invite_code = $data['invite_code'];
            $company_id = FunctionTool::decrypt_id($data['company_id']);
            $user_id=FunctionTool::decrypt_id($data['user_id']);
            $check = Doorman::check($invite_code);
            if (!$check) {
                Header("Location: https://www.baidu.com");
                exit;
            }
            $company = Company::find($company_id);
            $user=User::find($user_id);
            $c_data = [
                'company_id' => $company->id,
                'company_name' => $company->name,
                'user_id'=>$user->id,
                'user_name'=>$user->name,
            ];
            return view('/inviteStaff',['data'=>$c_data]);
        }catch (\Exception $exception){
            Header("Location: https://www.baidu.com");
            exit;
        }
    }
    /**
     * 3设置用户名和密码或验证用户名和密码(请求数据同用户注册一样)
     */
    public function setUser(Request $request)
    {
        return $this->getBusinessManagementTool->setUser($request);
    }
}