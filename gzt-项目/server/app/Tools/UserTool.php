<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;

use App\Http\Resources\user\UserBaseResource;
use App\Http\Resources\user\UserSimpleResource;
use App\Interfaces\SmsInterface;
use App\Interfaces\UserInterface;
use App\Models\CompanyNotice;
use App\Models\User;
use App\Models\UserCompanyInfo;
use App\Repositories\UserRepository;
use App\WebSocket\WebSocketClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Overtrue\EasySms\EasySms;
use swoole_client;
use swoole_websocket_server;

/**
 * 短信工具类
 */
class UserTool implements UserInterface
{
    static private $userTool;
    private $smsTool;//短信总接口
    private $userRepository;//用户仓库
    private $validateTool;//数据验证总接口
//    private $userOssTool;//个人云存储
    private $companyTool;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->userRepository=UserRepository::getUserRepository();
        $this->smsTool=SmsTool::getSmsTool();
        $this->validateTool=ValidateTool::getValidateTool();
//        $this->userOssTool=UserOssTool::getUserOssTool();
        $this->companyTool=CompanyTool::getCompanyTool();
    }
    /**
     * 单例模式
     */
    static public function getUserTool(){
        if(self::$userTool instanceof self)
        {
            return self::$userTool;
        }else{
            return self::$userTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 检查电话号码是否已经存在
     * @param \App\Interfaces\Request $request
     */
    public function checkTelExsit($tel)
    {
        // TODO: Implement checkTelExsit() method.
        $data=null;
        if($this->userRepository->checkTelExsit($tel)){
            $data=['status'=>true,'message'=>'手机号已存在'];
        }else{
            $data=['status'=>false,'message'=>'手机号不存在'];
        }
        return json_encode($data);
    }
    /**
     *检查用户名是否已经注册
     */
    public function  checkNameExsit($name){
        $data=null;
        if($this->userRepository->checkNameExsit($name)){
            $data=['status'=>true,'message'=>'用户名已存在'];
        }else{
            $data=['status'=>false,'message'=>'用户名可用'];
        }
        return json_encode($data);

    }
    /**
     *检查用户名是否已经注册
     */
    public function  checkEmailExsit($email){
        $data=null;
        if($this->userRepository->checkEmailExsit($email)){
            $data=['status'=>true,'message'=>'邮箱已存在'];
        }else{
            $data=['status'=>false,'message'=>'邮箱不存在'];
        }
        return json_encode($data);
    }
    /**
     * 生成唯一email_token字段
     */
    public function makeEmailToken()
    {
        // TODO: Implement makeEmailToken() method.
        $token=str_random(40);
        if($this->userRepository->checkEmailTokenExsit($token)){
            return $this->makeEmailToken();
        }else{
            return $token;
        }
    }
    /**
     * 用户邮箱验证
     */
    public function userEmailVerify($token)
    {
        // TODO: Implement userEmailVerify() method.
        $user = User::where('email_token',$token)->first();
        if (!is_null($user)){
            $user->email_verified = 1;
            $user->email_token = $this->makeEmailToken();
            $user->save();
            Auth::login($user);
            return json_encode(['status'=>'success','message'=>'邮箱验证成功']);
        }
        return json_encode(['status'=>'fail','message'=>'令牌已失效,验证失败']);
    }
    /**
     * 用户邮箱解绑
     */
    public function userEmailUnlink($token)
    {
        // TODO: Implement userEmailVerify() method.
        $user = User::where('email_token',$token)->first();
        if (!is_null($user)){
            $user->email_verified = 0;
            $user->email_token = $this->makeEmailToken();
            $user->save();
            return json_encode(['status'=>'success','message'=>'邮箱解绑成功']);
        }
        return json_encode(['status'=>'fail','message'=>'令牌已失效,解绑失败']);
    }
    /**
     * 用户手机验证
     */
    public function userTelVerify(Request $request)
    {
        // TODO: Implement userTelVerify() method.
        $validator= $this->validateTool->telcode_validate($request->all());//短信验证码验证
        if(is_array($validator)){
            return json_encode($validator);
        }
        $user=$this->userRepository->getUserByTel($request->tel);
        $user->tel_verified=1;
        $user->save();
        return json_encode(['status'=>'success','message'=>'手机号验证成功']);
    }
    /**
     * 用户手机解绑
     */
    public function userTelUnlink(Request $request)
    {
        // TODO: Implement userTelVerify() method.
        $validator= $this->validateTool->telcode_validate($request->all());
        if(is_array($validator)){
            return json_encode($validator);
        }
        $user=$this->userRepository->getUserByTel($request->tel);
        $user->tel_verified=0;
        $user->save();
        return json_encode(['status'=>'success','message'=>'手机号解绑成功']);
    }
    /**
     * 通过邮箱拿到用户
     */
    public function getUserByEmail($email)
    {
        // TODO: Implement getUserByEmail() method.
        return $this->userRepository->getUserByEmail($email);
    }
    /**
     * 通过手机号拿到用户
     * @param $tel
     */
    public function getUserByTel($tel)
    {
        // TODO: Implement getUserByTel() method.
        return $this->userRepository->getUserByTel($tel);
    }
    /**
     * 通过邮箱验证重置用户密码
     * @param Request $request
     */
    public function userSetPwdByEmail(Request $request)
    {
        // TODO: Implement userSetPwdByEmail() method.
        $validator_pwd= $this->pwdCheck($request->only(['password','password_confirmation']));
        if(is_array($validator_pwd)){
            return json_encode($validator_pwd);
        }
        $user = User::where('email_token',$request->token)->first();
        if (!is_null($user)){
            return    $this->updateUserData($user,[
                'email_token'=>$this->makeEmailToken(),
                'password'=>password_hash($request->password,PASSWORD_DEFAULT)
            ]);
        }
        return json_encode(['status'=>'fail','message'=>'链接已失效,重置失败']);
    }
    /**
     * 用户密码规则验证(用于密码重置)
     */
    public function pwdCheck($data){
        $validator = Validator::make($data, [
            'password' => 'required|string|min:6|confirmed|regex:'.config('regex.password'),
        ]);
        if($validator->fails()){
            $errors=$validator->errors();
            $messages=$errors->messages();
            return [
                'status'=>'fail',
                'password'=>($errors->has('password')?implode(',',$messages['password']):null),
            ];
        }else{
            return true;
        }
    }
    /**
     * 通过用户手机号重置密码
     * @param Request $request
     */
    public function userSetPwdByTel(Request $request)
    {
        // TODO: Implement userSetPwdByTel() method.
        $validator_pwd= $this->pwdCheck($request->only(['password','password_confirmation']));
        if(is_array($validator_pwd)){
            return json_encode($validator_pwd);
        }
        $validator_tel= $this->validateTool->telcode_validate($request->all());
        if(is_array($validator_tel)){
            return json_encode($validator_tel);
        }
        //重置用户密码
        return $this->updateUserData($this->getUserByTel($request->tel),['password'=>password_hash($request->password,PASSWORD_DEFAULT)]);
    }
    /**
     * 更新用户的信息
     * @param $id:用户id
     * @param $data:更新的信息
     */
    public function updateUserData(User $user, array $data)
    {
        // TODO: Implement updateUserData() method.
        try{
            $this->userRepository->updateUserData($user,$data);
            return json_encode(['status'=>'success','message'=>'恭喜您,密码重置成功~']);
        }catch (\Exception $e){
            dd($e);
            return json_encode(['status'=>'fail','message'=>'服务器开小差啦~']);
        }
    }
    /**
     * 生成用户唯一的api_token(用户访问令牌)
     */
    public function makeApiToken()
    {
        // TODO: Implement makeEmailToken() method.
        $token=str_random(128);
        if($this->userRepository->checkEmailTokenExsit($token)){
            return $this->makeApiToken();
        }else{
            return $token;
        }
    }
    /**
     * 为用户添加角色
     * @param $user_id 用户id
     * @param $role 角色名
     * @return mixed
     */
    public function giveUserRole(Request $request){
        $user_id=$request->user_id=1;
        $role=$request->role='name_550';
        try{
            User::find($user_id)->assignRole($role);
            return json_encode(['status'=>'success','message'=>'角色添加成功']);
        }catch (\Exception $e){
            return json_encode(['status'=>'fail','message'=>'服务器开小差啦~']);
        }
    }
    /**
     *随机抽取一个用户
     */
    public function getRandomUser()
    {
       $max_id=$this->userRepository->getUserMaxId();
       $user=User::find(rand(1,$max_id));
       while(is_null($user)){
           $user=User::find(rand(1,$max_id));
       }
       return $user;
    }
    public function zxc(){
//        $client = new swoole_client(SWOOLE_SOCK_TCP );
//        $connect=$client->connect('0.0.0.0', 9501, 1, 0);
//        if (!$connect)
//        {
//            exit("connect failed. Error: {$client->errCode}\n");
//        }
//        $client->send("POST / HTTP/1.1" . "\r\n" .
//            "Origin: null" . "\r\n" .
//            "Host: 0.0.0.0:9501" . "\r\n" .
//            "Sec-WebSocket-Key: null" . "\r\n" .
//            "User-Agent: gzt\r\n" .
//            "Upgrade: websocket" . "\r\n" .
//            "Connection: Upgrade" . "\r\n" .
//            "Sec-WebSocket-Protocol: wamp" . "\r\n" .
//            "Sec-WebSocket-Version: 13" . "\r\n" . "\r\n");
//        dd($client->recv());
//        $client->send(swoole_websocket_server::pack(json_encode(['a'=>'和艰苦看见好看 hello 456321']), WEBSOCKET_OPCODE_TEXT,true,false));

////        $client->send('4656546545');
////        $client->send('手动阀手动阀');
//
//        var_dump($client->recv()) ;
//        $client->close();

//
//        while (1){
          $client = WebSocketClient::getWsClient();
//        dd($client->);
            $client->send(json_encode([
                'notify_way'=>'active',
                'user_ids'=>[1,2,3,4],
                'need_email'=>1,
                'need_tel'=>1,
                'notify_data'=>[
                    'company_id'=>1,
                    'model_id'=>1,
                    'model_type'=>CompanyNotice::class,
                    'type'=>config('notify.class_type.'.CompanyNotice::class),
                    'message'=>'公告标题',
                ],
                'date_time'=>date('Y-m-d H:i:s'),
            ]));
            $client->close();
//        }
    }
    /**
     * 获取用户所在company的id数组
     */
    public function getCompanyIds($user_id){
        $data=[];
        $companys=User::find($user_id)->load('company:id')->company;
        if(count($companys)==0) return $data;
        foreach ($companys as $company){
            $data[]=$company->id;
        }
        return $data;
    }
    /**
     * 用户注册的逻辑
     * @param Request $request
     */
    public function register(Request $request)
    {
        $validator = $this->validateTool->register_validate($request->all());//数据验证
        if (is_array($validator)) {
            return json_encode($validator);
        }
        DB::beginTransaction();//开启事务管理
        try {
            $user = $this->create($request->all());//创建新用户
            BusinessManagementTool::registerSuccess($user);
            PersonalOssTool::makeRootPath($user);//生成个人云盘
            DB::commit();//提交数据库操作
        } catch (\Exception $e) {
            DB::rollBack();//数据库回滚
            return json_encode(['status' => 'fatil', 'message' => '服务器开小差了']);
        }
        return json_encode(['status' => 'success', 'message' => '恭喜您注册成功']);
    }
    /**
     *注册用户数据持久化
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => '用户_' . $data['tel'],
            'tel' => $data['tel'],
            'tel_verified' => 1,
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'email_token' => $this->makeEmailToken(),
        ]);
    }
    /**
     * 变更用户当前企业
     * @param $company
     */
    public function alterCurrentCompany($company_id)
    {
        //变更当前用户的当前企业
        $user=auth('api')->user();
        if($company_id!==0){
            $current_company_id=FunctionTool::decrypt_id($company_id);
        }else{
            $current_company_id=0;
        }
        $update=0;
        if(gettype($current_company_id)=='integer'){
            $update=User::where('id',$user->id)->update(['current_company_id'=>$current_company_id]);
        }
        $data=$this->companyTool->getCompanyList($current_company_id);
        if($update==0){
            return ['status'=>'fail','message'=>'切换失败,请重新检查','data'=>$data];
        }
        //推送企业id变更标识
//        $client = WebSocketClient::getWsClient();//实例化一个ws_client用来向ws服务器推送数据
//        $client->send(json_encode([
//            'notify_way' => config('notify.notify_way.company.current_company_alter'),//标识业务逻辑主动推送
//            'user_id' => $user->id,//目标人员
//        ]));
//        $client->close();
        return json_encode(['status'=>'success','message'=>'企业变更成功','data'=>$data]);
    }
    /**
     * 获取当前登陆用户的基础信息
     */
    public function getLoginUserInfo()
    {
        $user=auth('api')->user();
        $data=new UserBaseResource($user);
        return json_encode(['status'=>'success','data'=>$data]);
    }
    /**
     * 更新用户资料
     */
    public function eidtPersonalData($data)
    {
        $user=\auth('api')->user();
        $user_id=array_get($data,'user_id');
        $user_id=$user_id===null?$user->id:FunctionTool::decrypt_id($user_id);
        $data=[
            'name'=>$data['name'],
            'signature'=>$data['signature'],
        ];
        User::where('id',$user_id)->update($data);
        return ['status'=>'success','message'=>'更新成功'];
    }
    /**
     * 获取个人头像
     */
    public static function getPersonalAvatar()
    {
        $user=\auth('api')->user();
        $files=json_decode(PersonalOssTool::getTargetDirectoryInfo(['user_id'=>FunctionTool::encrypt_id($user->id),'target_directory'=>'avatar/']));
        $avatar=$files->data->files;
        if (count($avatar)==0){
            return ['status'=>'fail','message'=>'没有可用头像'];
        }
        $avatar=[
          'id'=>$avatar[0]->id,
          'name'=>$avatar[0]->name,
          'oss_path'=>$avatar[0]->oss_path,
        ];
        return ['status'=>'success','avatar'=>$avatar];
    }
    /**
     * 更新头像
     */
    public function editPersonalAvatar(Request $request)
    {
        try{
            DB::beginTransaction();
            $files=$_FILES;
            $user=\auth('api')->user();
            $oldAvatar=$this->getPersonalAvatar();
            if($oldAvatar['status']=='success'){
                $oldAvatarId=FunctionTool::decrypt_id($oldAvatar['avatar']['id']);
                //删除旧头像
                PersonalOssTool::deleteFile($oldAvatarId);
            }
            //上传新头像
            $upload=PersonalOssTool::uploadFile($files, [
                'oss_path' => $user->oss->root_path . 'avatar/',//上传的云路径
                'model_id' => $user->id,//关联模型的id
                'model_type' => User::class,//关联模型的类名
                'user_id' => $user->id,//所属用户的id
                'uploader_id' => $user->id,//上传者的id
            ]);
            if ($upload===true){
                DB::commit();
                $avatar=$this->getPersonalAvatar();
                return ['status'=>'success','message'=>'更新成功','avatar'=>$avatar];
            }else{
                DB::rollBack();
                return ['status'=>'fail','message'=>'更新失败'];
            }
        }catch (\Exception $exception) {
            DB::rollBack();
            dd($exception);
            return ['status'=>'fail','message'=>'服务器错误'];
        }


    }
    /**
     * 邀请列表
     */
    public function invitelist()
    {
        $user=\auth('api')->user();
        //公司邀请成为员工
        $staff=UserCompanyInfo::where('user_id',$user->id)->where('activation',0)->get()
            ->map(function ($user_company_info){
                return [
                    'company_id'=>FunctionTool::encrypt_id($user_company_info->company->id),
                    'company_name'=>$user_company_info->company->name,
                    'address'=>$user_company_info->address
                ];
            });
        //公司邀请成为外部联系人
        $externalContact=BusinessManagementTool::applyExternalContactCompanys();
        return['status'=>'success','data'=>['staff'=>$staff,'externalContact'=>$externalContact]];
    }
    /**
     *获取个人在公司的信息
     */
    public static function getCompanyData($user_id)
    {
        return UserCompanyInfo::where('user_id',$user_id)->get()
            ->map(function ($user_company_info){
                return [
                    'company_name'=>$user_company_info->company->name,
                    'user_name'=>$user_company_info->name,
                    'user_sex'=>$user_company_info->sex,
                    'user_tel'=>$user_company_info->tel,
//                    'department'=>$user_company_info->department->name,
                ];
            });
    }

    public static function getNewCompanyData($user_id,$company_id)
    {
        return UserCompanyInfo::where('user_id',$user_id)->where('company_id',$company_id)->get()
            ->map(function ($user_company_info){
                return [
                    'company_name'=>$user_company_info->company->name,
                    'user_name'=>$user_company_info->name,
                    'user_sex'=>$user_company_info->sex,
                    'user_tel'=>$user_company_info->tel,
//                    'department'=>$user_company_info->department->name,
                ];
            });
    }
}