<?php
namespace App\Interfaces;
use Illuminate\Http\Request;

/**
 * 动态模块所要实现的功能
 */
interface DynamicInterface
{
    public function makeListInfo();//组装用户动态列表信息
    public function getListDetailInfo(Request $request);//获取动态列表的详情信息
    public  function deleteListNode(Request $request);//删除动态列表信息中某个节点信息
    public  function getListInfo();//获取用户动态列表详细数据
    public  function getListUnReadCount();//获取用户动态列表未读数
    //静态方法
    public static function operateUserListInfo(int $user_id,$up_data,$operate_type);//某用户的动态列表信息的操作
    public static function getSingleListData(string $class,int $unread_count,string $identify_key,int $company_id,string $title,
                                             string $content, $created_at);//获取单个列表的数据(按照定好的数据格式)
}