<?php

namespace App\Http\Controllers\Api;


use App\Http\Resources\user\UserCardResource;
use App\Tools\FunctionTool;
use App\Tools\PstTool;
use App\Tools\UserTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 评审通控制器
 * Class UserController
 * @package App\Http\Controllers\Api
 */
class PstController extends Controller
{
    private $pstTool;//评审通工具类
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->pstTool=PstTool::getPstTool();
    }

//===============================评审通流程相关==========================================================>
    /**
     * 添加评审流程信息
     */
    public function addProcessTemplate(Request $request)
    {
        return $this->pstTool->addProcessTemplate($request->all());
    }
    /**
     * 删除指定的流程模板
     */
    public function deleteProcessTemplate(Request $request){
        return $this->pstTool->deleteProcessTemplate(FunctionTool::decrypt_id($request->id));
    }
    /**
     * 禁用 or 启用指定的流程模板
     */
    public function switchShowProcessTemplate(Request $request){
        return $this->pstTool->switchShowProcessTemplate(FunctionTool::decrypt_id($request->id));
    }
    /**
     * 将指定流程模板移动到指定分类下
     */
    public function moveProcessTemplate(Request $request){
        return $this->pstTool->moveProcessTemplate(FunctionTool::decrypt_id($request->template_id)
            ,FunctionTool::decrypt_id($request->type_id));
    }
    /**
     *  获取某企业所有的评审通模板信息
     * @param Request $request:company_id
     * @return mixed
     */
    public function getCompanyProcessTemplate(Request $request)
    {
        return $this->pstTool->getCompanyProcessTemplate();
    }
    /**
     * 通过id获取指定评审通流程的详细信息
     */
    public function getProcessTemplateById(Request $request)
    {
        return $this->pstTool->getProcessTemplateById(FunctionTool::decrypt_id($request->id));
    }
    /**
     * 更新评审通--流程模板的信息
     */
    public function updateProcessTemplate(Request $request)
    {
        return $this->pstTool->updateProcessTemplate($request->all());
    }
    /**
     * 添加评审通模板类型
     * @param array $data :前端传递数据组
     */
    public function addProcessTemplateType(Request $request)
    {
        return $this->pstTool->addProcessTemplateType($request->all());
    }
    /**
     * 重命名流程模板类型名称
     * @param int $type_id:目标类型id
     * @param string $name:更新的名称
     */
    public function alterProcessTemplateTypeName(Request $request){
        return $this->pstTool->alterProcessTemplateTypeName(FunctionTool::decrypt_id($request->id),$request->name);
    }
    /**
     * 删除评审通流程模板类型
     * @param int $type_id:类型id
     */
    public function deleteProcessTemplateType(Request $request){
        return $this->pstTool->deleteProcessTemplateType(FunctionTool::decrypt_id($request->id));
    }
    /**
     * 对流程模板类型进行排序
     * @param array $data
     * @return mixed
     */
    public function sortProcessTemplateType(Request $request){
        return $this->pstTool->sortProcessTemplateType($request->all());
    }
    /**
     * 按序获取企业所有的评审流程分类数据
     * @return mixed
     */
    public function getProcessTemplateType(Request $request)
    {
        return $this->pstTool->getProcessTemplateType();
    }

