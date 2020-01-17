<?php

namespace App\Tools;

use App\Exports\PstReportExportWord;
use App\Exports\PstSingleReportExportWord;
use App\Http\Resources\ApprovalResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\pst\PstDetailResource;
use App\Http\Resources\pst\PstExportTemplateListDisResource;
use App\Http\Resources\pst\PstExportTemplateListEnResource;
use App\Http\Resources\pst\PstExportTemplateResource;
use App\Http\Resources\pst\PstListResource;
use App\Http\Resources\pst\PstProcessTemplateResource;
use App\Http\Resources\pst\PstProcessTemplateTypeResource;
use App\Http\Resources\pst\PstRecordResource;
use App\Http\Resources\pst\PstTemplateResource;
use App\Http\Resources\pst\PstTemplateTypeResource;
use App\Http\Resources\user\UserBaseResource;
use App\Http\Resources\user\UserSimpleResource;
use App\Interfaces\PstInterface;
use App\Interfaces\SmsInterface;
use App\Interfaces\UserInterface;
use App\Models\Company;
use App\Models\CompanyNotice;
use App\Models\FileUseRecord;
use App\Models\OssFile;
use App\Models\Pst;
use App\Models\PstExportPackage;
use App\Models\PstExportTemplate;
use App\Models\PstExportType;
use App\Models\PstTemplateType;
use App\Models\User;
use App\Repositories\BasicRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\PstRepository;
use App\Repositories\UserRepository;
use App\WebSocket\WebSocketClient;
use Chumper\Zipper\Facades\Zipper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Overtrue\EasySms\EasySms;
use swoole_client;
use swoole_websocket_server;

/**
 * 评审通工具类
 */
