<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;

use App\Events\NotifiyEvent;
use App\Interfaces\NotifyInterface;
use App\Models\ApprovalTemplate;
use App\Models\Company;
use App\Models\User;
use App\Notifications\Notifiy;
use App\Repositories\NotifyRepository;
use App\Tools\CompanyBasisLimitTool;
use App\WebSocket\WebSocketClient;
use Illuminate\Support\Facades\Redis;
use App\Jobs\EmailJob;
/**
 * 短信工具类
 */
class NotifyTool implements NotifyInterface
{
    static private $notifyTool;
    private $notifyRepository;
    private $userTool;//用户工具类
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->notifyRepository=NotifyRepository::getNotifyRepository();
        $this->userTool=UserTool::getUserTool();
    }
    /**
     * 单例模式
     */
    static public function getNotifyTool(){
        if(self::$notifyTool instanceof self)
        {
            return self::$notifyTool;
        }else{
            return self::$notifyTool = new self;
        }
    }

    /**
     * ws实时通知,与数据库通知结合
     * @param array $user_ids:需要通知的人员数组
     * @param $company_id:标识通知的来源,0代表网站级通知,>0表示企业
     * @param $model:上级传递的模型
     * @param array $notification_way:通知方式数组
     * @param array $single_data:单条的数据
     * @param $model_class:模型类名
     * @param $special_class:特殊的一些类(主要是动态里矛盾展示问题)
     */
    protected static function wsNotify(array $user_ids, int $company_id, $model, array $notification_way, array $single_data, $model_class,$special_class): void
    {
        if (array_get($notification_way, 'need_notify', 0)) {
            //先进行数据库通知,组装相应的数据
            $notify_data = [
                'company_id' => $company_id,
                'model_id' => $model->id,
                'model_type' => $model_class,//与模块相对应
                'type' => is_null($special_class)?config('notify.class_type.' . $model_class):config('notify.class_type.' . $special_class),
                'message' => $single_data['data']['content'],
            ];//组装数据库通知所需要的数据
            foreach ($user_ids as $id) {
                event(new NotifiyEvent(new Notifiy(User::find($id), $notify_data)));//添加通知记录
            }
            //实时通知消息推送
            try {
                $client = WebSocketClient::getWsClient();//实例化一个ws_client用来向ws服务器推送数据
                $client->send(json_encode([
                    'notify_way' => config('notify.notify_way.active'),//标识业务逻辑主动推送
                    'user_ids' => $user_ids,//目标人员
                    'single_data' => $single_data,//推送的数据
                ]));
                $client->close();
            } catch (\Exception $e) {

            }
            //更新用户的动态列表数据
            foreach ($user_ids as $id) {
                DynamicTool::operateUserListInfo($id, $single_data, config('dynamic.operate_type.update'));
            }
        }
    }

    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 拉取未进行实时通知的记录并推送给对应的用户
     */
    public function loadUnPushedRecord(int $user_id)
    {

        $company_ids=$this->userTool->getCompanyIds($user_id);
        if(count($company_ids)==0)return [];//没有公司则返回空数组

        $data=config('notify.push_data_format');//返回的数据格式
        $data['type']=config('notify.ws_push_type.notify');//标识推送类型
        $data['data']['type']=config('notify.ws_push_type.notify');//标识数据类型
        $record_ids=[];//记录推送的记录id数组,用来标记已经推送过了
        //组装数据
        if(!$this->notifyRepository->checkUserHavePushRecord($user_id,$company_ids))return [];//若无需要推送的数据则返回[]
        foreach ($company_ids as $company_id) {
            $records=$this->notifyRepository->getCompanyNotWsRecord($user_id,$company_id);//拉取用户所需要进行实时通知的数据
            if(count($records)==0)continue;
            $data['data']['data']['company_id']=$company_id;
            $info=[];
            foreach ($records as $record){
                $info[]=[
                    'type'=>$record->type,
                    'model_id'=>$record->model_id,
                    'message'=>$record->message,
                    'updated_at'=>date('Y-m-d H:i:s',strtotime($record->updated_at)),
                ];
                $record_ids[]=$record->id;
            }
            $data['data']['data']['data']=$info;
        }
        //标记已推送的记录
        $this->notifyRepository->bathUpdate($record_ids,['ws_pushed'=>1]);
        return $data;
    }
    /**
     * 获取某用户最新通知--每个大类获取最新一条
     * @param int $user_id:所需组装信息的用户
     */
    public function getNewNotifyByType(int $user_id): array
    {
        $data=config('dynamic.list_data');
        //获取用户的company_ids,并加入id=0的元素代表gzt
        $company_ids=$this->userTool->getCompanyIds($user_id);
        $company_ids[]=0;
        //拿到所有大类--每一个元素都是一个array
        $types=config('notify.type');
        try {
            foreach ($types as $key=>$type) {
                foreach ($company_ids as $company_id){
                    $record = $this->notifyRepository->getNewRecordByType($type, $user_id,$company_id);
                    if (!is_null($record)) {
                        //列表单个数据的数据格式
                        $single_data=config('dynamic.list_single_data');
                        //标识类型
                        $single_data['type']=$key;
                        //unread_count数统计
                        $unread_count=$this->notifyRepository->getNewRecordCountByType($type,$user_id,$company_id);
                        $data['unread_count']+=$unread_count;//最外层未读数统计
                        //单个数据组装
                        $single_data['unread_count']+=$unread_count;//单独类型未读数统计
                        $single_data['data']['company_id']=FunctionTool::encrypt_id($company_id);
                        //抽取title
                        switch ($company_id){
                            case 0://代表gzt公告
                                $single_data['data']['title']='网站公告:来自工作通。';
                                break;
                            case -1://预留情况
                                break;
                            default://默认为企业工作通知
                                $single_data['data']['title']='工作通知:'.Company::find($company_id)->name;
                                break;
                        }
                        $single_data['data']['content']=$record->message;//抽取content
                        $single_data['data']['time']=date(config('basic.date_format'),strtotime($record->created_at));//通知时间抽取
                        $data['data'][] =$single_data ;
                    }
                }
            }
            $data['status']='success';
            return $data;
        } catch (\Exception $e) {
            $data['status']='fail';
            return $data;
        }
    }
    /**
     * 分页获取某一类通知的详情
     * @param array $data:[
     *      'user_id':,//目标用户
     *      'company_id':,//
     *      'types'=>[],//查找的类型
     *      'now_page'=>,//当前页
     *      'page_size'=>,//每次加载的数量
     * ]
     */
    public function getTypeDetail(array $data)
    {
        $offset= ($data['now_page']-1)*$data['page_size'];
        $limit=$data['page_size'];
        $records=$this->notifyRepository->getTypeDetail($data['user_id'],$data['company_id'],$data['types'],$offset,$limit);
        return $records;
    }
    /**
     * 将通知记录转化为动态需要的信息
     * @param $records:传入的通知记录
     */
    public function  convertToDynamicInfo($records)
    {
        //动态详情数据格式
        $data=config('dynamic.detail_data');
        if(count($records)==0){
            $data['status']='fail';
            $data['message']='到底了!';
            return json_encode($data);
        }
        try {
            foreach ($records as $record) {
                //列表单个数据的数据格式
                $single_data=config('dynamic.detail_single_data');
                $single_data['type']=$record->type;
                //获取通知对应的功能记录
                $model = $record->model;
                if(is_null($model))continue;
                //动态调用资源类,抽取相应的数据
                $source_name = config('dynamic.class_resource.' . $record->model_type);
                //PHP反射获取类--动态加载
                $rs = InstanceTool::newInstance($source_name, $model);
                //抽取对应模型数据
                $model_data=$rs->toArray(666);
                $model_data['time']=date(config('basic.date_format'),strtotime($record->created_at));//通知时间抽取
                $single_data['data']=$model_data;
                $data['data'][] =$single_data ;
            }
            $data['status']='success';
            $data['message']='请求成功';
            return json_encode($data);
        } catch (\ReflectionException $e) {
            $data['status']='fail';
            $data['message']='数据读取错误!';
            return json_encode($data);
        }
    }

    /**
     *标记某用户的某个企业的通知大类为已读
     * @param int $user_id:目标用户
     * @param int $company_id:目标企业
     * @param array $types:限定的type类型
     * @return mixed
     */
    public function markReadedByType(int $user_id, int $company_id, array $types)
    {
        return $this->notifyRepository->markReadedByType($user_id,$company_id,$types);
    }
    /**
     * 检查目标type类型是否属于notify中的某个大类并返回所属大类
     * @param string $type
     * @return mixed
     */
    public static function checkTypeExsitNotifyType(string $goal_type)
    {
        //拿到notify通知下的所有大类
        $types=config('notify.type');
        //判断是否存在于某个大类
        foreach ($types as $key=>$type){
            if(array_keys($type,$goal_type)){
                return $key;
            }
        }
        return false;
    }
    /**
     * 相关模块调用通知的方法
     * @param array $user_ids:需要通知的人员ids
     * @param int $company_id:标识通知的来源,0代表网站级通知,>0表示企业
     * @param $model :相应功能模块的实例
     * @param array $notification_way :通知的方式数组
     * @param array $single_data :单条动态通知列表数据
     * @param array $email_sms_data:邮件和短信通知所需要的data数组--需要各模块单独组装
     * @param  $special_class:动态里的一些特殊的类
     * @throws \ReflectionException
     */
    public static function publishNotify(array $user_ids, int $company_id,$model,array $notification_way,
                                         array $single_data,array $email_sms_data,$special_class=null){
        //判断对应的model是否需要多次通知
        $notified=is_null($model->notified)?1:$model->notified;
        //获取模型类名
        $model_class=get_class($model);
        if($notified){
            //邮件通知
//           self::emailNotify($user_ids, $notification_way);
           self::emailNotifyTest($user_ids,$company_id,$email_sms_data,$notification_way);
            //短信通知
//           self::smsNotify($user_ids, $notification_way);
           self::smsNotifyTest($user_ids,$company_id,$email_sms_data,$notification_way);
            //实时通知
           self::wsNotify($user_ids, $company_id, $model, $notification_way, $single_data, $model_class,$special_class);
            //所有通知方式走完视情况而定
        }
        return true;
    }
    /**
     * 通知模块--短信通知
     * @param array $user_ids:目标用户id数组
     * @param array $notification_way:通知方式数组
     */
    protected static function smsNotify(array $user_ids,  array $notification_way)
    {
        if (array_get($notification_way, 'need_tel', 0)) {
            //条数余额判断
//            $sms_count = CompanyBasisLimitTool::getRemainNum($company_id,'sms');
//            if($sms_count > 0 && $sms_count >= count($user_ids)){
//                foreach ($user_ids as $id) {
//                    $user = User::find($id);
//                    if (!($user->tel == '' || $user->tel == null || $user->tel_verified == 0)) {
//                        SmsJob::dispatch($user->tel,$email_sms_data,config('notify.sms_templet'))->delay()->onQueue('sms');
//                    }
//                }
//            }
            foreach ($user_ids as $id) {
                $user = User::find($id);
                if (!($user->tel == '' || $user->tel == null || $user->tel_verified == 0)) {
//               SmsTool::getSmsTool()->sendSms($user->tel,[],config('notify.sms_templet'));
//                        Log::info('用户' . $user->id . '-' . $this->data['notify_data']['message'] . '-DB:手机通知成功');
                }
            }
        }
    }
    /**
     * 通知模块--邮件通知
     * @param array $user_ids:目标用户id数组
     * @param array $notification_way:通知方式数组
     */
    protected static function emailNotify(array $user_ids, array $notification_way)
    {
        if (array_get($notification_way, 'need_email', 0)) {

            foreach ($user_ids as $id) {
                $user = User::find($id);
                if (!($user->email == '' || $user->email == null || $user->email_verified == 0)) {
//                EmailTool::getEmailTool()->sendEmail($this->user,'notify',['message'=>'456123']);
//                        Log::info('用户' . $user->id . '-' . $single_data['notify_data']['message'] . '-DB:邮件通知成功');
                }
            }
        }
    }

    /**
     * 邮件通知测试
     * @param array $user_ids
     * @param int $company_id
     * @param array $email_sms_data
     * @param array $notification_way
     */
    public static function emailNotifyTest(array $user_ids,int $company_id,array $email_sms_data,array $notification_way)
    {
        if (array_get($notification_way, 'need_email', 0)) {
            //条数余额判断
            $remainNum = CompanyBasisLimitTool::getRemainNum($company_id,'e-mail');
            if($remainNum > 0 && $remainNum >= count($user_ids)){
                  foreach($user_ids as $id){
                      $user = User::find($id);
                      if (!($user->email == '' || $user->email == null || $user->email_verified == 0)) {
                          //将待发邮件加入队列执行
                          EmailJob::dispatch($user,'notify',$email_sms_data)->delay(10)->onQueue('email');
                      }
                  }
                  //计算出剩余条数
                  $newNum = $remainNum - count($user_ids);
                  //更新条数
                  CompanyBasisLimitTool::updateAllNum($company_id,'e-mail',$newNum);

            }
        }
    }

    /**
     * 短信通知测试
     * @param array $user_ids
     * @param int $company_id
     * @param array $email_sms_data
     * @param array $notification_way
     */
    public static function smsNotifyTest(array $user_ids,int $company_id,array $email_sms_data,array $notification_way)
    {
        if (array_get($notification_way, 'need_tel', 0)) {
            //条数余额判断
            $remainNum = CompanyBasisLimitTool::getRemainNum($company_id, 'sms');
            if ($remainNum > 0 && $remainNum >= count($user_ids)) {
                foreach ($user_ids as $id) {
                    $user = User::find($id);
                    if (!($user->tel == '' || $user->tel == null || $user->tel_verified == 0)) {
                        SmsJob::dispatch($user->tel, $email_sms_data, config('notify.sms_templet'))->delay(10)->onQueue('sms');
                    }
                }
                //计算出剩余条数
                $newNum = $remainNum - count($user_ids);
                //更新条数
                CompanyBasisLimitTool::updateAllNum($company_id,'sms',$newNum);
            }

        }
    }


}