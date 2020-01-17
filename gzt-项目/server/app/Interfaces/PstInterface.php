<?php
namespace App\Interfaces;
use App\Models\OssFile;
use App\Models\Pst;
use Illuminate\Http\Request;

/**
 * 评审通模块所要实现的功能
 */
interface PstInterface
{
// ==============================评审通流程相关=======================================>
    /**
     * 添加评审通--流程模板信息
     */
    public function addProcessTemplate(array $data);
    /**
     * 删除指定的流程模板
     */
    public function deleteProcessTemplate(int $id);
    /**
     * 禁用 or 启用指定的流程模板
     */
    public function switchShowProcessTemplate(int $id);
    /**
     * 将指定流程模板移动到指定分类下
     * @param int $template_id:模板id
     * @param int $type_id:分类id
     * @return mixed
     */
    public function moveProcessTemplate(int $template_id,int $type_id);
    /**
     * 获取某企业所有的评审通--流程模板信息
     * @return mixed
     */
    public function getCompanyProcessTemplate();
    /**
     * 通过id获取指定评审通--流程模板的详细信息
     */
    public function getProcessTemplateById(int $id);
    /**
     * 更新评审通--流程模板的信息
     */
    public function updateProcessTemplate(array $data);
    /**
     * 添加评审通流程模板类型
     * @param array $data:前端传递数据组
     */
    public function addProcessTemplateType(array $data);
    /**
     *重命名评申通流程模板类型名称
     */
    public function alterProcessTemplateTypeName(int $type_id,string $name);
    /**
     * 删除评审通流程模板类型
     * @param int $type_id:类型id
     */
    public function deleteProcessTemplateType(int $type_id);
    /**
     * 对流程模板类型进行排序
     * @param array $data
     * @return mixed
     */
    public function sortProcessTemplateType(array $data);
    /**
     * 按序获取企业所有的评审流程分类数据
     * @return mixed
     */
    public function getProcessTemplateType();


// ==============================评审通模板相关=======================================>
    /**
     * 添加评审通模板
     * @param array $data
     * @return mixed
     */
    public function addPstTemplate(array $data);
    /**
     * 删除指定的模板
     */
    public function deletePstTemplate(int $id);
    /**
     * 禁用 or 启用指定的模板
     */
    public function switchShowPstTemplate(int $id);
    /**
     * 将指定模板移动到指定分类下
     * @param int $template_id
     * @param int $type_id
     * @return mixed
     */
    public function moveTemplate(int $template_id,int $type_id);
    /**
     * 获取某企业的所有评审通模板
     * @param int $company_id
     * @return mixed
     */
    public function getCompanyPstTemplate();
    /**
     * 通过id获取指定评审通模板的详细信息
     */
    public function getPstTemplateById(int $id);
    /**
     * 更新评审通模板的信息
     */
    public function updatePstTemplate(array $data);
    /**
     * 添加评审通模板类型
     * @param array $data:前端传递数据组
     */
    public function addPstTemplateType(array $data);
    /**
     *重命名模板类型名称
     */
    public function alterPstTemplateTypeName(int $type_id,string $name);
    /**
     * 删除评审通模板类型
     * @param int $type_id:类型id
     */
    public function deletePstTemplateType(int $type_id);
    /**
     * 按序获取评审通分类信息
     * @param array $data
     * @return mixed
     */
    public function getPstTemplateType();
    /**
     * 获取某公司的经典评审通模板
     * @param int $company_id:公司id
     */
    public function getClassicPstTemplate();


