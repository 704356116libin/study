<?php
namespace App\Interfaces;
use Illuminate\Http\Request;

/**
 * 通知接口所要实现的功能
 */
interface NotifyInterface
{
    public function loadUnPushedRecord(int $user_id);//拉取未通知的记录
    public function getNewNotifyByType(int $user_id):array;//抽取出某用户所有通知大类最新的一条数据
    public function getTypeDetail(array $data);//分页获取某一类通知的详情
    public function markReadedByType(int $user_id,int $company_id,array $types);//标记某用户的某个企业的通知大类为已读
    public function convertToDynamicInfo($records);//将通知记录转化为动态列表需要的数据
    public static function checkTypeExsitNotifyType(string $goal_type);//检查目标type类型是否属于notify中的某个大类并返回所属大类
}