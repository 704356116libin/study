<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/19
 * Time: 11:22
 */
namespace App\Http\Controllers\Api;

use App\Events\NotificationEvent;
use App\Functions;
use App\Http\Controllers\Auth\LoginController;
use App\Repositories\BasicRepository;
use App\Repositories\CouponRepository;
use App\Repositories\IntegralRepository;
use App\Repositories\SpreadRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\ThirdRelationRepositiry;
use App\Repositories\UserdataRepository;
use App\Repositories\UserRepository;
use App\Tools\CommissionTool;
use App\Tools\DlfNotifyTool;
use App\Tools\SpreadTool;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Overtrue\Socialite\SocialiteManager;//引用第三登录
use App\Http\Controllers\SessionLimitController;

/**
 * Class ThreeloginController
 * 第三方登录控制器
 * @package App\Http\Controllers
 */
class ThreeloginController extends Controller
{
    private $relationRepositiry;
    private $userdataRepositiry;
    private $userRepositiry;
    private $templateRepository;
    private $baseRepositiry;
    private $function;
    private $public;
    private $spread;
    private $integer;
    private $sessionLimite;
    private $loginlimit;
    /**
     * ThreeloginController constructor.
     */
    public function __construct(ThirdRelationRepositiry $relationRepositiry
        , UserdataRepository $userdataRepository, BasicRepository $basicRepository
        , TemplateRepository $templateRepository,UserRepository $userRepositiry,Functions $functions
        ,SpreadRepository $spreadRepository,IntegralRepository $integralRepository,SessionLimitController $limitController,
        LoginController $limit)
    {
        $this->relationRepositiry = $relationRepositiry;
        $this->userdataRepositiry = $userdataRepository;
        $this->baseRepositiry = $basicRepository;
        $this->templateRepository = $templateRepository;
        $this->userRepositiry = $userRepositiry;
        $this->function = $functions;
        $this->public=$functions;
        $this->spread=$spreadRepository;
        $this->integer=$integralRepository;
        $this->sessionLimite=$limitController;
        $this->loginlimit=$limit;
    }

    /**
     * QQ第三方登录授权页面
     */
    public function qq()
    {
            $socialite = new SocialiteManager(config('services'));//第三方配置信息
            return $socialite->driver('qq')->redirect();
    }

    /**
     *QQ授权回调处理
     */
    public function qqLogin(Request $request)
    {
        try{
        $socialite = new SocialiteManager(config('services'));//第三方配置信息
        $socialite->getRequest()->getSession()->set('state',$request->get('state'));
        $qqUser = $socialite->driver('qq')->user();
        //抓取用户有用的数据,返回到绑定页面
        $relation = $this->relationRepositiry->getRelation('qq', $qqUser['id']);//查询此用户是否已经绑定
        if (count($relation) != 0) {
            Auth::loginUsingId($relation->user_id,true);//直接登陆关联的本站账户,并定向到主页
            CommissionTool::getCommissionTool()->checkExsitRecord($relation->user_id);//生成登陆用户的佣金记录
            $this->loginlimit->loginRecord();
            $data =$this->sessionLimite->sessionLimit();
            if(!is_null($data)){
                return view('layouts.jump',compact('data'));
            }
            return redirect((is_null($request->getSession()->get('url.intended'))?'/'
                :$request->getSession()->get('url.intended')
            ));
        } else {
            $user = [
                'unionid' => $qqUser['id'],
                'nickname' => $qqUser['nickname'],
                'avatar' => $qqUser['avatar'],
                'type' => 'qq',
            ];
            return view('qq_wx.qq_wx', compact('user'));
        }
        }catch (\Exception $e){
            return redirect('/login_qq');
        }
    }

    /**
     * 微信登陆授权页面
     */
    public function wechat()
    {
        $socialite = new SocialiteManager(config('services'));
        return $socialite->driver('wechat')->redirect();
    }

