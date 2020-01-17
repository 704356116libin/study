<?php

namespace App\Admin\Controllers;

use App\Models\User;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class UserController extends Controller
{
    use ModelForm;
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
        return Admin::content(function (Content $content) use ($id) {
            $content->header('用户管理');
            $content->description('网站用户管理');
            $content->body($this->form()->edit($id));
        });
    }
    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('header');
            $content->description('description');
            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(User::class, function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->id('ID')->sortable();
            $grid->name('昵称');
            $grid->tel('手机号');
            $grid->tel_verified('手机验证')->display(function ($is_first) {
                return $is_first=='0' ? '未验证' : '已验证';
            });
            $grid->email('邮箱');
            $grid->email_verified('邮箱验证')->display(function ($is_first) {
                return $is_first=='0' ? '未验证' : '已验证';
            });
            $grid->created_at('注册时间');
//            $grid->updated_at();
        });
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(User::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->display('name','昵称');
            $form->display('email','邮箱');
            $form->display('tel_verified','手机验证')->options(['0' => '未验证', '1'=> '已验证']);
            $form->display('email_verified','邮箱验证')->options(['0' => '未验证', '1'=> '已验证']);
            $form->display('tel','手机号');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
