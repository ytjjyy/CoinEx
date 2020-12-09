<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/9/21
 * Time: 下午4:31
 */

namespace App\Admin\Controllers;

use App\Admin\Extensions\DeleteButton;
use App\Admin\Extensions\Tools\Filter;
use App\Admin\Extensions\Tools\UserTools;
use App\Repository\CashMonitor;
use App\Repository\CashMonitoringConfig;
use App\Repository\SpecialAccountHandler;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class CashMonitoringConfigController extends Controller
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

            $content->header('持币监控配置');
            $content->description('持币监控配置');

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

            $content->header('修改持币监控配置');
            $content->description('修改持币监控配置');

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

            $content->header('添加持币监控配置');
            $content->description('添加持币监控配置');

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
        return Admin::grid(CashMonitoringConfig::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->coinName('币种名称');
            $grid->rollInNumber('转入数量');
            $grid->rollOutNumber('转出数量');
            $grid->disableExport();
            $filter = new Filter();
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->append(new DeleteButton($actions->row, 'cashConfig/destroy', 'destroy', 'id'));
            });
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $tools->append($filter->render());
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
        return Admin::form(CashMonitoringConfig::class, function (Form $form) {

            $form->select('coinName', '币种名称')->options(getCoin());
            $form->text('rollInNumber', '转入数量');
            $form->text('rollOutNumber', '转出数量');
            $form->saved(function (Form $form) {
                if (!$form->model()->is_add) {
                    $message = isset($form->model()->errorMessage) && !empty($form->model()->errorMessage) ? $form->model()->errorMessage : '操作失败';
                    $error = new MessageBag([
                        'title' => '操作返回信息',
                        'message' => $message,
                    ]);
                    session()->flash('error', $error);
                    return back()->with(compact('error'));
                }
            });
        });
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function release(Request $request)
    {
        $params = $request->only(['id', 'action']);
        $data['Ids'] = $params['id'];
        $result = java_delete($params['action'], (array)$data['Ids'], $header = array('Content-Type:application/json'));
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '删除成功'];
        } else {
            dd($result);
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : '操作失败';
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }

    public function destroy(Request $request)
    {
        $param['id'] = $request->post('id');
        $result = java_get('cash_monitoring_config/del_cash_monitoring_config_byId', $param);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '删除成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : '删除失败';
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }

    public function monitor()
    {
        return Admin::content(function (Content $content) {
            $minute = 1;
            $result = java_get('/timeMonitoring/get-timeMonitoring-detail-byType', ['monitoringType' => 'TIME_MONITORING_COIN']);
            if (isset($result['statusCode']) && $result['statusCode'] == 0) {
                $minute = $result['content']['numMinutes'];
            }
            $content->header('持币监控');
            $content->description('持币监控');
            $content->body($this->monitor_grid(true, $minute));
        });
    }

    public function grid_html(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);
        $params['pageNo'] = $page;
        $params['pageSize'] = $perPage;
        $result = java_get('/cash_monitoring/get_cash_monitoring_list', $params);
        $data = [];
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $data = $result['content'];
        }
        return view('admin.grid.html.cash')->with(compact('data'));
    }

    public function monitor_grid($is_reload = false, $minute = 1)
    {
        return Admin::grid(CashMonitor::class, function (Grid $grid) use ($is_reload, $minute) {
            $grid->minute = $minute;
            $grid->is_reload = $is_reload;
            $grid->setView('admin.grid.cashTable');
            $grid->id('ID');
            $grid->account('用户');
            $grid->coinName('币种名称');
            $grid->goalAddress('目标地址');
            $grid->rollInNumber('转入数量');
            $grid->rollOutNumber('转出数量');
            $grid->sourceAddress('来源地址');
            $grid->type('类型')->display(function ($type){
                return $type == 1? '转入' : '转出';
            });
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableActions();
            $grid->tools(function ($tools)  {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
        });
    }
}