    /**
     * 微信登陆授权回调处理
     */
    public function wechatLogin(Request $request)
    {
        try{
        $socialite = new SocialiteManager(config('services'));//第三方配置信息
        $socialite->getRequest()->getSession()->set('state',$request->get('state'));
        $socialite = new SocialiteManager(config('services'));
        $weichatUser = $socialite->driver('wechat')->stateless()->user();
        //抓取用户有用的数据,返回到绑定页面
        $relation = $this->relationRepositiry->getRelation('wechat', $weichatUser['original']['unionid']);
        if (count($relation) != 0) {
            Auth::loginUsingId($relation->user_id,true);//直接登陆关联的本站账户,并定向到主页
            CommissionTool::getCommissionTool()->checkExsitRecord($relation->user_id);//生成登陆用户的佣金记录
            $this->loginlimit->loginRecord();
            $data =$this->sessionLimite->sessionLimit();
            if(!is_null($data)){
                return view('layouts.jump',compact('data'));
            }
            return redirect((is_null($request->getSession()->get('url.intended'))?'/'
                :$request->getSession()->get('url.intended')
            ))
            ;
        } else {
            $user = [
                'unionid' => $weichatUser['original']['unionid'],
                'nickname' => $weichatUser['nickname'],
                'avatar' => $weichatUser['avatar'],
                'type' => 'wechat',
            ];
            return view('qq_wx.qq_wx', compact('user'));
        }
        }catch (\Exception $e){
        return redirect('/login_wechat');
        }
    }

    /**
     * 微信公众号授权页跳转
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function wechat_phone()
    {
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize';
        $config=config('services.wechat_phone');
        $params = [
            'appid' => $config['client_id'],
            'redirect_uri' => $config['redirect'],
            'response_type' => 'code',
            'scope'=>'snsapi_userinfo',
            'state' => md5('dulifei' . time()),
        ];//请求拿到access_token的url参数数组
        session()->flash('wechat_connect_state', $params['state']);//将生成的state存到session域中以便验证
        $url .= '?' . http_build_query($params).'#wechat_redirect';//url拼接
        return redirect($url);//跳转到微信授权页面
    }
    /**
     * 微信登陆公众号测试-----授权回调处理
     */
    public function wechatLogin2(Request $request)
    {
        $config=config('services.wechat_phone');
        $token =json_decode($this->getWeChatAccessToken($config,$request->code),true) ;
        $userinfo = json_decode($this->getWechatUserInfo($token['access_token'],$token['openid']),true);
        session(['wxOpenid' => $token['openid']]);
        try{
            //抓取用户有用的数据,返回到绑定页面
            $relation = $this->relationRepositiry->getRelation('wechat', $userinfo['unionid']);
            if (count($relation) != 0) {
                Auth::loginUsingId($relation->user_id);//直接登陆关联的本站账户,并定向到主页
                CommissionTool::getCommissionTool()->checkExsitRecord($relation->user_id);//生成登陆用户的佣金记录
                $this->loginlimit->loginRecord();
                $data =$this->sessionLimite->sessionLimit();
                if(!is_null($data)){
                    return view('layouts.jump',compact('data'));
                }
                return redirect((is_null($request->getSession()->get('url.intended'))?'/'
                    :$request->getSession()->get('url.intended')
                ));
            } else {
                $user = [
                    'unionid' => $userinfo['unionid'],
                    'nickname' => $userinfo['nickname'],
                    'avatar' => $userinfo['headimgurl'],
                    'type' => 'wechat',
                ];
                return view('qq_wx.qq_wx', compact('user'));
            }
        }catch (\Exception $e){
            return redirect('/login_wechat');
        }
    }

    /**
     * 抓取用户头像并存至服务器
     * @param $url
     * @param $img_name
     */
    function saveImage($url, $img_name)
    {
        $image_name = strToLower($img_name);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $img = curl_exec($ch);
        curl_close($ch);
        $fp = fopen($image_name, 'w');
        fwrite($fp, $img);
        fclose($fp);
    }

