<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;
use App\Repositories\DlfNotifyRepository;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Log;

/**
 * 全站通知工具类(单例)
 * Class DlfNotifyTool
 * @package App\Tools
 */
class GztNoticeTool
{
    static private $dlfNotifyTool;
    private $dlfNotifyRepository;//全站通知仓库
    private $notificationRepository;//站内通知仓库
    private $NOTIFY_TYPE='dulifei';
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->dlfNotifyRepository=new DlfNotifyRepository();
        $this->notificationRepository=new NotificationRepository();
    }

    /**
     * 单例模式
     * @return DlfNotifyTool
     */
    static public function getDlfNotifyTool(){
        if(self::$dlfNotifyTool instanceof DlfNotifyTool)
        {
            return self::$dlfNotifyTool;
        }else{
            return self::$dlfNotifyTool = new DlfNotifyTool();
        }
    }

    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 过滤所有全站通知,15天以前的通知不再通知用户
     */
    public function filterDlfNotify(){
        $notifys=$this->dlfNotifyRepository->getAllDlfNotify();
        foreach ($notifys as $notify){
            $time=strtotime($notify->created_at);//通知创建时间
            if(time()-$time>15*24*60*60){
                try{
                    $this->dlfNotifyRepository->updataDlfNotifyById($notify->id,['show'=>0]);//不再通知用户
                    $this->dlfNotifyRepository->deleteRecordByNoticeId($notify->id);//删除该全站通知对应的用户通知记录
                }catch (\Exception $e){

                }
            }
        }
    }
    /**
     * 校验登陆用户是否有需要接收的全站通知
     * $user_id:用户的id
     */
    public function checkUserNeedDlfNotify($user_id){
        try{
            $notifys=$this->dlfNotifyRepository->getAllShowDlfNotify();
            foreach ($notifys as $notify){
                if(is_null($this->dlfNotifyRepository->getDlfNotifyStateRecord($user_id,$notify->id))){
                    //压入站内通知
                    $this->notificationRepository->addNofitication([
                        'user_id'=>$user_id,
                        'type'=>is_null($notify->notice_id)?'dulifei':'notice',
                        'message'=>$notify->message,
                        'path'=>$notify->path,//跳转路径
                    ]);
                    //压入对应通知的记录
                    $this->dlfNotifyRepository->addDlfNotifyState($user_id, $notify->id);
            }
            }
        }catch (\Exception $e){
        }

    }
}