class PstTool implements PstInterface
{
    static private $pstTool;
    private $basicRepository; //基础数据仓库类
    private $approvalTool; //审批工具类
    private $userRepository; //用户数据仓库
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->basicRepository = BasicRepository::getBasicRepository();
        $this->approvalTool = ApprovalTool::getApprovalTool();
        $this->userRepository = UserRepository::getUserRepository();
        $this->exportWord=new PstReportExportWord;
        $this->exportSingleWord=new PstSingleReportExportWord();
    }
    /**
     * 单例模式
     */
    static public function getPstTool()
    {
        if (self::$pstTool instanceof self) {
            return self::$pstTool;
        } else {
            return self::$pstTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone()
    { }

    //===============================评审通流程--模板--类型---相关==========================================================>
    /**
     * 添加评审通--流程模板信息
     */
    public function addProcessTemplate(array $data)
    {
        $user = auth('api')->user();
        $need_data = [];
        //对可见人数据进行处理
        if ($data['per'] != 'all') {
            $data['per']['staffId'] = FunctionTool::decrypt_id_array($data['per']['staffId']);
        }
        //组装数据
        $need_data['company_id'] = $user->current_company_id;
        $need_data['process_type_id'] = FunctionTool::decrypt_id($data['process_type_id']); //流程模板归属类型id
        $need_data['name'] = $data['name'];
        $need_data['process_template'] = json_encode(array_get($data, 'process_template', []));
        $need_data['per'] = json_encode($data['per']);
        $need_data['description'] = is_null($data['description']) ? '' : $data['description'];
        //存储审批模板信息
        PstRepository::addProcessTemplate($need_data);
        return json_encode(['status' => 'success', 'message' => '流程创建成功']);
    }
    /**
     * 删除指定的流程模板
     */
    public function deleteProcessTemplate(int $id)
    {
        PstRepository::deleteProcessTemplate($id);
        return json_encode(['status' => 'success', 'message' => '移除指定的模板']);
    }
    /**
     * 禁用 or 启用指定的流程模板
     */
    public function switchShowProcessTemplate(int $id)
    {
        $record = PstRepository::getProcessTemplateById($id);
        PstRepository::updateProcessTemplate($id, ['is_show' => $record->is_show == 1 ? 0 : 1]);
        return json_encode(['status' => 'success', 'message' => '操作成功']);
    }
    /**
     * 将指定流程模板移动到指定分类下
     * @param int $template_id:模板id
     * @param int $type_id:分类id
     * @return mixed
     */
    public function moveProcessTemplate(int $template_id, int $type_id)
    {
        PstRepository::updateProcessTemplate($template_id, ['process_type_id' => $type_id]);
        return json_encode(['status' => 'success', 'message' => '操作成功']);
    }
    /**
     * 获取某企业所有的评审通模板信息
     * @return mixed
     */
    public function getCompanyProcessTemplate()
    {
        $user = auth('api')->user();
        $data = PstRepository::getCompanyProcessTemplate($user->current_company_id, $user->id);
        return json_encode(['status' => 'success', 'data' => $data]);
    }
    /**
     * 通过id获取指定评审通--流程模板的详细信息
     */
    public function getProcessTemplateById(int $id)
    {
        $data = new PstProcessTemplateResource(PstRepository::getProcessTemplateById($id));
        $data = $data->toArray(1);

        return json_encode(['status' => 'success', 'data' => $data]);
    }
    /**
     * 更新评审通--流程模板的信息
     */
    public function updateProcessTemplate(array $data)
    {
        $id = FunctionTool::decrypt_id($data['id']);
        unset($data['id']);
        //对可见人数据进行处理
        if ($data['per'] != 'all') {
            $data['per']['staffId'] = FunctionTool::decrypt_id_array($data['per']['staffId']);
        }
        $data['per'] = json_encode($data['per']);
        $data['process_template'] = json_encode($data['process_template']);
        $data['process_type_id'] = FunctionTool::decrypt_id($data['process_type_id']);
        PstRepository::updateProcessTemplate($id, $data);
        return json_encode(['status' => 'success', 'message' => '变更成功']);
    }
    /**
     * 添加评审通模板类型
     * @param array $data :前端传递数据组
     */
    public function addProcessTemplateType(array $data)
    {
        $user = auth('api')->user();
        $data = [
            'company_id' => $user->current_company_id,
            'name' => $data['name'], //类型名称
        ];
        $record = PstRepository::addProcessTemplateType($data);
        return json_encode(['status' => 'success', 'message' => '流程类型添加成功', 'data' => ['id' => FunctionTool::encrypt_id($record->id)]]);
    }
    /**
     *重命名评申通流程模板类型名称
     */
    public function alterProcessTemplateTypeName(int $type_id, string $name)
    {
        $user = auth('api')->user();
        //检查名称是否已经存在
        if (PstRepository::checkProcessTemplateTypeExsit($user->current_company_id, $name)) {
            return json_encode(['status' => 'fail', 'message' => '类型已经存在']);
        }
        //更改类型名称
        PstRepository::updateProcessTemplateType($type_id, ['name' => $name]);
        return json_encode(['status' => 'success', 'message' => '更改成功']);
    }
    /**
     * 删除评审通流程模板类型
     * @param int $type_id:类型id
     */
    public function deleteProcessTemplateType(int $type_id)
    {
        //判断权限
        if (!true) {
            return json_encode(['status' => 'fail', 'message' => '权限不足']);
        }
        //先获取流程类型实例
        $type = PstRepository::getProcessTemplateTypeById($type_id);
        //判断该类型是否有流程模板记录
        if (count($type->processTemplates) != 0) {
            return json_encode(['status' => 'fail', '该流程类型下有模板信息,不能删除']);
        }
        //删除该流程类型
        $type->delete();
        return json_encode(['status' => 'success', 'message' => '删除成功']);
    }
    /**
     * 对流程模板类型进行排序
     * @param array $data
     * @return mixed
     */
    public function sortProcessTemplateType(array $data)
    {
        foreach ($data['sort_json'] as $k => $v) {
            PstRepository::updateProcessTemplateType(FunctionTool::decrypt_id($k), ['sequence' => $v]);
        }
        return ['status' => 'success', 'message' => '保存成功'];
    }
    /**
     * 按序获取企业所有的评审流程分类数据
     * @return mixed
     */
    public function getProcessTemplateType()
    {
        $user = auth('api')->user();
        $data = PstProcessTemplateTypeResource::collection(PstRepository::getProcessTemplateType($user->current_company_id));
        return json_encode(['status' => 'success', 'data' => $data]);
    }

    //===============================评审通模板--类型---相关==========================================================>
    /**
     * 添加评审通模板
     * @param array $data
     * @return mixed
     */
    public function addPstTemplate(array $data)
    {
        //对可见人数据进行处理
        if ($data['per'] != 'all') {
            $data['per']['staffId'] = FunctionTool::decrypt_id_array($data['per']['staffId']);
        }
        $user = auth('api')->user();
        //组装模板所需数据
        $data['name'] = $data['name'];
        $data['description'] = array_get($data, 'description', null); //描述
        $data['process_type'] = $data['need_approval'] ? $data['approval_method'] : '';
        $data['company_id'] = $user->current_company_id;
        $data['type_id'] = FunctionTool::decrypt_id($data['type_id']);
        $data['form_template'] = json_encode($data['form_template']); //表单数据
        $data['process_template'] = json_encode($data['process_template']); //人员流程信息
        $data['need_data'] = json_encode([]);
        $data['need_approval'] = $data['need_approval'] ? 1 : 0;
        $data['cc_users'] = json_encode(array_get($data, 'cc_users', []));
        $data['per'] = json_encode($data['per']);
        $data['users_info'] = json_encode($data['per']);

        //存储审批模板信息
        PstRepository::addPstTemplate($data);
        return json_encode(['status' => 'success', 'message' => '评审通模板创建成功']);
    }
    /**
     * 删除指定的模板
     */
    public function deletePstTemplate(int $id)
    {
        PstRepository::deletePstTemplate($id);
        return json_encode(['status' => 'success', 'message' => '移除指定的模板']);
    }
    /**
     * 禁用 or 启用指定的模板
     */
    public function switchShowPstTemplate(int $id)
    {
        $record = PstRepository::getPstTemplateById($id);
        PstRepository::updatePstTemplate($id, ['is_show' => $record->is_show == 1 ? 0 : 1]);
        return json_encode(['status' => 'success', 'message' => '操作成功']);
    }
    /**
     * 将指定模板移动到指定分类下
     * @param int $template_id
     * @param int $type_id
     * @return mixed
     */
    public function moveTemplate(int $template_id, int $type_id)
    {
        PstRepository::updatePstTemplate($template_id, ['type_id' => $type_id]);
        return json_encode(['status' => 'success', 'message' => '操作成功']);
    }
    /**
     * 获取某企业的所有评审通模板
     * @param int $company_id
     * @return mixed
     */
    public function getCompanyPstTemplate()
    {
        $user = auth('api')->user();
        $data = PstRepository::getCompanyPstTemplate($user->current_company_id, $user->id);
        return json_encode(['status' => 'success', 'data' => $data]);
    }
    /**
     * 通过id获取指定评审通模板的详细信息
     */
    public function getPstTemplateById(int $id)
    {
        $data = new PstTemplateResource(PstRepository::getPstTemplateById($id));
        $data = $data->toArray(1);
        return json_encode(['status' => 'success', 'data' => $data]);
    }
    /**
     * 更新评审通模板的信息
     */
    public function updatePstTemplate(array $data)
    {
        $id = FunctionTool::decrypt_id($data['id']);
        //对可见人数据进行处理
        if ($data['per'] != 'all') {
            $data['per']['staffId'] = FunctionTool::decrypt_id_array($data['per']['staffId']);
        }
        unset($data['id']);
        $data['name'] = $data['name'];
        $data['description'] = $data['description']; //描述
        $data['approval_method'] = $data['need_approval'] ? $data['approval_method'] : '';
        $data['type_id'] = FunctionTool::decrypt_id($data['type_id']);
        $data['form_template'] = json_encode($data['form_template']); //表单数据
        $data['process_template'] = json_encode($data['process_template']); //人员流程信息
        $data['form_values'] = json_encode($data['form_names']);
        $data['need_approval'] = $data['need_approval'] ? 1 : 0;
        $data['cc_users'] = json_encode($data['cc_users']);
        $data['per'] = json_encode($data['per']);
        $data['users_info'] = json_encode([]);
        unset($data['approval_method']);
        unset($data['form_names']); //这个值前端暂时错误
        PstRepository::updatePstTemplate($id, $data);
        return json_encode(['status' => 'success', 'message' => '信息变更成功']);
    }
    /**
     * 添加评审通模板类型
     * @param array $data :前端传递数据组
     */
    public function addPstTemplateType(array $data)
    {
        $user = auth('api')->user();
        $data = [
            'company_id' => $user->current_company_id,
            'name' => $data['name'], //类型名称
        ];
        $record = PstRepository::addPstTemplateType($data);
        return json_encode(['status' => 'success', 'message' => '类型添加成功', 'data' => ['id' => FunctionTool::encrypt_id($record->id)]]);
    }
    /**
     * 重命名模板类型名称
     * @param int $type_id:目标类型id
     * @param string $name:更新的名称
     */
    public function alterPstTemplateTypeName(int $type_id, string $name)
    {
        $user = auth('api')->user();
        //检查名称是否已经存在
        if (PstRepository::checkPstTemplateTypeExsit($user->current_company_id, $name)) {
            return json_encode(['status' => 'fail', 'message' => '类型已经存在']);
        }
        //更改类型名称
        PstRepository::updatePstTemplateType($type_id, ['name' => $name]);
        return json_encode(['status' => 'success', 'message' => '更改成功']);
    }
    /**
     * 删除评审通模板类型
     * @param int $type_id:类型id
     */
    public function deletePstTemplateType(int $type_id)
    {
        //判断权限
        if (!true) {
            return json_encode(['status' => 'fail', 'message' => '权限不足']);
        }
        //先获取流程类型实例
        $type = PstRepository::getPstTemplateTypeById($type_id);
        //判断该类型是否有流程模板记录
        if (count($type->pstTemplates) != 0) {
            return json_encode(['status' => 'fail', 'message' => '该类型下有模板信息,不能删除']);
        }
        //删除该流程类型
        $type->delete();
        return json_encode(['status' => 'success', '删除成功']);
    }
    /**
     * 对流程模板类型进行排序
     * @param array $data
     * @return mixed
     */
    public function sortPstTemplateType(array $data)
    {
        foreach ($data['sort_json'] as $k => $v) {
            PstRepository::updatePstTemplateType(FunctionTool::decrypt_id($k), ['sequence' => $v]);
        }
        return ['status' => 'success', 'message' => '保存成功'];
    }
    /**
     * 按序获取评审通分类信息
     * @param array $data
     * @return mixed
     */
    public function getPstTemplateType()
    {
        $user = auth('api')->user();
        $data = PstTemplateTypeResource::collection(PstRepository::getPstTemplateType($user->current_company_id));
        return json_encode(['status' => 'success', 'data' => $data]);
    }
    /**
     * 获取经典评审通模板
     * @param int $company_id:公司id
     */
    public function getClassicPstTemplate()
    {
        $data = PstRepository::getClassicPstTemplate();
        return json_encode(['status' => 'success', 'data' => $data]);
    }

    //===============================评审通基础表单数据相关==========================================================>
    /**
     * 表单基础数据的获取
     * 计算分类,工程分类,的列表类型--网站baisc数据
     * 送审业务负责科室,行为标签的列表类型--企业数据
     * @return mixed
     */
    public function getFormBasicData()
    {
        $user = auth('api')->user();
        //获取目标企业
        $company_id = $user->current_company_id;
        $data = [];
        //组装基础计算类型列表
        $data['count_type'] = json_decode($this->basicRepository->getBasicData(config('basic.c_pst_count_types'))->body);
        //组装基础工程分类列表
        $data['project_type'] = json_decode($this->basicRepository->getBasicData(config('basic.c_pst_project_types'))->body);

        //获取企业对应的评审通基础表单记录
        $basic_form_data = PstRepository::get_company_form_data($company_id);
        //组装基础送审业务负责科室数据
        $data['service_department'] = json_decode($basic_form_data->service_department);
        //组装基础行为标签数据
        $data['action_label'] = json_decode($basic_form_data->action_label);
        return json_encode(['status' => 'success', 'data' => $data]);
    }
    /**
     * 生成企业评审通表单基础数据
     * @return mixed
     */
    public function initCompanyFormBasicData(int $company_id)
    {
        //拉取基础的service_department json
        $service_department = $this->basicRepository->getBasicData(config('basic.c_pst_service_department'))->body;
        //拉取基础的action_type json
        $action_label = $this->basicRepository->getBasicData(config('basic.c_pst_action_label'))->body;
        //创建企业的评审通表单基础的数据记录
        PstRepository::add_or_update_form_data([
            'company_id' => $company_id, 'service_department' => $service_department,
            'action_label' => $action_label
        ]);
        //赋予公司初始评审通模板及模板分组
        PstTemplateType::where('company_id',0)->get()->map(function ($pst_template_type) use ($company_id){
            $type=$pst_template_type->toarray();
            $type['company_id']=$company_id;
            unset($type['id']);
            $type_id=DB::table('pst_template_type')->insertGetId($type);
            $pst_templates=[];
            $pstTemplates=$pst_template_type->pstTemplates->toarray();
            foreach ($pstTemplates as $pstTemplate){
                unset($pstTemplate['id']);
                $pstTemplate['type_id']=$type_id;
                $pstTemplate['company_id']=$company_id;
                $pst_templates[]=$pstTemplate;
            }
            DB::table('pst_template')->insert($pst_templates);
        });
    }
    /**
     *更新评审通企业基本表单数据
     */
    public function updateCompanyFormBasicData(array $data)
    {
        $user = auth('api')->user();
        $key = array_keys($data)[0];
        //获取目标企业
        $company_id = $user->current_company_id;
        PstRepository::update_company_form_data($company_id, [$key => json_encode($data[$key])]);
        return json_encode(['status' => 'success', 'message' => '数据变更成功']);
    }

    //===============================评审通相关==========================================================>
    /**
     * 创建一个评审通
     * @param \App\Interfaces\Request $request
     */
    public function createPst(array $data)
    {
        $files=$_FILES;
        $user = auth('api')->user();
        //获取当前操作用户的当前企业id
        $company_id = $user->current_company_id;
        //获取对应的company
        $company = Company::find($company_id);
        //计算企业云存储的剩余空间是否满足附件的大小&文件合法性校验
        if (!(count($files) == 0)) {
            $message = CompanyOssTool::ossSizeIsEnough($company_id, $files);
            if (count($message) != 0) {
                return json_encode(['status' => 'fail', 'message' => implode(',', $message)]);
            }
        }

        //验证有无权限
        if (!true) {
            return json_encode(['status' => 'fail', 'message' => '没有权限']);
        }
        //组装创建评审通的数据
        $pst_data = $this->makePstData($data, $user);
        //创建评审通记录
        DB::beginTransaction();
        try {
            //创建评审通
            $pst = PstRepository::addPst($pst_data);
            if(empty($pst)) {
                return json_encode(['status' => 'fail', 'message' => '评审通创建失败']);
            }
            //添加评审通操作记录
           $bool = PstRepository::addPstOperateRecord([
                'pst_id' => $pst->id,
                'company_id' => $pst->company_id,
                'type' => config('pst.operate_type.create_pst'),
                'operate_user_id' => $user->id,
                'info' => $user->name . ',发起评审'
            ]);
            if(empty($bool)){
                return json_encode(['status' => 'fail', 'message' => '评审通记录添加失败']);
            }
            //进行评审通关联操作
            if (array_get($data, 'associated_psts', false)) {
                $related_pst_ids = FunctionTool::decrypt_id_array(json_decode($data['associated_psts'],true));
                foreach ($related_pst_ids as $related_pst_id) {
                    DB::table('pst_self_related')->insert([
                        'target_pst_id' => $pst->id, //目标评审通id
                        'related_pst_id' => $related_pst_id, //所关联的评审通id
                    ]);
                }
            }
            $str = '';
            //附件处理
            {
                if (count($files) == 0) {
                    DB::commit();
                    $str = json_encode(['status' => 'success', 'message' => '添加成功']);
                } else {
                    $data = CompanyOssTool::uploadFile($files, [
                        'oss_path' => $company->oss->root_path . '评审通附件', //公告上传的云路径,其他模块与之类似
                        'model_id' => $pst->id, //关联模型的id
                        'model_type' => Pst::class, //关联模型的类名
                        'company_id' => $company_id, //所属公司的id
                        'uploader_id' => $user->id, //上传者的id
                    ]);
                    DB::commit();
                    if ($data === true) {
                        $str =  json_encode(['status' => 'success', 'message' => '创建成功']);
                    } else {
                        $str =  json_encode(['status' => 'fail', 'message' => '创建成功,但' . $data]);
                    }
                }
            }
            //是否需要审批的不同处理
            if ($pst->need_approval) {
                $state = $this->callApproval($pst, [
                    'pst_id' => $pst->id,
                    'callback_result' => null,
                    'state' => config('pst.approval_state.begin_start'), //评申通中的审批标识
                    'operate_id' => $user->id, //操作用户
                    'content' => ''
                ], $user);
            } else {
                //若不需要审批则直接进入审批通过的回调函数中
                $this->approvalCallBack([
                    'pst_id' => $pst->id,
                    'callback_result' => true,
                    'state' => config('pst.approval_state.begin_start'), //评申通中的审批标识
                    'operate_id' => $user->id, //操作用户
                    'content' => ''
                ]);
            }
            return $str;
        } catch (\Exception $e) {
            DB::rollBack();
            return json_encode(['status' => 'fail', 'message' => '系统创建出错啦']);
        }
    }
    /**
     * 组装发起评审通所需的数据
     * @param array $data:前端传递过来的数据
     * @param User $user:当前访问用户
     * @return array:返回组装好的数据
     */
    protected function makePstData(array $data, User $user): array
    {
        //抽取抄送人id数组
        $cc_user_ids = [];
        $current_handlers = [];
        foreach (json_decode($data['cc_users'],true)['checkedPersonnels']  as $v) {
            $cc_user_ids[] = $v['key'];
        }
        //抽取公司内部参与人id
        $inside_user_ids = FunctionTool::decrypt_id_array(json_decode($data['join_user_data'],true)['checkedIds']['organizational']);
        $company_partner_ids = FunctionTool::decrypt_id_array(json_decode($data['join_user_data'],true)['checkedIds']['partner']);
        $outside_user_ids = FunctionTool::decrypt_id_array(json_decode($data['join_user_data'],true)['checkedIds']['externalContact']);
        $need_approval=json_decode($data['need_approval']);
        if ($need_approval){
            $inside_user_ids =FunctionTool::decrypt_id_array(json_decode($data['process_template'],true)[0]['checkedInfo']['checkedKeys']);
            $current_handlers = [
                'inside_user_ids' => $inside_user_ids, //公司内部的参与人
            ];
        }else{
            $current_handlers = [
                'inside_user_ids' => $inside_user_ids, //公司内部的参与人
                'inside_receive_state' => $this->initInsideUserState($inside_user_ids), //公司内部参与人的接收状态
                'company_partner_ids' => $company_partner_ids, //合作伙伴的参与
                'outside_user_ids' => $outside_user_ids, //外部联系人的参与
            ];
        }

        return [
            'template_id' => FunctionTool::decrypt_id($data['template_id']),
            'publish_user_id' => $user->id, //发起人id
            'company_id' => $user->current_company_id, //所属企业的id
            'state' => $need_approval ? config('pst.state.wait_approval') : config('pst.state.under_way'), //评审状态
            'need_approval' => $need_approval ? 1 : 0, //相关是否需要审批标识
            'removed' => 0,
            'form_template' => $data['form_template'] == [] ? [] : $data['form_template'], //表单数据
            'form_values' => $data['form_values'], //所需要的数据 k-v对
            'process_template' => $data['process_template'] == [] ? [] : $data['process_template'], //审批流程人员信息
            'approval_method' => $data['approval_method'], //审批类型
            'current_handlers' => json_encode($current_handlers),
            'origin_data' => json_encode([]), //上一环传递过来的东西--暂定
            'join_user_data' => json_encode([
                'join_form_data' => $data['join_user_data'], //参与人表单选择信息
                'join_user_ids' => [
                    'inside_user_ids' => $inside_user_ids, //公司内部的参与人
                    'inside_receive_state' => $this->initInsideUserState($inside_user_ids), //公司内部参与人的接收状态
                    'company_partner_ids' => $company_partner_ids, //合作伙伴的参与
                    'outside_user_ids' => $outside_user_ids, //外部联系人的参与
                ]
            ]), //负责人信息--数据格式待定
            'duty_user_data' => json_encode([
                'duty_user_id' => FunctionTool::decrypt_id(json_decode($data['duty_user_data'],true)['key']),
                'duty_receive_state' => config('pst.state.wait_receive'),
                'duty_form_data' => $data['duty_user_data'],
            ]), //负责人数据,初始状态为待接收
            'transfer_duty_data' => null, //负责人转移数据
            'cc_user_data' => json_encode([
                'cc_users' => $data['cc_users'], //抄送人员源数据
                'cc_user_ids' => FunctionTool::decrypt_id_array($cc_user_ids), //目标抄送人员id数组
            ]), //抄送人员相关数据
        ];
    }
    /**
     * 更新指定的评审通
     */
    public function updatePst(array $data)
    { }
    /**
     * 移除一个评审通
     */
    public function removePst(array $data)
    { }
    /**
     * 按类型搜索与我相关的评审通--我发起的,我参与的,我负责的等状态的评审通数据
     * @param array $data:[now_page,page_size]
     */
    public function searchMyPstByType(array $data)
    {
        $now_page = $data['now_page'];
        $page_size = $data['page_size'];
        $type = $data['type'];
        $user = auth('api')->user();
        $company_id = $user->current_company_id;
        $data = PstRepository::searchMyPstByType($company_id, $type, $now_page, $page_size, $user->id);
        return json_encode([
            'status' => 'success',
            'page_count' => $data['page_count'],
            'page_size' => $page_size,
            'now_page' => $now_page,
            'all_count' => $data['count'],
            'data' => PstListResource::collection($data['data']),
        ]);
    }
    /**
     * 通过评审通流程状态查询某用户在某公司下可见的评审通记录(分页)
     * @param array $data:前端传递的request数组
     * @return mixed
     */
    public function searchPstByState(array $data)
    {
        $now_page = $data['now_page'];
        $page_size = $data['page_size'];
        $state_type = $data['state_type'];
        $user = auth('api')->user();
        $company_id = $user->current_company_id;
        //判断用户是否有高级权限-即该公司下的所有类型的评审通都可以看到
        $have_per = RoleAndPerTool::user_has_c_per($user->id, $company_id, ['c_super_manage_per'], 'any');
        $have_per = true; //暂定有相应的权限
        //分类型查询可见的数据
        $data = PstRepository::searchPstByState($state_type, $company_id, $user->id, $now_page, $page_size, $have_per);
        return json_encode([
            'status' => 'success',
            'page_count' => $data['page_count'],
            'page_size' => $page_size,
            'now_page' => $now_page,
            'all_count' => $data['count'],
            'data' => $data['data']===null?[]:PstListResource::collection($data['data']),
        ]);
    }
    /**
     * 查询某个评审通状态下的细分记录--联合查询--我参与的,我负责的。。。。。
     * @param array $data:前端传递的request数组
     * @return mixed
     */
    public function unionSearchPstByState(array $data)
    {
        $now_page = $data['now_page'];
        $page_size = $data['page_size'];
        $state_type = $data['state_type'];
        $user = auth('api')->user();
        $company_id = $user->current_company_id;
        //判断用户是否有高级权限-即该公司下的所有类型的评审通都可以看到
        $have_per = RoleAndPerTool::user_has_c_per($user->id, $company_id, [''], 'any');
        //分类型查询可见的数据
        $data = PstRepository::searchPstByState($state_type, $company_id, $user->id, $now_page, $page_size, $have_per, $data['search_type']);
        return json_encode([
            'status' => 'success',
            'page_count' => $data['page_count'],
            'page_size' => $page_size,
            'now_page' => $now_page,
            'all_count' => $data['count'],
            'data' => PstListResource::collection($data['data']),
        ]);
    }
    /**
     * 通过id 获取指定的评审通
     * @param array $data:所需要的数据包
     * @return mixed
     */
    public function getPstById(array $data)
    {
        $user = auth('api')->user();
        //取出评审通id
        $pst_id = FunctionTool::decrypt_id($data['id']);
        //取出对应记录
        $record = Pst::find($pst_id);
        $pst = PstRepository::getPstOperateData($pst_id); //拿到可操作数据的格式
        //返回评审通详情信息
        $data = new PstDetailResource($record);
        $data = $data->toArray(1);
        //加入按钮状态
        if ($pst->state == config('pst.state.archived')) { // 暂时这样处理
            $data['btn_status'] = [
                'btn_export' => true
            ];
        } else {
            $data['btn_status'] = self::btn_status($pst_id, auth('api')->id());
        }
        //加入单个评审通接收信息详情数据
        $data['detail_info'] = $this->getDetailRelationInfo($pst_id, auth('api')->id());
        //判断是否有高级权限
        $have_per = false;
        $data['identity_info'] = $this->makeUserIdentityInfo($user->id, $pst, $have_per);
        [
            'inside_receive_state' => $inside_receive_state,
            'join_pst_form_data' => $join_pst_form_data
        ] = $pst;
        $data['append_form'] = [];

        if(!empty(json_decode($inside_receive_state))){
            foreach (json_decode($inside_receive_state, true) as $k => $state) {
                if ($state == config('pst.state.finish')) {
                    $user_id =  (int)explode('_', $k)[1];
                    $data['append_form'][] = [
                        'user_name' => $this->userRepository->getUser($user_id)['name'],
                        'form_data' => json_decode($join_pst_form_data, true)['form_' . $user_id]['form_data'],
                    ];
                }
            }
        }else{

            if($join_pst_form_data!==null){
                $user_id = $pst['duty_user_id'];
                $data['append_form'][] = [
                    'user_name' => User::find($user_id)->name,
                    'form_data' => json_decode($join_pst_form_data, true)['form_' . $user_id]['form_data'],
                ];
            }
        }

        return json_encode([
            'status' => 'success',
            'data' => $data,
            'files' => FileResource::collection($record->files)
        ]);
    }
    /**
     * 组装用户在某个评审通中的身份信息
     * @param int $user_id:目标用户
     * @param Pst $v:目标评审通
     * @param bool $have_per:是否有相应的评审通高级权限
     * @return mixed
     */
    public function makeUserIdentityInfo(int $user_id, Pst $v, bool $have_per)
    {

        //需要返回的数组
        $data = [
            'user_id' => $user_id,
            'is_join' => false, //是参与人标识
            'is_duty' => false, //是负责人标识
            'is_publish' => false, //是发起人
            'is_cc' => false, //是抄送人
            'is_transfer_duty' => false, //是被转移负责人标识
            'is_transfer_join' => false, //是被转移参与人标识
        ];

        //获取参与人员的相关信息
        $join_user_data = json_decode($v->join_user_data, true); //参与人员json解析
        $transfer_join_data = json_decode($v->transfer_join_data, true); //转移参与人员json解析
        $duty_user_data = json_decode($v->duty_user_data, true); //负责人json解析
        $transfer_duty_data = json_decode($v->transfer_duty_data, true); //转移负责人json解析

        //过滤是否是负责人&&状态不为待接收,不为拒绝接收
        if (!is_null($duty_user_data)) {
            if (($duty_user_data['duty_user_id'] == $user_id) && ($duty_user_data['duty_receive_state'] != config('pst.state.wait_receive')) && ($duty_user_data['duty_receive_state'] != config('pst.state.refuse_receive'))
            ) {
                //加入该用户为负责人的标识
                $data['is_duty'] = true;
            }
        }
        //过滤是否是参与人--先判断是否是参与人，再判断该用户是否是被转移的参与人
        $inside_user_ids = json_decode($v->join_user_ids, true)['inside_user_ids']; //内部参与人id数组
        $inside_receive_state = json_decode($v->inside_receive_state, true); //内部参与人状态数组
        //查询是否是参与人员--接收状态不为待接收,拒绝接收
        if (
            !empty($inside_user_ids) && in_array($user_id, $inside_user_ids) && ($inside_receive_state['state_' . $user_id] != config('pst.state.wait_receive')) && ($inside_receive_state['state_' . $user_id] != config('pst.state.refuse_receive'))
        ) {
            //加入该用户为参与人的标识
            $data['is_join'] = true;
        }

        //判断是否需要抄送人的搜索条件
        $cc_user_ids = json_decode($v->cc_user_ids, true);
        if (!empty($cc_user_ids) && in_array($user_id, $cc_user_ids)) {
            //加入该用户为抄送人的标识
            $data['is_cc'] = true;
        }
        //判断是否需要发起人的搜索条件
        if (($v->publish_user_id == $user_id)) {
            //加入该用户为发起人的标识
            $data['is_publish'] = true;
        }
        //若有高级权限则也可以看到企业对应状态的记录
        if ($have_per) {
            if ($v->state == config('pst.state.wait_receive')) { }
        }
        return $data;
    }
    /**
     * 组装用户在某个评审通中具体各种人员的状态信息组----并提取接收,拒绝,打回,(参与人没有接收的时候可以进行移除) 按钮相应的状态
     * @param int $pst_id:目标评审通
     * @param int $user_id:目标用户
     * @return mixed
     */
    public function getDetailRelationInfo(int $pst_id, int $user_id)
    {
        //需要返回的数组的格式
        $data = [
            'pst' => [], //标识评审通自身
            'inside_user' => [
                config('pst.user_type.duty_user') => [], //负责人状态信息组
                config('pst.user_type.transfer_duty_user') => [], //转移负责人信息组
                config('pst.user_type.inside_join_user') => [], //n内部参与人状态信息组
                config('pst.user_type.transfer_join_user') => [], //转移参与人状态信息组
            ], //内部相关人员组
            'company_partner' => [], //合作伙伴状态信息组
            'outside_user' => [], //外部联系人状态信息组
            'current_user_info' => [] // 当前用户状态信息
        ];
        //获取目标人员
        $user = User::find($user_id);
        //拿到目标记录
        $pst = PstRepository::getPstOperateData($pst_id);
        //拿到内部参与人数组
        $inside_user_ids = $pst->inside_user_ids===null?[]:json_decode($pst->inside_user_ids, true);
        $inside_receive_state = json_decode($pst->inside_receive_state, true); //内部参与人接收状态
        $duty_user_id = json_decode($pst->duty_user_id); //拿到负责人id 被转移负责人id
        $transfer_duty_id = json_decode($pst->transfer_duty_id); //被转移负责人id

        //=================================平审通自身状态为 待接收 或者 拒绝接收时看不到详情信息================================>
        //若该评审通状态为待接收--则看不到相应的人员详情信息,及截断状态--接着需要判断目标人员是否有权看到对应的操作按钮


        $state1 = ($pst->state == config('pst.state.wait_receive')) || ($pst->state == config('pst.state.wait_approval'));
        if ($state1) {
            //压入默认数据
            $data['pst'] = [
                'type' => config('pst.user_type.pst'), //评审通自身标识
                'message' => '',
                'name' => '',
                'need_data' => [
                    'pst_id' => FunctionTool::encrypt_id($pst->id), //目标评审通id
                ], //需要的额外数据
                'state' => $pst->state,
                'btn_status' => [
                    'btn_receive' => false, //接收按钮
                    'btn_refuse_receive' => false, //拒绝按钮
                    'btn_back' => false, //打回按钮
                    'btn_finish' => false, //完成按钮(只针对内部参与人)
                ]
            ];
            //判断是否是外部联系人
            if (($pst->company_id == 0) && ($pst->outside_user_id != 0)) {
                //并标明类型为外部联系人
                $data['type'] = config('pst.user_type.outside_user');
                //再判断目标用户是否是外部联系人
                if ($pst->outside_user_id == $user->id) {
                    //压入外部联系人的name
                    $data['name'] = User::find($pst->outside_user_id)->name;
                    //表明可以看到接收按钮
                    $data['pst']['btn_status'] = [
                        'btn_receive' => true, //接收按钮
                        'btn_refuse_receive' => true, //拒绝按钮
                        'btn_back' => false, //打回按钮
                        'btn_finish' => false, //完成按钮(只针对内部参与人)
                    ];
                }
            } elseif (($pst->company_id != 0) && ($pst->last_pst_id != 0)) { //判断是否属于合作伙伴
                //并标明类型为合作伙伴
                $data['type'] = config('pst.user_type.company_partner');
                //压入合作伙伴的name
                $data['name'] = Company::find($pst->company_id)->name;
                //判断是否有评审通接收权
                if (RoleAndPerTool::user_has_c_per($user_id, $pst->company_id, [], 'any')) {
                    //表明可以看到接收按钮
                    $data['pst']['btn_status'] = [
                        'btn_receive' => true, //接收按钮
                        'btn_refuse_receive' => true, //拒绝按钮
                        'btn_back' => false, //打回按钮
                        'btn_finish' => false, //完成按钮(只针对内部参与人)
                    ];
                }
            } else {
                //判断是否有评审通接收权--这种情况好像不太可能出现(发起时就算是待接收的情况)
                if (RoleAndPerTool::user_has_c_per($user_id, $pst->company_id, [], 'any')) {
                    $data['type']='pst';
                    //表明可以看到接收按钮
                    $data['pst']['btn_status'] = [
                        'btn_receive' => true, //接收按钮
                        'btn_refuse_receive' => true, //拒绝按钮
                        'btn_back' => false, //打回按钮
                        'btn_finish' => false, //完成按钮(只针对内部参与人)
                    ];
                }
            }
            return $data; //截断逻辑
        }
        //若该评审通状态为拒绝--则看不到相应的人员详情信息,及截断状态--接着需要判断目标人员是否有权看到对应的操作按钮
        $state2 = ($pst->state == config('pst.state.refuse_receive'));
        if ($state2) {
            //压入默认数据
            $data['pst'] = [
                'type' => config('pst.user_type.pst'), //评审通自身标识
                'message' => '评审通状态:' . config('pst.state.refuse_receive'),
                'need_data' => [], //需要的额外数据
                'state' => json_decode($pst->state),
                'btn_status' => [
                    'btn_receive' => false, //接收按钮
                    'btn_refuse_receive' => false, //拒绝按钮
                    'btn_back' => false, //打回按钮
                    'btn_finish' => false, //完成按钮(只针对内部参与人)
                ]
            ];
            return $data; //截断逻辑
        }

        //=================================评审通属于正常流程的话抽取相应关系人员的状态数据================================>
        //抽取负责人信息
        {
            //若为被转移负责人则压入内部--负责人数据'
            $duty_receive_state = $pst->duty_receive_state; //负责人状态
            //判断是否显示接收,拒绝的按钮
            $show_receive = ($duty_receive_state == config('pst.state.wait_receive')) && ($pst->state != config('pst.state.wait_receive')) && ($pst->state != config('pst.state.wait_approval')) && ($pst->state != config('pst.state.cancled'));
            $data['inside_user'][config('pst.user_type.duty_user')] = [
                'type' => config('pst.user_type.duty_user'), //内部负责人标识
                'message' => '',
                'need_data' => [
                    'pst_id' => FunctionTool::encrypt_id($pst->id), //目标评审通id
                    'target_id' => $duty_user_id==null?null:FunctionTool::encrypt_id($duty_user_id), //负责人id
                ], //需要的额外数据
                'state' => $duty_receive_state,
                'name' => $duty_user_id===null?null:User::find($duty_user_id)->name,
                'is_my' => $user->id == $duty_user_id, //判断是否是负责人
                'btn_status' => [
                    'btn_receive' => $show_receive && ($user->id == $duty_user_id) ? true : false, //接收按钮
                    'btn_refuse_receive' => $show_receive && ($user->id == $duty_user_id) ? true : false, //拒绝按钮
                    'btn_back' => false, //打回按钮
                    'btn_finish' => false, //完成按钮(只针对内部参与人)
                ]
            ];
            if ($user->id == $duty_user_id) {
                $data['current_user_info'] = [
                    'identity' => ['duty_user'],
                    'state' => $duty_receive_state
                ];
            }
        }

        //判断用户是否是被转移负责人--只有自己是被转移的负责人才会看到
        if (($user->id == $transfer_duty_id)) {
            //若为负责人则压入内部--负责人数据'
            $transfer_duty_receive_state = json_decode($pst->transfer_duty_receive_state); //被转移负责人状态
            //判断是否显示接收,拒绝的按钮
            $show_receive = $transfer_duty_receive_state == config('pst.state.wait_receive') && ($pst->state != config('pst.state.wait_receive')) && ($pst->state != config('pst.state.wait_approval')) && ($pst->state != config('pst.state.cancled'));
            $data['inside_user'][config('pst.user_type.transfer_duty_user')] = [
                'type' => config('pst.user_type.transfer_duty_user'), //转移负责人标识
                'message' => '',
                'need_data' => [
                    'pst_id' => FunctionTool::encrypt_id($pst->id), //目标评审通id
                    'target_id' => FunctionTool::encrypt_id($transfer_duty_id), //所需数据为被转移人员的id
                ], //需要的额外数据
                'state' => $transfer_duty_receive_state,
                'name' => User::find($transfer_duty_id)->name,
                'is_my' => true,
                'btn_status' => [
                    'btn_receive' => $show_receive ? true : false, //接收按钮
                    'btn_refuse_receive' => $show_receive ? true : false, //拒绝按钮
                    'btn_back' => false, //打回按钮
                    'btn_finish' => false, //完成按钮(只针对内部参与人)
                ]
            ];
        }
        //抽取内部参与人表单数据
        $join_pst_form_data = json_decode($pst->join_pst_form_data, true);
        //抽取企业内部参与人信息(数组循环)
        {
            //迭代内部参与人id
            foreach ($inside_user_ids as $id) {
                //取出参与人意见
                $opinion = $join_pst_form_data && key_exists('form_' . $id, $join_pst_form_data) ? $join_pst_form_data['form_' . $id]['opinion'] : null;
                //取出参与人表单
                $form_data = $join_pst_form_data && key_exists('form_' . $id, $join_pst_form_data) ? $join_pst_form_data['form_' . $id]['form_data'] : null;
                //判断是否显示接收/拒绝按钮
                $show_receive = $inside_receive_state['state_' . $id] == config('pst.state.wait_receive') && ($pst->state != config('pst.state.wait_receive')) && ($pst->state != config('pst.state.wait_approval')) && ($pst->state != config('pst.state.cancled'));
                //判断是否显示打回按钮--参与人已完成审阅人员才能看到打回
                $show_back =
                    $inside_receive_state['state_' . $id] == config('pst.state.wait_approval') &&
                    $user->id == $duty_user_id;
                //判断是否显示参与人接收的按钮
                //获取目标内部参与人的接收状态
                // dd( $inside_receive_state );
                $user_state = $inside_receive_state['state_' . $id];
                //判断内部参与人的接收状态
                $show_finish = false;
                if (
                    ($user_state != config('pst.state.wait_receive')) && ($user_state != config('pst.state.refuse_receive')) && ($user_state != config('pst.state.wait_approval')) && ($user_state != config('pst.state.finish'))
                ) {
                    $show_finish = true;
                }
                //若目标用户id等于迭代的id,则判断相应的按钮状态,否则直接压入默认状态
                if ($user->id == $id) {
                    //压入参与人相关状态信息
                    $data['inside_user'][config('pst.user_type.inside_join_user')][] = [
                        'type' => config('pst.user_type.inside_join_user'), //内部参与人标识
                        'message' => '',
                        'need_data' => [
                            'pst_id' => FunctionTool::encrypt_id($pst->id), //目标评审通id
                            'target_id' => FunctionTool::encrypt_id($user->id), //我的id
                        ],
                        'opinion' => $opinion,
                        'form_data' => $form_data,
                        'state' => $inside_receive_state['state_' . $id],
                        'name' => User::find($id)->name,
                        'is_my' => true, //标记该用户是当前用户
                        'btn_status' => [
                            'btn_receive' => $show_receive ? true : false, //接收按钮
                            'btn_refuse_receive' => $show_receive ? true : false, //拒绝按钮
                            'btn_back' => $show_back ? true : false, //打回按钮
                            'btn_finish' => $show_finish ? true : false, //参与人完成按钮
                            'btn_agree' => $show_back ? true : false, //参与人信息审核通过按钮
                        ]
                    ];

                    $data['current_user_info']['identity'][] = 'join_user';
                    $data['current_user_info']['state'] = $inside_receive_state['state_' . $id];
                    $data['current_user_info']['append_form'] = $form_data;
                    $data['current_user_info']['opinion'] = $opinion;
                } else {
                    //压入参与人相关状态信息
                    $data['inside_user'][config('pst.user_type.inside_join_user')][] = [
                        'type' => config('pst.user_type.inside_join_user'), //内部参与人标识
                        'message' => '',
                        'need_data' => [
                            'pst_id' => FunctionTool::encrypt_id($pst->id), //目标评审通id
                            'target_id' => FunctionTool::encrypt_id($id), //参与人id
                        ],
                        'opinion' => $opinion,
                        'form_data' => $form_data,
                        'state' => $inside_receive_state['state_' . $id],
                        'name' => User::find($id)->name,
                        'is_my' => false,
                        'btn_status' => [
                            'btn_receive' => false, //接收按钮
                            'btn_refuse_receive' => false, //拒绝按钮
                            'btn_back' => $show_back ? true : false, //打回按钮
                            'btn_finish' => false, //参与人完成按钮
                            'btn_agree' => $show_back ? true : false, //参与人信息审核通过按钮
                        ]
                    ];
                }
            }
        }
        //获取被转移参与人员的数据信息
        $transfer_join_data = json_decode($pst->transfer_join_data, true);

        //循环比对查找当前用户是否是被转移参与人--拉取与我的被转移参与人信息
        if (!empty($transfer_join_data)) {
            foreach ($transfer_join_data as $key => $data) {
                //若当前用户id在参与人转移数组中,且接收状态为待接收
                if (($data['transfer_user_id'] == $user->id)) {
                    //被转移参与人只需要判断是否需要展示接收--打回按钮默认不展示
                    $show_receive = $data[$key]['receive_state'] == config('pst.state.wait_receive');
                    //压入被转移参与人信息
                    $data[] = [
                        'type' => config('pst.user_type.transfer_join_user'), //内部参与人标识
                        'message' => '',
                        'need_data' => [
                            'pst_id' => FunctionTool::encrypt_id($pst->id), //目标评审通id
                            'origin_user_id' => $key, //转移参与人的id
                            'target_id' => $data['transfer_user_id'], //被转移目标人员的id
                        ],
                        'origin_name' => User::find($key)->name,
                        'name' => User::find($data['transfer_user_id'])->name,
                        'is_my' => true,
                        'state' => $data[$key]['receive_state'], //被转移人接收状态
                        'btn_status' => [
                            'btn_receive' => $show_receive ? true : false, //接收按钮
                            'btn_refuse_receive' => $show_receive ? true : false, //拒绝按钮
                            'btn_back' => false, //打回按钮
                            'btn_finish' => $show_finish ? true : false, //参与人完成按钮
                        ]
                    ];
                }
            }
        }
        //循环拉取合作伙伴的状态信息
        $company_partner_ids = json_decode($pst->company_partner_ids, true);
        foreach ($company_partner_ids as $company_id) {
            //压入合作伙伴状态信息
            //先获取分发出去的合作伙伴的评审通
            $target_pst = Pst::where([
                ['last_pst_id', '=', $pst->id],
                ['company_id', '=', $company_id],
            ])->first();
            if (!empty($target_pst)) { //合作伙伴只需判断是否需要打回按钮的展示,接收/拒绝不需要考虑
                $show_back = $target_pst->state == config('pst.state.finish');
                $data[config('pst.user_type.company_partner')][] = [
                    'type' => config('pst.user_type.company_partner'), //内部参与人标识
                    'message' => '',
                    'need_data' => [
                        //合作伙伴不需要额外的数据
                    ],
                    'state' => $target_pst->state, //合作伙伴的接收状态
                    'name' => Company::find($company_id)->name,
                    'btn_status' => [
                        'btn_receive' => false, //接收按钮
                        'btn_refuse_receive' => false, //拒绝按钮
                        'btn_back' => $show_back ? true : false, //打回按钮
                        'btn_finish' => false,
                    ]
                ];
            }
        }
        //循环拉取外部联系人的状态信息
        $outside_user_ids = json_decode($pst->outside_user_ids, true);

        if (!empty($outside_user_ids)) {
            foreach ($outside_user_ids as $user_id) {
                //压入合作伙伴状态信息
                //先获取分发出去的合作伙伴的评审通
                $target_pst = Pst::where([
                    ['last_pst_id', '=', $pst->id],
                    ['outside_user_id', '=', $user_id],
                ])->first();
                if (!empty($target_pst)) { //合作伙伴只需判断是否需要打回按钮的展示,接收/拒绝不需要考虑
                    $show_back = $target_pst->state == config('pst.state.finish');
                    $data[config('pst.user_type.outside_user')][] = [
                        'type' => config('pst.user_type.outside_user'), //内部参与人标识
                        'message' => '',
                        'need_data' => [
                            //外部联系人不需要额外的数据
                        ],
                        'state' => $target_pst->state, //合作伙伴的接收状态
                        'name' => User::find($user_id)->name,
                        'btn_status' => [
                            'btn_receive' => false, //接收按钮
                            'btn_refuse_receive' => false, //拒绝按钮
                            'btn_back' => $show_back ? true : false, //打回按钮
                            'btn_finish' => false,
                        ]
                    ];
                }
            }
        }
        return $data;
    }
    //===============================评审通附件相关==========================================================>
    /**
     * 通过id 获取指定的评审通的资料清单
     * @param array $data:所需要的数据包
     * @return mixed
     */
    public function getPstFiles(array $data)
    {
        $user = auth('api')->user();
        //取出评审通id
        $pst_id = FunctionTool::decrypt_id($data['id']);
        //取出对应记录
        $record = Pst::find($pst_id);
        return json_encode([
            'status' => 'success',
            'pst_id' => FunctionTool::encrypt_id($pst_id),
            'files' => FileResource::collection($record->files)
        ]);
    }
    /**
     * 更新指定评审通附件
     * @param array $data:所需要的数据包
     * @return mixed
     */
    public function updatePstFiles(array $data)
    {
        $files=$_FILES;
        //拿到指定评审通id
        $pst_id = FunctionTool::decrypt_id($data['pst_id']);
        //获取指定评审通记录
        $pst = Pst::select(['id', 'company_id'])->find($pst_id);
        //获取该记录对应的company_id
        $company_id = $pst->compoany_id;
        //对应的公司
        $company = Company::find($company_id);
        //获取操作用户
        $user = auth('api')->user();
        //附件处理
        {
            if (count($files) == 0) {
                DB::commit();
                return json_encode(['status' => 'success', 'message' => '添加成功']);
            } else {
                $data = CompanyOssTool::uploadFile($files, [
                    'oss_path' => $company->oss->root_path . '评审通附件', //公告上传的云路径,其他模块与之类似
                    'model_id' => $pst->id, //关联模型的id
                    'model_type' => Pst::class, //关联模型的类名
                    'company_id' => $company_id, //所属公司的id
                    'uploader_id' => $user->id, //上传者的id
                ]);
                DB::commit();
                if ($data === true) {
                    return json_encode(['status' => 'success', 'message' => '创建成功']);
                } else {
                    return json_encode(['status' => 'fail', 'message' => '创建成功,但' . $data]);
                }
            }
        }
    }
    /**
     * 传递评审通附件至下级企业
     * @param OssFile $files:需要复制的OssFile文件记录组
     * @param int $company_id:目标企业id
     * @param string $target_directory:需要复制到的目标存储路径
     * @param array $data:进行文件模型多态关联所需要的数据
     * @return mixed|void
     */
    public static function copyFileToLastCompany($files, int $company_id, string $target_directory, array $data)
    {
        $company = Company::find($company_id);
        foreach ($files as $file) {
            //截取文件的扩展名--在$matches[1]中
            preg_match(config('regex.file_extension'), $file->name, $matches);
            //生成附件转移路径
            $to_path = $company->oss->root_path . $target_directory . '/' . FunctionTool::encrypt_id($file->id) . str_random(8) . '.' . $matches[1];
            //复制文件
            Storage::copy($file->oss_path, $to_path);
            //更新企业oss空间信息
            CompanyOssTool::updateNowSize($company_id, $file->size, 'add');
            //文件oss路径映射关系
            $file = OssFile::create([
                'name' => $file->name,
                'company_id' => $company_id,
                'uploader_id' => 0, //因为是从外部企业传递过来的暂定为0
                'size' => $file->size,
                'oss_path' => $to_path,
            ]);
            //文件与model多态关系--$data['model_id']==null时表示不进行多态文件关联
            if ($data['model_id'] != null) {
                DB::table('model_has_file')->insert(['model_id' => $data['model_id'], 'model_type' => $data['model_type'], 'file_id' => $file->id]);
            }
        }
    }
    /**
     * 获取目标评审通的下级所有已完成的附件--以分组的形式
     * @param array $need_data:所需要的数据
     *               -- pst_id:目标评审通id
     *               -- pst_id:目标评审通id
     */
    public  function getPstChildrenFiles(array $need_data)
    {
        //需要返回的数据基础格式i
        $data = [
            'company_internal'=>[],//公司内部
            'company_partner' => [], //合作伙伴的附件存放数组
            'outside_user' => [],  //外部联系人附件存放数组
        ];
        //拿到目标评审通id--默认加密
        $pst_id = FunctionTool::decrypt_id($need_data['pst_id']);
        $data['company_internal'][]=[
            'name' => '公司内部数据',
            'form_data' => FileResource::collection(Pst::find($pst_id)->files)->toArray(1),
        ];
        //获取所有下级子评审通记录--只加载id避免资源浪费
        $psts = PstRepository::getChildren($pst_id, 'simple');
        //开始循环抽取下级子类的附件信息--并分类型
        foreach ($psts as $pst) {
            //判断是合作伙伴 or 外部联系人
            if ($pst->company_id != 0) {
                //若是合作伙伴--压入附件数据
                $data['company_partner'][] = [
                    'name' => Company::find($pst->company_id)->name,
                    'data' => FileResource::collection($pst->files)->toArray(1),
                ];
            } else {
                //若是外部联系人-压入附件数据
                $data['outside_user'][] = [
                    'name' => User::find($pst->outside_user_id)->name,
                    'data' => FileResource::collection($pst->files)->toArray(1),
                ];
            }
        }
        return $data;
    }

    //===============================评审通操作记录相关==========================================================>
    /**
     * 获取评审通操作记录
     * @param int $pst_id
     * @return mixed
     */
    public  function getPstOperateRecord(array $data)
    {
        $pst_id = FunctionTool::decrypt_id($data['pst_id']);
        $now_page = array_get($data, 'now_page', 1); //当前页
        $page_size = array_get($data, 'page_size', 10);
        $data = PstRepository::getPstOperateRecord($pst_id, $now_page, $page_size);
        return json_encode([
            'status' => 'success',
            'page_count' => $data['page_count'],
            'page_size' => $page_size,
            'now_page' => $now_page,
            'all_count' => $data['count'],
            'data' => PstRecordResource::collection($data['data']),
        ]);
    }

    /**
     * 获取当前处理人信息
     * @param array $data
     */
    public function getPstCurrentHandlers(array $data){

//        $pst_id = $data['pst_id'];
        $pst_id = FunctionTool::decrypt_id($data['pst_id']);
        $pst = PstRepository::getPstOperateData($pst_id);

        $inside_user_ids = json_decode($pst->current_handlers,true)['inside_user_ids'];//待处理人内部参与人
        if(!$pst->need_approval){
            $company_partner_ids = json_decode($pst->current_handlers,true)['company_partner_ids'];//待处理人合作伙伴
            $outside_user_ids = json_decode($pst->current_handlers,true)['outside_user_ids'];//待处理人外部联系人
        }

        $inside_users = [];
        $company_partner = [];
        $outside_user = [];
        $user_data = [];


        //是否需要审批

        if($pst->need_approval){
            //内部参与人
            foreach($inside_user_ids as $value){
                $user=User::find($value);
                $avatar_path=$user->oss->root_path.'avatar/';
                $avatar=Storage::disk('oss')->allFiles($avatar_path);
                if($user){
                    $inside_users[] = ['name'=>$user->name,'id'=>FunctionTool::encrypt_id($user->id),'avator'=>(count($avatar)===0?'没有可用头像':'https://gzts.oss-cn-beijing.aliyuncs.com/'.$avatar[0])];
                }

            }
            $user_data[] = ['user_type'=>config('pst.user_type.inside_join_user'),'user_info'=>$inside_users];
            $handlers_data = ['type'=>'处理中','user_data'=>$user_data];
        }else{
            //内部参与人
            foreach($inside_user_ids as $value){
                $user=User::find($value);
                $avatar_path=$user->oss->root_path.'avatar/';
                $avatar=Storage::disk('oss')->allFiles($avatar_path);
                if($user){
                    $inside_users[] = ['name'=>$user->name,'id'=>FunctionTool::encrypt_id($user->id),'avator'=>(count($avatar)===0?'没有可用头像':'https://gzts.oss-cn-beijing.aliyuncs.com/'.$avatar[0])];
                }
            }
            $user_data[] = ['user_type'=>config('pst.user_type.inside_join_user'),'user_info'=>$inside_users];
            //合作伙伴
            foreach($company_partner_ids as $company){
                $userids = RoleAndPerTool::get_company_target_per_users($company,['c_pst_recive_per']);
                foreach ($userids as $userid){
                    $user=User::find($userid);
                    $avatar_path=$user->oss->root_path.'avatar/';
                    $avatar=Storage::disk('oss')->allFiles($avatar_path);
                    $company_partner[] = ['name'=>$user->name,'id'=>FunctionTool::encrypt_id($user->id),'avator'=>(count($avatar)===0?'没有可用头像':'https://gzts.oss-cn-beijing.aliyuncs.com/'.$avatar[0])];

                }
//                $user=User::find($company);
//                $avatar_path=$user->oss->root_path.'avatar/';
//                $avatar=Storage::disk('oss')->allFiles($avatar_path);
//                if($user){
//                    $company_partner[] = ['name'=>$user->name,'id'=>FunctionTool::encrypt_id($user->id),'avator'=>(count($avatar)===0?'没有可用头像':'https://gzts.oss-cn-beijing.aliyuncs.com/'.$avatar[0])];
//                }

            }
            $user_data[] = ['user_type'=>config('pst.user_type.company_partner'),'user_info'=>$company_partner];
            //外部联系人
            foreach($outside_user_ids as $outside_user_id){
                $user=User::find($outside_user_id);
                $avatar_path=$user->oss->root_path.'avatar/';
                $avatar=Storage::disk('oss')->allFiles($avatar_path);
                if($user){
                    $outside_user[] = ['name'=>$user->name,'id'=>FunctionTool::encrypt_id($user->id),'avator'=>(count($avatar)===0?'没有可用头像':'https://gzts.oss-cn-beijing.aliyuncs.com/'.$avatar[0])];
                }


            }
            $user_data[] = ['user_type'=>config('pst.user_type.outside_user'),'user_info'=>$outside_user];
            $handlers_data = ['type'=>'处理中','user_data'=>$user_data];
        }
        return json_encode($handlers_data);

    }

    /**
     * 更新当前处理人
     * @param array $data
     */
    public function updatePstCurrentHandler(array $data){
        $pst = PstRepository::getPstOperateData($data['pst_id']);
        $inside_user_ids = json_decode($pst->inside_user_ids,true);//内部联系人

        $inside_receive_state = json_decode($pst->inside_receive_state,true);//内部联系人状态
        $company_partner_ids = json_decode($pst->company_partner_ids,true); //合作伙伴
        $outside_user_ids = json_decode($pst->outside_user_ids,true);  //外部联系人
        $duty_user_id = json_decode($pst->duty_user_id,true);//负责人
        $duty_receive_state = json_decode($pst->duty_receive_state,true);//负责人状态
        $current_handlers = [];
        //是否需要审核
        if($pst->need_approval){
            foreach($inside_user_ids as $k=>$value){
                //获取内部参与人接收状态
                $user_state = $inside_receive_state['state_'.$value];
                //判断内部参与人状态是否完成
                if($user_state == config('pst.state.finish')){
                    unset($inside_user_ids[$k]);

                }
            }
            $current_handlers['inside_user_ids'] = array_values($inside_user_ids);
//            return json_encode($current_handlers);
        }else{
            //若没有内部参与人 负责人为当前处理人
            if(count($inside_user_ids) == 0){
                $current_handlers['inside_user_ids'][] = $duty_user_id;
                $current_handlers['inside_receive_state']['state_'.$duty_user_id] = $duty_receive_state;

            }else{
                foreach($inside_user_ids as $k=>$value){
                    $user_state = $inside_receive_state['state_'.$value];
                    if($user_state == config('pst.state.finish')){
                        unset($inside_receive_state['state_'.$value]);
                        unset($inside_user_ids[$k]);

                    }
                }
                $current_handlers['inside_user_ids'] = array_values($inside_user_ids);
                $current_handlers['inside_receive_state'] = $inside_receive_state;
            }
            //获取该评审通子类
            $psts = PstRepository::getChildren($data['pst_id'], 'simple');
            //判断是否存在子类
            if(!empty($psts)){
                foreach($psts as $pst){
                    //判断是否是合作伙伴
                    if($pst->company_id != 0){
                        foreach ($company_partner_ids as $k=>$company){
                            //获取合作伙伴可接收人
//                            $userids = RoleAndPerTool::get_company_target_per_users($company,['c_pst_recive_per']);
//                            foreach ($userids as $users){
//                                if($users == $pst->outside_user_id && $pst->state == config('pst.state.finish')){
//                                    unset($company_partner_ids[$k]);
//                                }
//                            }
                            //判断合作伙伴是否完成
                            if($pst->company_id == $company && $pst->state == config('pst.state.finish')){
                                unset($company_partner_ids[$k]);
                            }
                        }
                    }else{
                        //外部联系人处理
                        foreach($outside_user_ids as $k=>$outside_user_id){
                            //判断外部联系人是否完成
                            if($pst->outside_user_id == $outside_user_id && $pst->state == config('pst.state.finish')){
                                unset($outside_user_ids[$k]);
                            }
                        }
                    }

                }
                $current_handlers['company_partner_ids'] = array_values($company_partner_ids);
                $current_handlers['outside_user_ids'] = array_values($outside_user_ids);

            }else{
                //子类不存在
                $current_handlers['company_partner_ids'] = $company_partner_ids;
                $current_handlers['outside_user_ids'] = $outside_user_ids;
            }
//            return json_encode($current_handlers);
        }
        //更新评审通当前处理人信息
        return PstRepository::updatePst($data['pst_id'],[
            'current_handlers'=>json_encode($current_handlers)
        ]);

    }

    //===============================评审通操作相关==========================================================>
    /**
     * 评审通接收
     * @param Request $request:需要将单个评审通数据全部发送过来
     * @return mixed
     */
    public static function receive(array $data)
    {
        //判断到底是接收什么--根据前端传递的type值进行处理--同 getDetailRelationInfo() 方法压入的数据相关
        $user = auth('api')->user();

        $pst_id = FunctionTool::decrypt_id($data['need_data']['pst_id']);
        //拿到目标记录
        $pst = PstRepository::getPstOperateData($pst_id);
        //拿到内部参与人数组
        $inside_user_ids = json_decode($pst->inside_user_ids, true);
        $inside_receive_state = json_decode($pst->inside_receive_state, true); //内部参与人接收状态
        $duty_user_id = json_decode($pst->duty_user_id); //拿到负责人id 被转移负责人id
        $transfer_duty_id = json_decode($pst->transfer_duty_id); //被转移负责人id

        //拿到接收的类型标识
        $type = $data['type'];
        //分发接收处理请求
        switch ($type) {
                //代表评审通自身接收(源)--好像不太可能出现这种情况
            case config('pst.user_type.pst'):
                PstRepository::updatePst($pst_id, ['state' => '评审中']);
                return json_encode(['status' => 'success', 'message' => '接收成功']);
                break;
                //代表评审通自身接收(合作伙伴)
            case config('pst.user_type.company_partner'):
                //状态改为待指派
                PstRepository::updatePst($pst_id, ['state' => config('pst.state.wait_appoint')]);
                //添加评审通操作记录
                PstRepository::addPstOperateRecord([
                    'pst_id' => $pst_id,
                    'company_id' => $pst->company_id,
                    'type' => config('pst.operate_type.receive'),
                    'operate_user_id' => $user->id,
                    'info' => $user->name . ',接收了评审',
                ]);
                //追加上级评审通相应的记录
                PstRepository::addPstOperateRecord([
                    'pst_id' => $pst->last_pst_id,
                    'company_id' => $pst->company_id,
                    'type' => config('pst.operate_type.receive'),
                    'operate_user_id' => $user->id,
                    'info' => '合作伙伴:' . Company::find($pst->company_id)->name . ',接收了评审',
                ]);
                //告诉上级负责人---xxx合作伙伴已经接收
                $single_data = DynamicTool::getSingleListData(
                    Pst::class,
                    1,
                    'company_id',
                    $pst->company_id,
                    '评审通:合作伙伴 ' . Company::find($pst->company_id)->name . ',接收了你发起的评审通',
                    '评审通提醒',
                    $pst->created_at
                );
                NotifyTool::publishNotify([$pst->duty_user_id], $pst->last_pst_id, Pst::find($pst->last_pst_id), config('pst.default_notification_way'), $single_data, []);
                return json_encode(['status' => 'success', 'message' => '接收成功']);
                break;
                //代表评审通自身接收(外部联系人)
            case config('pst.user_type.outside_user'):
                //状态改为已接收
                PstRepository::updatePst($pst_id, ['state' => config('pst.state.under_way')]);
                //添加评审通操作记录
                PstRepository::addPstOperateRecord([
                    'pst_id' => $pst_id,
                    'company_id' => $pst->company_id,
                    'type' => config('pst.operate_type.receive'),
                    'operate_user_id' => $user->id,
                    'info' => $user->name . ',接收了评审',
                ]);
                //追加上级评审通相应的记录
                PstRepository::addPstOperateRecord([
                    'pst_id' => $pst->last_pst_id,
                    'company_id' => $pst->company_id,
                    'type' => config('pst.operate_type.receive'),
                    'operate_user_id' => $user->id,
                    'info' => '外部联系人:' . $user->name . ',接收了评审',
                ]);
                //告诉上级负责人---xxx已经接收
                $single_data = DynamicTool::getSingleListData(
                    Pst::class,
                    1,
                    'company_id',
                    $data['company_id'],
                    '评审通:外部联系人 ' . User::find($pst->outer_sider_id)->name . ',接收了你发起的评审通',
                    '评审通提醒',
                    $pst->created_at
                );
                NotifyTool::publishNotify([$pst->duty_user_id], $pst->last_pst_id, Pst::find($pst->last_pst_id), config('pst.default_notification_way'), $single_data, []);
                return json_encode(['status' => 'success', 'message' => '接收成功']);
                break;
                //负责人接收
            case config('pst.user_type.duty_user'):
                //取出负责人id
                $duty_user_id = FunctionTool::decrypt_id($data['need_data']['target_id']);
                PstRepository::updatePst($pst_id, [
                    'duty_user_data->duty_receive_state' => config('pst.state.received'), //负责人接收状态变为已接收
                ]);
                //追加评审通相应的记录
                PstRepository::addPstOperateRecord([
                    'pst_id' => $pst->id,
                    'company_id' => $pst->company_id,
                    'type' => config('pst.operate_type.receive_duty'),
                    'operate_user_id' => $user->id,
                    'info' => $user->name . ',接收了负责人',
                ]);
                //通知参与人,抄送人
                //参与人的处理--通知
                self::handleJoinUser($pst);
                //抄送人的处理--通知
                self::handleCcUser($pst);
                return json_encode(['status' => 'success', 'message' => '负责人接收成功']);
                break;
                //转移负责人接收
            case config('pst.user_type.transfer_duty_user'):
                //将transfer_duty_data字段中的被转移负责人信息更新到duty_user_data字段中,同时将transfer_duty_data字段设为null
                PstRepository::updatePst($pst_id, [
                    'duty_user_data->duty_receive_state' => config('pst.duty_receive_state.received'), //负责人接收状态变为已接收
                    'duty_user_data->duty_user_id' => $transfer_duty_id, //替换被转移负责人的id
                    'transfer_duty_data' => null, //清空被转移负责人数据
                ]);
                //更新下级评审通的  上级负责人id
                Pst::where('last_pst_id', $pst_id)->update('last_duty_user_id', $transfer_duty_id);
                //追加评审通相应的记录
                PstRepository::addPstOperateRecord([
                    'pst_id' => $pst->id,
                    'company_id' => $pst->company_id,
                    'type' => config('pst.operate_type.receive_transfer_duty'),
                    'operate_user_id' => $user->id,
                    'info' => $user->name . ',接收了 ' . User::find($duty_user_id)->name . ' 转移的负责人',
                ]);
                //通知转移负责人员已接收
                $single_data = DynamicTool::getSingleListData(
                    Pst::class,
                    1,
                    'company_id',
                    $pst->company_id,
                    '评审通:负责人转移' . User::find($duty_user_id)->name . ',接收了你的负责人转移',
                    '负责人转移成功',
                    $pst->created_at
                );
                NotifyTool::publishNotify([$duty_user_id], $pst->id, $pst, config('pst.default_notification_way'), $single_data, []);
                return json_encode(['status' => 'success', 'message' => '转移负责人接收成功']);
                break;
                //参与人接收
            case config('pst.user_type.inside_join_user'):
                //拿到对应评审通的内部参与人表单数
                $join_pst_form_data = json_decode($pst->join_pst_form_data, true);
                if (is_null($join_pst_form_data)) $join_pst_form_data = [];
                //改变参与人对应的接收状态&&添加对应参与人的表单接收数组
                $join_pst_form_data['form_' . $user->id] = [
                    'form_data' => [], //存放参与人表单
                    'opinion' => [], //存放参与人意见
                ];
                $up_data = [
                    'join_user_data->join_user_ids->inside_receive_state->' . 'state_' . $user->id => config('pst.state.under_way'),
                    'join_pst_form_data' => json_encode($join_pst_form_data),
                ];
                PstRepository::updatePst($pst_id, $up_data);
                //追加评审通相应的记录
                PstRepository::addPstOperateRecord([
                    'pst_id' => $pst->id,
                    'company_id' => $pst->company_id,
                    'type' => config('pst.operate_type.receive_inside_join'),
                    'operate_user_id' => $user->id,
                    'info' => $user->name . ',接收了参与人',
                ]);
                if ($duty_user_id != 0) { //通知相关人员,--负责人
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $pst->company_id,
                        '评审通:参与人接收通知',
                        '评审通:参与人接收' . User::find($user->id)->name . ',接收了评审通',
                        $pst->created_at
                    );
                    NotifyTool::publishNotify([$duty_user_id], $pst->id, $pst, config('pst.default_notification_way'), $single_data, []);
                }
                return json_encode(['status' => 'success', 'message' => '内部参与人接收成功']);
                break;
                //转移参与人接收
            case config('pst.user_type.transfer_join_user'):
                //获取被转移参与人员的数据信息
                $transfer_join_data = json_decode($pst->transfer_join_data, true);
                foreach ($transfer_join_data as $key => $data) {
                    //若当前用户id在参与人转移数组中,且接收状态为待接收
                    if (($data['tranfser_user_id'] == $user->id) && ($data['recive_state'] == config('pst.state.wait_receive'))) {
                        //原参与人id
                        $origin_user_id = FunctionTool::decrypt_id($data['need_data']['origin_user_id']);
                        //删除被转移参与人的id数据
                        unset($transfer_join_data[$key]);
                        //删除原参与人表单数据--将被转移参与人表单数据加入到参与人表单数据中
                        $join_user_form = json_decode($pst->join_user_form, true);
                        //将被转移参与人员的id加入到join_user_data->inside_user_ids中,并移除原参与人员的id
                        $index = array_search($origin_user_id, $inside_user_ids); //拿到原参与人员的id索引位置
                        //移除原参与人id--原参与人接收状态
                        unset($inside_user_ids[$index]);
                        $inside_user_ids = array_values($inside_user_ids);
                        $inside_user_ids[] = $user->id;
                        unset($inside_receive_state['state_' . $origin_user_id]);
                        //加入被转移参与人接收状态
                        $inside_receive_state['state_' . $user->id];
                        //只取特殊字段
                        $record = Pst::select('join_user_data', 'join_pst_form_data')->find($pst_id);
                        $join_user_data = json_decode($record->join_user_data, true);
                        //更新参与人相关数
                        $join_user_data['join_user_ids']['inside_user_ids'] = $inside_user_ids;
                        $join_user_data['join_user_ids']['inside_receive_state'] = $inside_receive_state;

                        //更新参与人表单--即将被转移的参与人添加至表单中
                        $join_pst_form_data = json_decode($record->join_pst_form_data, true);
                        if (is_null($join_pst_form_data)) $join_pst_form_data = [];
                        //改变参与人对应的接收状态&&添加对应参与人的表单接收数组
                        $join_pst_form_data['form_' . $user->id] = $join_pst_form_data['form_' . $origin_user_id];
                        //转移原参与人的表单数据
                        unset($join_pst_form_data['form_' . $origin_user_id]);
                        PstRepository::updatePst($pst_id, [
                            'join_user_data' => $join_user_data,
                            'join_pst_form_data' => $join_pst_form_data, //内部参与表单数
                        ]);
                        //追加评审通相应的记录
                        PstRepository::addPstOperateRecord([
                            'pst_id' => $pst->id,
                            'company_id' => $pst->company_id,
                            'type' => config('pst.operate_type.receive_transfer_inside_join'),
                            'operate_user_id' => $user->id,
                            'info' => $user->name . ',接收了转移参与人',
                        ]);
                    }
                }
                return json_encode(['status' => 'success', 'message' => '内部转移参与人接收成功']);
                break;
            default:
                return json_encode(['status' => 'fail', 'message' => '没有对应的接收类型']);
                break;
        }
    }
    /**
     * 评审通拒绝接收
     * @param Request $request:需要将单个评审通数据全部发送过来
     * @return mixed
     */
    public function refuse_receive(Request $request)
    {
        //判断到底是接收什么--根据前端传递的type值进行处理--同 getDetailRelationInfo() 方法压入的数据相关
        $data = $request->all();
        $user = auth('api')->user();

        $pst_id = FunctionTool::decrypt_id($data['need_data']['pst_id']);
        //拿到目标记录
        $pst = PstRepository::getPstOperateData($pst_id);
        //拿到内部参与人数组
        $inside_user_ids = json_decode($pst->inside_user_ids, true);
        $inside_receive_state = json_decode($pst->inside_receive_state, true); //内部参与人接收状态
        $duty_user_id = json_decode($pst->duty_user_id); //拿到负责人id 被转移负责人id
        $transfer_duty_id = json_decode($pst->transfer_duty_id); //被转移负责人id

        //拿到拒绝接收的类型标识
        $type = $data['type'];
        //分发拒绝接收处理请求
        switch ($type) {
                //代表评审通自身接收(源)--好像不太可能出现这种情况
            case config('pst.user_type.pst'):

                break;
                //代表评审通自身接收(合作伙伴)
            case config('pst.user_type.company_partner'):
                //状态改为待指派
                PstRepository::updatePst($pst_id, ['state' => config('pst.state.refuse_receive')]);
                //告诉上级负责人---xxx合作伙伴拒绝接收
                $single_data = DynamicTool::getSingleListData(
                    Pst::class,
                    1,
                    'company_id',
                    $pst->company_id,
                    '评审通:合作伙伴 ' . Company::find($pst->company_id)->name . ',拒绝接收了你发起的评审通',
                    '评审通提醒',
                    $pst->created_at
                );
                NotifyTool::publishNotify([$pst->duty_user_id], $pst->last_pst_id, Pst::find($pst->last_pst_id), config('pst.default_notification_way'), $single_data, []);
                //追加评审通相应的记录
                PstRepository::addPstOperateRecord([
                    'pst_id' => $pst->id,
                    'company_id' => $pst->company_id,
                    'type' => config('pst.operate_type.refuse_receive'),
                    'operate_user_id' => $user->id,
                    'info' => Company::find(1)->name . ',拒绝接收评审',
                ]);
                return json_encode(['status' => 'success', 'message' => '拒绝接收成功']);
                break;
                //代表评审通自身接收(外部联系人)
            case config('pst.user_type.outside_user'):
                //状态改为已接收
                PstRepository::updatePst($pst_id, ['state' => config('pst.state.refuse_receive')]);
                //告诉上级负责人---xxx已经接收
                $single_data = DynamicTool::getSingleListData(
                    Pst::class,
                    1,
                    'company_id',
                    $pst->company_id,
                    '评审通:外部联系人 ' . User::find($pst->outer_sider_id)->name . ',拒绝接收了你发起的评审通',
                    '评审通提醒',
                    $pst->created_at
                );
                NotifyTool::publishNotify([$pst->duty_user_id], $pst->last_pst_id, Pst::find($pst->last_pst_id), config('pst.default_notification_way'), $single_data, []);
                //追加评审通相应的记录
                PstRepository::addPstOperateRecord([
                    'pst_id' => $pst->id,
                    'company_id' => $pst->company_id,
                    'type' => config('pst.operate_type.refuse_receive'),
                    'operate_user_id' => $user->id,
                    'info' => $user->name . ',拒绝接收评审',
                ]);
                return json_encode(['status' => 'success', 'message' => '拒绝接收成功']);
                break;
                //负责人接收
            case config('pst.user_type.duty_user'):
                PstRepository::updatePst($pst_id, [
                    'duty_user_data->duty_receive_state' => config('pst.state.refuse_receive'), //负责人接收状态变为已接收
                ]);
                //通知相关人员负责人拒绝接收
                //追加评审通相应的记录
                PstRepository::addPstOperateRecord([
                    'pst_id' => $pst->id,
                    'company_id' => $pst->company_id,
                    'type' => config('pst.operate_type.refuse_duty'),
                    'operate_user_id' => $user->id,
                    'info' => $user->name . ',拒绝接收负责人',
                ]);
                return json_encode(['status' => 'success', 'message' => '负责人拒绝接收成功']);
                break;
                //转移负责人接收
            case config('pst.user_type.transfer_duty_user'):
                //将transfer_duty_data字段中
                PstRepository::updatePst($pst_id, [
                    'transfer_duty_data->duty_receive_state' => config('pst.state.refuse_receive'), //被转移负责人接收状态变为拒绝接收
                ]);
                //追加评审通相应的记录
                PstRepository::addPstOperateRecord([
                    'pst_id' => $pst->id,
                    'company_id' => $pst->company_id,
                    'type' => config('pst.operate_type.refuse_transfer_duty'),
                    'operate_user_id' => $user->id,
                    'info' => $user->name . ',拒绝接收转移负责人',
                ]);
                //通知转移负责人员被转移负责人员拒绝接收
                $single_data = DynamicTool::getSingleListData(
                    Pst::class,
                    1,
                    'company_id',
                    $data['company_id'],
                    '评审通:负责人转移' . User::find($duty_user_id)->name . ',拒绝接收了你的负责人转移',
                    '负责人转移',
                    $pst->created_at
                );
                NotifyTool::publishNotify([$duty_user_id], $pst->id, $pst, config('pst.default_notification_way'), $single_data, []);
                return json_encode(['status' => 'success', 'message' => '拒绝接收了你的负责人转移']);
                break;
                //参与人接收
            case config('pst.user_type.inside_join_user'):
                //改变参与人对应的接收状态
                PstRepository::updatePst($pst_id, ['join_user_data->join_user_ids->inside_receive_state->' . 'state_' . $user->id => config('pst.state.refuse_receive')]);
                //追加评审通相应的记录
                PstRepository::addPstOperateRecord([
                    'pst_id' => $pst->id,
                    'company_id' => $pst->company_id,
                    'type' => config('pst.operate_type.refuse_inside_join'),
                    'operate_user_id' => $user->id,
                    'info' => $user->name . ',拒绝接收参与人',
                ]);
                if ($duty_user_id != 0) { //通知相关人员,--负责人
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $pst->company_id,
                        '评审通:参与人接收通知',
                        '评审通:参与人' . User::find($user->id)->name . ',拒绝接收了评审通',
                        $pst->created_at
                    );
                    NotifyTool::publishNotify([$duty_user_id], $pst->id, $pst, config('pst.default_notification_way'), $single_data, []);
                }
                return json_encode(['status' => 'success', 'message' => '参与人拒绝接收成功']);
                break;
                //转移参与人接收
            case config('pst.user_type.transfer_join_user'):
                //获取被转移参与人员的数据信息
                $transfer_join_data = json_decode($pst->transfer_join_data, true);
                $target_user_id = FunctionTool::decrypt_id($data['need_data']['origin_user_id']);; //谁转移的--前端传递
                foreach ($transfer_join_data as $key => $data) {
                    //若当前用户id在被转移参与人转移数组中,且接收状态为待接收
                    if (($key == $target_user_id)
                        && ($data['tranfser_user_id'] == $user->id)
                        && ($data['recive_state'] == config('pst.state.wait_receive'))
                    ) {
                        //标记转移参与人接收状态为拒绝接收
                        PstRepository::updatePst($pst_id, [
                            'transfer_join_data->' . $user->id . '->receive_state' => config('pst.state.refuse_receive'),
                        ]);
                        //追加评审通相应的记录
                        PstRepository::addPstOperateRecord([
                            'pst_id' => $pst->id,
                            'company_id' => $pst->company_id,
                            'type' => config('pst.operate_type.refuse_transfer_inside_join'),
                            'operate_user_id' => $user->id,
                            'info' => $user->name . ',拒绝接收转移参与人',
                        ]);
                    }
                }
                break;
            default:
                return json_encode(['status' => 'fail', 'message' => '没有对应的接收类型']);
                break;
        }
    }
    /**
     * 评审通编辑
     * @param Request $request
     */
    public function editor(Request $request)
    {
        $user = auth('api')->user();
        $data = $request->all();
        //评审通id
        $id = FunctionTool::decrypt_id($data['pst_id']);
        //获取目标记录
        $pst = PstRepository::getPstOperateData($id);
        //负责人可修改
        if(json_decode($pst->duty_user_id) == $user->id){
            //是否需要审批的不同处理
//            if($pst->need_approval){
//                $this->callApproval($pst,[
//                    'pst_id' => $pst->id,
//                    'callback_result' => null,
//                    'state' => config('pst.approval_state.editor'), //评申通中的审批标识
//                    'operate_id' => $user->id, //操作用户
//                    'content' => '',
//                    'company_id' => $pst->company_id, //所属企业
//                    'data' => $data, //将前端传递的所有参数全部塞进审批额外数据中-供操作回调函数进行调用
//                ],$user);
//                return json_encode(['status'=>'success','message'=>'修改申请已发出']);
//            }else{
                return $this->editor_operate($data, $pst, $user->id, $pst->company_id);
//            }

        }else{
            return json_encode(['status' => 'fail', 'message' => '没有权限']);
        }
    }
    /**
     * 评审通指派(即指派负责人)
     * 前端需要将所选的负责人信息给传递过来
     * @param Request $request:
     * pst_id,
     * 负责人的信息
     */
    public function appoint(Request $request)
    {
        //验证有无人员指派权限
        if (!true) {
            return json_encode(['status' => 'fail', 'message' => '没有权限']);
        }
        //获取前端传递的数据
        $data = $request->all();
        //评审通id
        $id = FunctionTool::decrypt_id($data['pst_id']);
        //获取目标记录
        $pst = PstRepository::getPstOperateData($id);
        //获取当前用户
        $user = auth('api')->user();
        //判断是否需要审批--需要的话则发起相应的审批--否则的话直接触发相应的操作
        if ($pst->need_approval) {
            //发起指派事件对应的评审通
            $this->callApproval($pst, [
                'pst_id' => $pst->id, //评审通id
                'callback_result' => null, //审批回调标识
                'state' => config('pst.approval_state.appoint'), //评申通中-审批标识
                'content' => '',
                'operate_id' => auth('api')->id(), //当前操作人id
                'company_id' => $pst->company_id, //所属企业id
                'data' => $data, //将前端传递的所有参数全部塞进审批额外数据中-供操作回调函数进行调用
            ], $user);
        } else {
            //调用指派事件触发的操作
            return $this->appoint_operate($data, $pst, $user->id, $pst->company_id);
        }
    }
    /**
     * 评审通移交负责人
     * @param Request $request
     * 前端需要将选择的负责人数组gei
     */
    public function transfer_duty(Request $request)
    {
        //获取前端传递的数据
        $data = $request->all();
        //获取目标评审通id
        $id = FunctionTool::decrypt_id($data['pst_id']);
        //获取目标记录
        $pst = PstRepository::getPstOperateData($id);
        $user = auth('api')->user();
        if ($pst->need_approval) {
            //若是负责人，并且接收状态不为 待接收，拒绝接收时
            $state1 = (json_decode($pst->duty_user_id) == $user->id)
                && (json_encode($pst->duty_receive_state) != config('pst.state.wait_receive '))
                && (json_encode($pst->duty_receive_state) != config('pst.state.refuse_receive '));
            //有高级权限也可移交负责人--需要选择参与人与被参与人的信息
            if ($state1 || true) {
                //修改评审通状态为待审核
                PstRepository::updatePst($pst->id, ['state' => config('pst.state.wait_approval')]);

                $this->callApproval($pst, [
                    'pst_id' => $pst->id, //评审通id
                    'callback_result' => null, //审批回调标识
                    'state' => config('pst.approval_state.transfer_duty'), //转移评申通中-审批标识
                    'content' => '',
                    'operate_id' => $user->id, //操作用户
                    'company_id' => $pst->company_id, //所属企业
                    'data' => $data, //将前端传递的所有参数全部塞进审批额外数据中-供操作回调函数进行调用
                ], $user);
                return json_encode(['status' => 'success', 'message' => '负责人转移申请审批已发出']);
            } else {
                return json_encode(['status' => 'fail', 'message' => '没有相关权限']);
            }
        } else {
            //调起转移负责人事件处理函数
            return $this->transfer_duty_operate($data, $pst, $user->id, $pst->company_id);
        }
    }
    /**
     * 评审通移交参与人
     * @param Request $request
     */
    public function transfer_join(Request $request)
    {
        //验证当前用户是否是参与人
        $user = auth('api')->user();
        //获取前端传递的数据
        $data = $request->all();
        //获取目标评审通id
        $id = FunctionTool::decrypt_id($data['id']);
        //获取目标记录
        $pst = PstRepository::getPstOperateData($id);
        //若是参与人，并且接收状态不是拒绝接收状态就能够进行参与人转移
        //拿到内部参与人数组
        $inside_join_ids = json_decode($pst->inside_user_ids, true);
        //拿到内部参与人接收状态的数组
        $inside_receive_state = json_decode($pst->inside_receive_state, true);
        //判断该用户是否为内部参与人&&接收状态不是待接收 或者 拒绝接收状态
        $state1 = (in_array($user->id, $inside_join_ids) && ($inside_receive_state['state_' . $user->id] != config('pst.state.wait_receive'))
            && ($inside_receive_state['state_' . $user->id] != config('pst.state.refuse_receive')));
        //若需要审批则进行转移参与人审批申请
        if ($pst->need_approval) {
            if ($state1) {
                $this->callApproval($pst, [
                    'pst_id' => $pst->id, //评审通id
                    'callback_result' => null, //审批回调标识
                    'state' => config('pst.approval_state.transfer_join'), //转移评申通中-审批标识
                    'content' => '',
                    'operate_id' => $user->id, //操作用户
                    'company_id' => $pst->company_id, //所属企业
                    'data' => $data, //将前端传递的所有参数全部塞进审批额外数据中-供操作回调函数进行调用
                ], $user);
                return json_encode(['status' => 'success', 'message' => '人员转移申请审批已发出']);
            } else {
                return json_encode(['status' => 'fail', 'message' => '权限不足,不能转移!']);
            }
        } else {
            //调起转移参与人事件处理函数
            return $this->transfer_join_operate($data, $pst, $user->id, $pst->company_id);
        }
    }
    /**
     * 评审通递交--(相当于选择参与人一样)
     * @param Request $request
     */
    public function deliver(Request $request)
    {

        $user = auth('api')->user();
        //获取用户当前的company
        $origin_company = null;
        if ($user->current_company_id != 0) {
            $origin_company = Company::find($user->current_company_id);
        }
        //获取前端传递的数据
        $data = $request->all(); //获取目标评审通id
        $id = FunctionTool::decrypt_id($data['id']); //获取目标记录
        $pst = PstRepository::getPstOperateData($id); //拿到内部参与人数组
        $duty_user_id = json_decode($pst->duty_user_id); //负责人id
        $duty_receive_state = json_decode($pst->duty_receive_state); //负责人接收状态
        //如果需要审批的话,发起递交的审批操作(负责人可操作)
        if ($pst->need_approval) {
            if (true && ($duty_user_id == $user->id) && ($duty_receive_state == config('pst.state.received'))) {
                $this->callApproval($pst, [
                    'pst_id' => $pst->id, //评审通id
                    'callback_result' => null, //审批回调标识
                    'state' => config('pst.approval_state.deliver'), //转移评申通中-审批标识
                    'content' => '',
                    'operate_id' => $user->id, //操作用户
                    'company_id' => $pst->company_id, //所属企业
                    'data' => $data, //将前端传递的所有参数全部塞进审批额外数据中-供操作回调函数进行调用
                ], $user);
                return json_encode(['status' => 'success', 'message' => '递交申请审批已发出']);
            }else{
                return json_encode(['status' => 'fail', 'message' => '没有相关权限']);
            }
        } else {
            //调起递交事件操作函数
            return $this->deliver_operate($data, $pst, $user->id, $pst->company_id);
        }
    }
    /**
     * 评审通召回
     * @param Request $request
     */
    public function recall(Request $request)
    {
        $user = auth('api')->user();
        //获取前端传递的数据
        $data = $request->all();
        //获取目标评审通id
        $id = FunctionTool::decrypt_id($data['id']);
        //获取目标记录
        $pst = PstRepository::getPstOperateData($id);
        //若是负责人，则可以进行召回--有高级权限也可进行召回
        $state1 = json_decode($pst->duty_user_id) == $user->id;
        //有没有高级权限
        $state2 = RoleAndPerTool::user_has_c_per($user->id, $pst->company_id, [], 'any');
        if ($pst->need_approval) {
            //判断是否能够召回--负责人可以召回,有相关权限的可以召回
            //先判断是否有召回权限
            if ($state2 && $state1) {
                //修改评审通状态为待审核
                PstRepository::updatePst($pst->id, ['state' => config('pst.state.wait_approval')]);
                //发起召回审批申请
                $this->callApproval($pst, [
                    'pst_id' => $pst->id, //评审通id
                    'callback_result' => null, //审批回调标识
                    'state' => config('pst.approval_state.recall'), //转移评申通中-审批标识
                    'content' => '',
                    'operate_id' => $user->id, //操作用户
                    'company_id' => $pst->company_id, //所属企业
                    'data' => $data, //将前端传递的所有参数全部塞进审批额外数据中-供操作回调函数进行调用
                ], $user);
                return json_encode(['status' => 'success', 'message' => '召回申请审批已发出']);
            } else {
                return json_encode(['status' => 'fail', 'message' => '没有权限']);
            }
        } else {
            //调起召回事件处理函数
            return $this->recall_operate($data, $pst, $user->id, $pst->company_id);
        }
    }
    /**
     * 评审通作废
     * @param Request $request
     */
    public function cancle(Request $request)
    {
        $user = auth('api')->user();
        //获取前端传递的数据
        $data = $request->all();
        //获取目标评审通id
        $id = FunctionTool::decrypt_id($data['pst_id']);
        //获取目标记录
        $pst = PstRepository::getPstOperateData($id);
        //若是负责人，则可以进行作废
        $state1 = json_decode($pst->duty_user_id) == $user->id;
        //有没有高级权限
        $state2 = RoleAndPerTool::user_has_c_per($user->id, $pst->company_id, [], 'any');
        if ($pst->need_approval) {
            //判断是否能够召回--负责人可以召回,有相关权限的可以召回
            //先判断是否有召回权限
            if ($state2 || $state1) {
                //修改评审通状态为待审核
                PstRepository::updatePst($pst->id, ['state' => config('pst.state.wait_approval')]);
                //发起作废审批申请
                $this->callApproval($pst, [
                    'pst_id' => $pst->id, //评审通id
                    'callback_result' => null, //审批回调标识
                    'state' => config('pst.approval_state.cancle'), //转移评申通中-审批标识
                    'content' => '',
                    'operate_id' => $user->id, //操作用户
                    'company_id' => $pst->company_id, //所属企业
                    'data' => $data, //将前端传递的所有参数全部塞进审批额外数据中-供操作回调函数进行调用
                ], $user);
                return json_encode(['status' => 'success', 'message' => '作废申请已发出']);
            } else {
                return json_encode(['status' => 'fail', 'message' => '没有权限']);
            }
        } else {
            //调起作废事件的处理函数
            return $this->cancle_operate($data, $pst, $user->id, $pst->company_id);
        }
    }
    /**
     * 评审通打回
     * @param Request $request
     */
    public function back(Request $request)
    {
        //判断到底是打回什么--根据前端传递的type值进行处理--同 getDetailRelationInfo() 方法压入的数据相关
        $data = $request->all();
        $pst_id = FunctionTool::decrypt_id($data['need_data']['pst_id']);
        //拿到目标记录
        $pst = PstRepository::getPstOperateData($pst_id);
        $user = auth('api')->user();
        if ($pst->need_approval) {
            //发起作废审批申请
            $this->callApproval($pst, [
                'pst_id' => $pst->id, //评审通id
                'callback_result' => null, //审批回调标识
                'state' => config('pst.approval_state.cancle'), //转移评申通中-审批标识
                'content' => '',
                'operate_id' => $user->id, //操作用户
                'company_id' => $pst->company_id, //所属企业
                'data' => $data, //将前端传递的所有参数全部塞进审批额外数据中-供操作回调函数进行调用
            ], $user);
        } else {
            return self::back_operate($data, $pst, $user->id, $pst->company_id);
        }
    }
    /**
     * 评审通撤回
     * @param Request $request
     */
    public function retract(Request $request)
    {
        $user = auth('api')->user();
        //获取前端传递的数据
        $data = $request->all();
        //获取目标评审通id
        $id = FunctionTool::decrypt_id($data['id']);
        //获取目标记录
        $pst = PstRepository::getPstOperateData($id);
        //若是负责人，则可以进行撤回
        $state1 = json_decode($pst->duty_user_id) == $user->id;
        //有没有高级权限
        $state2 = RoleAndPerTool::user_has_c_per($user->id, $pst->company_id, [], 'any');
        //判断是否需要进行审批操作
        if ($pst->need_approval) {
            if ($state1 && $state2) {
                //修改评审通状态为待审核
                PstRepository::updatePst($pst->id, ['state' => config('pst.state.wait_approval')]);
                //发起撤回审批申请
                $this->callApproval($pst, [
                    'pst_id' => $pst->id, //评审通id
                    'callback_result' => null, //审批回调标识
                    'state' => config('pst.approval_state.retract'), //转移评申通中-审批标识
                    'content' => '',
                    'operate_id' => $user->id, //操作用户
                    'company_id' => $pst->company_id, //所属企业
                    'data' => $data, //将前端传递的所有参数全部塞进审批额外数据中-供操作回调函数进行调用
                ], $user);
                return json_encode(['status' => 'success', 'message' => '召回申请已发起']);
            } else {
                return json_encode(['status' => 'fail', 'message' => '没有权限']);
            }
        } else {
            //调起撤回事件处理函数
            return $this->retract_operate($data, $pst, $user->id, $pst->company_id);
        }
    }
    /**
     * 评审通参与人完成
     * @param Request $request
     */
    public function finish(Request $request)
    {
        try{
            DB::beginTransaction();
            $user = auth('api')->user();
            //获取前端传递的数据
            $data = $request->all();
            //获取目标评审通id
            $id = FunctionTool::decrypt_id($data['pst_id']);
            //获取目标记录
            $pst = PstRepository::getPstOperateData($id);
            // 若是负责人，则可以进行完成
            if ($pst->duty_user_id == $user->id) {
                $jg= $this->pst_finish($pst,$data['finish_form']);
                DB::commit();
                return $jg;
            }
            //有没有高级权限
            $state2 = RoleAndPerTool::user_has_c_per($user->id, $pst->company_id, [], 'any');
            //有高级权限也可点击完成
            if($pst->need_approval) {
                if ($state2) {
                    //发起完成审批申请
                    $this->callApproval($pst, [
                        'pst_id' => $pst->id,//评审通id
                        'callback_result' => null,//审批回调标识
                        'state' => config('pst.approval_state.finish'),//转移评申通中-审批标识
                        'content' => '',
                        'operate_id' => $user->id,//操作用户
                        'company_id' => $pst->company_id,//所属企业
                        'data' => $data,//将前端传递的所有参数全部塞进审批额外数据中-供操作回调函数进行调用
                    ],$user);
                    return json_encode(['status' => 'success', 'message' => '完成申请已发起']);
                } else {
                    return json_encode(['status' => 'fail', 'message' => '没有权限']);
                }
            }else{
                //调起完成事件的处理函数
                $result=$this->finish_operate($data, $pst, $user->id, $pst->company_id);
                DB::commit();
                return $result;
            }
        }catch (\Exception $e){
            DB::rollBack();
            return ['status'=>'fail','message'=>'服务器错误'];
        }

    }
    /**
     * 公司负责人点击完成
     * 评审通各级节点完成操作--需要合并数据--(待处理)
     * @param Request $request
     */
    public function pst_finish(Pst $pst,array $data)
    {
        $user=\auth('api')->user();
        $pst_id=$pst->id;
        PstRepository::updatePst($pst_id, [
            'state' => '已完成', //负责人接收状态变为已完成
            'finish_form'=>json_encode($data),
        ]);
        self::updatePstCurrentHandler(['pst_id'=>$pst_id]);
        // 追加评审通相应的记录
        PstRepository::addPstOperateRecord([
            'pst_id' => $pst_id,
            'company_id' => $pst->company_id,
            'type' => config('pst.operate_type.finish'),
            'operate_user_id' => $user->id,
            'info' => $user->name
        ]);
        // 通知转移负责人员已接收
        $single_data = DynamicTool::getSingleListData(
            Pst::class,
            1,
            'company_id',
            $pst->company_id,
            '工作通知: ' . $pst->company_id,
            $user->name . '已完成评审通',
            $pst->created_at
        );
//            NotifyTool::publishNotify([$user->id], $pst->id, $pst, config('pst.default_notification_way'), $single_data, []);
        return json_encode(['status' => 'success', 'message' => '该评审完成']);

    }
    /**
     * 评审通归档
     * @param Request $request
     */
    public function archive(Request $request)
    {
        $user = auth('api')->user();
        //获取前端传递的数据
        $data = $request->all();
        //获取目标评审通id
        $id = FunctionTool::decrypt_id($data['id']);
        //获取目标记录
        $pst = PstRepository::getPstOperateData($id);
        //若是负责人，则可以进行撤回
        $is_duty_user = $pst->duty_user_id == $user->id;
        //有没有高级权限
        $state2 = RoleAndPerTool::user_has_c_per($user->id, $pst->company_id, [], 'any');

        //有高级权限也可点击完成
        //有高级权限也可点击完成
        if ($pst->need_approval) {
            if ($is_duty_user && $state2) {
                //修改评审通状态为待审核
                PstRepository::updatePst($pst->id, ['state' => config('pst.state.wait_approval')]);
                //发起撤回审批申请
                $this->callApproval($pst, [
                    'pst_id' => $pst->id, //评审通id
                    'callback_result' => null, //审批回调标识
                    'state' => config('pst.approval_state.archive'), //转移评申通中-审批标识
                    'content' => '',
                    'operate_id' => $user->id, //操作用户
                    'company_id' => $pst->company_id, //所属企业
                    'data' => $data, //将前端传递的所有参数全部塞进审批额外数据中-供操作回调函数进行调用
                ], $user);
                return json_encode(['status' => 'success', 'message' => '召回申请已发起']);
            } else {
                return json_encode(['status' => 'fail', 'message' => '没有权限']);
            }
        } else {
            //调起归档事件的处理函数
            return $this->archive_operate($data, $pst, $user->id, $pst->company_id);
        }
    }
    /**
     * 评审通 负责人审阅参与人提交的东西
     * @param Request $request
     */
    public function duty_agree_join(Request $request)
    {
        //判断到底是确定哪个参与人的信息
        $data = $request->all();
        $pst_id = FunctionTool::decrypt_id($data['need_data']['pst_id']);
        //拿到目标记录
        $pst = PstRepository::getPstOperateData($pst_id);
        $user = auth('api')->user();
        //需要改变的参与人id
        $join_user_id = FunctionTool::decrypt_id($data['need_data']['target_id']);
        //改变参与人对应的接收状态
        PstRepository::updatePst($pst_id, ['join_user_data->join_user_ids->inside_receive_state->' . 'state_' . $join_user_id => config('pst.state.finish')]);
        $single_data = DynamicTool::getSingleListData(
            Pst::class,
            1,
            'company_id',
            $pst->company_id,
            '评审通:进度提醒',
            User::find($user->id)->name . ',通过审核',
            $pst->created_at
        );
        NotifyTool::publishNotify([$join_user_id], $pst->id, $pst, config('pst.default_notification_way'), $single_data, []);
        return json_encode([
            'status' => 'success',
            'message' => '成功'
        ]);
    }
    /**
     * 单个评审通的按钮情况检测
     * @param int $pst_id:目标评审通id
     * @param int $user_id:目标用户id
     * @return mixed
     */
    public static function btn_status(int $pst_id, int $user_id)
    {
        //获取对应的评审通记录
        $pst = PstRepository::getPstOperateData($pst_id);
        //获取所有按钮以及状态
        $btn_status = config('pst.btn_status');
        //接收按钮的状态判断
        //先获取函数名称
        $btn_names = array_keys(config('pst.btn_status'));
        //根据函数名称动态调用响应的函数
        foreach ($btn_names as $name) {
            try {
                $btn_status = call_user_func([self::getPstTool(), $name], $user_id, $pst, $btn_status);
            } catch (\Exception $e) {
                continue;
            }
        }
        return $btn_status;
    }
    /**
     * 获取合并下级数据
     * @param array $data;所需要的数据组
     * @return mixed
     */
    public function getMergeData(array $data)
    {
        //获取目标评审通id
        $pst_id = $data['pst_id'];
        //获取下级资料清单数据
        $files_data = $this->getPstChildrenFiles(['pst_id' => $pst_id]);
        $form_data = $this->getPstChildrenMergeFormData(['pst_id' => $pst_id]);
        return json_encode([
            'status' => 'success',
            'files' => $files_data,
            'form_data' => $form_data,
        ]);
    }
    /**
     * 获取目标评审通的下级所有需要合并的表单数据--以分组的形式
     * @param array $need_data:所需要的数据
     *               -- pst_id:目标评审通id
     *               -- pst_id:目标评审通id
     */
    public  function getPstChildrenMergeFormData(array $need_data)
    {
        //需要返回的数据基础格式i
        $data = [
            'company_internal'=>[],//公司内部
            'company_partner' => [], //合作伙伴的附件存放数组
            'outside_user' => [],  //外部联系人附件存放数组
        ];
        //拿到目标评审通id--默认加密
        $pst_id = FunctionTool::decrypt_id($need_data['pst_id']);
        //获取内部公司已完成的参与人的表单数据
        $data['company_internal'][]=$this->get_company_internal($pst_id);
        //获取所有下级子评审通记录--只加载id避免资源浪费
        $psts = PstRepository::getChildren($pst_id, 'complex');
        //开始循环抽取下级子类的所需表单信息--并分类型
        foreach ($psts as $pst) {
            //判断是合作伙伴 or 外部联系人
            if ($pst->company_id != 0&&$pst->state == '已完成') {
                //若是合作伙伴--压入附件数据
                $data['company_partner'][] = [
                    'name' => Company::find($pst->company_id)->name,
                    'form_data' => json_decode($pst->join_pst_form_data, true),
                ];
            } elseif ($pst->state == '已完成') {
                //若是外部联系人-压入附件数据
                $data['outside_user'][] = [
                    'name' => User::find($pst->outside_user_id)->name,
                    'form_data' => json_decode($pst->join_pst_form_data, true),
                ];
            }
        }
        return $data;
    }
    private function get_company_internal($pst_id)
    {
        $pst=Pst::find($pst_id);
        $join_pst_form_data=json_decode($pst->join_pst_form_data,true);
        //拿到内部参与人数组
        $inside_user_ids = json_decode($pst->inside_user_ids, true);
        //内部参与人接收状态
        $inside_receive_state = json_decode($pst->inside_receive_state, true);
        //获取目标内部参与人的接收状态
        $form_data=[];
        if($inside_user_ids!==null){
            foreach ($inside_user_ids as $inside_user_id){
                $user_state = $inside_receive_state['state_' . $inside_user_id];
                //如果参与人状态为已完成,则取出该数据
                if($user_state=='已完成'){
                    $form_data['form_'.$inside_user_id]=$join_pst_form_data['form_'.$inside_user_id];
                }
            }
        }
        return [
            'name' => '公司内部数据',
            'form_data' => $form_data,
        ];
    }
    /**
     * 更新内部参与人表单数据
     * @param array $data:所需要传递过来的数据
     *                    --form_data:参与人的表单数据
     *                    --pst_id:目标评审通id
     */
    public function updateInsideJoinForm(array $data)
    {
        $user = auth('api')->user();
        //目标评审通id
        $pst_id = FunctionTool::decrypt_id($data['pst_id']);
        //取出参与人表单数据
        $form_data = $data['form_data'];
        //取出指定字段的评审通
        $record = PstRepository::getPstTargetValue(['join_pst_form_data'], $pst_id);
        $join_pst_form_data = json_decode($record->join_pst_form_data, true);
        $join_pst_form_data['form_' . $user->id] = [
            'form_data' => $form_data,
            'opinion' => array_get($join_pst_form_data, 'opinion', '')
        ];
        //更新指定参与人的信息
        PstRepository::updatePst($pst_id, [
            'join_pst_form_data' => json_encode($join_pst_form_data),
        ]);
        return json_encode([
            'status' => 'success',
            'message' => '表单更新成功'
        ]);
    }
    //==============================单个评审通按钮状态判断组方法=============================================>
    /**
     * 接收按钮的状态判断
     * @param int $user_id:目标用户
     * @param $pst:目标评审通
     * @param $btn_status
     */
    public function btn_receive(int $user_id, $pst, array $btn_status): array
    {
        //接收按钮的状态判断

        //该评审通是否处于待接收状态(外部企业传递过来的)
        $state1 = $pst->state == config('pst.state.wait_receive')
            && RoleAndPerTool::user_has_c_per($user_id, $pst->company_id, [], 'any');
        //判断该用户是否是外部联系人&&该评审通是否是外部联系人的评审通
        $state2 = $pst->company_id == 0 && $pst->outside_user_id != 0 && $pst->outside_user_id == $user_id;
        //抽取参与人信息
        $join_user_data = json_decode($pst->join_user_data, true);
        //内部参与人id数组
        $inside_user_ids = $join_user_data['inside_user_ids'];
        //内部参与人接收状态数组
        $inside_receive_state = $join_user_data['inside_receive_state'];
        //该用户是企业内部参与人&&该用户未接收该评审通
        $state3 = false;
        if (!empty($inside_receive_state)) {
            $state3 = CompanyRepository::companyHaveUser($pst->company_id, $user_id) && in_array($user_id, $inside_user_ids)
                && $inside_receive_state[$user_id] != config('pst.state.wait_receive');
        }
        //被转移的参与人员信息
        $state4 = false;
        $transfer_join_data = $pst->transfer_join_data;
        //循环比对查找当前用户是否是被转移参与人
        if (!empty($transfer_join_data)) {
            foreach ($transfer_join_data as $key => $data) {
                //若当前用户id在参与人转移数组中,且接收状态为待接收
                if (($data['tranfser_user_id'] == $user_id) && ($data['recive_state'] == config('pst.state.wait_receive'))) {
                    $state4 = true;
                }
            }
        }
        //该用户是负责人
        //若是负责人，并且接收状态为待接收时可以有接收按钮
        $state5 = json_decode($pst->duty_user_id) == $user_id && json_decode($pst->duty_receive_state) == config('pst.state.wait_receive');
        //判断该用户是负责人被转移人员
        $state6 = json_decode($pst->transfer_duty_id) == $user_id && json_decode($pst->transfer_duty_receive_state) == config('pst.state.wait_receive');
        if ($pst->state!='待审核'&&($state1 || $state2 || $state3 || $state4 || $state5 || $state6)) {
            $btn_status['btn_receive'] = true;
            $btn_status['btn_refuse_receive'] = true;
        }
        return $btn_status;
    }
    /**
     * 指派按钮的状态判断
     * @param int $user_id:目标用户
     * @param $pst:目标评审通
     * @param $btn_status
     */
    public function btn_editor(int $user_id, $pst, array $btn_status): array
    {
        //发起人可编辑(评审通未处于待审核状态)
        if ($pst->publish_user_id==$user_id && $pst->state != config('pst.state.wait_approval')) {
            $btn_status['btn_editor'] = true;
        }
        return $btn_status;
    }
    /**
     * 指派按钮的状态判断
     * @param int $user_id:目标用户
     * @param $pst:目标评审通
     * @param $btn_status
     */
    public function btn_appoint(int $user_id, $pst, array $btn_status): array
    {
        //该评审通是否处于待指派状态&有人员指派权
        $state1 = $pst->state == config('pst.state.wait_appoint')
            && RoleAndPerTool::user_has_c_per($user_id, $pst->company_id, [], 'any');
        if ($pst->state=='待指派'&&$state1) {
            $btn_status['btn_appoint'] = true;
        }
        return $btn_status;
    }
    /**
     * 移交负责人按钮的状态判断
     * @param int $user_id:目标用户id
     * @param $pst:目标评审通
     * @param array $btn_status:按钮状态数组
     */
    public function btn_transfer_duty(int $user_id, $pst, array $btn_status): array
    {
        //若是负责人，则可以转移负责人
        $state1 = (json_decode($pst->duty_user_id) == $user_id) && ($pst->state != config('pst.state.wait_receive')) && ($pst->state != config('pst.state.wait_approval')) && ($pst->state != config('pst.state.cancled'));
        //可转移负责人条件判断
        $state2=$pst->state!=config('pst.state.finish');
        if ($state1&&$state2) {
            $btn_status['btn_transfer_duty'] = true;
        }
        return $btn_status;
    }
    /**
     * 移交参与人按钮的状态判断--
     * @param int $user_id:目标用户id
     * @param $pst:目标评审通
     * @param array $btn_status:按钮状态数组
     */
    public function btn_transfer_join(int $user_id, $pst, array $btn_status): array
    {
        $user = auth('api')->user();
        //若是内部参与人，则可以转移参与的位置
        //获取该评审通的内部参与人数组
        $inside_user_ids = json_decode($pst->inside_user_ids, true);
        //获取该评审通内部参与人状态数组
        $inside_receive_state = json_decode($pst->inside_receive_state, true);
        //判断当前用户是否在内部参与人数组中&&参与人状态不是待接收
        $state1 = in_array($user_id, $inside_user_ids) && ($inside_receive_state['state_' . $user->id] == config('pst.state.wait_receive'))
            && ($inside_receive_state['state' . $user->id] != config('pst.state.refuse_receive'));
        //可转移参与人条件判断
        $state2=$pst->state!=config('pst.state.finish');
        //接收前端传递的被转移目标人员数据
        if ($state1&&$state2) {
            $btn_status['btn_transfer_join'] = true;
        }
        return $btn_status;
    }
    /**
     * 递交按钮的状态判断--主要进行评审通信息传递
     * @param int $user_id:目标用户id
     * @param $pst:目标评审通
     * @param array $btn_status:按钮状态数组
     */
    public function btn_deliver(int $user_id, $pst, array $btn_status): array
    {
        //先判断是否有递交权限（是否是负责人）
        $state1 = $user_id == json_decode($pst->duty_user_id);
        if (!$state1) return $btn_status;
        //先取出所有参与人的状态
        $inside_receive_state = json_decode($pst->inside_receive_state, true);
        $state2 = true;
        foreach ($inside_receive_state as $state) {
            $s = ($state == config('pst.state.finish'));
            $state2 = $state2 && $s;
        }
        if ($state1 && $state2) {
            $btn_status['btn_deliver'] = true;
        }
        return $btn_status;
    }
    /**
     * 召回按钮的状态判断
     * @param int $user_id:目标用户id
     * @param $pst:目标评审通
     * @param array $btn_status:按钮状态数组
     */
    public function btn_recall(int $user_id, $pst, array $btn_status): array
    {
        //若是负责人，则可以进行召回
        $state1 = json_decode($pst->duty_user_id) == $user_id;
        //判断是否有子项
        $state2 = PstRepository::checkHaveUnfinishedChildren($pst->id);
        if ($state1 && $state2) {
            $btn_status['btn_recall'] = true;
        }
        return $btn_status;
    }
    /**
     * 作废按钮的状态判断
     * @param int $user_id:目标用户id
     * @param $pst:目标评审通
     * @param array $btn_status:按钮状态数组
     */
    public function btn_cancle(int $user_id, $pst, array $btn_status): array
    {
        //若是负责人，则可以进行作废
        $state1 = json_decode($pst->duty_user_id) == $user_id;
        //若是发起人,则可以进行作废
        $state2 = $pst->publish_user_id == $user_id;
        //可作废条件判断
        $state3=$pst->state!=config('pst.state.cancled')&&$pst->state!=config('pst.state.finish');
        //判断是否有子项
        //        $state3=PstRepository::checkHaveRecallChildren($pst->id);
        if ($state3&&($state1 || $state2)) {
            $btn_status['btn_cancle'] = true;
        }
        return $btn_status;
    }
    /**
     * 打回按钮的状态判断
     * @param int $user_id:目标用户id
     * @param $pst:目标评审通
     * @param array $btn_status:按钮状态数组
     */
    public function btn_back(int $user_id, $pst, array $btn_status): array
    {
        //若是负责人，则可以进行打回
        $state1 = json_decode($pst->duty_user_id) == $user_id;
        //若是发起人,则可以进行打回
        $state2 = $pst->publish_user_id == $user_id;
        //可打回条件判断完成
        $state3=$pst->state!=config('pst.state.finish');
        //可打回条件判断作废
        $state4=$pst->state!=config('pst.state.cancled');
        //可打回条件判断归档
        $state5=$pst->state!=config('pst.state.archived');
        //判断是否有子项
        //        $state3=PstRepository::checkHaveRecallChildren($pst->id);
        if ($state3&& $state4 && $state5 && ($state1 || !$state2)) {
            $btn_status['btn_back'] = true;
        }
        return $btn_status;
    }
    /**
     * 撤回按钮的状态判断
     * @param int $user_id:目标用户id
     * @param $pst:目标评审通
     * @param array $btn_status:按钮状态数组
     */
    public function btn_retract(int $user_id, $pst, array $btn_status): array
    {
        //若是发起人,则可以进行撤回
        $state1 = $pst->publish_user_id == $user_id;
        //判断是否有本相处于未接收
        $state2 = ($pst->duty_receive_state != config('pst.state.received'));
        //判断是否有子项处于未接收
        $state3 = PstRepository::checkHaveTargetChildren($pst->id, config('pst.state.wait_receive'));
        //判断是处于撤回和作废
        $state4=$pst->state!=config('pst.state.retracted');
        //可条件判断作废
        $state5=$pst->state!=config('pst.state.cancled');
        //判断审批是否未审批状态，待完善
        $state6=$pst->stete!=config('pst.state.wait_approval');
        if ($state1 && $state2 && !$state3 && $state4 && $state5) {
            $btn_status['btn_retract'] = true;
        }
        return $btn_status;
    }
    /**
     * 完成-按钮的状态判断
     * @param int $user_id:目标用户id
     * @param $pst:目标评审通
     * @param array $btn_status:按钮状态数组
     */
    public function btn_finish(int $user_id, $pst, array $btn_status): array
    {
        //负责人接收状态
        $duty_receive_state = $pst->duty_receive_state;
        //若是负责人，接收状态不为 待接收,拒绝接收，已完成状态时可以看到完成按钮
        $state1 = (json_decode($pst->duty_user_id) == $user_id) && ($duty_receive_state == config('pst.state.received'));

        //判断该评审通处于未完成状态,&& 所有子项都处于完成状态
        $state3 = $pst->state != config('pst.state.finish') && self::checkPstCanFinish($pst);
        if ($state1 && $state3) {
            $btn_status['btn_finish'] = true;
        }

        return $btn_status;
    }
    /**
     * 归档-按钮的状态判断
     * @param int $user_id:目标用户id
     * @param $pst:目标评审通
     * @param array $btn_status:按钮状态数组
     */
    public function btn_archive(int $user_id, $pst, array $btn_status): array
    {
        //若是负责人，则可以进行归档
        $state1 = json_decode($pst->duty_user_id) == $user_id;
        //若是发起人,则可以进行归档
        $state2 = $pst->publish_user_id == $user_id;
        //判断该评审通处于完成状态,&& 所有子项都处于完成状态
        if ($pst->last_pst_id != 0) {
            $pst = PstRepository::getTopPst($pst->last_pst_id);
        }
        $state3 = $pst->state == config('pst.state.finish') && self::checkPstCanFinish($pst);
        if (($state1 || $state2) && $state3) {
            $btn_status['btn_archive'] = true;
        }
        return $btn_status;
    }
    //==============================评审通相应的操作事件的处理(公用)=============================================>

    /**
     * 修改事件对应的操作
     * @param array $data
     * @param Pst $pst
     * @param int $operate_id
     * @param int $company_id
     * @return false|string
     */
    public function editor_operate(array $data, Pst $pst, int $operate_id, int $company_id)
    {
        $files=$_FILES;
        $user = User::find($operate_id);

        //获取对应的company
        $company = Company::find($company_id);

        //计算企业云存储的剩余空间是否满足附件的大小&文件合法性校验
        if (!(count($files) == 0)) {
            $message = CompanyOssTool::ossSizeIsEnough($company_id, $files);
            if (count($message) != 0) {
                return json_encode(['status' => 'fail', 'message' => implode(',', $message)]);
            }
        }
        //组装创建评审通的数据
        $pst_data = $this->makePstData($data, $user);
        DB::beginTransaction();
        try{
            //更新评审通基础信息
            PstRepository::updatePst($pst->id,$pst_data);
            //添加记录
            PstRepository::addPstOperateRecord([
                'pst_id' => $pst->id,
                'company_id' => $pst_data['company_id'],
                'type' => config('pst.operate_type.create_pst'),
                'operate_user_id' => $user->id,
                'info' => $user->name . ',修改评审'
            ]);
            //进行评审通关联操作
            //清除原有关联
            DB::table('pst_self_related')->where('target_pst_id',$pst->id)->delete();
            //创建新的关联
            if (array_get($data, 'associated_psts', false)) {
                $related_pst_ids = FunctionTool::decrypt_id_array(json_decode($data['associated_psts'],true));
                foreach ($related_pst_ids as $related_pst_id) {
                    DB::table('pst_self_related')->insert([
                        'target_pst_id' => $pst->id, //目标评审通id
                        'related_pst_id' => $related_pst_id, //所关联的评审通id
                    ]);
                }
            }

            {
                //附件处理
                //清除原有附件
                $oldfiles = FileResource::collection($pst->files);
                if($oldfiles){
                    foreach($oldfiles as $file){
                        CompanyOssTool::deleteFile($file['id']);
                    }
                }
                //处理新附件
                if (count($files) == 0) {
                    DB::commit();
                    $str = json_encode(['status' => 'success', 'message' => '添加成功']);
                } else {
                    $result = CompanyOssTool::uploadFile($files, [
                        'oss_path' => $company->oss->root_path . '评审通附件', //公告上传的云路径,其他模块与之类似
                        'model_id' => $pst->id, //关联模型的id
                        'model_type' => Pst::class, //关联模型的类名
                        'company_id' => $company_id, //所属公司的id
                        'uploader_id' => $user->id, //上传者的id
                    ]);
                    DB::commit();
                    if ($result === true) {
                        $str =  json_encode(['status' => 'success', 'message' => '修改成功']);
                    } else {
                        $str =  json_encode(['status' => 'fail', 'message' => '修改成功,但' . $result]);
                    }
                }
            }

            return $str;
        }catch (\Exception $e){
            DB::rollBack();
            return json_encode(['status' => 'fail', 'message' => '评审通修改出错啦']);
        }
    }
    /**
     * 指派事件对应的操作
     * @param array $data:所需要的前端数据
     * @param Pst $pst:目标评审通记录
     * @param int $operate_id:操作的用户id
     * @param int $company_id:企业的id
     * @return array
     */
    public static function appoint_operate(array $data, Pst $pst, int $operate_id, int $company_id)
    {
        $user = User::find($operate_id);
        DB::beginTransaction();
        try {
            //判断该评审通是否需要指派，-1.没有负责人.2.状态为待指派状态
            if ($pst->state != config('pst.state.wait_appoint') || empty($pst->duty_user_id)) {
                return json_encode(['status' => 'fail', 'message' => '该评审通目前不能指派']);
            }
            //需要更新的数据组
            $up_data = [];
            //获取传递过来的负责人id
            $duty_user_id = FunctionTool::decrypt_id($data['duty_user_id']);
            //负责人表单数据
            $duty_form_data = [];
            //变更评审通的状态为待接收--在分类展示评审通的时候需要对所有人员的接收信息进行汇总展示
            $up_data['state'] = config('pst.state.wait_receive');
            $up_data['duty_user_data'] = json_encode([
                'duty_user_id' => $duty_user_id,
                'receive_state' => ($pst->publish_user_id == $duty_user_id) ? config('pst.state.received') : config('pst.state.wait_receive'),
            ]); //负责人数据,初始状态为待接收
            //更新数据
            PstRepository::updatePst($pst->id, $up_data);
            //追加评审通相应的记录
            PstRepository::addPstOperateRecord([
                'pst_id' => $pst->id,
                'company_id' => $pst->company_id,
                'type' => config('pst.operate_type.appoint'),
                'operate_user_id' => $user->id,
                'info' => $user->name . ',指派了相关人员',
            ]);
            //通知相关负责人--$data['company_id']为单条动态通知的分组
            $single_data = DynamicTool::getSingleListData(
                Pst::class,
                1,
                'company_id',
                $pst->company_id,
                '评审通:' . User::find($pst->publish_user_id)->name . ' 将你任命为负责人',
                '评审通负责人任命通知',
                $pst->created_at
            );
            NotifyTool::publishNotify([$duty_user_id], $pst->company_id, $pst, config('pst.default_notification_way'), $single_data, []);
            //或许还可以选参与人--参与人员的数据存储--待定
            DB::commit();
            return json_encode(['status' => 'success', 'message' => '指派人员成功']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return json_encode(['status' => 'fail', 'message' => '指派出错']);
        }
    }
    /**
     * 转移负责人事件的操作处理
     * @param array $data:所需要的前端数据
     * @param Pst $pst:目标评审通
     * @param int $operate_id:操作人员id
     * @param int $company_id:操作人员所属的company_id
     * @return string
     */
    public static function transfer_duty_operate(array $data, Pst $pst, int $operate_id, int $company_id)
    {
        //拿到目标操作用户
        $user = User::find($operate_id);
        //若是负责人，并且接收状态不为 待接收，拒绝接收时
        $state1 = (json_decode($pst->duty_user_id) == $user->id)
            && (json_encode($pst->duty_receive_state) != config('pst.state.wait_receive '))
            && (json_encode($pst->duty_receive_state) != config('pst.state.refuse_receive '));
        //判断是否有指派负责人的权限
        $state2 = RoleAndPerTool::user_has_c_per($operate_id, $company_id, [], 'any');
        if ($state1 || $state2) {
            DB::beginTransaction();
            try {
                //需要更新的数据组
                $up_data = [];
                //获取传递过来的负责人id
                $duty_user_id = FunctionTool::decrypt_id($data['duty_user_id']);
                //变更评审通的状态为待接收(此时为负责人待接收)--在分类展示评审通的时候需要对所有人员的接收信息进行汇总展示
                $up_data['state'] = config('pst.state.wait_receive');
                $up_data['transfer_duty_data'] = json_encode([
                    'duty_user_id' => $duty_user_id,
                    'duty_receive_state' => config('pst.state.wait_receive'),
                    'duty_form_data' => [], //转移负责人员的表单数据
                ]); //负责人数据,初始状态为待接收
                //更新数据
                PstRepository::updatePst($pst->id, $up_data);
                //追加评审通相应的记录
                PstRepository::addPstOperateRecord([
                    'pst_id' => $pst->id,
                    'company_id' => $pst->company_id,
                    'type' => config('pst.operate_type.transfer_duty'),
                    'operate_user_id' => $user->id,
                    'info' => $user->name . ' 将负责人转移给 ' . User::find($duty_user_id)->name,
                ]);
                //通知相关负责人
                $single_data = DynamicTool::getSingleListData(
                    Pst::class,
                    1,
                    'company_id',
                    $pst->company_id,
                    '评审通:' . $user->name . ' 将评审通负责人转移给你',
                    '评审通负责人转移通知',
                    $pst->created_at
                );
                NotifyTool::publishNotify([$duty_user_id], $pst->company_id, $pst, config('pst.default_notification_way'), $single_data, []);
                DB::commit();
                return json_encode(['status' => 'success', 'message' => '已发送移交申请']);
            } catch (\Exception $e) {
                DB::rollBack();
                dd($e);
                return json_encode(['status' => 'fail', 'message' => '转移负责人出错']);
            }
        } else {
            return json_encode(['status' => 'success', 'message' => '不是负责人']);
        }
    }
    /**
     * 移交事件的操作处理
     * @param array $data
     * @param Pst $pst
     * @param int $operate_id
     * @param int $company_id
     * @return string
     */
    public static function transfer_join_operate(array $data, Pst $pst, int $operate_id, int $company_id)
    {
        //验证操作用户是否是参与人--参与人可以转移参与人
        $user = User::find($operate_id);
        //若是参与人，并且接收状态不是拒绝接收状态就能够进行参与人转移
        //拿到内部参与人数组
        $inside_join_ids = json_decode($pst->inside_user_ids, true);
        //拿到内部参与人接收状态的数组
        $inside_receive_state = json_decode($pst->inside_receive_state, true);
        //判断该用户是否为内部参与人&&接收状态不是待接收 或者 拒绝接收状态
        $state1 = (in_array($user->id, $inside_join_ids) && ($inside_receive_state['state_' . $user->id] != config('pst.state.wait_receive'))
            && ($inside_receive_state['state_' . $user->id] != config('pst.state.refuse_receive')));
        //看有相关权限的人员是否能进行人员转移--(待定)

        if ($state1) {
            DB::beginTransaction();
            try {
                //获取传递过来的参与人id
                $tranfser_user_id = FunctionTool::decrypt_id($data['tranfser_user_id']); //
                //获取传递过来的被转移人员的表单数据--只需要所选人员的表单数据信息(主要进行表单信息的更新)
                $transfer_join_form = $data['transfer_join_form'];
                //拿到被转移参与人json数据
                $transfer_join_data = $pst->transfer_join_data;
                //判断是否有某个参与人的转移记录
                if (!array_key_exists($user->id, $transfer_join_data)) { //将被参与人数据更新进去
                    $up_data = json_encode([
                        'transfer_join_data' => [
                            'duty_user_id' => $tranfser_user_id,
                            'duty_receive_state' => config('pst.state.wait_receive'),
                            'duty_form_data' => $transfer_join_form,
                        ],
                    ]); //更新数据
                    PstRepository::updatePst($pst->id, $up_data); //通知被转移人员
                    //追加评审通相应的记录
                    PstRepository::addPstOperateRecord([
                        'pst_id' => $pst->id,
                        'company_id' => $pst->company_id,
                        'type' => config('pst.operate_type.transfer_join'),
                        'operate_user_id' => $user->id,
                        'info' => $user->name . ' 将参与人转移给 ' . User::find($tranfser_user_id)->name,
                    ]);
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $company_id,
                        '评审通:' . $user->name . ' 将评审通参与人转移给你',
                        '评审通参与人转移通知',
                        $pst->created_at
                    );
                    NotifyTool::publishNotify([$tranfser_user_id], $company_id, $pst, config('pst.default_notification_way'), $single_data, []);
                    return json_encode(['status' => 'success', 'message' => '转移参与人成功']);
                } else {
                    return json_encode(['status' => 'fail', 'message' => '您已经转移过了']);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return json_encode(['status' => 'fail', 'message' => '指派出错']);
            }
        } else {
            return json_encode(['status' => 'success', 'message' => '不是参与人']);
        }
    }
    /**
     * 递交事件的操作处理
     * @param array $data
     * @param Pst $pst
     * @param int $operate_id
     * @param int $company_id
     * @throws \ReflectionException
     */
    public static function deliver_operate(array $data, Pst $pst, int $operate_id, int $company_id)
    {

        //操作人
        $user = User::find($operate_id);
        $origin_company = Company::find($company_id);
        $duty_user_id = json_decode($pst->duty_user_id); //负责人id
        $duty_receive_state = json_decode($pst->duty_receive_state); //负责人接收状态

        $inside_user_ids = FunctionTool::decrypt_id_array(json_decode($data['join_user_data'],true)['checkedIds']['organizational']);
        $company_partner_ids = FunctionTool::decrypt_id_array(json_decode($data['join_user_data'],true)['checkedIds']['partner']);
        $outside_user_ids = FunctionTool::decrypt_id_array(json_decode($data['join_user_data'],true)['checkedIds']['externalContact']);
        //有高级权限 负责人 可递交
        if (($duty_user_id == $operate_id) && ($duty_receive_state == config('pst.state.received'))) {
            $origin_inside_join_ids = json_decode($pst->inside_user_ids, true); //拿到源内部参与人id组
            $origin_inside_receive_state = json_decode($pst->inside_receive_state, true); //源内部参与人接收状态组
            //判断所有的内部参与人状态全部为已完成--否则不让递交
            $state1 = true;
            foreach ($origin_inside_receive_state as $state) {
                if ($state != config('pst.state.finish')) {
                    $state1 = false;
                    break;
                }
            }
            if (!$state1) {
                return json_encode(['status' => 'fail', 'message' => '内部参与人存在没有完成的']);
            }
            //对递交的内部参与人的处理--前端传递
//            $inside_user_id = $data['inside_user_id'];
//            $inside_user_id = [1, 2, 3];
            if(!empty($inside_user_ids)){
                foreach ($inside_user_ids as $user_id) {
                    //判断是否已经是参与人了
                    if (!in_array($user_id, $origin_inside_join_ids)) {
                        //将所选的递交内部参与人id加入到已有的id组中并设置初始状态
                        $origin_inside_join_ids[] = $user_id;
                        $origin_inside_receive_state['state_' . $user_id] = config('pst.state.wait_receive');

                        //并进行递交通知
                        $single_data = DynamicTool::getSingleListData(
                            Pst::class,
                            1,
                            'company_id',
                            $company_id,
                            '评审通:递交提醒',
                            '递交给你一个评审通',
                            $pst->created_at
                        );
                        NotifyTool::publishNotify([$user_id], $company_id, $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序
                    }
                }
                PstRepository::updatePst($pst->id,[
                    'join_user_data->join_user_ids->inside_user_ids'=>$origin_inside_join_ids,
                    'join_user_data->join_user_ids->inside_receive_state'=>$origin_inside_receive_state
                ]);
            }


            //获取前端传递的合作伙伴信息
//            $company_partner_ids = json_decode($data['company_partner_ids'],true);
//            $company_partner_ids = [1, 2, 3];
            //获取源合作伙伴id组
            $origin_company_partner_ids = json_decode($pst->company_partner_ids,true);
            //复制评审通源数据传递出去
            $data = [
                'last_pst_id' => $pst->id, //上级评审通id
                'template_id' => 0,
                'publish_user_id' => 0, //发起人id
                'company_id' => 0, //所属企业的id
                'state' => config('pst.state.wait_receive'), //评审状态待接收
                'need_approval' => 1, //相关是否需要审批标识-传递到下一级默认需要审批
                'removed' => 0, //软删除标识
                'form_template' => $pst->form_template, //表单数据
                'form_values' => $pst->form_values, //所需要的数据 k-v对
                'process_template' => json_encode([]), //审批流程人员信息-下级重新选择
                'approval_method' => null, //审批流程人员信息
                'origin_data' => json_encode([]), //上一环传递过来的东西--暂定
                'join_user_data' => null, //参与人信息--下级传递需重新选择
                'duty_user_data' => null, //负责人数据
                'cc_user_data' => null, //抄送人员相关数据--下级传递需重新选择
                'allow_user_ids' => json_encode([])
            ];
            //拿到关联文件
            $files = $pst->files;
            //对递交的合作伙伴的处理
            if(!empty($company_partner_ids)){
                foreach ($company_partner_ids as $company_id) {
                    if (!in_array($company_id, $origin_company_partner_ids)) {
                        //添加目标合作伙伴至相应数组中
                        $origin_company_partner_ids[] = $company_id;
                        //处理合作伙伴表单信息
                        //先确定通知合作伙伴企业中的哪些用户
                        $data['company_id'] = $company_id;
                        $new_pst = Pst::create($data);
                        //复制附件至对应的企业
                        self::copyFileToLastCompany($files, $company_id, config('pst.oss_directory'), [
                            'model_id' => $new_pst->id,
                            'model_type' => gettype($new_pst)
                        ]);

                        //进行该企业的通知
                        //调取企业下拥有评审通外部数据接收权的用户id
                        $user_ids = RoleAndPerTool::get_company_target_per_users($company_id, []);
                        $single_data = DynamicTool::getSingleListData(
                            Pst::class,
                            1,
                            'company_id',
                            $data['company_id'],
                            '评审通:提醒' . $origin_company->name . '发起一个评审通需要你的参与',
                            '评审通参与邀请',
                            $pst->created_at
                        );
                        NotifyTool::publishNotify($user_ids, $data['company_id'], $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序
                    }
                }
                //更新源评审通数据
                PstRepository::updatePst($pst->id,[
                    'join_user_data->join_user_ids->company_partner_ids'=>$origin_company_partner_ids,
                ]);
            }

            //外部联系人的处理
//            $outside_user_ids = json_decode($data['outside_user_ids']);
//            $outside_user_ids = [1, 2, 3];
            //获取源外部联系人
            $origin_outside_user_ids = json_decode($pst->outside_user_ids);
            $data['company_id'] = 0; //数据格式变更为外部联系人推送的格式
            if(!empty($outside_user_ids)){
                foreach ($outside_user_ids as $user_id) {
                    $origin_outside_user_ids[] = $user_id;
                    //复制评审通源数据传递出去--先变更成外部联系人的格式
                    $data['outside_user_id'] = $user_id;

                    Pst::create($data);
                    //通知外部联系人
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class . 'pst_beside',
                        1,
                        'company_id',
                        $data['company_id'],
                        '评审通:递交提醒',
                        $origin_company->name . '递交给你一个评审通',
                        $pst->created_at
                    );
                    NotifyTool::publishNotify($outside_user_ids, $data['company_id'], $pst, config('pst.default_notification_way'), $single_data, [], Pst::class . 'pst_beside'); //此处方法顺序待调整
                }
                //追加评审通相应的记录
                PstRepository::addPstOperateRecord([
                    'pst_id' => $pst->id,
                    'company_id' => $pst->company_id,
                    'type' => config('pst.operate_type.deliver'),
                    'operate_user_id' => $user->id,
                    'info' => $user->name . '进行递交操作',
                ]);
                //更新源评审通数据
                PstRepository::updatePst($pst->id,[
                    'join_user_data->join_user_ids->outside_user_ids'=>$origin_company_partner_ids,
                ]);
            }

        }
    }
    /**
     * 召回事件的操作处理
     * @param array $data
     * @param Pst $pst
     * @param int $operate_id
     * @param int $company_id
     * @return string
     */
    public static function recall_operate(array $data, Pst $pst, int $operate_id, int $company_id)
    {
        $user = User::find($operate_id);
        //若是负责人，则可以进行召回--有高级权限也可进行召回
        $state1 = json_decode($pst->duty_user_id) == $operate_id;
        //有没有高级权限
        $state2 = RoleAndPerTool::user_has_c_per($operate_id, $company_id, [], 'any');

        if ($state1 && $state2) {
            //设置当前评审通为召回状态
            PstRepository::updatePst($pst->id,config('pst.state.recall'));
            //召回所有子项目,并进行递归标记召回
            PstRepository::markChildrenTargetState($pst->id, config('pst.state.recall')); //是否通知待定
            //追加评审通相应的记录
            PstRepository::addPstOperateRecord([
                'pst_id' => $pst->id,
                'company_id' => $pst->company_id,
                'type' => config('pst.operate_type.recall'),
                'operate_user_id' => $user->id,
                'info' => $user->name . '进行召回操作',
            ]);
            return json_encode(['status' => 'success', 'message' => '召回标记成功']);
        } else {
            return json_encode(['status' => 'fail', 'message' => '没有权限']);
        }
    }
    /**
     * 作废按钮事件的操作处理
     * @param array $data
     * @param Pst $pst
     * @param int $operate_id
     * @param int $company_id
     * @return string
     */
    public static function cancle_operate(array $data, Pst $pst, int $operate_id, int $company_id)
    {
        $user = User::find($operate_id);
        //若是负责人，则可以进行作废
        $state1 = json_decode($pst->duty_user_id) == $operate_id;
        //有没有高级权限
        $state2 = RoleAndPerTool::user_has_c_per($operate_id, $pst->company_id, [], 'any');
        if ($state2 || $state1) {
            //判断本项目是否已经为作废状态
            $psts = PstRepository::getPstTargetValue(['state'],$pst->id);
            if($psts->state == config('pst.state.cancled')) {
                return json_encode(['status' =>'fail', 'message'=>'该评审已作废']);
            }
                //标记本项目为作废状态
                PstRepository::updatePst($pst->id, ['state' => config('pst.state.cancled')]);
                //作废所有子项目,并进行递归标记作废
                PstRepository::markChildrenTargetState($pst->id, config('pst.state.cancled')); //是否通知待定
                //追加评审通相应的记录
                PstRepository::addPstOperateRecord([
                    'pst_id' => $pst->id,
                    'company_id' => $pst->company_id,
                    'type' => config('pst.operate_type.cancle'),
                    'operate_user_id' => $user->id,
                    'info' => $user->name . '进行作废操作',
                ]);
            return json_encode(['status' => 'success', 'message' => '作废标记成功']);
        } else {
            return json_encode(['status' => 'fail', 'message' => '没有权限标记']);
        }
    }
    /**
     * 打回按钮事件的操作处理
     * @param array $data
     * @param Pst $pst
     * @param int $operate_id
     * @param int $company_id
     * @return string
     */
    public static function back_operate(array $data, Pst $pst, int $operate_id, int $company_id)
    {
        $user = User::find($operate_id);
        //拿到内部参与人数组
        $inside_user_ids = json_decode($pst->inside_user_ids, true);
        $inside_receive_state = json_decode($pst->inside_receive_state, true); //内部参与人接收状态
        $duty_user_id = json_decode($pst->duty_user_id); //拿到负责人id 被转移负责人id
        $transfer_duty_id = json_decode($pst->transfer_duty_id); //被转移负责人id
        $pst_id = $pst->id;
        //拿到打回的类型标识
        $type = $data['type'];
        //分发打回处理请求
        switch ($type) {
                //代表评审通自身打回(源)--好像不太可能出现这种情况
            case config('pst.user_type.pst'):

                break;
                //代表评审通自身打回(合作伙伴)
            case config('pst.user_type.company_partner'):
                //状态改为打回
                PstRepository::updatePst($pst_id, ['state' => config('pst.state.back')]);
                //告诉上级负责人---xxx合作伙伴已经接收
                $single_data = DynamicTool::getSingleListData(
                    Pst::class,
                    1,
                    'company_id',
                    $pst->company_id,
                    '评审通:进度提醒 ' . Company::find($pst->company_id)->name . ',打回了你发起的评审通',
                    '评审通提醒',
                    $pst->created_at
                );
                NotifyTool::publishNotify([$pst->duty_user_id], $pst->last_pst_id, Pst::find($pst->last_pst_id), config('pst.default_notification_way'), $single_data, []);
                return json_encode(['status' => 'success', 'message' => '打回成功']);
                break;
                //代表评审通自身接收(外部联系人)
            case config('pst.user_type.outside_user'):
                //状态改为打回
                PstRepository::updatePst($pst_id, ['state' => config('pst.state.back')]);
                //告诉上级负责人---xxx已经接收
                $single_data = DynamicTool::getSingleListData(
                    Pst::class,
                    1,
                    'company_id',
                    $pst->company_id,
                    '评审通:进度提醒 ' . User::find($pst->outer_sider_id)->name . ',打回了你发起的评审通',
                    '评审通提醒',
                    $pst->created_at
                );
                NotifyTool::publishNotify([$pst->duty_user_id], $pst->last_pst_id, Pst::find($pst->last_pst_id), config('pst.default_notification_way'), $single_data, []);
                return json_encode(['status' => 'success', 'message' => '打回成功']);
                break;
                //参与人
            case config('pst.user_type.inside_join_user'):
                //需要改变的参与人id
                $join_user_id = FunctionTool::decrypt_id('target_id');
                //改变参与人对应的接收状态
                PstRepository::updatePst($pst_id, ['join_user_data->join_user_ids->inside_receive_state->' . 'state_' . $join_user_id => config('pst.state.back')]);
                //通知参与人人
                $single_data = DynamicTool::getSingleListData(
                    Pst::class,
                    1,
                    'company_id',
                    $pst->company_id,
                    '评审通:进度提醒',
                    User::find($user->id)->name . ',打回了评审通',
                    $pst->created_at
                );
                NotifyTool::publishNotify([$join_user_id], $pst->id, $pst, config('pst.default_notification_way'), $single_data, []);

                return json_encode(['status' => 'success', 'message' => '参与人提交打回成功']);
                break;
            default:
                return json_encode(['status' => 'fail', 'message' => '没有对应的接收类型']);
                break;
        }
    }
    /**
     * 撤回事件的操作处理
     * @param array $data
     * @param Pst $pst
     * @param int $operate_id
     * @param int $company_id
     * @return string
     */
    public static function retract_operate(array $data, Pst $pst, int $operate_id, int $company_id)
    {
        $user = User::find($operate_id);
        //若是负责人，则可以进行撤回
        $state1 = $pst->publish_user_id == $operate_id;
        //有没有高级权限
        $state2 = RoleAndPerTool::user_has_c_per($operate_id, $company_id, [], 'any');
        if ($state2 || $state1) {
            //标记本项目为作废状态
            PstRepository::updatePst($pst->id, ['state' => config('pst.state.retracted')]);
            //作废所有子项目,并进行递归标记作废
            PstRepository::markChildrenTargetState($pst->id, config('pst.state.retracted'));
            //是否通知待定

            //追加评审通相应的记录
            PstRepository::addPstOperateRecord([
                'pst_id' => $pst->id,
                'company_id' => $pst->company_id,
                'type' => config('pst.operate_type.retract'),
                'operate_user_id' => $user->id,
                'info' => $user->name . '进行撤回操作',
            ]);
//            //通知本项目参与人
//            $single_data = DynamicTool::getSingleListData(
//                Pst::class,
//                1,
//                'company_id',
//                $company_id,
//                '评审通:' . $user->name . ' 将评审通撤回',
//                '评审通撤回通知',
//                $pst->created_at
//            );
//            NotifyTool::publishNotify([], $company_id, $pst, config('pst.default_notification_way'), $single_data, []);
            return json_encode(['status' => 'success', 'message' => '撤回标记成功']);
        }
    }
    /**
     * 完成-按钮的状态判断
     * @param array $data
     * @param Pst $pst
     * @param int $operate_id
     * @param int $company_id
     */
    public static function finish_operate(array $data, Pst $pst, int $operate_id, int $company_id)
    {
        $user = User::find($operate_id);
        //拿到内部参与人数组
        $inside_user_ids = json_decode($pst->inside_user_ids, true);
        //内部参与人接收状态
        $inside_receive_state = json_decode($pst->inside_receive_state, true);
        //先判断是否是内部负责人-若是则做出对应的操作
        if (in_array($operate_id, $inside_user_ids)) {
            //获取目标内部参与人的接收状态
            $user_state = $inside_receive_state['state_' . $operate_id];
            //判断内部参与人的接收状态
            if (
                ($user_state != config('pst.state.wait_receive')) && ($user_state != config('pst.state.refuse_receive')) && ($user_state != config('pst.state.finish'))
            ) {
                //前端传递过来的内部参与人表单信息
                $form_data = $data['form_data'];
                //更新内部参与人的表单信息
                $record = PstRepository::getPstTargetValue(['join_pst_form_data'], $pst->id);
                $join_pst_form_data = json_decode($record->join_pst_form_data, true);
                $join_pst_form_data['form_' . $user->id] = [
                    'form_data' => $form_data,
                    'opinion' => $data['complete_summary']
                ];
                //标记内部参与人状态为已完成状态&&替换参与人的表单信息
                PstRepository::updatePst($pst->id, [
                    'join_user_data->join_user_ids->inside_receive_state->' . 'state_' . $operate_id => config('pst.state.wait_approval'),
                    'join_pst_form_data' => json_encode($join_pst_form_data),
                ]);
                self::updatePstCurrentHandler(['pst_id'=>$pst->id]);
                return json_encode(['status' => 'success', 'message' => '参与人完成状态标记成功']);
            }
        }
        return json_encode(['status' => 'fail', 'message' => '未查到指定数据']);
    }
    /**
     * 归档-按钮的状态判断
     * @param int $user_id:目标用户id
     * @param $pst:目标评审通
     * @param array $btn_status:按钮状态数组
     */
    public static function archive_operate(array $data, Pst $pst, int $operate_id, int $company_id)
    {
        $user = User::find($operate_id);
        //若是负责人，则可以进行撤回
        $is_duty_user = json_decode($pst->duty_user_id) == $operate_id;
        //有没有高级权限
        $state2 = RoleAndPerTool::user_has_c_per($operate_id, $company_id, [], 'any');
        if ($is_duty_user || $state2) { // 暂时把 && 改为 || 把逻辑往下进行, 以后再研究是否合适
            //改变评审通状态为已完成
            PstRepository::updatePst($pst->id, ['state' => config('pst.state.archived')]);
            //追加评审通相应的记录
            PstRepository::addPstOperateRecord([
                'pst_id' => $pst->id,
                'company_id' => $pst->company_id,
                'type' => config('pst.operate_type.archive'),
                'operate_user_id' => $user->id,
                'info' => $user->name . '进行完成操作',
            ]);
            return json_encode(['status' => 'success', 'message' => '归档操作成功']);
        } else {
            return json_encode(['status' => 'fail', 'message' => '没有相关操作权限']);
        }
    }
    //===============================评审通关联审批相关==========================================================>
    /**
     * 查询出某评审通所关联的审批(需要分页)
     * @return mixed
     */
    public function  getPstRelationApproval(array $data)
    {
        $pst_id = FunctionTool::decrypt_id($data['pst_id']); //目标评审通id
        $now_page = array_get($data, 'now_page', 1); //当前页
        $page_size = array_get($data, 'page_size', 10);
        $data = PstRepository::getPstRelationApproval($pst_id, $now_page, $page_size);
        return json_encode([
            'status' => 'success',
            'page_count' => $data['page_count'],
            'page_size' => $page_size,
            'now_page' => $now_page,
            'all_count' => $data['count'],
            'data' => ApprovalResource::collection($data['data']),
        ]);
    }
    /**
     * 移除某审批与评审通的关联待定
     * @return mixed
     */
    public function  removePstApprovalRelation(array $data)
    { }
    //===============================评审通关联自身相关==========================================================>
    /**
     * 查询出某评审通所关联的评审(需要分页)
     * @return mixed
     */
    public function  getPstSelfRelation(array $data)
    {
        $pst_id = FunctionTool::decrypt_id($data['pst_id']); //目标评审通id
        $now_page = array_get($data, 'now_page', 1); //当前页
        $page_size = array_get($data, 'page_size', 10);
        $data = PstRepository::getPstSelfRelation($pst_id, $now_page, $page_size);
        return json_encode([
            'status' => 'success',
            'page_count' => $data['page_count'],
            'page_size' => $page_size,
            'now_page' => $now_page,
            'all_count' => $data['count'],
            'data' => PstListResource::collection($data['data']),
        ]);
    }
    /**
     * 移除某审批与评审通的关联待定
     * @return mixed
     */
    public function  removePstSelfRelation(array $data)
    {
        $target_pst_id = $data['target_pst_id'];
        $related_pst_id = $data['related_pst_id'];
        DB::table('pst_self_related')
            ->where('target_pst_id', $target_pst_id)
            ->where('related_pst_id', $related_pst_id)
            ->delete();
        return json_encode(['status' => 'success', 'message' => '移除关联成功']);
    }
    /**
     * 查询出某评审通所关联的评审(需要分页)
     * @return mixed
     */
    public function  getCanRelationPst(array $data)
    {
        $user = auth('api')->user();
        $company_id = $user->current_company_id; //目标企业id
        $now_page = array_get($data, 'now_page', 1); //当前页
        $page_size = array_get($data, 'page_size', 10);
        $data = PstRepository::getCanRelationPst($company_id, $now_page, $page_size);
        return json_encode([
            'status' => 'success',
            'page_count' => $data['page_count'],
            'page_size' => $page_size,
            'now_page' => $now_page,
            'all_count' => $data['count'],
            'data' => PstListResource::collection($data['data']),
        ]);
    }
    //==============================评审通公用内部方法=========================================================>
    /**
     * 评审通发起审批的回调方法
     * @param array $data:评审通中调起审批时所传递过去的额外数据
     */
    public static function approvalCallBack(array $data)
    {
        $pst_id = $data['pst_id'];
        $state = $data['state'];
        //获取指定的评审通记录--调取必要的数据
        $pst = PstRepository::getPstOperateData($pst_id);
        //分发相应的评审通标识
        switch ($state) {
                //处理审批评通过审通开始时的审批--都包含同意or不同意两种结果
            case 'begin_start':
                //改变评审通的状态
                if ($data['callback_result']) {
                    //将评审通状态由待审核变为评审中
                    $pst->state = config('pst.state.under_way');
                    $pst->save();
                    //处理评审通发起操作
                    //负责人的处理--通知
                    self::handleDutyUser($pst);
                    //追加评审通相应的记录
                    if($pst->need_approval){
                        PstRepository::addPstOperateRecord([
                            'pst_id' => $pst->id,
                            'company_id' => $pst->company_id,
                            'type' => config('pst.operate_type.approval_type.agree_begin_start'),
                            'operate_user_id' => $data['operate_id'],
                            'info' => '评审发起审批通过',
                        ]);
                    }
                    //通知发起人
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $pst->company_id,
                        '评审通:' . '进度提醒',
                        User::find($pst->publish_user_id)->name . '您所建的评审通,已经通过审批,处于待接收状态',
                        $pst->created_at
                    );
                    NotifyTool::publishNotify([$pst->publish_user_id], $pst->company_id, $pst, config('pst.default_notification_way'), $single_data, []);
                } else {
                    $pst->state = config('pst.state.approval_refuse');
                    $pst->save();
                    //追加评审通相应的记录
                    PstRepository::addPstOperateRecord([
                        'pst_id' => $pst->id,
                        'company_id' => $pst->company_id,
                        'type' => config('pst.operate_type.approval_type.refuse_begin_start'),
                        'operate_user_id' => $data['operate_id'],
                        'info' => '评审发起审批未通过',
                    ]);
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $pst->company_id,
                        '评审通:' . (is_null($pst->project_name) ? '' : $pst->project_name) . '审批结果',
                        '评申通发起申请未通过:' . $data['opinion'],
                        $pst->created_at
                    );
                    NotifyTool::publishNotify([$pst->publish_user_id], $pst->company_id, $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序待调整
                }
                break;
                //指派事件审批回调
            case 'appoint':
                //判断审批是否通过
                if ($data['callback_result']) {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    if($pst->need_approval) {
                        //追加评审通相应的记录
                        PstRepository::addPstOperateRecord([
                            'pst_id' => $pst->id,
                            'company_id' => $pst->company_id,
                            'type' => config('pst.operate_type.approval_type.agree_appoint'),
                            'operate_user_id' => $data['operate_id'],
                            'info' => '评审指派审批通过',
                        ]);
                    }
                    //调起审批事件处理函数
                    self::appoint_operate($data['data'], $pst, $data['operate_id'], $pst->company_id);
                } else {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    //追加评审通相应的记录
                    PstRepository::addPstOperateRecord([
                        'pst_id' => $pst->id,
                        'company_id' => $pst->company_id,
                        'type' => config('pst.operate_type.approval_type.refuse_appoint'),
                        'operate_user_id' => $data['operate_id'],
                        'info' => '评审指派审批未通过',
                    ]);
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $pst->company_id,
                        '评审通:' . (is_null($pst->project_name) ? '' : $pst->project_name) . '审批结果',
                        '评申通指派申请未通过:' . $data['opinion'],
                        date('Y-m-d H:i:s', time())
                    );
                    NotifyTool::publishNotify([$pst->publish_user_id], $pst->company_id, $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序待调整
                }
                break;
                //转移负责人审批回调
            case 'transfer_duty':
                if ($data['callback_result']) {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    if($pst->need_approval) {

                        //追加评审通相应的记录
                        PstRepository::addPstOperateRecord([
                            'pst_id' => $pst->id,
                            'company_id' => $pst->company_id,
                            'type' => config('pst.operate_type.approval_type.agree_transfer_duty'),
                            'operate_user_id' => $data['operate_id'],
                            'info' => '转移负责人审批通过',
                        ]);
                    }
                    //调起审批事件处理函数
                    self::transfer_duty_operate($data['data'], $pst, $data['operate_id'], $pst->company_id);
                } else {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    if($pst->need_approval) {

                        //追加评审通相应的记录
                        PstRepository::addPstOperateRecord([
                            'pst_id' => $pst->id,
                            'company_id' => $pst->company_id,
                            'type' => config('pst.operate_type.approval_type.refuse_transfer_duty'),
                            'operate_user_id' => $data['operate_id'],
                            'info' => '转移负责人审批未通过',
                        ]);
                    }
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $pst->company_id,
                        '评审通:' . (is_null($pst->project_name) ? '' : $pst->project_name) . '审批结果',
                        '评审转移负责人申请未通过:' . $data['opinion'],
                        date('Y-m-d H:i:s', time())
                    );
                    NotifyTool::publishNotify([$pst->publish_user_id], $pst->company_id, $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序待调整
                }
                break;
                //转移参与人审批回调
            case 'transfer_join':
                if ($data['callback_result']) {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    if($pst->need_approval) {

                        //追加评审通相应的记录
                        PstRepository::addPstOperateRecord([
                            'pst_id' => $pst->id,
                            'company_id' => $pst->company_id,
                            'type' => config('pst.operate_type.approval_type.agree_transfer_join'),
                            'operate_user_id' => $data['operate_id'],
                            'info' => '转移参与人审批通过',
                        ]);
                    }
                    //调起审批事件处理函数
                    self::transfer_join_operate($data['data'], $pst, $data['operate_id'], $pst->company_id);
                } else {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    //追加评审通相应的记录
                    PstRepository::addPstOperateRecord([
                        'pst_id' => $pst->id,
                        'company_id' => $pst->company_id,
                        'type' => config('pst.operate_type.approval_type.refuse_transfer_join'),
                        'operate_user_id' => $data['operate_id'],
                        'info' => '转移参与人审批未通过',
                    ]);
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $pst->company_id,
                        '评审通:' . (is_null($pst->project_name) ? '' : $pst->project_name) . '审批结果',
                        '评申转移参与人申请未通过:' . $data['opinion'],
                        date('Y-m-d H:i:s', time())
                    );
                    NotifyTool::publishNotify([$pst->publish_user_id], $pst->company_id, $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序待调整
                }
                break;
            case 'deliver':
                if ($data['callback_result']) {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    if($pst->need_approval) {

                        //追加评审通相应的记录
                        PstRepository::addPstOperateRecord([
                            'pst_id' => $pst->id,
                            'company_id' => $pst->company_id,
                            'type' => config('pst.operate_type.approval_type.agree_deliver'),
                            'operate_user_id' => $data['operate_id'],
                            'info' => '递交审批通过',
                        ]);
                    }
                    //调起审批事件处理函数
                    self::deliver_operate($data['data'], $pst, $data['operate_id'], $pst->company_id);
                } else {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    //追加评审通相应的记录
                    PstRepository::addPstOperateRecord([
                        'pst_id' => $pst->id,
                        'company_id' => $pst->company_id,
                        'type' => config('pst.operate_type.approval_type.refuse_deliver'),
                        'operate_user_id' => $data['operate_id'],
                        'info' => '递交审批未通过',
                    ]);
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $pst->company_id,
                        '评审通:' . (is_null($pst->project_name) ? '' : $pst->project_name) . '审批结果',
                        '评申转移递交申请未通过:' . $data['opinion'],
                        date('Y-m-d H:i:s', time())
                    );
                    NotifyTool::publishNotify([$pst->publish_user_id], $pst->company_id, $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序待调整
                }
                break;
            case 'recall':
                if ($data['callback_result']) {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    if($pst->need_approval) {

                        //追加评审通相应的记录
                        PstRepository::addPstOperateRecord([
                            'pst_id' => $pst->id,
                            'company_id' => $pst->company_id,
                            'type' => config('pst.operate_type.approval_type.agree_recall'),
                            'operate_user_id' => $data['operate_id'],
                            'info' => '召回审批通过',
                        ]);
                    }
                    //调起审批事件处理函数
                    self::recall_operate($data['data'], $pst, $data['operate_id'], $pst->company_id);
                } else {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    //追加评审通相应的记录
                    PstRepository::addPstOperateRecord([
                        'pst_id' => $pst->id,
                        'company_id' => $pst->company_id,
                        'type' => config('pst.operate_type.approval_type.refuse_recall'),
                        'operate_user_id' => $data['operate_id'],
                        'info' => '召回审批未通过',
                    ]);
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $pst->company_id,
                        '评审通:' . (is_null($pst->project_name) ? '' : $pst->project_name) . '审批结果',
                        '评申转移召回申请未通过:' . $data['opinion'],
                        date('Y-m-d H:i:s', time())
                    );
                    NotifyTool::publishNotify([$pst->publish_user_id], $pst->company_id, $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序待调整
                }
                break;
            case 'cancle':
                if ($data['callback_result']) {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    if($pst->need_approval) {

                        //追加评审通相应的记录
                        PstRepository::addPstOperateRecord([
                            'pst_id' => $pst->id,
                            'company_id' => $pst->company_id,
                            'type' => config('pst.operate_type.approval_type.agree_cancle'),
                            'operate_user_id' => $data['operate_id'],
                            'info' => '作废审批通过',
                        ]);
                    }
                    //调起审批事件处理函数
                    self::cancle_operate($data['data'], $pst, $data['operate_id'], $pst->company_id);
                } else {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $pst->company_id,
                        '评审通:' . (is_null($pst->project_name) ? '' : $pst->project_name) . '审批结果',
                        '评申作废申请未通过:' . $data['opinion'],
                        date('Y-m-d H:i:s', time())
                    );
                    //追加评审通相应的记录
                    PstRepository::addPstOperateRecord([
                        'pst_id' => $pst->id,
                        'company_id' => $pst->company_id,
                        'type' => config('pst.operate_type.approval_type.refuse_cancle'),
                        'operate_user_id' => $data['operate_id'],
                        'info' => '作废审批未通过',
                    ]);
                    NotifyTool::publishNotify([$pst->publish_user_id], $pst->company_id, $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序待调整
                }
                break;
                //打回审批回调
            case 'back':
                if ($data['callback_result']) {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    if($pst->need_approval) {

                        //追加评审通相应的记录
                        PstRepository::addPstOperateRecord([
                            'pst_id' => $pst->id,
                            'company_id' => $pst->company_id,
                            'type' => config('pst.operate_type.approval_type.agree_back'),
                            'operate_user_id' => $data['operate_id'],
                            'info' => '打回审批通过',
                        ]);
                    }
                    //调起审批事件处理函数
                    self::back_operate($data['data'], $pst, $data['operate_id'], $pst->company_id);
                } else {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    //追加评审通相应的记录
                    PstRepository::addPstOperateRecord([
                        'pst_id' => $pst->id,
                        'company_id' => $pst->company_id,
                        'type' => config('pst.operate_type.approval_type.refuse_back'),
                        'operate_user_id' => $data['operate_id'],
                        'info' => '打回审批未通过',
                    ]);
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $pst->company_id,
                        '评审通:' . (is_null($pst->project_name) ? '' : $pst->project_name) . '审批结果',
                        '评申打回申请未通过:' . $data['opinion'],
                        date('Y-m-d H:i:s', time())
                    );
                    NotifyTool::publishNotify([$pst->publish_user_id], $pst->company_id, $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序待调整
                }
                break;
                //撤回审批回调
            case 'retract':
                if ($data['callback_result']) {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    if($pst->need_approval) {
                        //追加评审通相应的记录
                        PstRepository::addPstOperateRecord([
                            'pst_id' => $pst->id,
                            'company_id' => $pst->company_id,
                            'type' => config('pst.operate_type.approval_type.agree_retract'),
                            'operate_user_id' => $data['operate_id'],
                            'info' => '撤回审批通过',
                        ]);
                    }
                    //调起审批事件处理函数
                    self::retract_operate($data['data'], $pst, $data['operate_id'], $pst->company_id);
                } else {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    //追加评审通相应的记录
                    PstRepository::addPstOperateRecord([
                        'pst_id' => $pst->id,
                        'company_id' => $pst->company_id,
                        'type' => config('pst.operate_type.approval_type.refuse_retract'),
                        'operate_user_id' => $data['operate_id'],
                        'info' => '撤回审批未通过',
                    ]);
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $pst->company_id,
                        '评审通:' . (is_null($pst->project_name) ? '' : $pst->project_name) . '审批结果',
                        '评申撤回申请未通过:' . $data['opinion'],
                        date('Y-m-d H:i:s', time())
                    );
                    NotifyTool::publishNotify([$pst->publish_user_id], $pst->company_id, $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序待调整
                }
                break;
            case 'finish': //待定
                if ($data['callback_result']) {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    //调起审批事件处理函数
                    self::finish_operate($data['data'], $pst, $data['operate_id'], $pst->company_id);
                } else {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $pst->company_id,
                        '评审通:' . (is_null($pst->project_name) ? '' : $pst->project_name) . '审批结果',
                        '评申完成申请未通过:' . $data['opinion'],
                        date('Y-m-d H:i:s', time())
                    );
                    NotifyTool::publishNotify([$pst->publish_user_id], $pst->company_id, $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序待调整
                }
                break;
            case 'archive': //待定
                if ($data['callback_result']) {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    //调起审批事件处理函数
                    self::archive_operate($data['data'], $pst, $data['operate_id'], $pst->company_id);
                } else {
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $pst->company_id,
                        '评审通:' . (is_null($pst->project_name) ? '' : $pst->project_name) . '审批结果',
                        '评申归档申请未通过:' . $data['opinion'],
                        date('Y-m-d H:i:s', time())
                    );
                    NotifyTool::publishNotify([$pst->publish_user_id], $pst->company_id, $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序待调整
                }
                break;
            case 'editor'://待定
                if($data['callback_result']){
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    self::editor_operate($data['data'],$pst,$data['operate_id'],$pst->company_id);
                }else{
                    $pst = PstRepository::getPstOperateData($data['pst_id']);
                    $single_data = DynamicTool::getSingleListData(
                        Pst::class,
                        1,
                        'company_id',
                        $pst->company_id,
                        '评审通:' . (is_null($pst->project_name) ? '' : $pst->project_name) . '审批结果',
                        '评审编辑申请未通过:' . $data['opinion'],
                        date('Y-m-d H:i:s', time())
                    );
                    NotifyTool::publishNotify([$pst->publish_user_id], $pst->company_id, $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序待调整
                }
        }
    }
    /**
     * 调起审批模块
     * @param array $data:插入审批中,回调中需要的数据
     * @return mixed
     */
    protected function callApproval(Pst $pst, array $data, User $user)
    {
        //获取审批人员流程数据信息
        $process_template = $pst->process_template;
        //组装审批数据
        $need_data = [
            'name'=>'作废',
            'type_id' => 1, //标识从评审通过来的
            'publish_user_id'=>$pst->publish_user_id,//评审通发起人id
            'process_template' => $process_template,
            'approval_method' => $pst->approval_method, //
            'approval_number' => 'PST', //
            'notification_way' => ['need_notify' => 1],
            'extra_data' => $data, //额外携带的数据--每种评审通回调所需要的数据
            'related_pst_id' => $pst->id, //审批相关的评审通id
            'form_template' => $pst->form_template, //审批展示的表单数据
        ];

        //添加评审通操作记录
        PstRepository::addPstOperateRecord([
            'pst_id' => $pst->id,
            'company_id' => $pst->company_id,
            'type' => config('pst.operate_type.create_approval'),
            'operate_user_id' => $user->id,
            'info' => $user->name . ',发起' . config('pst.operate_type.approval_type' . $data['state']) . '审批'
        ]);
        return $this->approvalTool->createApproval($need_data); //接收创建审批返回数据
        return true;
    }
    /**
     * 检查某个评审通是否可以进行完成操作
     * @param Pst $pst:目标评审通--必须通过仓库类的getPstOperateData()方法获取的
     */
    public static function checkPstCanFinish(Pst $pst, $is_first = true): bool
    {
        //外部联系人评审通记录没有参与人信息
        $state1 = false;
        //标识是否是外部联系人
        $is_outsider = false;
        if (!$is_first) {
            if ($pst->outside_user_id != 0) {
                $is_outsider = true;
            }
            //检查内部参与人的状态是否都为已完成
            if (!$is_outsider) {
                $inside_receive_state = json_decode($pst->inside_receive_state, true);
                foreach ($inside_receive_state as $state) {
                    $s = ($state == config('pst.state.finish'));
                    $state1 = $s;
                }
            }
        }
        //获取子项记录
        $children = PstRepository::getChildren($pst->id);

        //检查是否有子项
        $state2 = false;
        if (count($children) != 0) {
            //循环子项
            foreach ($children as $child) {
                //判断子项的状态是否为--已完成状态
                if ($child->state == config('pst.state.finish')) {
                    $state2 = $state2 && self::checkPstCanFinish($child);
                } else {
                    $state2 = true;
                    break;
                }
            }
            return $state2 && $state1;
        } else {
            return true;
        }
    }
    /**
     *处理评审通项目进行时对参与人员的处理
     * @param array $data:pst中join_user_data数据
     */
    protected static function handleJoinUser(Pst $pst)
    {
        //将属性转为array
        $data = $pst->toArray();
        $created_at = $data['created_at'];
        //获取源公司的信息
        $origin_company = Company::find($data['company_id']);
        $origin_publish_user_id = $data['publish_user_id'];
        //重新拉取一下完整的评审通记录
        $record = Pst::find($pst->id);
        //参与人数据的解析
        $join_user_ids = json_decode($data['join_user_ids'], true);
        //企业内人员的处理
        //判断参与人中是否有发起人  若有的话则接收状态直接变为接收
        $inside_user_ids = $join_user_ids['inside_user_ids'];
        foreach ($inside_user_ids as $key => $id) {
            if ($id = $pst->publish_user_id) {
                $join_pst_form_data['form_' . $id] = [
                    'form_data' => [], //存放参与人表单
                    'opinion' => [], //存放参与人意见
                ];
                //对应的参与人接收状态直接变为接收
                PstRepository::updatePst($pst->id, [
                    'join_user_data->join_user_ids->inside_receive_state->state_' . $id => config('pst.state.received'),
                    'join_pst_form_data' => json_encode($join_pst_form_data),
                ]);
                //移除发布人所占的参与人
                unset($inside_user_ids[$key]);
                $inside_user_ids = array_values($inside_user_ids);
            }
        }
        $single_data = DynamicTool::getSingleListData(
            Pst::class,
            1,
            'company_id',
            $data['company_id'],
            '评审通:' . User::find($data['publish_user_id'])->name . '发起一个评审通需要你的参与',
            '评审通参与邀请',
            $created_at
        );
        NotifyTool::publishNotify($inside_user_ids, $data['company_id'], $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序待调整
        //合作伙伴的处理
        $company_partner_ids = $join_user_ids['company_partner_ids'];
        //复制评审通源数据传递出去
        $data = [
            'last_pst_id' => $pst->id, //上级评审通id
            'template_id' => 0,
            'publish_user_id' => 0, //发起人id
            'company_id' => 0, //所属企业的id
            'state' => config('pst.state.wait_receive'), //评审状态待接收
            'need_approval' => 1, //相关是否需要审批标识-传递到下一级默认需要审批
            'removed' => 0, //软删除标识
            'form_template' => $pst->form_template, //表单数据
            'form_values' => $pst->form_values, //所需要的数据 k-v对
            'process_template' => json_encode([]), //审批流程人员信息-下级重新选择
            'approval_method' => null, //审批流程人员信息
            'origin_data' => json_encode([]), //上一环传递过来的东西--暂定
            'join_user_data' => null, //参与人信息--下级传递需重新选择
            'duty_user_data' => null, //负责人数据
            'cc_user_data' => null, //抄送人员相关数据--下级传递需重新选择
            'allow_user_ids' => json_encode([]),
            'last_duty_user_id' => (int)$pst->duty_user_id, //上级负责人id
        ];
        //获取对应评审通的附件
        $files = $pst->files;
        //循环传递评审通至下级企业
        foreach ($company_partner_ids as $company_id) {
            //先确定通知合作伙伴企业中的哪些用户
            $data['company_id'] = $company_id;
            $new_pst = Pst::create($data);
            //复制附件至对应的企业
            self::copyFileToLastCompany($files, $company_id, config('pst.oss_directory'), [
                'model_id' => $new_pst->id,
                'model_type' => gettype($new_pst)
            ]);
            //进行该企业的通知
            //调取企业下拥有评审通外部数据接收权的用户id
            $user_ids = RoleAndPerTool::get_company_target_per_users($company_id, []);
            $single_data = DynamicTool::getSingleListData(
                Pst::class,
                1,
                'company_id',
                $data['company_id'],
                '评审通:' . $origin_company->name . '发起一个评审通需要你的参与',
                '评审通参与邀请',
                $created_at
            );
            NotifyTool::publishNotify($user_ids, $data['company_id'], $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序待调整
        }
        //外部联系人的处理
        $outside_user_ids = $join_user_ids['outside_user_ids'];
        $data['company_id'] = 0; //数据格式变更为外部联系人推送的格式
        foreach ($outside_user_ids as $user_id) {
            //复制评审通源数据传递出去--先变更成外部联系人的格式
            $data['outside_user_id'] = $user_id;
            Pst::create($data);
            //通知外部联系人
            $single_data = DynamicTool::getSingleListData(
                Pst::class . 'pst_beside',
                1,
                'company_id',
                $data['company_id'],
                '评审通:' . $origin_company->name . '发起一个评审通需要你的参与',
                '评审通参与邀请',
                $created_at
            );
            NotifyTool::publishNotify($outside_user_ids, $data['company_id'], $pst, config('pst.default_notification_way'), $single_data, [], Pst::class . 'pst_beside'); //此处方法顺序待调整
        }
    }
    /**
     *处理评审通项目进行时对抄送人员的处理
     * @param array $data:pst中join_user_data数据
     */
    protected static function handleCcUser(Pst $pst)
    {
        //将属性转为array
        $data = $pst->toArray();
        $created_at = $data['created_at'];
        //获取源公司的信息
        $origin_company = Company::find($data['company_id']);
        //抄送人员id,json解析
        $cc_user_ids = json_decode($data['cc_user_ids'], true);
        //插入抄送人员记录--暂定是否需要待商榷
        $insert_data = [];
        foreach ($cc_user_ids as $user_id) {
            $insert_data[] = [
                'user_id' => $user_id,
                'pst_id' => $pst->id,
                'company_id' => $pst->company_id,
            ];
        }
        //插入评审通抄送记录
        PstRepository::insertCcRecord($insert_data);
        $single_data = DynamicTool::getSingleListData(
            Pst::class,
            1,
            'company_id',
            $data['company_id'],
            '评审通:' . User::find($data['publish_user_id'])->name . '抄送给你一份评审通',
            '评审通参抄送通知',
            $created_at
        );
        NotifyTool::publishNotify($cc_user_ids, $data['company_id'], $pst, config('pst.default_notification_way'), $single_data, []); //此处方法顺序待调整

    }
    /**
     * 处理评审通项目审批完成后对负责人的处理
     * @param Pst $pst:目标评审通记录
     * @throws \ReflectionException
     */
    protected static function handleDutyUser(Pst $pst)
    {
        //将属性转为array
        $data = $pst->toArray();
        $created_at = $data['created_at'];
        //获取源公司的信息
        $origin_company = Company::find($data['company_id']);
        //获取负责人id
        //        preg_match('/^"([0-9]+)"$/',$data['duty_user_id'],$matches);
        $duty_user_id = $data['duty_user_id'];
        //若负责人与发布人为同一个人则直接调用接收方法
        if ($pst->publish_user_id == $duty_user_id) {
            //直接将负责人状态变为接收状态&&不需要通知
            self::receive([
                'type' => config('pst.user_type.duty_user'), //内部负责人标识
                'need_data' => [
                    'pst_id' => FunctionTool::encrypt_id($pst->id), //目标评审通id
                    'target_id' => FunctionTool::encrypt_id($duty_user_id), //负责人id
                ], //需要的额外数据
            ]);
            return;
        }


        $single_data = DynamicTool::getSingleListData(
            Pst::class,
            1,
            'company_id',
            $data['company_id'],
            '评审通:' . User::find($data['publish_user_id'])->name . ' 将你任命为负责人',
            '评审通负责人任命通知',
            $created_at
        );
        NotifyTool::publishNotify([$duty_user_id], $data['company_id'], $pst, config('pst.default_notification_way'), $single_data, []);
    }
    /**
     * 初始化评审通内部接收人状态数组
     * @param array $ids:内部接收人id数组
     * @param $type='join_state/join_form'  标识看初始化参与人状态信息,还是参与人表单信息
     */
    protected  function initInsideUserState(array $ids, $type = 'join_state')
    {
        $data = [];
        if ($type == 'join_state') {
            foreach ($ids as $id) {
                if (isset($data['state_' . $id])) {
                    continue;
                }
                //内部参与人初始为待接收状态
                $data['state_' . $id] = config('pst.state.wait_receive');
            }
        } else {
            foreach ($ids as $id) {
                //初始化参与人的表单提交数据--(基础数据空间)
                if (isset($data['form_' . $id])) {
                    continue;
                }
                //内部参与人初始表单数据
                $data['form_' . $id] = [];
            }
        }
        return $data;
    }
    //======================================企业评审通报告文号相关====================================================================>
     /**
     * 设置/更新  某企业的评审通文号规则
     * @param array $data
     * @return mixed
     */
    public function makeReportNumber(array $data)
    {
        $rule_data = $data['rule_data'];
        // 判断有没有增长值规则
        $start_number = null;
        foreach ($rule_data as $rule) {
            if ($rule['type'] == 'plus') {
                $start_number = $rule['rule']['startNumber'];
                break;
            }
        }
        $user = auth('api')->user();
        $company_id = $user->current_company_id;
        //检查目标企业是否已经有规则信息表记录
        $need_data = [
            'rule_data' => json_encode($rule_data),
            'company_id' => $company_id,
        ];
        if (PstRepository::checkCompanyExistRecord($company_id)) {
            $current_number = PstRepository::getCompanyReportRule($company_id)->current_number;
            // 如果增长值规则->起始值 大于 当前增长到的数字 更新（就是说起始值只能增不能减）
            if ($start_number && $start_number > $current_number) {
                $need_data['current_number'] = $start_number;
            }
            PstRepository::updateReportNumber($company_id, $need_data);
            return ['status' => 'success', 'message' => '规则设定成功'];
        } else {
            $data['company_id'] = $company_id;
            $need_data['current_number'] = $start_number ? $start_number : 1;
            PstRepository::makeReportNumber($need_data);
            return json_encode([
                'status' => 'success',
                'message' => '设置成功',
            ]);
        }
    }
    /** 获取 某企业的评审通文号规则 */
    public function getReportNumber()
    {
        $user = auth('api')->user();
        $company_id = $user->current_company_id;
        return [
            'status' => 'success',
            'data' => json_decode(PstRepository::getCompanyReportRule($company_id)->rule_data)
        ];
    }
    /**
     * 按照企业的文号规则获取下一个文号
     * @param array $data
     * @return mixed
     */
    public static function  getNextReportNumber(int $company_id): string
    {
        //返回的文号
        $report_number = '';
        //判断目标企业是否存在pst报告规则记录
        if (PstRepository::checkCompanyExistRecord($company_id)) {
            //存放规则的子项数组--最后使用implode()拼接即可
            $number_arr = [];
            //取出目标企业的规则记录
            $report_rule = PstRepository::getCompanyReportRule($company_id);
            $rule = json_decode($report_rule->rule_data, true);
            $current_number = $report_rule->current_number;
            //获取时间戳
            $timestramp = time();
            foreach ($rule as $v) {
                switch ($v['type']) {
                        //标签规则处理
                    case 'label':
                        //压入文号子项
                        $number_arr[] = $v['value'];
                        break;
                        //日期规则处理
                    case 'date':
                        //压入文号子项
                        switch ($v['rule']) {
                            case '年':
                                $number_arr[] = date('Y', $timestramp);
                                break;
                            case '年月':
                                $number_arr[] = date('Ym', $timestramp);
                                break;
                            case '年-月':
                                $number_arr[] = date('Y-m', $timestramp);
                                break;
                            case '年月日':
                                $number_arr[] = date('Ymd', $timestramp);
                                break;
                            case '年-月-日':
                                $number_arr[] = date('Y-m-d', $timestramp);
                                break;
                        }
                        break;
                        //自增值规则处理
                    case 'plus':
                        //取出开始值、步长、位数
                        [
                            'step' => $step,
                            'dight' => $dight,
                        ] = $v['rule'];
                        //生成自增值
                        $next_number = $current_number + $step;
                        // 当前数字长度小于位数的话 补 0
                        $next_number = strlen($next_number) < $dight ? str_pad($next_number, $dight, '0', STR_PAD_LEFT) : $next_number;
                        //压入文号子项
                        $number_arr[] = $next_number;
                        break;
                }
            }
            // 更新起始值
            PstRepository::updateReportNumber($company_id, [
                'current_number' => $next_number,
            ]);
            $report_number = implode('', $number_arr);;
        } else {
            //不存在则生成默认的文号规则
            $report_number = 'PST-' . time() . '-' . str_random(6);
        }
        return $report_number;
    }
    //===========================================评审通导出模板设置============================================================================>

    /**
     * 获取分组列表
     */
    public function getExportTypeList()
    {
        $user=auth('api')->user();
        return PstExportType::where('company_id',$user->current_company_id)->orderBy('sequence','asc')->get()->map(function ($type){
            return [
                'id'=>FunctionTool::encrypt_id($type->id),
                'name'=>$type->name,
                'count'=>$type->exportTems->count(),
            ];
        });
    }
    /**
     * 创建分组
     */
    public function createExportType($name)
    {
        $user=auth('api')->user();
        $pstExportType=PstExportType::create(['name'=>$name,'company_id'=>$user->current_company_id]);
        return ['status'=>'success','message'=>'创建成功','data'=>['id'=>FunctionTool::encrypt_id($pstExportType->id)]];
    }
    /**
     * 删除分组
     */
    public function deleteExportType($id)
    {
        $id=FunctionTool::decrypt_id($id);
        $pstExportType=PstExportType::find($id);
        $count=$pstExportType->exportTems->count();
        if($count>0){
            return ['status'=>'fail','message'=>'删除失败,该分组下存在导出模板'];
        }
        $pstExportType->delete();
        return ['status'=>'success','message'=>'删除成功'];
    }
    /**
     * 编辑分组
     */
    public function editExportType($id,$name)
    {
        $id=FunctionTool::decrypt_id($id);
        $count=PstExportType::find($id)->update(['name'=>$name]);
        if($count>0){
            return ['status'=>'success','message'=>'编辑成功'];
        }else{
            return ['status'=>'fail','message'=>'删除失败'];
        }
    }
    /**
     * 保存分组排序
     */
    public function saveExportTypeSequence($data)
    {
        foreach ($data as $id=>$sequence){
            $pstExportType=PstExportType::find(FunctionTool::decrypt_id($id));
            $pstExportType->sequence=$sequence;
            $pstExportType->save();
        }
        return ['status'=>'success','message'=>'保存成功'];
    }
    /**
     * 创建评审通导出模板
     */
    public function createExportTemplate($data)
    {
        $html='<h3><strong style="font-size: 20px">官方电话</strong>h3h3h33h3h3</h3><h3 style="text-align:center;"><span style="line-height:1.5">清源水净化有限公司双电源厂区工程</span></h3><h3 style="text-align:center;"><span style="line-height:1.5">审核报告</span></h3><p style="text-align:center;"><span style="color:#f32784"><span style="line-height:1.5">{{文号}}</span></span></p><p><strong><span style="line-height:1.5">长葛市财政局投资评审中心:</span></strong></p><p style="text-indent:2em;"><span style="line-height:1.5">我单位接受贵单位委托，对清源水净化有限公司双电源厂区工程进行了审核，上述工程项目相关资料由贵单位提供，我们的责任是根据《河南省通用安装工程预算定额》(HA02-31-2016)及相关配套文件的规定，按照客观、公正、公平、合理的原则，组织有关专业技术人员对此项工程造价进行审核，并发表审核意见，出具审核报告。在审核过程中，我们根据贵单位提供的资料，专业技术人员会同相关单位及相关人员，认真地分析、认真计算，对工程量的计算、定额的套用、材料分析、工程取费、材料价格的调整等必要的审核程序严格审核，现已审核结束，并将审核结果报告如下：</span></p><p><span style="line-height:1.5"><strong>    一、工程概况：</strong></span></p><p style="text-indent:2em;"><span style="line-height:1.5">    本工程为清源水净化有限公司双电源厂区工程，工程内容含高压开闭所安装、户外高压计量箱、顶管和电缆线路工程等。</span></p><p><span style="line-height:1.5"><strong>    二、审核范围：</strong></span></p><p style="text-indent:2em;"><span style="line-height:1.5">   清源水净化有限公司双电源厂区工程提供施工图及预算内的全部内容。</span></p><p><span style="line-height:1.5"><strong>    三、审核依据：</strong></span></p><p style="text-indent:2em;"><span style="line-height:1.5">1、依据建设单位提供的图纸及预算；</span></p><p style="text-indent:2em;"><span style="line-height:1.5">2、<span style="color:#f32784">{{水利审核依据}}</span>；</span></p><p style="text-indent:2em;"><span style="line-height:1.5">3、《河南省通用安装工程预算定额》(HA02-31-2016)及配套的定额综合解释和现行的有关造价文件</span></p><p style="text-indent:2em;"><span style="line-height:1.5">4、人工费价格执行豫建标定【2018】40号文；</span></p><p style="text-indent:2em;"><span style="line-height:1.5">5、税金根据豫建设标【2018】22号文，按10%计入；</span></p><p style="text-indent:2em;"><span style="line-height:1.5">6、材料价格依据《许昌工程造价信息》2018年第六期，信息价中没有的材料，其价格参考市场价进行调整；</span></p><p style="text-indent:2em;"><span style="line-height:1.5">7、现行的法律法规、标准图集、规范、工艺标准、材料做法等。</span></p><p> <span style="line-height:1.5"><strong>   四、审核原则：</strong></span></p><p style="text-indent:2em;"><span style="line-height:1.5">    客观、公平、公正、实事求是。</span></p><p><span style="line-height:1.5"><strong>    五、审核方法：</strong></span></p><p style="text-indent:2em;"><span style="line-height:1.5">    根据该工程实际情况，我们采取了普查的方法对该工程招标控制价进行了审核。</span></p><p> <span style="line-height:1.5"><strong>六、审核结果：</strong></span></p><p style="text-indent:2em;"><span style="line-height:1.5">    清源水净化有限公司双电源厂区工程审核结果为：原报送审金额<span style="color:#f32784">{{送审金额}}</span>元，审定金额：<span style="color:#f32784">{{审定金额}}</span>元，审减金额  元。</span></p><p> </p><p></p><p><span style="line-height:1.5">编制人 ：                                                 审核人:</span></p><p style="text-align:right;"><span style="line-height:1.5">                       河南英华咨询有限公司</span></p><p style="text-align:right;"> </p><p style="text-align:right;"><span style="line-height:1.5">2019年 2月12日</span></p>';
        $html=$data['text'];
        $user=\auth('api')->user();
        $pst_data=[
          'name'=>$data['name'],
          'type_id'=>$data['type_id'],
          'company_id'=>$user->current_company_id,
          'is_show'=>array_get($data,'is_show')===null?1:$data['is_show'],
          'header'=>$data['header'],
          'footer'=>$data['footer'],
          'text'=>json_encode($html),
//          'parameter'=>json_encode($data['parameter']),//参数设置说明
          'per'=>json_encode($data['per']),
          'description'=>$data['description'],
        ];
        PstExportTemplate::create($pst_data);
        return ['status'=>'success','message'=>'创建成功'];
    }
    /**
     * 评审通导出模板列表
     */
    public function exportTemplateList()
    {
        $user=\auth('api')->user();
        $pstExportType=PstExportType::where('company_id',$user->current_company_id)->orderBy('sequence','asc')->get();
        $enable=PstExportTemplateListEnResource::collection($pstExportType);
        $exportTems=PstExportTemplate::where('company_id',$user->current_company_id)->where('is_show',0)->get();
        $disable=PstExportTemplateResource::collection($exportTems);
        return ['status'=>'success','data'=>['enable'=>$enable,'disable'=>$disable]];
    }
    /**
     * 评审通导出模板编辑
     */
    public function exportTemplateEdit($id)
    {
        $id=FunctionTool::decrypt_id($id);
        $pstExportTemplate=PstExportTemplate::where('id',$id)->get();
        PstExportTemplateResource::$type_name=$pstExportTemplate[0]->exportType->name;
        $pstExport=PstExportTemplateResource::collection($pstExportTemplate);
        return ['status'=>'success','data'=>$pstExport];
    }
    /**
     * 评审通导出模板保存编辑
     */
    public function exportTemplateSaveEdit($data)
    {
        $pst_data=[
            'name'=>$data['name'],
            'type_id'=>$data['type_id'],
            'is_show'=>$data['is_show'],
            'header'=>$data['header'],
            'footer'=>$data['footer'],
            'text'=>json_encode($data['text']),
            'parameter'=>json_encode($data['parameter']),//参数设置说明
            'per'=>json_encode($data['per']),
            'description'=>$data['description']
        ];
        $id=array_get($data,'id');
        $id=FunctionTool::decrypt_id($id);
        PstExportTemplate::where('id',$id)->update($pst_data);
        return ['status'=>'success','message'=>'保存成功'];
    }
    /**
     * 评审通导出模板禁用(启用)
     */
    public function exportTemplateEnable($data)
    {
        $is_show=$data['is_show'];
        $id=$data['id'];
        $id=FunctionTool::decrypt_id($id);
        if($is_show=='disable'){//禁用
            PstExportTemplate::where('id',$id)->update(['is_show'=>0]);
            return ['status'=>'success','message'=>'禁用成功'];
        }elseif($is_show=='enable'){//启用
            PstExportTemplate::where('id',$id)->update(['is_show'=>1]);
            return ['status'=>'success','message'=>'启用成功'];
        }else{
            return ['status'=>'fail','message'=>'操作失败'];
        }
    }
    /**
     * 评审通导出模板移动
     */
    public function exportTemplateMove($data)
    {
        $id=$data['id'];
        $id=FunctionTool::decrypt_id($id);
        $typeId=FunctionTool::decrypt_id($data['typeId']);
        PstExportTemplate::where('id',$id)->update(['type_id'=>$typeId]);
        return ['status'=>'success','message'=>'转移成功'];
    }
    /**
     * 创建导出模板打包分组
     */
    public function createExportPackage($data)
    {
        $id=array_get($data,'id');
        $temIds=array_get($data,'temIds');
        $name=array_get($data,'name');
        if($id!==null){
            return $this->editTemplatePackage($id,$temIds,$name);
        }
        $user=\auth('api')->user();
        $data=[
            'name'=>$name,
            'user_id'=>$user->id,
            'export_template'=>json_encode($temIds),
            'company_id'=>$user->current_company_id,
        ];
        PstExportPackage::create($data);
        return ['status'=>'success','message'=>'创建成功'];
    }
    /**
     * 删除导出模板打包分组
     */
    public function deleteExportPackage($id)
    {
        $id=FunctionTool::decrypt_id($id);
        $user=\auth('api')->user();
        PstExportPackage::where('id',$id)->where('user_id',$user->id)->where('company_id',$user->current_company_id)->delete();
        return ['status'=>'success','message'=>'删除成功'];
    }
    /**
     * 编辑导出模板打包分组名称
     */
    public function editExportPackageName($id,$name)
    {
        $id=FunctionTool::decrypt_id($id);
        $user=\auth('api')->user();
        $data=[
            'name'=>$name,
        ];
        $count=PstExportPackage::where('id',$id)->where('user_id',$user->id)->where('company_id',$user->current_company_id)->update($data);
        if($count>0){
            return ['status'=>'success','message'=>'更新成功'];
        }else{
            return ['status'=>'success','message'=>'没有可更新,更新失败'];
        }
    }
    /**
     * 个人选择导出模板进行打包
     * $packageIds :导出模板包,选中的导出模板ids
     */
    public function editTemplatePackage($id,$temIds,$name)
    {
        $id=FunctionTool::decrypt_id($id);
        $user=\auth('api')->user();
        $data=[
            'name'=>$name,
            'export_template'=>json_encode($temIds),
        ];
        $count=PstExportPackage::where('id',$id)->where('user_id',$user->id)->where('company_id',$user->current_company_id)->update($data);
        if($count>0){
            return ['status'=>'success','message'=>'保存成功'];
        }else{
            return ['status'=>'success','message'=>'没有可更新,更新失败'];
        }
    }
    /**
     * 个人导出模板包列表
     */
    public function exportPackageLike()
    {
        $user=\auth('api')->user();
        $pstExportPackage=PstExportPackage::where('user_id',$user->id)->where('company_id',$user->current_company_id)->get();
        return $pstExportPackage->map(function ($pstExportPackage){
            //导出模板ids
            $ids=$pstExportPackage->export_template===null?array():FunctionTool::decrypt_id_array(json_decode($pstExportPackage->export_template));
            return [
                'id'=>FunctionTool::encrypt_id($pstExportPackage->id),
                'name'=>$pstExportPackage->name,
                'export_template'=>$this->packageTemplate($ids),
            ];
        });
    }
    /**
     * @param $ids
     * 返回导出模板可见人
     */
    public static function exportTemAbleUser($ids)
    {
        $ids=FunctionTool::decrypt_id_array($ids);
        return DB::table('users')->whereIn('id',$ids)->pluck('name')->toArray();
    }
    //获取导出模板
    private function packageTemplate(array $ids)
    {
        return DB::table('pst_export_template')->whereIn('id',$ids)->where('is_show',1)->get()->map(function ($pst_export_template){
            return [
                'id'=>FunctionTool::encrypt_id($pst_export_template->id),
                'name'=>$pst_export_template->name,
                'description'=>$pst_export_template->description,
            ];
        });
    }
    /**
     * 生成单个评审通报告(变量替换返回模板)
     */
    public function getReplacedVarTemplate($data)
    {
        $user=\auth('api')->user();
        $pst_id=FunctionTool::decrypt_id($data['pst_id']);
        $temId=FunctionTool::decrypt_id($data['temId']);
        $pst=Pst::find($pst_id);
        $tem=PstExportTemplate::find($temId);
        //获取所有替换变量的真实数据
        $var_data=$this->getRealVar($user->current_company_id,$pst);
        //模板正文假变量替换成真实数据
        $text=$this->replacedVar(json_decode($tem->text),$var_data);
        //组合所有导出word所需数据
        $data=[
            'name'=>$tem->name,
            'type_id'=>$tem->type_id,
            'company_id'=>$tem->company_id,
            'is_show'=>$tem->is_show,
            'header'=>$tem->header,
            'footer'=>$tem->footer,
            'text'=>json_decode($text),
            'parameter'=>json_decode($tem->parameter),
            'per'=>json_decode($tem->per),
            'description'=>$tem->description,
        ];
        return ['status'=>'success','data'=>$data];
    }
    /**
     * 获取所有需要的真实数据
     */
    private function getRealVar($current_company_id,Pst $pst)
    {
        //取出评审通表中的最终表单数据
//        $var_data=$pst->finish_form;
        $document_number=self::getNextReportNumber($current_company_id);//生成文号
        $var_data=[
            '水利审核依据'=>'防火等级划分',
            '送审金额'=>12000,
            '审定金额'=>2222,
        ];
        $var_data['文号']=$document_number;
        return $var_data;
    }

    /**
     * @param $html :模板正文
     * @param $var_data :要替换模板变量的真是数据
     */
    private function replacedVar($html,$var_data)
    {
//        $pattern = '/\{\{[a-z0-9\x80-\xff]*\}\}/';
//        $matches=[];
//        preg_match_all($pattern,$html,$matches);
        foreach ($var_data as $var=>$value){
            $html=str_replace('{{'.$var.'}}',$value,$html);
        }
        return json_encode($html);
    }

    /**
     * 个人选择导出模板进行打包导出(单个模板导出)
     * 选择模板进行导出操作
     */
    public function exportSingleTemplatePackage($data)
    {
        $user=\auth('api')->user();
        $user_id=FunctionTool::encrypt_id($user->id);
        $this->exportSingleWord->exportWord($data,$user_id);
    }
    /**
     * 个人选择导出模板进行打包导出(多模板导出)
     * 选择模板进行导出操作
     */
    public function exportTemplatePackage($data)
    {
        $user=\auth('api')->user();
//        dataVar=$data['data'];//正文数据变量
        $ids=FunctionTool::decrypt_id_array($data['temIds']);//模板ids
        //获取所有选中的导出模板
        $export_tem=DB::table('pst_export_template')->whereIn('id',$ids)->get()->toArray();
        //导出word
        $files=[];
        $user_id=FunctionTool::encrypt_id($user->id);
        //循环导出,组合files文件
        $pst=Pst::find($data['pst_id']);
        foreach ($export_tem as $tem){
            //获取所有替换变量的真实数据
            $var_data=$this->getRealVar($user->current_company_id,$pst);
            //模板正文假变量替换成真实数据
            $text=$this->replacedVar(json_decode($tem->text),$var_data);
            //组合所有导出word所需数据
            $data=[
                'name'=>$tem->name,
                'header'=>$tem->header,
                'footer'=>$tem->footer,
                'text'=>json_decode($text),
            ];
            //循环导出,组合files文件
            $this->exportWord->exportWord($data,$files,$user_id);
        }
        //压缩文件
        Zipper::make($user_id.'.zip')->add($files)->close();
        //循环删除项目本地word文件
        foreach ($files as $file){
            unlink($file);
        }
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: " . filesize($user_id.'.zip'));
        header("filename:".$user_id.".zip");
        readfile($user_id.'.zip');
        unlink($user_id.'.zip');
        exit();
    }

    /**
     * 下载评审通附件
     * @param $data
     */
    public function downloadPstFile($data)
    {
        $user = auth('api')->user();
        $file_id = FunctionTool::decrypt_id($data['file_id']);
        return PersonalOssTool::singleFileUpload([$file_id],'company',$user->current_company_id);
    }

    /**
     * 评审通附件存网盘
     * @param $data
     */
    public function transferFile($data)
    {
        $file_id = FunctionTool::decrypt_id($data['file_id']);
        return CompanyOssTool::copyFileToPersonal($file_id,$data['target_directory']);
    }

    /**
     * 获取附件访问记录
     * @param $data
     * @return false|string
     */
    public function getFileAccessLog($data)
    {
        $user = auth('api')->user();
        $fileid = FunctionTool::decrypt_id($data['id']);
        $file = OssFile::find($fileid);
        //获取访问记录（待定）
        $log = FileUseRecord::where('path',$file->oss_path)->get();
        if($log){
            return json_encode(['status' => 'success', 'message' => '获取记录成功','log'=>$log]);
        }else{
            return json_encode(['status' => 'error', 'message' => '暂无访问记录','log'=>'']);
        }
    }


}