    /**
     * 用户没有账号的时候生成账号并绑定账号(通过事务)
     * @param $type :第三方登陆类型
     * @param $unionid :用户的唯一标识
     */
    public function createAccountAndBind(Request $request)
    {
        $this->validator($request->all())->validate();
        $type = $request->get('type');//拿到登陆账号类型
        $unionid = $request->get('unionid');//拿到用户唯一标识
        $avatar = $request->get('avatar');//拿到用户头像地址
        DB::beginTransaction();//开启事务管理
        try {
            $user_id = User::insertGetId([
                'name' => $type . '_' . $unionid,
                'verified' => 0,
                'tel'=>$request->get('tel'),
                'tel_verified'=>1,
                'updated_at'=>Carbon::now().'',
                'created_at'=>Carbon::now().'',
            ]);
            $basic = new BasicRepository();
            $this->integer->addNewData($user_id,8,'绑定手机赠送积分',$basic->getWebData(34)->body);
            $this->saveImage($avatar, 'upload/header/' . date('Ymd').$user_id.'.jpg');  //抓取用户头像到服务器
            $this->function->givePermission($user_id, 1);//赋予权限
            $this->userdataRepositiry->addUserDatas($user_id, '/header/' . date('Ymd').$user_id.'.jpg'); //创建用户信息
//            $this->templateRepository->addUserTemplate($user_id, 0, $this->baseRepositiry->getWebData(29)->name, $this->baseRepositiry->getWebData(29)->body);//创建用户默认模板信息,绑定用户id
            $this->relationRepositiry->addRelation($user_id, $type, $unionid);//绑定用户
            $this->function->deductIntegralByRole($user_id, 9,'激活账号',4);
            Auth::loginUsingId($user_id,true);//以用户id登陆用户
            $this->sendRegisterNotify($basic);//发送注册通知
//            $this->afterThreeLogin();
            CommissionTool::getCommissionTool()->checkExsitRecord($user_id);//生成登陆用户的佣金记录
            $this->giveCoupon($user_id);
            $this->loginlimit->loginRecord();
            $data =$this->sessionLimite->sessionLimit();
            if(!is_null($data)){
                return view('layouts.jump',compact('data'));
            }
            //判断是否是从邀请链接过来的
            if(!is_null($request->spread_path)){
                $path=$request->spread_path;//取出邀请码
                $u_id=$this->public->analyzeSpreadUrl($path);//解码
                $this->spread->addSpreadRelation([
                    'from_id'=>$u_id,
                    'user_id'=>$user_id,
                    'state'=>1,
                ]);
                CommissionTool::getCommissionTool()->addRelationRecord($u_id,$user_id);//生成佣金关系记录
                $state=$this->public->giveSpreadReward($u_id,$user_id,$this->spread,$this->userdataRepositiry);//发送奖励
                if(!$state){
                    return 'F';
                }
            }
            DB::commit();//提交数据库操作
            return [
                    'state'=>'T',
                    'redirectUrl'=>(is_null($request->getSession()->get('url.intended'))?'/'
                    :$request->getSession()->get('url.intended')
                    )
            ];
        } catch (\Exception $e) {
            DB::rollBack();//数据库回滚
            return 'F';
        }
    }

    /**
     * 验证用户密码是否匹配
     * @param Request $request
     */
    public function bindUser(Request $request,LoginController $login)
    {
        $email = $request->get('email');
        $password = $request->get('password');
        $type = $request->get('type');//拿到登陆账号类型
        $unionid = $request->get('unionid');//拿到用户唯一标识
        $avatar = $request->get('avatar');//拿到用户头像地址
        $relation = $this->relationRepositiry->getRelation($type,$unionid );
        if (count($relation) != 0) {
            Auth::loginUsingId($relation->user_id,true);//直接登陆关联的本站账户,并定向到主页
            CommissionTool::getCommissionTool()->checkExsitRecord($relation->user_id);//生成登陆用户的佣金记录
            $this->loginlimit->loginRecord();
            $data =$this->sessionLimite->sessionLimit();
            if(!is_null($data)){
                return view('layouts.jump',compact('data'));
            }
            return [
                'state'=>'T',
                'redirectUrl'=>(is_null($request->getSession()->get('url.intended'))?'/'
                    :$request->getSession()->get('url.intended')
                )
            ];
        }else {
            if (Auth::attempt(array('email' => $email, 'password' => $password))
            ||Auth::attempt(array('tel' => $email, 'password' => $password))) {
                $user = $this->userRepositiry->getUserByEmail($email)->first();
                $user=is_null($user)? $this->userRepositiry->getUserByTel($email):$user;
                $userdata=$this->userdataRepositiry->getUserData($user->id);//拿到该用户的数据信息
                if($userdata->head_img=='/header/header.jpg'){
                $this->saveImage($avatar, 'upload/header/' . date('Ymd').$user->id  . '.jpg');  //抓取用户头像到服务器
                $this->userdataRepositiry->setUserData($user->id, ['head_img' => '/header/' . date('Ymd').$user->id . '.jpg']);//替换用户头像路径
                }
                $this->relationRepositiry->addRelation($user->id, $type, $unionid);//绑定用户
                Auth::loginUsingId($user->id);//登陆用户
                CommissionTool::getCommissionTool()->checkExsitRecord($user->id);//生成登陆用户的佣金记录
                $this->getNotification();//拉取通知信息
//                $this->afterThreeLogin();
                $this->loginlimit->loginRecord();
                $data =$this->sessionLimite->sessionLimit();
                if(!is_null($data)){
                    return view('layouts.jump',compact('data'));
                }
                return  [
                    'state'=>'T',
                    'redirectUrl'=>(is_null($request->getSession()->get('url.intended'))?'/'
                        :$request->getSession()->get('url.intended')
                    )
                ];//定向主页
            } else {
                return 'F';
            }
        }
    }

