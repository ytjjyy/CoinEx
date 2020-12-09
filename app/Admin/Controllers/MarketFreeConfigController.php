<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DeleteButton;
use App\Repository\MarketFreeConfig;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class MarketFreeConfigController extends Controller
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

            $content->header('交易市场手续费配置');
            $content->description('交易市场手续费配置');

            $content->body($this->grid(true));
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

            $content->header('特殊分组手续费修改');
            $content->description('特殊分组手续费续费修改');

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

            $content->header('特殊分组手续费配置');
            $content->description('特殊分组手续费配置');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    public function grid($is_reload = false)
    {
        return Admin::grid(MarketFreeConfig::class, function (Grid $grid) use ($is_reload) {
            $grid->is_reload = $is_reload;
            $grid->setView('admin.grid.marketFreeConfigTable');
            $grid->coinName('币种名称');
            $grid->buyConValue('买入手续费(1为1%)');
            $grid->sellConValue('卖出手续费(1代表1%)');
            $grid->name('交易市场名称');
            $grid->groupName('分组名称');
            $grid->settlementCurrency('结算货币');
            $grid->disableFilter();
            $grid->disableExport();
            $grid->actions(function ($actions) {
                $actions->setKey($actions->row->marketGroupConfigId);
                if (is_null($actions->row->marketGroupConfigId)) {
                    $actions->disableEdit();
                }
                $actions->disableDelete();
                $actions->append(new DeleteButton($actions->row, 'marketFreeConfig/destroy', 'delete', 'marketGroupConfigId'));
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
        return Admin::form(MarketFreeConfig::class, function (Form $form) {

            $market = java_get('market', ['pageNo' => 1, 'pageSize' => 100]);
            $arr = [];
            if (isset($market['statusCode']) && $market['statusCode'] == 0) {
                foreach ($market['content'] as $item) {
                    $arr[$item['id']] = $item['name'];
                }
            }
            $form->select('marketId', '交易对名称')->options($arr);
            $group = java_get('userConfig/getUserGroupConfigList', []);
            $options = [];
            if (isset($group['statusCode']) && $group['statusCode'] == 0) {
                foreach ($group['content'] as $vo) {
                    $options[$vo['id']] = $vo['groupName'];
                }
            }
            $form->select('groupId', '分组名称')->options($options);
            $form->number('buyConValue', '买入手续费(1代表1%)');
            $form->number('sellConValue', '卖出手续费');
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
        $param['marketGroupConfigId'] = $request->post('id');
        $result = java_get('marketGroupConfig/del-market-group-config-byId', $param);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '删除成功'];
        } else {
            $arr = ['status' => false, 'message' => '删除失败'];
//            admin_log($params['action'], $params, $result, 'post');
        }
        return response()->json($arr);
    }
}
