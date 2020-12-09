<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DeleteButton;
use App\Repository\TradeWarnConfig;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class TrandeWarningController extends Controller
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

            $content->header('交易警告配置');
            $content->description('交易警告配置');

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

            $content->header('交易警告修改');
            $content->description('交易警告修改');

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

            $content->header('添加交易警告配置');
            $content->description('添加交易警告配置');

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
        return Admin::grid(TradeWarnConfig::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->coinName('交易币种');
            $grid->amount('警告配置的数量')->editable('text');
            $grid->type('类型')->display(function ($type){
                return $type == 1 ? '买入' : '卖出';
            });
            $grid->name('交易对名称');
            $grid->settlementCurrency('用于结算的货币');
            $grid->disableFilter();
            $grid->disableExport();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->append(new DeleteButton($actions->row, 'tradeWarnConfig/destroy', 'delete', 'id'));
            });
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
        return Admin::form(TradeWarnConfig::class, function (Form $form) {
            $market = java_get('market', ['pageNo' => 1, 'pageSize' => 100]);
            $arr = [];
            if (isset($market['statusCode']) && $market['statusCode'] == 0) {
                foreach ($market['content'] as $item) {
                    $arr[$item['id']] = $item['name'];
                }
            }
            $form->select('marketId', '交易对名称')->options($arr);
            $form->select('type','类型')->options(TradeWarnConfig::$type)->default('1');
            $form->text('amount', '警告配置总额');
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
        $param = (array)$request->post('id');
        $result = java_delete('trade-warning/del-trade-warning-byIds', $param,array('Content-Type:application/json'));
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '删除成功'];
        } else {
            $arr = ['status' => false, 'message' => '删除失败'];
//            admin_log($params['action'], $params, $result, 'post');
        }
        return response()->json($arr);
    }
}