    /**           
     *微信二维码
     */
    public function  wechat_img(){
        $redirect_uri="http://www.dulifei.com/login_wechat/wechat";
        $redirect_uri=urlencode($redirect_uri);//该回调需要url编码
        $appID="wx701fec62b983c7da";
        $state=time().random_int(0,100000);
        session()->flash('state',$state);
        $scope="snsapi_login";//写死，微信暂时只支持这个值
        //准备向微信发请求
        $url = "https://open.weixin.qq.com/connect/qrconnect?appid=" . $appID."&redirect_uri=".$redirect_uri
            ."&response_type=code&scope=".$scope."&state=".$state."#wechat_redirect";
        //请求返回的结果(实际上是个html的字符串)
        $result = file_get_contents($url);
        //替换图片的src才能显示二维码
        $result = str_replace("/connect/qrcode/", "https://open.weixin.qq.com/connect/qrcode/", $result);
        return $result; //返回页面
    }
    public function error(){
        return view('errors.500');
    }
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'tel'=>'required|string|min:11|unique:users|telnum|regex:/^1[3456789][0-9]{9}$/',
            'tel_code'=>'required|string|code',
        ]);
    }

    /**
     * 微信浏览器获取acesstoken
     * @param $config
     * @param $code
     * @return bool|string
     */
    public function getWeChatAccessToken($config,$code){
        $url='https://api.weixin.qq.com/sns/oauth2/access_token';
        $params = [
            'appid' => $config['client_id'],
            'secret' => $config['client_secret'],
            'code' => $code,
            'grant_type'=>'authorization_code'
        ];//请求拿到access_token的url参数数组
        $url .= '?' . http_build_query($params);//url拼接
        return file_get_contents($url);
    }
    /**
     * 微信浏览器-拉取用户信息
     */
    public function getWechatUserInfo($token,$openid){
        $url='https://api.weixin.qq.com/sns/userinfo';
        $params = [
            'access_token' => $token,
            'openid' => $openid,
            'lang' => 'zh_CN',
        ];
        $url .= '?' . http_build_query($params);//url拼接
        return file_get_contents($url);
    }

    /**三方登录走登陆限制
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function afterThreeLogin(){
        $this->loginlimit->loginRecord();
        $data =$this->sessionLimite->sessionLimit();
        if(!is_null($data)){
            return response()->view('layouts.jump',compact('data'));
        }
    }

    /**
     * 拉取通知信息
     */
    public function getNotification()
    {
        DlfNotifyTool::getDlfNotifyTool()->checkUserNeedDlfNotify(Auth::id());//拉取全站通知信息
        SpreadTool::getPerTimeOutTool()->judgeUserNeedNotify(Auth::user());//拉取老带新通知信息
    }

    /**
     *发送第三方注册通知
     */
    public function sendRegisterNotify(BasicRepository $basic)
    {
        $user=Auth::user();
        event(new NotificationEvent($user, [
            'type' => 'dulifei',
            'message' => '欢迎来到独立费,呐~这是使用攻略请查收',
            'path' => '/easystart',
        ]));//发送绑定手机通知
        event(new NotificationEvent($user, [
            'type' => 'dulifei',
            'message' => '欢迎来到独立费,系统赠送您一张Vip优惠券,请签收',
            'path' => '/personalcenter/vip/conversion',
        ]));//发放赠送优惠券站内通知
        event(new NotificationEvent($user, [
            'type' => 'dulifei',
            'message' => '温馨提醒:恭喜您绑定手机成功,赠送您' . $basic->getWebData(34)->body . '积分,请查收。',
            'path' => '/personalcenter/integral/inquire',
        ]));//发送绑定手机通知
    }
    /**
     * 注册送优惠券
     */
    public function giveCoupon($user_id){
        $c=new CouponRepository();
        $c->addCoupon([
            'user_id' => $user_id,
            'discount' => 6,
            'sum_limit' => 68,
            'type' => 'VIP优惠券',
            'start_end_time' => date('Y-m-d', time()) . ' - ' . date('Y-m-d', time() + 30 * 24 * 60 * 60),
            'state' => 0,
            'publisher' => '独立费',
            'message' => '欢迎来到独立费,系统赠送您一张Vip优惠券,请签收',
        ]);
    }
}
