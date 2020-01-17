<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;
use App\Http\Resources\notice\CompanyNoticeColumnResource;
use App\Interfaces\CompanyNoticeColumnInterface;
use App\Models\CompanyNoticeColumn;
use App\Repositories\BasicRepository;
use App\Repositories\CompanyNoticeColumnRepository;

/**
 * 企业公告栏目工具类
 */
class CompanyNoticeColumnTool implements CompanyNoticeColumnInterface
{
    static private $cNoticeColumnTool;
    private $userTool;//用户工具类
    private $basicRepository;//基础表仓库类
    private $cNoticeColumnRepository;//企业公告栏目仓库类
    private $validateTool;//数据验证工具类
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->userTool=UserTool::getUserTool();
        $this->cNoticeColumnRepository=CompanyNoticeColumnRepository::getCompanyNoticeRepository();
        $this->basicRepository=BasicRepository::getBasicRepository();
        $this->validateTool=ValidateTool::getValidateTool();
    }
    /**
     * 单例模式
     */
    static public function getCompanyNoticeColumnTool(){
        if(self::$cNoticeColumnTool instanceof self)
        {
            return self::$cNoticeColumnTool;
        }else{
            return self::$cNoticeColumnTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 初始化企业栏目信息
     * @param int $company_id
     */
    public function initCompanyColumn(int $company_id)
    {
        $data=json_decode($this->basicRepository->getBasicData(config('basic.c_notice_column'))->body,true);
        foreach ($data as $v){
            $v['company_id']=$company_id;//动态加入企业id
            $this->cNoticeColumnRepository->add($v);
        }
        return true;
    }
    /**
     * 添加栏目
     * @param $company_id
     * @param $data
     */
    public function addColumn(array $data)
    {
        $user=auth('api')->user();
        $company_id=$user->current_company_id;
        //检查所属公司是否存在重名的name栏目
        if($this->cNoticeColumnRepository->checkColumnExsit($company_id,$data['name'])){
            return json_encode(['status'=>'fail','message'=>'栏目创建已存在']);
        };
        //name敏感词过滤
        $validator = $this->validateTool->sensitive_word_validate(['name'=>$data['name']]);
        if (is_array($validator)) {
            $validator['index']='name';
            return json_encode($validator);
        }
        $count =$this->cNoticeColumnRepository->getCNoticeColumnCount($company_id);
        $data['order']=$count+1;
        $data['company_id']=$company_id;
        $column=$this->cNoticeColumnRepository->add($data);
        if ($column){
           return json_encode(['status'=>'success','message'=>'栏目创建成功','column'=>['name'=>$column->name,'id'=>FunctionTool::encrypt_id($column->id)]]);
        }else{
           return json_encode(['status'=>'fail','message'=>'栏目创建不成功']);
        }
    }
    /**
     * 移除栏目
     * @param array $data
     */
    public function removeColumn(array $data)
    {
        $user=auth('api')->user();
        $column_id=FunctionTool::decrypt_id($data['column_id']);//栏目id
        $company_id=$user->current_company_id;;//对应的企业id
        $record=CompanyNoticeColumn::find($column_id);
        if (!count($record->cnotice)&&$company_id==$record->company_id){
            $record->delete($column_id);
            return json_encode(['status'=>'success','message'=>'删除成功~']);
        }else{
            return json_encode(['status'=>'fail','message'=>'该栏目下有相关公告不能删除~']);
        }
    }
    /**
     * 栏目排序(前端传参)
     * @param $data
     */
    public function sortColumn(array $data)
    {
        $array=json_decode($data['sort_json'],true);
        $keys=array_keys($array);
       foreach ($keys as $key){
           $record=CompanyNoticeColumn::find(FunctionTool::decrypt_id($key));
           $record->order=$array[$key];
           $record->save();
       }
       return json_encode(['status'=>'success','message'=>'排序成功']);
    }
    /**
     * 获取某企业下的公告栏目信息(排序)
     * @param $company_id
     */
    public function getAllColumn($company_id)
    {
        $user=auth('api')->user();
        $data=CompanyNoticeColumnResource::collection($this->cNoticeColumnRepository->getAllColumn($user->current_company_id));
        return json_encode(collect($data)->toArray()) ;
    }
    /**
     * 更改栏目信息
     * @param array $data
     */
    public function alterColumn(array $data)
    {
        $this->cNoticeColumnRepository->update(FunctionTool::decrypt_id($data['column_id']),['name'=>$data['name']]);
        return json_encode(['status'=>'success','message'=>'修改成功']);
    }
}