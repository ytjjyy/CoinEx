<?php

namespace App\Admin\Controllers;

use App\Repository\CoinGroupConfig;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class CoinGroupConfigController extends Controller
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

            $content->header('用户分组币种配置');
            $content->description('用户分组币种配置');

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

            $content->header('修改用户分组币种配置');
            $content->description('修改用户分组币种配置');

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
        return Admin::grid(CoinGroupConfig::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->groupName('分组名称');
            $grid->coinName('币种名称');
            $grid->conValue('手续费设置(0.1代表10%)')->editable('text');
//            $grid->remark('备注')->editable('text');
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
            });
            $grid->disableFilter();
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableExport();
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
//            $grid->disablePagination();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(CoinGroupConfig::class, function (Form $form) {
            $coin = java_get('coin', ['pageNo' => 1, 'pageSize' => 100]);
            if (isset($coin['statusCode']) && $coin['statusCode'] == 0) {
                foreach ($coin['content'] as $vo) {
                    $options[$vo['displayName']] = $vo['displayName'];
                }
            }
            $group = java_get('userConfig/getUserGroupConfigList', []);
            $arr = [];
            if (isset($group['statusCode']) && $group['statusCode'] == 0) {
                foreach ($group['content'] as $vo) {
                    $arr[$vo['groupType']] = $vo['groupName'];
                }
            }
            $form->select('groupType', '分组名称')->options($arr);
            $form->select('coinName', '币种名称')->options($options);
            $form->text('conValue', '手续费(0.1代表10%)');
//            $form->text('remark', '备注');
        });
    }
}