//===============================评审通模板相关==========================================================>
    /**
     * 添加评审通模板
     * @param array $data
     * @return mixed
     */
    public function addPstTemplate(Request $request)
    {
        return $this->pstTool->addPstTemplate($request->all());
    }
    /**
     * 删除指定的模板
     */
    public function deletePstTemplate(Request $request){
        return $this->pstTool->deletePstTemplate(FunctionTool::decrypt_id($request->id));
    }
    /**
     * 禁用 or 启用指定的模板
     */
    public function switchShowPstTemplate(Request $request){
        return $this->pstTool->switchShowPstTemplate(FunctionTool::decrypt_id($request->id));
    }
    /**
     * 将指定模板移动到指定分类下
     * @param int $template_id
     * @param int $type_id
     * @return mixed
     */
    public function moveTemplate(Request $request){
        return $this->pstTool->moveTemplate(FunctionTool::decrypt_id($request->template_id)
                                            ,FunctionTool::decrypt_id($request->type_id));
    }
    /**
     * 获取某企业的所有评审通模板
     * @param int $company_id
     * @return mixed
     */
    public function getCompanyPstTemplate(Request $request)
    {
        return $this->pstTool->getCompanyPstTemplate();
    }
    /**
     * 通过id获取指定评审通模板的详细信息
     */
    public function getPstTemplateById(Request $request)
    {
        return $this->pstTool->getPstTemplateById(FunctionTool::decrypt_id($request->id));
    }
    /**
     * 更新评审通模板的信息
     */
    public function updatePstTemplate(Request $request)
    {
        return $this->pstTool->updatePstTemplate($request->all());
    }
    /**
     * 添加评审通模板类型
     * @param array $data :前端传递数据组
     */
    public function addPstTemplateType(Request $request)
    {
       return $this->pstTool->addPstTemplateType($request->all());
    }
    /**
     * 重命名模板类型名称
     * @param int $type_id:目标类型id
     * @param string $name:更新的名称
     */
    public function alterPstTemplateTypeName(Request $request){
        return $this->pstTool->alterPstTemplateTypeName(FunctionTool::decrypt_id($request->id),$request->name);
    }
    /**
     * 删除评审通模板类型
     * @param int $type_id:类型id
     */
    public function deletePstTemplateType(Request $request){
        return $this->pstTool->deletePstTemplateType(FunctionTool::decrypt_id($request->id));
    }
    /**
     * 对流程模板类型进行排序
     * @param array $data
     * @return mixed
     */
    public function sortPstTemplateType(Request $request){
        return $this->pstTool->sortPstTemplateType($request->all());
    }
    /**
     * 按序获取评审通分类信息
     * @param array $data
     * @return mixed
     */
    public function getPstTemplateType(Request $request)
    {
        return $this->pstTool->getPstTemplateType();
    }
    /**
     * 获取经典评审通模板
     * @param int $company_id:公司id
     */
    public function getClassicPstTemplate(Request $request){
       return $this->pstTool->getClassicPstTemplate();
    }

//===============================评审通基础表单数据相关==========================================================>
    /**
     * 表单基础数据的获取
     * 计算分类,工程分类,的列表类型--网站baisc数据
     * 送审业务负责科室,行为标签的列表类型--企业数据
     * @return mixed
     */
    public function getFormBasicData(Request $request)
    {
        return $this->pstTool->getFormBasicData();
    }
    /**
     *更新评审通企业基本表单数据
     */
    public function updateCompanyFormBasicData(Request $request)
    {
        return $this->pstTool->updateCompanyFormBasicData($request->all());
    }

//===============================评审通相关==========================================================>
    /**
     * 创建一个评审通
     * @param \App\Interfaces\Request $request
     */
    public function createPst(Request $request)
    {
       return $this->pstTool->createPst($request->all());
    }
    /**
     * 按类型搜索与我相关的评审通--我发起的,我参与的,我负责的等状态的评审通数据
     * @param array $data:[now_page,page_size]
     */
    public function searchMyPstByType(Request $request){
        return $this->pstTool->searchMyPstByType($request->all());
    }
    /**
     * 通过评审通流程状态搜索评审通
     * @param array $data:前端传递的request数组
     * @return mixed
     */
    public function searchPstByState(Request $request){
        return $this->pstTool->searchPstByState($request->all());
    }
    /**
     * 查询某个评审通状态下的细分记录--联合查询--我参与的,我负责的。。。。。
     * @param array $data:前端传递的request数组
     * @return mixed
     */
    public function unionSearchPstByState(Request $request){
      return $this->pstTool->unionSearchPstByState($request->all());
    }
    /**
     * 通过id 获取指定的评审通详情
     * @param array $data:所需要的数据包
     * @return mixed
     */
    public function getPstById(Request $request){
        return $this->pstTool->getPstById($request->all());
    }
//===============================评审通附件相关==========================================================>
    /**
     * 通过id 获取指定的评审通的资料清单
     * @param array $data:所需要的数据包
     * @return mixed
     */
    public function getPstFiles(Request $request){
        return $this->pstTool->getPstFiles($request->all());
    }
    /**
     * 更新指定评审通附件
     * @param array $data:所需要的数据包
     * @return mixed
     */
    public function updatePstFiles(Request $request){
        return $this->pstTool->updatePstFiles($request->all());
    }
    /**
     * 获取目标评审通的下级所有已完成的附件--以分组的形式
     * @param array $need_data:所需要的数据
     *               -- pst_id:目标评审通id
     *               -- pst_id:目标评审通id
     */
    public  function getPstChildrenFiles(Request $request){
        return $this->pstTool->getPstChildrenFiles($request->all());
    }
