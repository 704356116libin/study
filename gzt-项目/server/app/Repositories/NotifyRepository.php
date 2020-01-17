<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/27
 * Time: 13:56
 */
namespace App\Repositories;

use App\Counter;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

/**
 * 网站站内通知总表
 * Class NotifyRepository
 * @package App\Repositories
 */
class NotifyRepository
{
    static private $notifyRepository;

    /**
     *私有构造函数防止new
     */
    private function __construct()
    {

    }
    /**
     * 单例模式
     */
    static public function getNotifyRepository(){
        if(self::$notifyRepository instanceof self)
        {
            return self::$notifyRepository;
        }else{
            return self::$notifyRepository = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }


    /**
     * 新建一个通知记录
     * @param $data
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function addNofitication($data){
        return Notification::create($data);
    }

    /**
     * 更新记录
     * @param $id
     * @param $data
     * @return bool
     */
    public function updateNotification($id,$data){
        return Notification::where('id',$id)
                            ->update($data);
    }
    /**
     *拿到用户的所有通知信息(按时间由近及往)
     */
    public function getUserAllNotification($user_id){
        return Notification::where('user_id',$user_id)
            ->orderBy('created_at','desc')
            ->paginate(15);
    }
    /**
     * 获取用户所有已读信息
     */
    public function getUserReadNotification($user_id){
        return Notification::where('user_id',$user_id)
            ->where('readed', 1)
            ->orderBy('created_at','desc')
            ->paginate(15);
    }
    /**
     *拿到用户前5条未读通知
     */
    public function getUserAheadNotification($user_id){
        return Notification::where('user_id',$user_id)
            ->where('readed', 0)
            ->orderBy('created_at','desc')
            ->take(5)
            ->get();
    }
    /**
     *获取用户所有未读信息
     */
    public function getUserNotReadNotification($user_id){
        return Notification::where('user_id',$user_id)
            ->where('readed',0)
            ->orderBy('created_at','desc')
            ->paginate(15);
    }
    /**
     * 删除指定记录(可以批量删除)
     */
    public function deleteNotification($id){
        return Notification::whereIn('id',$id)
                            ->delete();
    }
    /**
     * 删除用户所有通知
     */
    public function deleteUserNotification($user_id){
        return Notification::where('user_id',$user_id)
            ->delete();
    }
    /**
     * 删除用户所有已读
     */
    public function deleteUserReaded($user_id){
        return Notification::where('user_id',$user_id)
            ->where('readed',1)
            ->delete();
    }
    /**
     * 删除用户所有未读
     */
    public function deleteUserNotReaded($user_id){
        return Notification::where('user_id',$user_id)
            ->where('readed',0)
            ->delete();
    }
    /**
     * 拿到用户的最新公告通知
     */
    public function getNewNotice($user_id){
        return Notification::where('user_id',$user_id)
            ->where('type','notice')
            ->orderBy('created_at','desc')
            ->first();
    }
    /**
     * 获取某用户某企业下没进行过实时通知的通知记录
     */
    public function getCompanyNotWsRecord($user_id,$company_id){
        return Notification::where('user_id',$user_id)
            ->where('company_id',$company_id)
            ->where('ws_pushed',0)
            ->get();
    }
    /**
     * 批量更新数据(已使用)
     * @param $user_id
     */
    public function bathUpdate($ids,$data){
        Notification::whereIn('id',$ids)
            ->update($data);
    }



    /**
     * 判断某用户的所有企业是否有需要推送的记录
     */
    public function checkUserHavePushRecord($user_id,$company_ids){
        $count=Notification::where('user_id',$user_id)
                            ->where('ws_pushed',0)
                            ->whereIn('company_id',$company_ids)
                            ->count();
        return $count==0?false:true;
    }
    /**
     * 获取通知某大类下的最新一条记录
     * @param array $types:大类参数数组---工作通知包含多个type所以定为数组
     * @param int $user_id:目标用户
     */
    public function getNewRecordByType(array $types,int $user_id,int $company_id){
        return Notification::whereIn('type',$types)
                            ->where('user_id',$user_id)
                            ->where('company_id',$company_id)
                            ->orderBy('created_at','desc')
                            ->first();
    }
    /**
     * 获取通知某大类下的未读记录的个数
     * @param array $types:大类参数数组---工作通知包含多个type所以定为数组
     * @param int $user_id:目标用户
     */
    public function getNewRecordCountByType(array $types,int $user_id,int $company_id){
        return Notification::whereIn('type',$types)
            ->where('user_id',$user_id)
            ->where('company_id',$company_id)
            ->where('readed',0)
            ->count();
    }
    /**
     * 分页获取通知指定大类的记录
     */
    public function getTypeDetail(int $user_id,int $company_id,array $types,int $offset,int $limit){
        return Notification::whereIn('type',$types)
            ->where('user_id',$user_id)
            ->where('company_id',$company_id)
            ->orderBy('created_at','desc')
            ->orderBy('id','desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
    }
    /**
     * 标记某用户的某个企业的通知大类为已读
     */
    public function markReadedByType(int $user_id,int $company_id,array $types){
        return Notification::whereIn('type',$types)
            ->where('user_id',$user_id)
            ->where('company_id',$company_id)
            ->update(['readed'=>1]);
    }
}