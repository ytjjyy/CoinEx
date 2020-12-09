<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DeleteButton;
use App\Repository\MonitoringConfig;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class MonitoringConfigController extends Controller
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

            $content->header('监控刷新时间设置');
            $content->description('监控刷新时间设置');

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

            $content->header('修改监控刷新时间设置');
            $content->description('修改监控刷新时间设置');

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

            $content->header('添加监控刷新时间设置');
            $content->description('添加监控刷新时间设置');

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
        return Admin::grid(MonitoringConfig::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->monitoringName('监控名称');
//            $grid->monitoringType('监控类型');
            $grid->numMinutes('时间(分钟)');
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
//                $actions->append(new DeleteButton($actions->row, 'monitoringConfig/destroy', 'destroy-config', 'id'));
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
        return Admin::form(MonitoringConfig::class, function (Form $form) {
            $form->text('monitoringName', '监控名称');
//            $form->text('monitoringType','监控类型');
            $form->number('numMinutes', '时间(分钟)');
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
        $param['id'] = $request->post('id');
        $result = java_get('timeMonitoring/get-timeMonitoring-detail-ById', $param);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $param['monitoringType'] = $result['content']['monitoringType'];
        }
        $result = java_get('timeMonitoring/del-TimeMonitoring-byId', $param);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '删除成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : '删除失败';
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }

    public function serviceStatus()
    {
        $result = java_get('service_monitor/serviceinfo');
        $service = [];
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $service = $result['content'];
        }
        $minute = 1;
        $result = java_get('/timeMonitoring/get-timeMonitoring-detail-byType', ['monitoringType' => 'TIME_MONITORING_SYSTEM']);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $minute = $result['content']['numMinutes'];
        }
        return Admin::content(function (Content $content) use ($service,$minute) {
            $content->body(view('admin.system.service',compact('service','minute')));
        });
    }
}