//===============================评审通操作相关==========================================================>
    /**
     * 评审通接收
     * @param Request $request:需要将单个评审通数据全部发送过来
     * @return mixed
     */
    public function receive(Request $request){
      return PstTool::receive($request->all());
    }
    /**
     * 评审通拒绝接收
     * @param Request $request:需要将单个评审通数据全部发送过来
     * @return mixed
     */
    public function refuse_receive(Request $request){
        return $this->pstTool->refuse_receive($request);
    }
    /**
     * 评审通编辑
     * @param Request $request
     */
    public function editor(Request $request){
        return $this->pstTool->editor($request);
    }
    /**
     * 评审通指派(即指派负责人)
     * 前端需要将所选的负责人信息给传递过来
     * @param Request $request:
     * pst_id,
     * 负责人的信息
     */
    public function appoint(Request $request){
        return $this->pstTool->appoint($request);
    }
    /**
     * 评审通移交负责人
     * @param Request $request
     * 前端需要将选择的负责人数组gei
     */
    public function transfer_duty(Request $request){
        return $this->pstTool->transfer_duty($request);
    }
    /**
     * 评审通移交参与人
     * @param Request $request
     */
    public function transfer_join(Request $request){
        return $this->pstTool->transfer_join($request);
    }
    /**
     * 评审通递交
     * @param Request $request
     */
    public function deliver(Request $request){
        return $this->pstTool->deliver($request);
    }
    /**
     * 评审通召回
     * @param Request $request
     */
    public function recall(Request $request){
        return $this->pstTool->recall($request);
    }
    /**
     * 评审通作废
     * @param Request $request
     */
    public function cancle(Request $request){
        return $this->pstTool->cancle($request);
    }
    /**
     * 评审通打回
     * @param Request $request
     */
    public function back(Request $request){
        return $this->pstTool->back($request);
    }
    /**
     * 评审通撤回
     * @param Request $request
     */
    public function retract(Request $request){
        return $this->pstTool->retract($request);
    }
    /**
     * 评审通完成
     * @param Request $request
     */
    public function finish(Request $request){
        return $this->pstTool->finish($request);
    }
    /**
     * 评审通归档
     * @param Request $request
     */
    public function archive(Request $request){
        return $this->pstTool->archive($request);
    }
    /**
     * 获取评审通需要合并的下级数据
     * @param array $data;所需要的数据组
     * @return mixed
     */
    public function getMergeData(Request $request){
       return $this->pstTool->getMergeData($request->all());
    }
    /**
     * 更新内部参与人表单数据
     * @param array $data:所需要传递过来的数据
     *                    --form_data:参与人的表单数据
     *                    --pst_id:目标评审通id
     */
    public function updateInsideJoinForm(Request $request){
       return $this->pstTool->updateInsideJoinForm($request->all());
    }
    /**
     * 评审通 负责人审阅参与人提交的东西
     * @param Request $request
     */
    public function duty_agree_join(Request $request){
       return $this->pstTool->duty_agree_join($request);
    }
//===============================评审通关联审批==========================================================>
    /**
     * 查询出某评审通所关联的审批(需要分页)
     * @return mixed
     */
    public function  getPstRelationApproval(Request $request){
        return $this->pstTool->getPstRelationApproval($request->all());
    }
//===============================评审通关联评审通=========================================================>
    /**
     * 查询出某评审通所关联的评审(需要分页)
     * @return mixed
     */
    public function  getPstSelfRelation(Request $request){
        return $this->pstTool->getPstSelfRelation($request->all());
    }
    /**
     * 移除某审批与评审通的关联待定
     * @return mixed
     */
    public function  removePstSelfRelation(Request $request){
        return $this->pstTool->removePstSelfRelation($request->all());
    }
    /**
     * 查询出某评审通所关联的评审(需要分页)
     * @return mixed
     */
    public function  getCanRelationPst(Request $request){
        return $this->pstTool->getCanRelationPst($request->all());
    }
