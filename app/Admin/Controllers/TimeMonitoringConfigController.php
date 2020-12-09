<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DeleteButton;
use App\Admin\Extensions\DetailButton;
use App\Admin\Extensions\Tools\Filter;
use App\Repository\HighTransaction;
use App\Repository\TimeMonitoringConfig;

use App\Repository\TransferHistory;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class TimeMonitoringConfigController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
//        if (empty(request()->get('monitoringType'))) {
//            $result = java_get('/timeMonitoring/get-timeMonitoring-list');
//            if (isset($result['statusCode']) && $result['statusCode'] == 0) {
//                request()->offsetSet('monitoringType', $result['content'][0]['monitoringType']);
//            }
//        }
        return Admin::content(function (Content $content) {

            $content->header('高频设置');
            $content->description('高频设置');

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

            $content->header('编辑高频设置');
            $content->description('编辑高频设置');

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

            $content->header('添加高频设置');
            $content->description('添加高频设置');

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
        return Admin::grid(TimeMonitoringConfig::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->coinName('币种名称');
            $grid->buyNumber('买入次数');
            $grid->monitoringType('监控类型')->display(function ($type) {
                $result = java_get('timeMonitoring/get-timeMonitoring-detail-byType', ['monitoringType' => $type]);
                if (isset($result['statusCode']) && $result['statusCode'] == 0) {
                    return $result['content']['monitoringName'];
                } else {
                    return '<span style="color: red">未找到该类型</span>';
                }
            });
            $grid->numMinutes('时间');
            $grid->sellNumber('卖出次数');
            $grid->settlementCurrency('结算');
            $grid->disableExport();
            $grid->disableFilter();
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $result = java_get('/timeMonitoring/get-timeMonitoring-list');
                $arr = [];
                if (isset($result['statusCode']) && $result['statusCode'] == 0) {
                    foreach ($result['content'] as $value) {
                        $arr[$value['monitoringType']] = $value['monitoringName'];
                    }
                }
                $filter->SetParams(array('lable' => '监控类型', 'name' => 'monitoringType', 'options' => $arr))->select();
                $tools->append($filter->render());
            });

            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->append(new DeleteButton($actions->row, 'timeMonitoringConfig/destroy', 'destroy', 'id'));
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
        return Admin::form(TimeMonitoringConfig::class, function (Form $form) {
            $options = getCoin();
//            $result = java_get('/timeMonitoring/get-timeMonitoring-list');
//            if (isset($result['statusCode']) && $result['statusCode'] == 0) {
//                foreach ($result['content'] as $value) {
//                    $arr[$value['monitoringType']] = $value['monitoringName'];
//                }
//            }
            $arr['TIME_MONITORING_H_F_TRADE'] = '高频交易';
            $form->select('coinName', '币种名称')->options($options);
            $form->number('buyNumber', '买入次数');
            $form->select('monitoringType', '警告类型')->options($arr);
            $form->number('numMinutes', '时间(分钟)');
            $form->number('sellNumber', '卖出次数');
            $form->select('settlementCurrency', '结算货币')->options($options);
            $form->saved(function (Form $form) {
                if (!$form->model()->is_save || !$form->model()->is_add) {
                    $message = isset($form->model()->errorMessage) && !empty($form->model()->errorMessage) ? $form->model()->errorMessage : '操作失败';
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

    public function destroy(Request $request)
    {
        $param['id'] = $request->post('id');
        $result = java_get('timeMonitoringConfig/del-TimeMonitoring-config-byId', $param);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '删除成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : '操作失败';
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }

    /**
     * 高频交易
     */
    public function highTransaction()
    {
//        $market = java_get('market', ['pageNo' => 1, 'pageSize' => 20]);
//        if (isset($market['statusCode']) && $market['statusCode'] == 0) {
//            if (empty(request('coin_name'))) {
//                request()->offsetSet('coin_name', $market['content'][0]['coinName'] . '/' . $market['content'][0]['settlementCurrency']);
//            }
//        }
        return Admin::content(function (Content $content) {
            $minute = 1;
            $result = java_get('/timeMonitoring/get-timeMonitoring-detail-byType', ['monitoringType' => 'TIME_MONITORING_H_F_TRADE']);
            if (isset($result['statusCode']) && $result['statusCode'] == 0) {
                $minute = $result['content']['numMinutes'];
            }
            $content->header('高频交易监控');
            $content->description('高频交易监控');
            $content->body($this->transaction_grid(true, $minute));
        });
    }

    public function transaction_grid($is_reload = false, $minute = 1)
    {
        return Admin::grid(HighTransaction::class, function (Grid $grid) use ($is_reload, $minute) {
            $grid->minute = $minute;
            $grid->is_reload = $is_reload;
            $grid->setView('admin.grid.transactionTable');
            $grid->id('ID');
            $grid->buyCount('买入次数');
            $grid->sellCount('卖出次数');
            $grid->mobile('用户手机号');
            $grid->email('邮箱');
            $grid->coinName('币种名称');
            $grid->settlementCurrency('结算币种');
            $grid->freshDate('刷新时间');
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableActions();
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $options = [];
                $market = java_get('market', ['pageNo' => 1, 'pageSize' => 20]);
                if (isset($market['statusCode']) && $market['statusCode'] == 0) {
                    foreach ($market['content'] as $vo) {
                        $options[$vo['coinName'] . '/' . $vo['settlementCurrency']] = $vo['coinName'] . '/' . $vo['settlementCurrency'];
                    }
                }
                $filter->SetParams(array('lable' => '交易市场', 'options' => $options, 'name' => 'coin_name'))->select();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
        });
    }

    public function grid_html(Request $request)
    {
        $params['coin_name'] = $request->input('coin_name');
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);
        $params['pageNo'] = $page;
        $params['pageSize'] = $perPage;
        if (isset($params['coin_name']) && !empty($params['coin_name'])) {
//            $market = java_get('market', ['pageNo' => 1, 'pageSize' => 20]);
//            if (isset($market['statusCode']) && $market['statusCode'] == 0) {
//                if (empty(request('coin_name'))) {
//                    $params['coin_name'] = $market['content'][0]['coinName'] . '/' . $market['content'][0]['settlementCurrency'];
//                }
//            }
            $params['coinName'] = explode('/', $params['coin_name'])[0];
            $params['settlementCurrency'] = explode('/', $params['coin_name'])[1];
        }
        $result = java_get('hfmonitor/get_high_frequency_list', $params);
        $data = [];
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $data = $result['content'];
        }
        return view('admin.grid.html.transaction')->with(compact('data'));
    }
}
