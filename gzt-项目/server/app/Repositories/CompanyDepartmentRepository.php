<?php
namespace App\Repositories;
use App\Http\Resources\department\DepartmentSimpleResource;
use App\Http\Resources\DepartmentResource;
use App\Models\CompanyDepartmentInfo;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/10/29
 * Time: 13:58
 */

class CompanyDepartmentRepository
{
    static private $departmentRepository;
    private $department_info_table='company_department_info';//存储公司部门信息的表名
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {

    }
    /**
     * 单例模式
     */
    static public function getDepartmentRepository(){
        if(self::$departmentRepository instanceof self)
        {
            return self::$departmentRepository;
        }else{
            return self::$departmentRepository = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 存储公司的department_info
     */
    public function add_department_info(int $company_id,array $data){
        //先检查是否有记录
        $record=CompanyDepartmentInfo::find($company_id);
        if(!is_null($record)){
            return ['status'=>false,'message'=>'已经存在记录'];
        }else{
            CompanyDepartmentInfo::create($data);
            return ['status'=>true,'message'=>'插入成功'];
        }
    }
    /**
     * 更新公司的department_info
     */
    public function update_department_info(int $id,array $data){
        //先检查是否有记录
        $record=CompanyDepartmentInfo::find($id);
        if(is_null($record)){
            return ['status'=>false,'message'=>'不存在记录'];
        }else{
            $record->update($data);
            $record->save();
            CompanyDepartmentInfo::where('id',$id)
                                ->update($data);
            return ['status'=>true,'message'=>'更新'];
        }
    }
    /**
     * 创建一个根节点
     * @param $data
     * @return mixed
     */
    public function addRootNode($data)
    {
        return Department::create($data)->saveAsRoot();
    }
    /**
     * 某个节点后追加节点
     * @param Department $parentNode
     * @param array $data
     * @return bool
     */
    public function appendRootNode(Department $parentNode, array $data)
    {
        return $parentNode->appendNode(Department::create($data));
    }
    /**
     * 拿到某个公司完整的部门树
     * @param $company_id
     * @return mixed
     */
    public function makeDepartmentInfo($company_id,$activation)
    {
        $id=$this->rootNodeByCompanyId($company_id);
        DepartmentSimpleResource::$company_id=$company_id;
        DepartmentSimpleResource::$activation=$activation;
        $info=DepartmentSimpleResource::collection(Department::withDepth()
        ->descendantsAndSelf($id )->toTree()
        );
        if($activation==1){
            $info=json_encode([
                'version'=>str_random(9),
                'data'=>$info[0],
            ]);
            if(!$this->checkExsitDepartmentInfo($company_id)){//不存在记录则插入一条新的
                CompanyDepartmentInfo::create(['company_id'=>$company_id,'info'=>$info]);
            }else{//存在的话则更新
                CompanyDepartmentInfo::
                where('company_id',$company_id)
                    ->update(['info'=>$info])
                ;
            }
        }
        return $info;
    }

    /**
     * 改变组织结构树时将树保存到info字段
     */
    public function saveInfo($company_id=null)
    {
        $company_id=$company_id===null?auth('api')->user()->current_company_id:$company_id;
//        return $company_id;
        $id=$this->rootNodeByCompanyId($company_id);
        DepartmentSimpleResource::$company_id=$company_id;
        $info=DepartmentSimpleResource::collection(Department::withDepth()
            ->descendantsAndSelf($id )->toTree()
        );
        $info=json_encode([
            'version'=>str_random(9),
            'data'=>$info[0],
        ]);
        if(!$this->checkExsitDepartmentInfo($company_id)){//不存在记录则插入一条新的
            CompanyDepartmentInfo::create(['company_id'=>$company_id,'info'=>$info]);
        }else{//存在的话则更新
            CompanyDepartmentInfo::
            where('company_id',$company_id)
                ->update(['info'=>$info])
            ;
        }
    }
    /**
     * 检查某公司是否存在部门信息json记录
     */
    public function checkExsitDepartmentInfo($company_id){
        $record=DB::table($this->department_info_table)
                ->where('company_id',$company_id)
                ->count();
        return $record==0?false:true ;
    }
    /**
     * 获取某公司的部门信息
     */
    public function getDepartmentInfo($company_id){
        return DB::table($this->department_info_table)
            ->where('company_id',$company_id)
            ->first()
            ->info;
    }
    /**
     * 拿到某个节点的子树
     * @param $depth:查询的深度
     */
    public function getNodeDescendantsTree($node_id)
    {
        return DepartmentResource::collection(Department::
            withDepth()
            ->where('id',$node_id)
            ->with('descendants.users:id,name,email,email_verified,tel,tel_verified')
            ->first()
            ->descendants
            ->toTree()
        );
    }

    /**
     * 根据一个节点,获取所有子节点树(包含自己)
     */
    public function getNodeChildrens($department_id){
        return Department::descendantsAndSelf($department_id)->toTree()->first()->toArray();
    }
    /**
     * 根据一个节点,获取所有的祖节点数组(包含自己)
     */
    public function getAllAncestorsNode($department_id){
        return Department::find($department_id)->ancestors;
    }
    /**
     * 根据一个节点,获取所有的子节点数组(不包含自己)
     */
    public function getAllDescendantsNode($department_id){
        return Department::descendantsOf($department_id);
    }
    /**
     * 根据一个节点,获取其一级子类
     */
    public function getChildrenNode($department_id){
        return Department::find($department_id)->children;
    }
    /**
     * 根据公司id,获取部门的根节点
     */
    public function rootNodeByCompanyId($company_id){
        return Department::where('parent_id',null)->where('company_id',$company_id)->value('id');
    }
    /**
     * 获取某个公司的部门ids
     */
    public function getCompanyDepartmentIds($company_id){
        return Department::where('company_id',$company_id)->get()->pluck('id')->toArray();
    }
}