//===============================评审通操作记录相关==========================================================>
    /**
     * 获取评审通操作记录
     * @param int $pst_id
     * @return mixed
     */
    public  function getPstOperateRecord(Request $request){
       return $this->pstTool->getPstOperateRecord($request->all());
    }

    /**
     * 获取评审通当前处理人信息
     * @param Request $request
     * @return false|string
     */
    public function getPstCurrentHandler(Request $request){
        return $this->pstTool->getPstCurrentHandlers($request->all());
    }
//========================================评审通报告文号============================================================>
    /**
     * 设置/更新  某企业的评审通文号规则
     * @param array $data
     * @return mixed
     */
    public function  makeReportNumber(Request $request){
        return $this->pstTool->makeReportNumber($request->all());
    }
    /** 获取 某企业的评审通文号规则 */
    public function getReportNumber(Request $request){
        return $this->pstTool->getReportNumber($request->all());
    }
    //===========================================评审通导出模板设置============================================================================>
    /**
     * 获取分组列表
     */
    public function getExportTypeList()
    {
        return $this->pstTool->getExportTypeList();
    }
    /**
     * 创建分组
     */
    public function createExportType(Request $request)
    {
        return $this->pstTool->createExportType($request->name);
    }
    /**
     * 删除分组
     */
    public function deleteExportType(Request $request)
    {
        return $this->pstTool->deleteExportType($request->id);
    }
    /**
     * 编辑分组
     */
    public function editExportType(Request $request)
    {
        return $this->pstTool->editExportType($request->id,$request->name);
    }
    /**
     * 保存分组排序
     */
    public function saveExportTypeSequence(Request $request)
    {
        return $this->pstTool->saveExportTypeSequence($request->all());
    }
    /**
     * 创建评审通导出模板
     */
    public function createExportTemplate(Request $request)
    {
        return $this->pstTool->createExportTemplate($request->all());
    }
    /**
     * 评审通导出模板列表
     */
    public function exportTemplateList(Request $request)
    {
        return $this->pstTool->exportTemplateList($request->all());
    }
    /**
     * 评审通导出模板编辑
     */
    public function exportTemplateEdit(Request $request)
    {
        return $this->pstTool->exportTemplateEdit($request->id);
    }
    /**
     * 评审通导出模板保存编辑
     */
    public function exportTemplateSaveEdit(Request $request)
    {
        return $this->pstTool->exportTemplateSaveEdit($request->all());
    }
    /**
     * 评审通导出模板禁用(启用)
     */
    public function exportTemplateEnable(Request $request)
    {
        return $this->pstTool->exportTemplateEnable($request->all());
    }
    /**
     * 评审通导出模板移动
     */
    public function exportTemplateMove(Request $request)
    {
        return $this->pstTool->exportTemplateMove($request->all());
    }
    /**
     * 创建导出模板打包分组
     */
    public function createExportPackage(Request $request)
    {
        return $this->pstTool->createExportPackage($request->all());
    }
    /**
     * 删除导出模板打包分组
     */
    public function deleteExportPackage(Request $request)
    {
        return $this->pstTool->deleteExportPackage($request->id);
    }
    /**
     * 编辑导出模板打包分组名称
     */
    public function editExportPackageName(Request $request)
    {
        return $this->pstTool->editExportPackageName($request->id,$request->name);
    }
    /**
     * 个人选择导出模板进行打包导出(单个模板导出)
     * 选择模板进行导出操作
     */
    public function exportSingleTemplatePackage(Request $request)
    {
        return $this->pstTool->exportSingleTemplatePackage($request->all());
    }
    /**
     * 个人选择导出模板进行打包导出(多个模板导出)
     */
    public function exportTemplatePackage(Request $request)
    {
        return $this->pstTool->exportTemplatePackage($request->all());
    }
    /**
     * 个人导出模板包列表
     */
    public function exportPackageLike()
    {
        return $this->pstTool->exportPackageLike();
    }
    /**
     * 生成单个评审通报告(变量替换返回模板)
     */
    public function getReplacedVarTemplate(Request $request)
    {
        return $this->pstTool->getReplacedVarTemplate($request->all());
    }

    public function test(){
        return $this->pstTool->getPstCurrentHandlers(['pst_id'=>29]);
    }
}
