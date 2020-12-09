<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DeleteButton;
use App\Repository\MerchantConfig;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class MerchantConfigController extends Controller
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

            $content->header('兑换商币种汇率配置');
            $content->description('兑换商币种汇率配置');

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

            $content->header('兑换商币种汇率修改');
            $content->description('兑换商币种汇率修改');

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

            $content->header('兑换商币种汇率添加');
            $content->description('兑换商币种汇率添加');

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
        return Admin::grid(MerchantConfig::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->coinName('币种名称');
            $grid->cnyPrice('人民币价格');
            $grid->dollarPrice('美元价格');
            $grid->hkdollarPrice('港币价格');
            $grid->orderMinAmount('最小买卖个数');
            $grid->orderMaxAmount('最大买卖个数');
            $grid->feeRate('费率(0.2表示20%)');
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->append(new DeleteButton($actions->row, 'merchantCoin/destroy', 'deleteRow', 'id'));
            });
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableRowSelector();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(MerchantConfig::class, function (Form $form) {
            $form->select('coinName', '币种名称')->options(getCoin());
            $form->text('cnyPrice', '人民币价格');
            $form->text('dollarPrice', '美元价格');
            $form->text('hkdollarPrice', '港币价格');
            $form->text('orderMinAmount', '最小买卖个数');
            $form->text('orderMaxAmount', '最大买卖个数');
            $form->text('feeRate','费率(0.2表示20%)');
            $form->saved(function (Form $form) {
                if (!$form->model()->is_save || !$form->model()->is_add) {
                    $error = new MessageBag([
                        'title' => '操作提示',
                        'message' => '操作失败',
                    ]);
                    session()->flash('error', $error);
                    return back()->with(compact('error'));
                }
            });
        });
    }

    public function destroy(Request $request)
    {
        $id = $request->post('id');
        $result = java_post('merchant/del-coin', ['id' => $id]);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '删除成功'];
        } else {
            $arr = ['status' => false, 'message' => '删除失败'];
        }
        return response()->json($arr);
    }
}
