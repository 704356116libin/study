<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;
use App\Http\Resources\CompanyResource;
use App\Interfaces\CompanyInterface;
use App\Interfaces\DynamicInterface;
use App\Models\Company;
use App\Models\Dynamic;
use App\Models\Role;
use App\Models\User;
use App\Repositories\BasicRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\DynamicRepository;
use App\Repositories\UserRepository;
use App\WebSocket\WebSocketClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 动态模块-工具类
 */
class DynamicTool implements DynamicInterface
{
    static private $dynamicTool;
    private $notifyTool;//通知工具类
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->notifyTool=NotifyTool::getNotifyTool();
    }
    /**
     * 单例模式
     */
    static public function getInstance(){
        if(self::$dynamicTool instanceof self)
        {
            return self::$dynamicTool;
        }else{
            return self::$dynamicTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 获取单个列表的数据(按照定好的数据格式
     * @param string $class:模块的class
     * @param int $unread_count:未读数为1---每次都只发一条
     * @param int $identify_key:唯一标识的id
     * @param string $identify_id:相应的标识
     * @param string $title:列表标题
     * @param string $content:列表内容
     * @param $created_at:时间
     */
    public static function getSingleListData(string $class,int $unread_count,string $identify_key,int $identify_id,
                                             string $title,string $content, $created_at){
        $single_data=config('dynamic.list_single_data');
        $single_data['type']=NotifyTool::checkTypeExsitNotifyType(config('notify.class_type.' . $class));
        $single_data['unread_count']=$unread_count;
        $single_data['data']=[
            $identify_key=>  FunctionTool::encrypt_id($identify_id),
            'title'=>$title,
            'content'=>$content,
            'time'=>date(config('basic.date_format'),strtotime($created_at)),
        ];
        //非功能模块唯一标识的话,就移除功能模块标识company_id
        if($identify_key!=='company_id'){
            unset($single_data['data']['company_id']);
        }
        return $single_data;
    }
    /**
     * 组装用户的动态列表信息
     * @param int $user_id:用户
     */
    public function makeListInfo()
    {
        $user=auth('api')->user();
        //通知模块需要拉取到动态列表的数据组装
        $notify_list=$this->notifyTool->getNewNotifyByType($user->id);
        //后续聊天组装的数据--后续预留(数据格式同通知保持一致)
//        $chat_list=json_decode(config('dynamic.chat_list'),true);//聊天模块假数据
//        $chat_list2=json_decode(config('dynamic.chat_list'),true);//聊天模块假数据
        //数据整合---后续预留
        $list_info=$this->mergeListInfo($notify_list);
        $unread_count=$list_info['unread_count'];
        $list_info=json_encode($list_info);
        //保存用户动态记录
        $user->dynamic()->save(new Dynamic(['user_id'=>$user->id,'list_info'=>$list_info,'unread_count'=>$unread_count]));
        return $list_info;
    }
    /**
     * 获取动态列表的详情信息--分页加载
     * @return mixed
     */
    public function getListDetailInfo(Request $request)
    {
        $user=auth('api')->user();
        switch ($request->type){
            case config('notify.type_key.work_dynamic')://工作动态
                $records=$this->notifyTool->getTypeDetail([
                    'user_id'=>$user->id,
                    'company_id'=>FunctionTool::decrypt_id($request->company_id),
                    'types'=> config('notify.type.'.'work_dynamic'),
                    'now_page'=>$request->get('now_page',1),
                    'page_size'=>config('dynamic.detail_load_list_count'),
                ]);
                //重置对应类型的未读数&动态列表的json数据
                $this->notifyTool->markReadedByType($user->id,FunctionTool::decrypt_id($request->company_id)
                    ,config('notify.type.'.'work_dynamic'));
                self::operateUserListInfo($user->id,['type'=>$request->type,'company_id'=>$request->company_id],config('dynamic.operate_type.reset_unread'));//重置操作
                return $this->notifyTool->convertToDynamicInfo($records);
                break; 
            case config('notify.type_key.web_notice')://网站级的通知company_id=0
                $records=$this->notifyTool->getTypeDetail([
                    'user_id'=>$user->id,
                    'company_id'=>0,
                    'types'=> config('notify.type.'.'web_notice'),
                    'now_page'=>$request->get('now_page',1),
                    'page_size'=>config('dynamic.detail_load_list_count'),
                ]);
                //重置对应类型的未读数&动态列表的json数据
                $this->notifyTool->markReadedByType($user->id,0,config('notify.type.'.'web_notice'));
                self::operateUserListInfo($user->id,['type'=>$request->type,'company_id'=>0],config('dynamic.operate_type.reset_unread'));//重置操作
                return $this->notifyTool->convertToDynamicInfo($records);
                break;
            case config('chat.type.single')://单人聊天预留---

                break;
            case config('chat.type.group')://群组聊天预留---

                break;
            default:
                return json_encode(['status'=>'fail','不存在相应的type']);
                break;
        }
    }
    /**
     * 获取用户的动态列表信息
     */
    public function getListInfo(){
        $user=auth('api')->user();
        if(is_null($user->dynamic)){
            return $this->makeListInfo();
        }else{
            return $user->dynamic->list_info;
        }
    }
    /**
     * 获取用户的动态列表信息
     */
    public function getListUnReadCount(){
       $user=auth('api')->user();
        if(is_null($user->dynamic)){
            return json_encode(['status'=>'faile','message'=>'暂无动态列表记录']);
        }else{
            return json_encode(['status'=>'success','unread_count'=>$user->dynamic->unread_count]);
        }
    }
    /**
     * 删除列表信息中的某个节点数据
     * @param Request $request:type,company_id
     */
    public function deleteListNode(Request $request)
    {
        $user=auth('api')->user();
        //根据type类型组装不同的single_data
        try {
            switch ($request->type) {
                case config('notify.type_key.work_dynamic')://工作动态
                    self::operateUserListInfo($user->id, ['type' => $request->type, 'company_id' => $request->company_id], config('dynamic.operate_type.delete'));//删除操作
                    break;
                case config('notify.type_key.web_notice')://网站公告company_id=0
                    self::operateUserListInfo($user->id, ['type' => $request->type, 'company_id' => $request->company_id], config('dynamic.operate_type.delete'));//删除操作
                    break;
                case config('chat.type.single')://单人聊天预留---
                    self::operateUserListInfo($user->id, ['type' => $request->type, 'company_id' => $request->company_id], config('dynamic.operate_type.delete'));//删除操作
                    break;
                case config('chat.type.group')://群组聊天预留---
                    self::operateUserListInfo($user->id, ['type' => $request->type, 'company_id' => $request->company_id], config('dynamic.operate_type.delete'));//删除操作
                    break;
                default:
                    return json_encode(['status' => 'fail', '不存在相应的type']);
                    break;
            }
        } catch (\Exception $e) {
            return json_encode(['status'=>'fail','message'=>'出错了']);
        }
        return json_encode(['status'=>'success','message'=>'节点删除成功']);
    }
    /**
     * 用户的动态列表数据操作------增,删,查,改--第一步
     * @param int $user_id:目标用户
     * @param $single_data:所传递过来的单个数据--严格按照单个数据格式
     * @param $operate_type:update--代表更新操作,reset_unread--代表重置目标节点未读数,delete--代表删除目标节点
     * @return null
     */
    public static function operateUserListInfo(int $user_id, $single_data,$operate_type)
    {
        $dynamic=User::find($user_id)->dynamic;//拿到用户的动态数据
        if(is_null($dynamic)){
            return;}
        //解析用户的动态列表json为数组
        $list_info=json_decode($dynamic->list_info,true);
        $items=$list_info['data'];//用户的动态列表数据
        //循环比对动态列表信息
        $switch=false;//循环开关
        try {
            foreach ($items as $key => $item) {
                self::dynamicTypeFilter($single_data, $operate_type, $item, $key, $list_info, $switch);
                if ($switch) break;
            }//不存在相应节点则顶部插入--只有更新数据的情况需要
            if (!$switch && $operate_type == config('dynamic.operate_type.update')) {
                $list_info['data'] = array_merge([$single_data], $list_info['data']);
                //更新总的未读数
                $list_info['unread_count'] += $single_data['unread_count'];
                //更新用户动态数据
            }
            //数据发生变更就推送至ws服务器,实时通知消息推送
            try {
                $client = WebSocketClient::getWsClient();//实例化一个ws_client用来向ws服务器推送数据
                $client->send(json_encode([
                    'notify_way' => config('notify.notify_way.dynamic.refresh'),//标识业务逻辑主动推送
                    'user_id' => $user_id,//目标人员
                ]));
                $client->close();
            } catch (\Exception $e) {
            }
            DynamicRepository::getInstance()->update($dynamic->id, ['list_info' => json_encode($list_info),'unread_count'=>$list_info['unread_count']]);
        } catch (\Exception $e) {
            dd($e);
            return false;
        }
        return true;
    }
    /**
     * 动态列表确定single_data的type类型从而进行不同的处理(方法可优化)---第二步
     * @param $single_data:单个节点的数据
     * @param $operate_type:操作类型
     * @param $item:上级传来的单个节点
     * @param $key:item索引
     * @param $list_info:动态数据信息
     * @param $switch:开关
     */
    public static function dynamicTypeFilter($single_data, $operate_type, $item, $key, &$list_info, &$switch): void
    {
        switch ($item['type']) {
            case config('notify.type_key.work_dynamic')://工作动态对比company_id
                //update操作和删除重置未读数操作比对数值不一样
                $state=$operate_type!=config('dynamic.operate_type.update')? $single_data['company_id']:$single_data['data']['company_id'];
                if (FunctionTool::decrypt_id($item['data']['company_id']) == FunctionTool::decrypt_id($state)) {
                    self::distributeOperateMethod($single_data, $list_info, $key, $switch, $operate_type);
                }
                break;
            case config('notify.type_key.web_notice')://网站通知对比company_id=0
                //update操作和删除重置未读数操作比对数值不一样
                $state=$operate_type!=config('dynamic.operate_type.update')? $single_data['company_id']:$single_data['data']['company_id'];
                if (FunctionTool::decrypt_id($item['data']['company_id']) == FunctionTool::decrypt_id($state)) {
                    self::distributeOperateMethod($single_data, $list_info, $key, $switch, $operate_type);
                }
                break;
            case config('chat.type.single')://单人聊天预留---
                //update操作和删除重置未读数操作比对数值不一样
                $state=$operate_type!=config('dynamic.operate_type.update')? $single_data['user_id']:$single_data['data']['user_id'];
                if (FunctionTool::decrypt_id($item['data']['user_id']) == FunctionTool::decrypt_id($state)) {
                    self::distributeOperateMethod($single_data, $list_info, $key, $switch, $operate_type);
                }
                break;
            case config('chat.type.group')://群组聊天预留---
                //update操作和删除重置未读数操作比对数值不一样
                $state=$operate_type!=config('dynamic.operate_type.update')? $single_data['company_id']:$single_data['data']['company_id'];
                if (FunctionTool::decrypt_id($item['data']['群组_id']) == FunctionTool::decrypt_id($state)) {
                    self::distributeOperateMethod($single_data, $list_info, $key, $switch, $operate_type);
                }
                break;
            default:
                break;
        }
    }
    /**
     * 动态列表信息的操作方法分发 ---第三步
     * @param $single_data:需要操作的单个数据
     * @param $list_info:总动态列表数据
     * @param $key:操作的索引位置
     * @param $switch:顶层循环开关
     * @param $operate_type:动作类型
     */
    protected static function distributeOperateMethod($single_data, &$list_info, $key,&$switch,$operate_type){
        switch ($operate_type){
            case config('dynamic.operate_type.update')://更新列表数据的操作
                Log::info('列表更新方法执行!!');
                self::updateListNodeInfo($single_data, $list_info, $key,$switch);
                break;
            case config('dynamic.operate_type.delete')://删除列表指定节点数据的操作
                Log::info('删除列表指定节点数据方法执行!!');
                self::deleteListNodeInfo($list_info,$key,$switch);
                break;
            case config('dynamic.operate_type.reset_unread')://重置列表指定节点未读数的操作
                Log::info('重置列表指定节点未读数方法执行!!');
                self::resetListNodeUnRead($list_info,$key,$switch);
                break;
            default:

                break;
        }
    }
    /**
     * 更新某个节点的信息(只供内部调用)
     * @param $single_data:需要更新的节点
     * @param $list_info:动态列表主信息
     * @param $key:需要更新的列表索引位置
     */
    protected static function updateListNodeInfo($single_data, &$list_info, $key,&$switch)
    {
        Log::info('进入列表更新方法');
        //替换相应节点的数据
        $list_info['data'][$key]['data'] = $single_data['data'];
        //更新相应节点的未读数
        $list_info['data'][$key]['unread_count'] += $single_data['unread_count'];
        //更新总的未读数
        $list_info['unread_count'] += $single_data['unread_count'];
        //置顶相应节点
        $list_info['data']=FunctionTool::top_array_header($list_info['data'],$key);
        //关闭循环
        $switch = true;
    }
    /**
     * 重置节点未读数某个节点的信息(只供内部调用)
     * @param $list_info:动态列表主信息
     * @param $key:需要操作的列表索引位置
     */
    protected static function resetListNodeUnRead( &$list_info, $key,&$switch)
    {
        //更新总的未读数
        $list_info['unread_count'] -= $list_info['data'][$key]['unread_count'];
        //更新相应节点的未读数
        $list_info['data'][$key]['unread_count'] = 0;
        //关闭循环
        $switch = true;
    }
    /**
     * 删除某个节点的信息(只供内部调用)
     * @param $list_info:动态列表主信息
     * @param $key:需要操作的列表索引位置
     */
    protected static function deleteListNodeInfo( &$list_info, $key,&$switch)
    {
        $list_info['unread_count']-=$list_info['data'][$key]['unread_count'];
        //删除某个节点
        unset($list_info['data'][$key]);
        //关闭循环
        $list_info['data']=array_values($list_info['data']);
        $switch = true;
    }
    /**
     * 合并各模块抽取出来的动态列表数据--必须保证各模块抽取出的数据格式统一(只供内部调用)
     * @param array[] ...$args
     */
    protected function mergeListInfo(array ...$args)
    {
        $data=null;//所要合并的基础数据
        $arrs=func_get_args();//获取传过来的数据数组
        foreach ($args as $key=>$arr){
            if($arr['status']=='success'){
                $data=$arr;//赋值基础数据
                unset($arrs[$key]);//从参数中移除基础数据
                array_values($arrs);//重新排序
                break;
            }
        }
        //合并数据
        foreach ($arrs as $arr){
            if($arr['status']=='success'){
                $data['unread_count']+=$arr['unread_count'];//基础未读数累计
                $data['data']=array_merge( $data['data'],$arr['data']);//合并list_data
            }
        }
        return $data;
    }
}