<?php

namespace App\Admin\Controllers;

use App\Repository\C2CConfig;


use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class C2CConfigController extends Controller
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

            $content->header('c2c配置列表');
            $content->description('c2c配置列表');

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

            $content->header('c2c配置修改');
            $content->description('c2c配置修改');

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

            $content->header('c2c配置添加');
            $content->description('c2c配置添加');

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
        return Admin::grid(C2CConfig::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->coinName('币种名称');
            $grid->legalName('法币名称');
            $grid->lastPrice('最新成交价');
            $grid->expiredTimeCancel('付款时间(分钟)');//超时取消
            $grid->expiredTimeFreeze('放行时间(分钟)');//超时取消冻结
            $grid->feeRate('卖家挂单手续费(0.1代表10%)');
            $grid->maxApplBuyCount('最大挂单数(买单)');
            $grid->maxApplSellCount('最大挂单数(卖单)');

            $grid->disableExport();
            $grid->disableFilter();
            $grid->actions(function ($actions) {
                $actions->setKey($actions->row->coinName);
                $actions->disableDelete();
            });
            $grid->disableCreateButton();
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(C2CConfig::class, function (Form $form) {

            $form->text('coinName', '币种名称');
            $form->select('legalName', '法币名称')->options(['cny' => 'cny'])->default('cny');
            $form->text('expiredTimeCancel', '付款时间(分钟)'); //超时取消
            $form->text('expiredTimeFreeze', '放行时间(分钟)'); //超时取消冻结
            $form->text('feeRate', '卖家挂单手续费(0.1为10%)');
            $form->text('maxApplBuyCount', '最大挂单数(买单)');
            $form->text('maxApplSellCount', '最大挂单数(卖单)');
            $form->text('maxApplBuyNum','当天累积买入量');
            $form->text('maxApplSellNum','当天累积卖出量');
        });
    }
}
