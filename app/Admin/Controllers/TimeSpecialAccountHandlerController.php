<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\Filter;
use App\Admin\Extensions\Tools\UserTools;
use App\Repository\SpecialAccountHandler;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class TimeSpecialAccountHandlerController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        request()->offsetSet('noWarningType','TIME_MONITORING_H_F_TRADE');
        return Admin::content(function (Content $content) {

            $content->header('高频特殊账号处理');
            $content->description('高频特殊账号处理');

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

            $content->header('修改高频特殊账号');
            $content->description('修改高频特殊账号');

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

            $content->header('添加高频特殊账号');
            $content->description('添加高频特殊账号');

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
        return Admin::grid(SpecialAccountHandler::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->userName('用户名');
            $grid->disableExport();
            $grid->disableActions();
            $filter = new Filter();
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                    $batch->add('删除', new UserTools('tradeNowarningUser/del-no-warning-user'));
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
        return Admin::form(SpecialAccountHandler::class, function (Form $form) {

            $form->text('userName', '用户名');
            $form->hidden('noWarningType','类型')->default('TIME_MONITORING_H_F_TRADE');
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
        $result = java_delete($params['action'], (array)$params['id'], $header = array('Content-Type:application/json'));
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '删除成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : '操作失败';
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }
}
