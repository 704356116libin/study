<?php
namespace App\Repositories;
use App\Http\Resources\pst\PstProcessTemplateResource;
use App\Http\Resources\pst\PstProcessTemplateTypeResource;
use App\Http\Resources\pst\PstTemplateResource;
use App\Http\Resources\pst\PstTemplateTypeResource;
use App\Models\Approval;
use App\Models\Pst;
use App\Models\PstOperateRecord;
use App\Models\PstProcessTemplate;
use App\Models\PstProcessTemplateType;
use App\Models\PstTemplate;
use App\Models\PstTemplateType;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * 评审通相关表操作仓库类
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/10/29
 * Time: 13:58
 */

class PstRepository
{
    private static $pst_approval_template_table='pst_approval_template';//评审通审批流程模板表
    private static $pst_form_data_table='pst_form_data';//评审通基础表单数据表
    private static $pst_report_number_table='pst_report_number';//评审通报告生成规则信息表
    private static $need_data=[
        'id',
        'last_pst_id',//上级评审通id
        'company_id',//所属公司id
        'outside_user_id',//外部联系人id
        'state',//评审通状态
        'need_approval',//是否需要审批标识
        'approval_method',//审批方式标识
        'form_template',//表单数据
        'process_template',//审批人员数据
        'form_values',//
        'publish_user_id',//发起人id
        'created_at',//创建时间
        'updated_at',//更新时间
        'current_handlers',//当前处理人
        'form_values->project_name as project_name',//表单中的工程名称--待定
        'join_user_data->join_user_ids as join_user_ids',//所有的参与人员json数据,内部,合作伙伴,外部联系人
        'join_user_data->join_user_ids->inside_user_ids as inside_user_ids',//内部参与人id json
        'join_user_data->join_user_ids->inside_receive_state as inside_receive_state',//内部参与人状态json
        'join_user_data->join_user_ids->company_partner_ids as company_partner_ids',//合作伙伴id组
        'join_user_data->join_user_ids->outside_user_ids as outside_user_ids',//外部联系人id组
        'join_pst_form_data',//内部参与人员表单信息
        'transfer_join_data',//被转移参与人员信息json
        'cc_user_data->cc_user_ids as cc_user_ids',//抄送人员id json
        'duty_user_data->duty_user_id as duty_user_id',//负责人id
        'duty_user_data->duty_receive_state as duty_receive_state',//负责人接收状态
        'transfer_duty_data->duty_user_id as transfer_duty_id',//被转移人员id
        'transfer_duty_data->duty_receive_state as transfer_duty_receive_state',//被转移人员id
    ];//评审通基础表单数据表
//===============================评审通流程相关==========================================================>
    /**
     * 添加评审通--流程模板信息
     */
    public static function addProcessTemplate(array $data){
        return PstProcessTemplate::create($data);
    }
    /**
     * 删除评审通流程模板
     * @param array $data
     * @return mixed
     */
    public static function deleteProcessTemplate(int $id){
        return PstProcessTemplate::where('id',$id)
            ->delete();
    }
    /**
     * 获取某企业所有的评审通--流程模板信息
     * @param int $company_id:目标企业id
     * @param int $user_id:目标用户id
     * @return array
     */
    public static function getCompanyProcessTemplate(int $company_id,int $user_id)
    {
        $data = ['enable' => [], 'disable' => []];
        //先查询出企业的所有流程类型
        $types = PstProcessTemplateType::where('company_id', $company_id)
            ->orderBy('sequence', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
        foreach ($types as $type) {
            $data_type=(new PstProcessTemplateTypeResource($type))->toArray(1);
            $enable = $type->processTemplates->filter(function ($v)use ($user_id) {
                //先判断是否展示
                $state1=$v->is_show==1;
                //再判断是否在可见人数组中
                $state2=false;
                $per=json_decode($v->per,true);
                if($per=='all'){
                    $state2=true;
                }else{
                    //取出可见人数组
                    $staff_ids=$per['staffId'];
                    $state2=in_array($user_id,$staff_ids)?true:false;
                }
                return $state1&&$state2;
            });
            $enable_data=json_decode(json_encode(PstProcessTemplateResource::collection($enable)));
            $disable = $type->processTemplates->filter(function ($v)use ($user_id) {
                //先判断是否为禁用
                $state1=$v->is_show!=1;
                //再判断是否在可见人数组中
                $state2=false;
                $per=json_decode($v->per,true);
                if($per=='all'){
                    $state2=true;
                }else{
                    //取出可见人数组
                    $staff_ids=$per['staffId'];
                    $state2=in_array($user_id,$staff_ids)?true:false;
                }
                return $state1&&$state2;
            });
            $disable_data=json_decode(json_encode(PstProcessTemplateResource::collection($disable)));
            if(count($disable)!=0){
//                $data_type['data']=$disable_data;
                $data['disable']=array_merge($data['disable'],$disable_data);
            }
            $data_type['data']=$enable_data;
            $data['enable'][]=$data_type;
            //组装模板数据
        }
        return $data;
    }
    /**
     * 通过id获取指定评审通流程的详细信息
     */
    public static function getProcessTemplateById(int $id){
        return PstProcessTemplate::
            where('id',$id)
            ->first();
    }
    /**
     * 更新评审通--流程模板的信息
     */
    public static function updateProcessTemplate(int $id, array $data)
    {
        return PstProcessTemplate::where('id',$id)
                        ->update($data);
    }
    /**
     * 添加评审通流程模板类型
     * @param array $data:前端传递数据组
     */
    public static function addProcessTemplateType(array $data){
        return PstProcessTemplateType::create($data);
    }
    /**
     * 更新评审通模板类型的信息
     */
    public static function updateProcessTemplateType(int $id, array $data)
    {
        return PstProcessTemplateType::where('id',$id)
            ->update($data);
    }
    /**
     * 按序获取评审通分类信息
     * @param array $data
     * @return mixed
     */
    public static function getProcessTemplateType(int $company_id)
    {
        return PstProcessTemplateType::where('company_id',$company_id)
                            ->orderBy('sequence','asc')
                            ->get();
    }
    /**
     * 获取指定评审通流程分类信息
     */
    public static function getProcessTemplateTypeById(int $type_id)
    {
        return PstProcessTemplateType::find($type_id);
    }
    /**
     * 检查评审通流程模板类型名称是否已经存在
     * @param int $company_id:目标企业id
     * @param string $name:目标名称
     */
    public static function checkProcessTemplateTypeExsit(int $company_id,string $name)
    {
        $count=PstProcessTemplateType::where('company_id',$company_id)
            ->where('name',$name)
            ->count();
        return $count==0?false:true;
    }

//===============================评审通模板相关==========================================================>
    /**
     * 拉取网站默认的评审通模板--用于用户创建评审通模板
     */
    public static function getClassicPstTemplate(){
        $data = [];
        //先查询出企业的所有流程类型
        $types = PstTemplateType::where('company_id', 0)
            ->orderBy('sequence', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($types as $type) {
            //组装模板数据
            $data_type=(new PstTemplateTypeResource($type))->toArray(1);
            $enable = $type->pstTemplates->filter(function ($v) {
                return $v->is_show==1;
            });
            $enable_data=json_decode(json_encode(PstTemplateResource::collection($enable)));
            $data_type['data']=$enable_data;
            $data[]=$data_type;

        }
        return $data;
    }
    /**
     * 添加评审通模板
     * @param array $data
     * @return mixed
     */
    public static function addPstTemplate(array $data){
        return PstTemplate::create($data);
    }
    /**
     * 删除评审通模板
     * @param array $data
     * @return mixed
     */
    public static function deletePstTemplate(int $id){
        return PstTemplate::where('id',$id)
                            ->delete();
    }
    /**
     * 获取某企业的所有评审通模板
     * @param int $company_id:目标企业
     * @param int $user_id:目标用户
     * @return mixed
     */
    public static function getCompanyPstTemplate(int $company_id,int $user_id)
    {
        $data = ['enable' => [], 'disable' => []];
        //先查询出企业的所有流程类型
        $types = PstTemplateType::where('company_id', $company_id)
            ->orderBy('sequence', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($types as $type) {
            //组装模板数据
            $data_type=(new PstTemplateTypeResource($type))->toArray(1);
            $enable = $type->pstTemplates->filter(function ($v)use($user_id) {
                //先判断是否展示
                $state1=$v->is_show==1;
                //再判断是否在可见人数组中
                $state2=false;
                $per=json_decode($v->per,true);
                if($per=='all'){
                    $state2=true;
                }else{
                    //取出可见人数组
                    $staff_ids=$per['staffId'];
                    $state2=in_array($user_id,$staff_ids)?true:false;
                }
                return $state1&&$state2;
            });
            $enable_data=json_decode(json_encode(PstTemplateResource::collection($enable)));
            $disable = $type->pstTemplates->filter(function ($v)use($user_id) {
                //先判断是否为禁用
                $state1=$v->is_show!=1;
                //再判断是否在可见人数组中
                $state2=false;
                $per=json_decode($v->per,true);
                if($per=='all'){
                    $state2=true;
                }else{
                    //取出可见人数组
                    $staff_ids=$per['staffId'];
                    $state2=in_array($user_id,$staff_ids)?true:false;
                }
                return $state1&&$state2;
            });
            $disable_data=json_decode(json_encode(PstTemplateResource::collection($disable)));
            if(count($disable)!=0){
//                $data_type['data']=$disable_data;
                $data['disable']=array_merge($data['disable'],$disable_data);
            }
            $data_type['data']=$enable_data;
            $data['enable'][]=$data_type;
        }
        return $data;
    }
    /**
     * 拉取可见人的name数组
     * @param array $ids:可见人id数组
     * @return array
     */
    public static function getPerUserNames($per):array{
        $per=json_decode($per,true);
        $data=[];
        if ($per=='all') {
            $data[]='全体可见';
        }else{
            $ids=$per['staffId'];//取出可见人id数据
            foreach ($ids as $id) {
                $user=User::find($id);
                if (!empty($user)) {
                    $data[] = $user->name;
                }
            }
        }
        return $data;
    }
    /**
     * 通过id获取指定评审通模板的详细信息
     */
    public static function getPstTemplateById(int $id){
        return PstTemplate::
            where('id',$id)
            ->first();
    }
    /**
     * 更新评审通模板的信息
     */
    public static function updatePstTemplate(int $id, array $data)
    {
        return PstTemplate::where('id',$id)
            ->update($data);
    }
    /**
     * 添加评审通模板类型
     * @param array $data :前端传递数据组
     */
    public static function addPstTemplateType(array $data)
    {
        return PstTemplateType::create($data);
    }
    /**
     * 检查评审通模板类型名称是否已经存在
     * @param int $company_id:目标企业id
     * @param string $name:目标名称
     */
    public static function checkPstTemplateTypeExsit(int $company_id,string $name)
    {
        $count=PstTemplateType::where('company_id',$company_id)
            ->where('name',$name)
            ->count();
        return $count==0?false:true;
    }
    /**
     * 更新评审通模板类型的信息
     */
    public static function updatePstTemplateType(int $id, array $data)
    {
        return PstTemplateType::where('id',$id)
            ->update($data);
    }
    /**
     * 按序获取评审通分类信息
     * @param array $data
     * @return mixed
     */
    public static function getPstTemplateType(int $company_id)
    {
        return PstTemplateType::where('company_id',$company_id)
            ->orderBy('sequence','asc')
            ->get();
    }
    /**
     * 获取指定评审通分类信息
     * @param array $data
     * @return mixed
     */
    public static function getPstTemplateTypeById(int $type_id)
    {
        return PstTemplateType::find($type_id);
    }
    /**
     * 获取评审通模板类型的最大队列值
     * @param int $company_id:目标公司
     * @return mixed
     */
    public static function getPstTemplateTypeMaxSequence(int $company_id){
        return PstTemplateType::where('company_id',$company_id)
            ->max('sequence');
    }
    /**
     * 获取目标公司评审通模板类型的最大sequence
     */

//===============================评审通基础表单相关=======================================================>
    /**
     * 更新或者插入评审通的基础表单数据
     */
    public static function add_or_update_form_data(array $data){
        return DB::table(self::$pst_form_data_table)->updateOrInsert($data);
    }
    /**
     * 获取企业的评审通基础表单记录
     */
    public static function get_company_form_data(int $company_id){
        return DB::table(self::$pst_form_data_table)
                    ->where('company_id',$company_id)
                    ->first();
    }
    /**
     * 更新企业的评审通基础表单数据
     */
    public static function update_company_form_data(int $company_id,array $data){
        return DB::table(self::$pst_form_data_table)
            ->where('company_id',$company_id)
            ->update($data);
    }

//===============================评审通相关==========================================================>
    /**
     * 插入一条评审通记录
     * @param array $data
     */
    public static function addPst(array $data){
        return Pst::create($data);
    }
    /**
     * 更新评审通的记录
     * @param int $id:目标id
     * @param array $data:需要更新的数据
     */
    public static function updatePst(int $id,array $data){
        return Pst::where('id',$id)->update($data);
    }
    /**
     * 获取在进行评审通操作时的各种所需数据
     * @param int $pst_id:目标评审通id
     * @return mixed
     */
    public static function getPstOperateData(int $pst_id){
        return Pst::select(self::$need_data)
            ->where('id',$pst_id)
            ->first();
    }
    /**
     * 获取指定平申通的指定字段值
     */
    public static function getPstTargetValue(array $selects,int $pst_id){
        return Pst::select($selects)
                    ->where('id',$pst_id)
                    ->first();
    }
//=========================================↓↓评审通分类分页查询↓↓======================================>
    /**
     * 通过评审通的state来搜索评审通-- 待接收,待指派等等&&并且该用户可见
     * @param string $state_type:目标类型
     * @param int $company_id:目标公司id
     * @param int $now_page:当前页
     * @param int $page_size:每页数据条数
     * @param bool $have_per:在该公司下是否有相应的高级权限
     * @param string $search_type:细分的类型(二级联合搜索标识)-默认为查询所有与我相关的
     * @return array:返回的数据
     */
    public static function searchPstByState(string $state_type,int $company_id,int $user_id,int $now_page
                        ,int $page_size,bool $have_per,string $search_type='all'){
        $count=0;
        $records=null;
        switch ($state_type){
            case config('pst.state.under_way')://评审中
                $records=DB::table('pst')
                    ->where('company_id',$company_id)
                    ->orderBy('created_at','desc')
                    ->get()
                    ->filter(function ($v)use ($user_id,$have_per,$search_type){

                        return self::searchJudge($v,$user_id,$search_type,$have_per,config('pst.state.under_way'));
                    })
                    ->slice(($now_page-1)*$page_size, $page_size);
                $count=$records->count();
                break;
            case config('pst.state.wait_receive')://待接收--分为个人待接收的,企业待接收的(特殊)
                $records=DB::table('pst')
                    ->where('company_id',$company_id)
                    ->orderBy('created_at','desc')
                    ->get()
                    ->filter(function ($v)use ($user_id,$have_per,$search_type){
                        return self::wait_receive_judge($v,$user_id,$search_type,$have_per);
                    })
                    ->slice(($now_page-1)*$page_size, $page_size);
                $count=$records->count();
                break;
            case config('pst.state.wait_appoint')://待指派
                $records=DB::table('pst')
                    ->where('company_id',$company_id)
                    ->orderBy('created_at','desc')
                    ->get()
                    ->filter(function ($v)use ($user_id,$have_per,$search_type){
                        return self::searchJudge($v,$user_id,$search_type,$have_per,config('pst.state.wait_appoint'));
                    })
                    ->slice(($now_page-1)*$page_size, $page_size);
                $count=$records->count();
                break;
            case config('pst.state.wait_approval')://待审核
                $records=DB::table('pst')
                    ->where('company_id',$company_id)
                    ->orderBy('created_at','desc')
                    ->get()
                    ->filter(function ($v)use ($user_id,$have_per,$search_type){
                        return self::searchJudge($v,$user_id,$search_type,$have_per,config('pst.state.wait_approval'));
                    })
                    ->slice(($now_page-1)*$page_size, $page_size);
                $count=$records->count();
                break;
            case config('pst.state.finish')://评审完成
                $records=DB::table('pst')
                    ->where('company_id',$company_id)
                    ->orderBy('created_at','desc')
                    ->get()
                    ->filter(function ($v)use ($user_id,$have_per,$search_type){
                        return self::searchJudge($v,$user_id,$search_type,$have_per,config('pst.state.finish'));
                    })
                    ->slice(($now_page-1)*$page_size, $page_size);
                $count=$records->count();
                break;
            case config('pst.state.archived')://已归档
                $records=DB::table('pst')
                    ->where('company_id',$company_id)
                    ->orderBy('created_at','desc')
                    ->get()
                    ->filter(function ($v)use ($user_id,$have_per,$search_type){
                        return self::searchJudge($v,$user_id,$search_type,$have_per,config('pst.state.archived'));
                    })
                    ->slice(($now_page-1)*$page_size, $page_size);
                $count=$records->count();
                break;
            case config('pst.state.cancled')://已作废
                $records=DB::table('pst')
                    ->where('company_id',$company_id)
                    ->orderBy('created_at','desc')
                    ->get()
                    ->filter(function ($v)use ($user_id,$have_per,$search_type){
                        return self::searchJudge($v,$user_id,$search_type,$have_per,config('pst.state.cancled'));
                    })
                    ->slice(($now_page-1)*$page_size, $page_size);
                $count=$records->count();
                break;
            case config('pst.state.retracted')://已撤回
                $records=DB::table('pst')
                    ->where('company_id',$company_id)
                    ->orderBy('created_at','desc')
                    ->get()
                    ->filter(function ($v)use ($user_id,$have_per,$search_type){
                        return self::searchJudge($v,$user_id,$search_type,$have_per,config('pst.state.retracted'));
                    })
                    ->slice(($now_page-1)*$page_size, $page_size);
                $count=$records->count();
                break;
            case 'all':
                $records=DB::table('pst')
                    ->where('company_id',$company_id)
                    ->orderBy('created_at','desc')
                    ->get()
                    ->filter(function ($v)use ($user_id,$have_per,$search_type){
                        //加入评审中的类型记录
                        $state1=self::searchJudge($v,$user_id,$search_type,$have_per,config('pst.state.under_way'));
                        //加入待接收
                        $state2=self::wait_receive_judge($v,$user_id,$search_type,$have_per);
                        //加入待指派
                        $state3=self::searchJudge($v,$user_id,$search_type,$have_per,config('pst.state.wait_appoint'));
                        //加入待审批
                        $state4=self::searchJudge($v,$user_id,$search_type,$have_per,config('pst.state.wait_approval'));
                        //加入完成
                        $state5=self::searchJudge($v,$user_id,$search_type,$have_per,config('pst.state.finish'));
                        //加入归档
                        $state6=self::searchJudge($v,$user_id,$search_type,$have_per,config('pst.state.archived'));
                        //加入作废
                        $state7=self::searchJudge($v,$user_id,$search_type,$have_per,config('pst.state.cancled'));
                        //加入撤回
                        $state8=self::searchJudge($v,$user_id,$search_type,$have_per,config('pst.state.retracted'));
                        return ($state1||$state2||$state3||$state4||$state5||$state6||$state7||$state8);
                    })
                    ->slice(($now_page-1)*$page_size, $page_size);
                $count=$records->count();
                break;
            default:break;
        }
        return [
            'count'=>$count,
            'data'=>$records,
            'page_count'=>(int)ceil($count/$page_size),
        ];
    }
    /**
     * 评审通分类分页查询时的条件判断(几种公用的方法)
     * @param $v:评审通记录
     * @param int $user_id:目标用户
     * @param string $search_type:搜索的条件值
     * @param bool $have_per;是否有高级权限
     * @param string $target_type:搜索的目标类型
     * @return bool
     */
    public static function searchJudge($v,int $user_id,string $search_type,bool $have_per,string $target_type){
        //获取参与人员的相关信息
        $join_user_data=json_decode($v->join_user_data,true);//参与人员json解析
        $transfer_join_data=json_decode($v->transfer_join_data,true);//转移参与人员json解析
        $duty_user_data=json_decode($v->duty_user_data,true);//负责人json解析
        $transfer_duty_data=json_decode($v->transfer_duty_data,true);//转移负责人json解析

        $state1=false;
        //判断是否需要负责人搜索条件
        if (($search_type == config('pst.search_type.my_duty')) || ($search_type == config('pst.search_type.all'))) {
            //过滤是否是负责人&&该评审通状态为待审核
            if (!is_null($duty_user_data)) {
                if (($duty_user_data['duty_user_id'] == $user_id) &&
                    ($v->state==$target_type)
                ) {
                    $state1 = true;
                    return $state1;
                }
            }
        }
        $state2 = false;
        //判断是否需要参与人的搜索条件
        if (($search_type == config('pst.search_type.my_join')) || ($search_type == config('pst.search_type.all'))) {
            $inside_user_ids = $join_user_data['join_user_ids']['inside_user_ids'];//内部参与人id数组
            $inside_receive_state = $join_user_data['join_user_ids']['inside_receive_state'];//内部参与人id数组
            //过滤是否是参与人--先判断是否是参与人，
            if(!empty($inside_user_ids)&&in_array($user_id,$inside_user_ids)&&$inside_receive_state['state_'.$user_id]==$target_type){
                $state2=true;
                return $state2;
            }
        }
        //判断是否需要抄送人的搜索条件
        $state3=false;
        if(($search_type == config('pst.search_type.cc_my')) || ($search_type == config('pst.search_type.all'))){
            $cc_user_data=json_decode($v->cc_user_data,true);
            $cc_user_ids=$cc_user_data['cc_user_ids'];
            if(!empty($cc_user_ids)&&in_array($user_id,$cc_user_ids)&&($v->state==$target_type)){
                $state3=true;
                return $state3;
            }
        }
        //判断是否需要发起人的搜索条件
        $state4=false;
        if (($search_type == config('pst.search_type.my_publish')) || ($search_type == config('pst.search_type.all'))) {
            if (($v->publish_user_id == $user_id) && ($v->state == $target_type)) {
                $state4 = true;
                return $state4;
            }
        }
        //若有高级权限则也可以看到企业对应状态的记录
        $state5=false;
        if ($search_type == config('pst.search_type.all')) {
            if ($have_per) {
                if ($v->state == $target_type){
                    $state5 = true;
                    return $state5;
                }
            }
        }
        return ($state1||$state2||$state3||$state4||$state5);
    }
    /**
     * 过滤待接收分类查询的判断方法
     * @param $v
     * @param int $user_id
     * @param string $search_type
     * @param bool $have_per
     * @return bool
     */
    public static function wait_receive_judge($v,int $user_id,string $search_type,bool $have_per){
        //获取参与人员的相关信息
        $join_user_data=json_decode($v->join_user_data,true);//参与人员json解析
        $transfer_join_data=json_decode($v->transfer_join_data,true);//转移参与人员json解析
        $duty_user_data=json_decode($v->duty_user_data,true);//负责人json解析
        $transfer_duty_data=json_decode($v->transfer_duty_data,true);//转移负责人json解析
        //动态变更state
//                        $v->state=config('pst.state.wait_receive');
        $state1=false;
        $state2=false;
        //判断是否需要负责人搜索条件
        if (($search_type == config('pst.search_type.my_duty')) || ($search_type == config('pst.search_type.all'))) {
            //过滤是否是负责人&&状态为待接收&&评审通状态不为 待接收,待审核,拒绝,作废
            if (!is_null($duty_user_data)) {
                if (($duty_user_data['duty_user_id'] == $user_id) &&
                    ($duty_user_data['duty_receive_state'] == config('pst.state.wait_receive'))&&
                    ($v->state!=config('pst.state.wait_receive'))&&
                    ($v->state!=config('pst.state.wait_approval'))&&
                    ($v->state!=config('pst.state.cancled'))&&
                    ($v->state!=config('pst.state.refuse_received'))
                ) {
                    $state1 = true;
                    return true;
                }
            }
            //过滤是否是被转移负责人员&&且状态为待接收&&评审通状态不为 待接收,待审核,拒绝,作废
            if (!is_null($duty_user_data)) {
                if (
                    ($transfer_duty_data['duty_user_id'] == $user_id) &&
                    ($transfer_duty_data['duty_receive_state'] == config('pst.state.wait_receive'))&&
                    ($v->state!=config('pst.state.wait_receive'))&&
                    ($v->state!=config('pst.state.wait_approval'))&&
                    ($v->state!=config('pst.state.cancled'))&&
                    ($v->state!=config('pst.state.refuse_received'))
                ) {
                    $state2 = true;
                    return true;
                }
            }
        }
        $state3 = false;
        //判断是否需要参与人的搜索条件
        if (($search_type == config('pst.search_type.my_join')) || ($search_type == config('pst.search_type.all'))) {
            //过滤是否是参与人--先判断是否是参与人，再判断该用户是否是被转移的参与人
            $inside_user_ids = $join_user_data['join_user_ids']['inside_user_ids'];//内部参与人id数组
            $inside_receive_state = $join_user_data['join_user_ids']['inside_receive_state'];//内部参与人状态数组
            //查询是否是参与人员--直接接收
            if(!empty($inside_user_ids)&&
                in_array($user_id,$inside_user_ids)&&
                ($inside_receive_state['state_' . $user_id] == config('pst.state.wait_receive')) &&
                ($v->state!=config('pst.state.wait_receive'))&&
                ($v->state!=config('pst.state.wait_approval'))&&
                ($v->state!=config('pst.state.cancled'))&&
                ($v->state!=config('pst.state.refuse_received'))
            ){
                $state3 = true;
                return true;
            }
            //循环比对查找当前用户是否是被转移参与人
            if (!is_null($transfer_join_data)) {
                foreach ($transfer_join_data as $key => $data) {
                    //若当前用户id在参与人转移数组中,且接收状态为待接收&&评审通状态不为 待接收,待审核,拒绝,作废
                    if (
                        ($data['tranfser_user_id'] == $user_id) &&
                        ($data['recive_state'] == config('pst.state.wait_receive'))&&
                        ($v->state!=config('pst.state.wait_receive'))&&
                        ($v->state!=config('pst.state.wait_approval'))&&
                        ($v->state!=config('pst.state.cancled'))&&
                        ($v->state!=config('pst.state.refuse_received'))
                    ) {
                        $state3 = true;
                        return true;
                    }
                }
            }
        }
        //判断是否需要抄送人的搜索条件
        $state4=false;
        if(($search_type == config('pst.search_type.cc_my')) || ($search_type == config('pst.search_type.all'))){
            $cc_user_data=json_decode($v->cc_user_data,true);
            $cc_user_ids=$cc_user_data['cc_user_ids'];
            if(!empty($cc_user_ids)&&in_array($user_id,$cc_user_ids)&&($v->state==config('pst.state.wait_receive'))){
                $state4=true;
                return true;
            }
        }
        //判断是否需要发起人的搜索条件
        $state5=false;
        if (($search_type == config('pst.search_type.my_publish')) || ($search_type == config('pst.search_type.all'))) {
            if (($v->publish_user_id == $user_id) && ($v->state == config('pst.state.wait_receive'))) {
                $state4 = true;
                return true;
            }
        }
        //若有高级权限则也可以看到企业对应状态的记录
        $state6=false;
//                        if ($search_type == config('pst.search_type.all')) {
        if ($have_per) {
            if ($v->state == config('pst.state.wait_receive')){
                $state6 = true;
                return true;
            }
        }
//                        }

        return ($state1||$state2||$state3||$state4||$state5||$state6);
    }
//============================================================↑↑===================================================================

    /**
     * 获取评审通中指定json字段的指定值
     */
    public static function getTargetValue(int $pst_id,string $target_column,array $target_value ){
        return Pst::select($target_column.'->'.implode('->',$target_value).' as '.end($target_value))
                    ->where('id',$pst_id)
                    ->first()
                    ;
    }
    /**
     * 插入用户的评申通抄送记录
     * @param array $data:需要插入的数据组
     */
    public static function insertCcRecord(array $data){
        return DB::table('pst_cc_record')
                ->insert($data);
    }
    /**
     * 检查目标评审通是否有未完成子项--(可召回未完成的子项,可作废未完成的子项)
     * @param int $pst_id:目标评审通id
     * @param string $target_state:目标状态
     */
    public static function checkHaveUnfinishedChildren(int $pst_id){
        return  DB::table('pst')->where([
            ['last_pst_id','=',$pst_id],
            ['state','!=',config('pst.state.finish ')],
        ])->exists();
    }
    /**
     * 检查目标评审通是否有未完成子项--(可召回未完成的子项,可作废未完成的子项)
     * @param int $pst_id:目标评审通id
     * @param string $target_state:目标状态
     */
    public static function checkHaveTargetChildren(int $pst_id,string $target){
        return  DB::table('pst')->where([
            ['last_pst_id','=',$pst_id],
            ['state','=',$target],
        ])->exists();
    }
    /**
     * 获取目标评审通分支子项
     */
    public static function getChildren(int $pst_id,string $type='complex'){
        if ($type=='complex') {
            return Pst::select(self::$need_data)
                ->where('last_pst_id', $pst_id)
                ->get();
        }else{
            return Pst::select([
                'id',
                'company_id',//所属公司id
                'outside_user_id',//外部联系人id
                'state',//状态
                ])
                ->where('last_pst_id', $pst_id)
                ->get();
        }
    }
    /**
     * 拿到某评审通的顶级记录
     */
    public static function getTopPst(int $last_pst_id){
       $pst=Pst::find($last_pst_id);
       if($pst->last_pst_id!=0){
           return self::getTopPst($pst->last_pst_id);
       }else{
           return $pst;
       }
    }
    /**
     * 标记指定记录的所有子项为指定的状态
     */
    public static function markChildrenTargetState(int $pst_id,string $state){
        //判断是否有子项
        $state1=DB::table('pst')->where([
            ['last_pst_id','=',$pst_id],
        ])->exists();
        //获取子项的id数组
        $children_ids=DB::table('pst')->where([
            ['last_pst_id','=',$pst_id],
        ])
        ->pluck('id')
        ->toArray();
        if($state1){
            foreach ($children_ids as $id){
                //标记评审通的状态
                self::updatePst($id,['state'=>$state]);
                //递归标记
                self::markChildrenTargetState($id,$state);
            }
        }else{
            return;
        }
    }
    /**
     * 更新目标评审通的可见人id数据
     */
    public static function updateAllowUser(array $ids,$pst_id){
        //先获取目标记录
        $pst=Pst::find($pst_id);
        //拿到原可见人数据
        $origin_allow_ids=json_decode($pst->allow_user_ids,true);
        //合并可见人数据
        $origin_allow_ids=array_unique(array_merge($origin_allow_ids,$ids));
        //更新目标评审通
        $pst['allow_user_ids']=json_encode($origin_allow_ids);
        return true;
    }
//===============================评审通关联审批相关==========================================================>
    /**
     * 查询出某评审通所关联的审批(需要分页)
     * @return mixed
     */
    public static function  getPstRelationApproval(int $pst_id,int $now_page,int $page_size){
        //查询相关审批记录
        $records=Approval::where('related_pst_id',$pst_id)
                            ->orderBy('created_at','desc')
                            ->offset(($now_page-1)*$page_size)
                            ->limit($page_size)
                            ->get();
        $count=$records->count();
        return [
            'count'=>$count,
            'data'=>$records,
            'page_count'=>(int)ceil($count/$page_size),
        ];
    }
//===============================评审通自身相关==========================================================>
    /**
     * 查询出某评审通所关联的评审(需要分页)
     * @return mixed
     */
    public static function  getPstSelfRelation(int $pst_id,int $now_page,int $page_size){
        //先查询出评审同所关联的id数组
        $pst_ids=DB::table('pst_self_related')->where('target_pst_id',$pst_id)->pluck('related_pst_id')->toArray();
        //查询相关审批记录
        $records=Pst::whereIn('id',$pst_ids)
                            ->orderBy('created_at','desc')
                            ->offset(($now_page-1)*$page_size)
                            ->limit($page_size)
                            ->get();
        $count=$records->count();
        return [
            'count'=>$count,
            'data'=>$records,
            'page_count'=>(int)ceil($count/$page_size),
        ];
    }
    /**
     * 查询出某企业能够让关联的评审通
     * @param int $pst_id:所要排除的评审通
     * @param int $now_page:当前页
     * @param int $page_size:每页数据条数
     * @return array
     */
    public static function  getCanRelationPst(int $company_id,int $now_page,int $page_size){
        //查询相关审批记录
        $records=Pst::where('company_id',$company_id)
            ->orderBy('created_at','desc')
            ->offset(($now_page-1)*$page_size)
            ->limit($page_size)
            ->get();
        $count=$records->count();
        return [
            'count'=>$count,
            'data'=>$records,
            'page_count'=>(int)ceil($count/$page_size),
        ];
    }
//===============================评审通操作记录相关========================================================>=
    /**
     * 添加评审通操作记录
     * @param array $data:
     * @return mixed
     */
    public static function  addPstOperateRecord(array $data){
        return PstOperateRecord::create($data);
    }
    /**
     *获取某评审通的操作记录
     * @param int $pst_id
     * @return \Illuminate\Support\Collection
     */
    public static function  getPstOperateRecord(int $pst_id,int $now_page,int $page_size){
        $records=PstOperateRecord::where('pst_id',$pst_id)
                ->orderBy('created_at')
                ->offset(($now_page-1)*$page_size)
                ->limit($page_size)
                ->get();
        $count=$records->count();
        return [
            'count'=>$count,
            'data'=>$records,
            'page_count'=>(int)ceil($count/$page_size),
        ];
    }
//==========================================评审通报告文号相关=========================================================>
    /**
     * 设置某企业的评审通文号规则
     * @param array $data
     * @return mixed
     */
    public static function  makeReportNumber(array $data){
        return DB::table(self::$pst_report_number_table)->insert($data);
    }
    /**
     * 更新企业的评审通报告文号规则
     * @param int $id:目标记录id
     * @param int $company_id:目标企业id
     */
    public static function updateReportNumber(int $company_id,array $up_data){
        return DB::table(self::$pst_report_number_table)
                ->where([
                    ['company_id',$company_id],
                ])
                ->update($up_data);
    }
    /**
     * 检查某企业是否已经存在 规则生成记录
     * @param int $company_id
     */
    public static function checkCompanyExistRecord(int $company_id){
        return DB::table(self::$pst_report_number_table)
                    ->where('company_id',$company_id)
                    ->exists();
    }
    /**
     * 获取目标企业的文号规则
     * @param int $company_id
     */
    public static function getCompanyReportRule(int $company_id){
        return DB::table(self::$pst_report_number_table)
            ->select('rule_data', 'current_number')
            ->where('company_id',$company_id)
            ->first();
    }
}
