<?php

namespace App\Admin\Controllers;

use App\Http\Resources\ApprovalResource;
use App\Http\Resources\FileResource;
use App\Models\Approval;
use App\Models\Company;

use App\Models\CompanyLicense;
use App\Models\OssFile;
use App\Models\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CompanyCertificationInfoController extends Controller
{
    use ModelForm;
    protected static $id;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('用户管理');
            $content->description('网站用户管理');
            $content->body($this->grid());
        });
    }
    /**
     * Edit interface.
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        self::$id=$id;
        return Admin::content(function (Content $content) use ($id) {
            $content->header('企业认证');
            $content->description('企业认证审批');
            $content->body(view('/companyLicense',['file_path'=>$this->file()]));
            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Company::class, function (Grid $grid) {
            $grid->model()->where('verified', 1)->orderBy('id', 'desc');//所有待审核的公司
            $grid->id('ID')->sortable();
            $grid->name('公司名');
            $grid->verified('验证状态')->display(function ($verified){
                return $verified==0?'未认证':($verified==1?'等待认证审核':($verified==2?'认证通过': '认证不通过'));
            });
            $grid->license_id('认证文件')->display(function ($license_id){
                return self::file_url($license_id);
            });
            $grid->created_at('注册时间');
        });
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Company::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->display('name','公司名');
//            $form->radio('verified', '认证状态')->options([0 => '未认证', 1=> '已认证',2=>'认证审核中',3=>'认证不通过,请修改后再提交审核']);
            $form->radio('verified', '认证状态')->options([ 1=> '认证审核中',2=>'审核通过',3=>'审核不通过']);
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
            $form->saving(function (Form $form){
                $this->deal($form->id,$form->verified);
            });
        });
    }
    /**
     * 提交编辑后,处理其他操作
     */
    public function deal($id,$verified)
    {
        $company=Company::find($id);

        //添加认证后的权限等
        return true;
    }
    /**
     * 企业认证文件
     */
    public function file()
    {
        $company_id=self::$id;
        $companyLicense=CompanyLicense::where('company_id',$company_id)->first();
        if(count($companyLicense->files)>0){
            $oss_path=$companyLicense->files[0]->oss_path;
        }else{
            return null;
        }
        $ali_path=Storage::url($oss_path);//文件在ali上url
        return $ali_path;
    }
    /**
     * 企业认证文件名
     */
    public static function file_url($license_id)
    {
        $companyLicense=CompanyLicense::find($license_id);
        if(count($companyLicense->files)>0){
            $oss_path=$companyLicense->files[0]->oss_path;
        }else{
            return null;
        }
        $ali_path=Storage::url($oss_path);//文件在ali上url
        return $ali_path;
    }
}