 // ==============================评审通基础表单相关=======================================>
    /**
     * 表单基础数据的获取
     * @return mixed
     */
    public function getFormBasicData();
    /**
     * 生成企业评审通表单基础数据
     * @return mixed
     */
    public function initCompanyFormBasicData(int $company_id);
    /**
     *更新评审通企业基本表单数据
     */
    public function updateCompanyFormBasicData(array $data);

// ==============================评审通相关=======================================>
    /**
     * 创建一个评审通
     * @param Request $request
     */
    public function createPst(array $data);
    /**
     * 更新指定的评审通
     */
    public function updatePst(array $data);
    /**
     * 移除一个评审通
     */
    public function removePst(array $data);
    /**
     * 通过id 获取指定的评审通
     * @param array $data:所需要的数据包
     * @return mixed
     */
    public function getPstById(array $data);
    /**
     * 通过id 获取指定的评审通的资料清单
     * @param array $data:所需要的数据包
     * @return mixed
     */
    public function getPstFiles(array $data);
    /**
     * 更新指定评审通附件
     * @param array $data:所需要的数据包
     * @return mixed
     */
    public function updatePstFiles(array $data);
    /**
     * 评审通发起审批的回调方法
     */
    public static function approvalCallBack(array $data);
    /**
     * 按类型搜索与我相关的评审通--我发起的,我参与的,我负责的等状态的评审通数据
     * @param array $data:[now_page,page_size]
     */
    public function searchMyPstByType(array $data);
    /**
     * 通过评审通流程状态搜索评审通
     * @param array $data:前端传递的request数组
     * @return mixed
     */
    public function searchPstByState(array $data);
    /**
     * 查询某个评审通状态下的细分记录--联合查询--我参与的,我负责的。。。。。
     * @param array $data:前端传递的request数组
     * @return mixed
     */
    public function unionSearchPstByState(array $data);
    /**
     * 组装用户在某个评审通中的身份信息
     * @param int $user_id:目标用户
     * @param int $pst_id:目标评审通
     * @return mixed
     */
    public function makeUserIdentityInfo(int $user_id,Pst $v,bool $have_per);
    /**
     * 组装用户在某个评审通中具体的人员状态信息
     * @param int $user_id:目标用户
     * @param Pst $v:目标评审通
     * @param bool $have_per:是否有相应的评审通高级权限
     * @return mixed
     */
    public function getDetailRelationInfo(int $pst_id,int $user_id);
    /**
     * 获取评审通操作记录
     * @param int $pst_id
     * @return mixed
     */
    public  function getPstOperateRecord(array $data);
//==================================评审通附件====================================>
    /**
     * 传递评审通附件至下级企业
     * @param $files
     * @param int $company_id
     * @return mixed
     */
    public static function copyFileToLastCompany($files,int $company_id,string $target_directory,array $data);
    /**
     * 获取目标评审通的下级所有已完成的附件--以分组的形式
     * @param array $data:所需要的数据
     *               -- pst_id:目标评审通id
     *               -- pst_id:目标评审通id
     */
    public  function getPstChildrenFiles(array $data);
// ==============================评审通操作相关====================================>
    /**
     * 评审通接收
     * @param Request $request
     * @return mixed
     */
    public static function receive(array $data);
    /**
     * 评审通拒绝接收
     * @param Request $request
     * @return mixed
     */
    public function refuse_receive(Request $request);
    /**
     * 评审通编辑
     * @param Request $request
     */
    public function editor(Request $request);
    /**
     * 评审通指派
     * @param Request $request
     */
    public function appoint(Request $request);
    /**
     * 评审通移交负责人
     * @param Request $request
     */
    public function transfer_duty(Request $request);
    /**
     * 评审通移交参与人
     * @param Request $request
     */
    public function transfer_join(Request $request);
    /**
     * 评审通递交
     * @param Request $request
     */
    public function deliver(Request $request);
    /**
     * 评审通召回
     * @param Request $request
     */
    public function recall(Request $request);
    /**
     * 评审通作废
     * @param Request $request
     */
    public function cancle(Request $request);
    /**
     * 评审通撤回
     * @param Request $request
     */
    public function retract(Request $request);
    /**
     * 评审通完成
     * @param Request $request
     */
    public function finish(Request $request);
    /**
     * 评审通归档
     * @param Request $request
     */
    public function archive(Request $request);
    /**
     * 单个评审通中操作按钮状态判断
     * @param int $pst_id:目标评审通id
     * @param int $user_id:目标用户id
     * @return mixed
     */
    public static function btn_status(int $pst_id,int $user_id);
    /**
     * 获取合并下级数据
     * @param array $data;所需要的数据组
     * @return mixed
     */
    public function getMergeData(array $data);
    /**
     * 评审通各级节点完成操作--需要合并数据
     * @param Request $request
     */
    public function pst_finish(Pst $pst,array $data);
    /**
     * 更新内部参与人表单数据
     * @param array $data:所需要传递过来的数据
     *                    --form_data
     */
    public function updateInsideJoinForm(array $data);
//===============================评审通关联审批相关==========================================================>
    /**
     * 查询出某评审通所关联的审批(需要分页)
     * @return mixed
     */
    public function  getPstRelationApproval(array $data);
    /**
     * 移除某审批与评审通的关联
     * @return mixed
     */
    public function  removePstApprovalRelation(array $data);
//===============================评审通关联自身相关==========================================================>
    /**
     * 查询出某评审通所关联的评审(需要分页)
     * @return mixed
     */
    public function  getPstSelfRelation(array $data);
    /**
     * 移除某审批与评审通的关联待定
     * @return mixed
     */
    public function  removePstSelfRelation(array $data);
    /**
     * 查询出某评审通所关联的评审(需要分页)
     * @return mixed
     */
    public function  getCanRelationPst(array $data);
//=======================评审通报告文号生成规则=====================================================================>
    /**
     * 设置某企业的评审通文号规则
     * @param array $data
     * @return mixed
     */
    public function  makeReportNumber(array $data);

    /**
     * 按照企业的文号规则获取下一个文号
     * @param int $company_id:目标企业id
     * @return mixed
     */
    public static function  getNextReportNumber(int $company_id):string;
}