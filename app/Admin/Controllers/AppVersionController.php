<?php

namespace App\Admin\Controllers;

use App\Repository\AppVersion;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;

class AppVersionController extends Controller
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

            $content->header('app版本管理');
            $content->description('app版本管理');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('修改app版本');
            $content->description('修改app版本');

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

            $content->header('添加app版本');
            $content->description('添加app版本');

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
        return Admin::grid(AppVersion::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->platform('平台名称');
            $grid->versionName('版本名称');
            $grid->force('是否强制升级')->display(function ($force) {
                return $force ? '强制' : '非强制';
            });
            $grid->lastTime('更新时间');
            $grid->actions(function ($actions){
                $actions->disableDelete();
            });
            $grid->disableFilter();
            $grid->disableExport();
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
            $grid->disableCreateButton();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(AppVersion::class, function (Form $form) {
            $form->text('platform', '平台名称');
            $form->text('versionName', '版本名称');
            $form->text('code','序号');
            $form->radio('force', '是否强制升级')->options(['0' => '非强制', '1' => '强制']);
            $form->text('url','链接');
            $form->saved(function (Form $form) {
                if (!$form->model()->is_save) {
                    $message = isset($form->model()->errorMessage) ? $form->model()->errorMessage : '操作失败';
                    $error = new MessageBag([
                        'title' => '操作提示',
                        'message' => $message,
                    ]);
                    session()->flash('error', $error);
                    return back()->with(compact('error'))->withInput();
                }
            });
        });
    }
}
