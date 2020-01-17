<?php
namespace App\Repositories;
use App\Models\CompanyNotice;
use App\Models\User;
use App\Tools\FunctionTool;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/10/29
 * Time: 13:58
 */

class CompanyNoticeRepository
{
    static private $companyNoticeRepository;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {

    }
    /**
     * 单例模式
     */
    static public function getCompanyNoticeRepository(){
        if(self::$companyNoticeRepository instanceof self)
        {
            return self::$companyNoticeRepository;
        }else{
            return self::$companyNoticeRepository = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }

    /**
     * 拿到某企业所有置顶的已发布公告
     * @param $columns:所要提取的列
     * @param $company_id:企业id
     */
    public function getTopShowNotice($columns,$company_id){
      return  CompanyNotice::select($columns)
            ->where('company_id',$company_id)
            ->where('is_top',1)
            ->where('is_show',1)
            ->orderBy('updated_at','desc')
            ->get();
    }
    /**
     * 拿到某企业所有非置顶的已发布公告
     * @param $columns:所要提取的列
     * @param $company_id:企业id
     */
    public function getShowNotice($columns,$company_id){
        return  CompanyNotice::select($columns)
            ->where('company_id',$company_id)
            ->where('is_top','!=',1)
            ->where('is_show',1)
            ->orderBy('updated_at')
            ->get();
    }
    /**
     * 拿到某企业所有非置顶的已发布公告
     * @param $columns:所要提取的列
     * @param $company_id:企业id
     */
    public function getNotShowNotice($columns,$company_id){
        return  CompanyNotice::select($columns)
            ->where('company_id',$company_id)
            ->where('is_top','!=',1)
            ->where('is_show',0)
            ->orderBy('updated_at')
            ->get();
    }
    /**
     * 拿到某用户可见的企业公告
     * @return static
     */
    public function getAllShow($company_id,User $user){
        return DB::table('company_notice')
            ->where('is_top',1)
            ->where('company_id',$company_id)
            ->where('is_show',1)
//            ->orderBy('created_at','desc')
            ->unionAll(
                DB::table('company_notice')
                    ->where('is_top',0)
                    ->where('company_id',$company_id)
                    ->where('is_show',1)
//                    ->orderBy('order','desc')
            )
            ->orderBy('is_top','desc')
            ->orderBy('order','desc')
            ->get()
            ->filter(function ($notice) use ($user){
                if($notice->allow_user=='all'){
                    return true;
                }else{
                    return in_array($user->id,FunctionTool::decrypt_id_array(json_decode($notice->allow_user,true)['company_u_ids']));//判断某个值是否在某数组中
                }
            });
    }

    /**
     * @param $company_id
     * @param $partners
     * @return \Illuminate\Support\Collection
     * (合作伙伴)外部公告
     */
    public function allShowNotice($partners)
    {
        return DB::table('company_notice')
            ->where('is_top',1)
            ->where('is_show',1)
            ->whereIn('id',$partners)
            ->unionAll(
                DB::table('company_notice')
                    ->where('is_top',0)
                    ->where('is_show',1)
                    ->whereIn('id',$partners)
//                    ->orderBy('order','desc')
            )
            ->orderBy('is_top','desc')
            ->orderBy('order','desc')
            ->get()
            ;
    }
    /**
     * 拿到某用户可见的企业公告--搜索title
     * @return static
     */
    public function searchNoticeByTitle($data,User $user){
        $company_id=$user->current_company_id;
        return DB::table('company_notice')
            ->where('is_top',1)
            ->where('company_id',$company_id)
            ->where('title','like','%'.$data['title'].'%')
            ->where('is_show',1)
//            ->orderBy('updated_at','desc')
            ->unionAll(
                DB::table('company_notice')
                    ->where('is_top',0)
                    ->where('company_id',$company_id)
                    ->where('title','like','%'.$data['title'].'%')
                    ->where('is_show',1)
//                    ->orderBy('order')
            )
            ->orderBy('is_top','desc')
            ->orderBy('order','desc')
            ->get()
            ->filter(function ($notice) use ($user){
                if($notice->allow_user=='all'){
                    return true;
                }else{
                    return in_array($user->id,FunctionTool::decrypt_id_array(json_decode($notice->allow_user,true)['company_u_ids']));
                }
            });
    }
    /**
     * 拿到某用户可见的企业公告--分栏目
     * @return static
     */
    public function getAllShowByColumn($data,User $user){
        $company_id=$data['company_id'];
        $column_id=FunctionTool::decrypt_id($data['column_id']);
        return DB::table('company_notice')
            ->where('is_top',1)
            ->where('company_id',$company_id)
            ->where('c_notice_column_id',$column_id)
            ->where('is_show',1)
//            ->orderBy('updated_at','desc')
            ->unionAll(
                DB::table('company_notice')
                    ->where('is_top',0)
                    ->where('company_id',$company_id)
                    ->where('c_notice_column_id',$column_id)
                    ->where('is_show',1)
//                    ->orderBy('order')
            )
            ->get()
            ->filter(function ($notice) use ($user){
                if($notice->allow_user=='all'){
                    return true;
                }else{
                    return in_array($user->id,FunctionTool::decrypt_id_array(json_decode($notice->allow_user,true)['company_u_ids']));
                }
            });
    }
    /**
     * 拿到某用户在某企业所关注的公告记录--分页
     * @return static
     */
    public function getUserFollowNoticeList($company_id,$user_id,$offset,$limit){
        $notice_ids=DB::table('user_notice_follow')
                        ->where('user_id',$user_id)
                        ->get()
                        ->pluck('notice_id')
                        ->toArray();
        $records=CompanyNotice::whereIn('id',$notice_ids)
            ->where('company_id',$company_id)
            ->where('is_show',1)
            ->orderBy('is_top','desc')
            ->orderBy('created_at','desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        $count=CompanyNotice::whereIn('id',$notice_ids)
            ->where('company_id',$company_id)
            ->where('is_show',1)
            ->count();
        return [
            'count'=>$count,
            'records'=>$records,
        ];
    }
    /**
     * 撤销某个公告
     * @param $notice_id
     */
    public function cancelNotice($notice_id)
    {
       return CompanyNotice::where('id',$notice_id)
                        ->update(['is_show'=>0,'is_draft'=>1]);
    }
    /**
     * 获取所有草稿状态的公告
     * @param $company_id
     */
    public function getDraftNotice($company_id)
    {
     return   DB::table('company_notice')
            ->where('company_id',$company_id)
            ->where('is_draft',1)
            ->where('is_show',0)
            ->get();
    }
    /**
     * 更新某个公告的内容()
     */
    public function updateNotice($notice_id,$data){
        return CompanyNotice::where('id',$notice_id)
                            ->update($data);
    }
    /**
     * 拿到某条公告浏览的人员ids
     */
    public function getNoticeBrowseUserIds(int $notice_id,$offset=0,$limit=0,$type='page'):array {
        if ($type=='page') {
            $records = DB::table('company_notice_browse_record')
                ->where('notice_id', $notice_id)
                ->offset($offset)
                ->limit($limit)
                ->get()
                ->pluck('user_id')
                ->toArray();
            $count = DB::table('company_notice_browse_record')
                ->where('notice_id', $notice_id)
                ->count();
            return [
                'count' => $count,
                'records' => $records
            ];
        }else{
          return DB::table('company_notice_browse_record')
              ->where('notice_id', $notice_id)
              ->get()
              ->pluck('user_id')
              ->toArray();
        }
    }
    /**
     *删除某条公告的浏览记录
     */
    public function removeNoticeBrowseRecord($notice_id){
        return DB::table('company_notice_browse_record')
                 ->where('notice_id',$notice_id)
                 ->delete();
    }
    /**
     *删除某条公告的对应的关注记录
     */
    public function removeNoticeFollowRecord($notice_id){
        return DB::table('user_notice_follow')
            ->where('notice_id',$notice_id)
            ->delete();
    }
}