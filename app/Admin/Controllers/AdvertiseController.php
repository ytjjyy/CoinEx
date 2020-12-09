<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DeleteButton;
use App\Admin\Extensions\Status;
use App\Admin\Extensions\Tools\UserTools;
use App\Repository\Advertise;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class AdvertiseController extends Controller
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

            $content->header('广告管理');
            $content->description('广告列表');

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

            $content->header('广告管理');
            $content->description('修改广告');

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

            $content->header('广告管理');
            $content->description('添加广告');

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
        return Admin::grid(Advertise::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->name("广告名称");
            $grid->link('广告链接');
            $grid->type("广告类型")->display(function ($type) {
                return $type == 'TEXT' ? '图文' : "链接";
            });
            $grid->clientType('显示类型')->display(function ($clientType) {
                return $clientType == 1 ? '网页' : '手机';
            });
            $grid->locale('语言')->display(function ($locale) {
                switch ($locale) {
                    default:
                    case "zh_CN":
                        return '中文';
                    case "en_US":
                        return '英文';
                    case "zh_TW":
                        return '繁体';
                }
            });
            $grid->startTime('广告开始时间');
            $grid->endTime('广告结束时间');
            $grid->lastTime('更新时间');
            $grid->status('状态')->display(function ($status) {
                return $status == 'SHOW' ? '启用' : '禁用';
            });
            $grid->disableExport(); //去掉导出按钮


            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->append(new Status($actions->row, 'advertise/ChangeStatus', 'changeStatus', 'status'));
                $actions->append(new DeleteButton($actions->row, 'advertise/destroy', 'advertise_destroy', 'id'));
            });
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                    $batch->add('删除', new UserTools('/advertise'));
                });
            });

            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
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
        return Admin::form(Advertise::class, function (Form $form) {

            $form->text("name", '广告名称');
            $form->radio("type", '广告类型')->options(['TEXT' => '图文', 'LINK' => '链接']);
            $form->radio('clientType','显示类型')->options(['1'=>'网页','2'=>'手机'])->default('1');
            $form->select('locale','语言')->options(['zh_CN' => '中文', 'en_US' => '英文', 'zh_TW' => '繁体'])->default('zh_CN');
            $form->datetime("startTime", '广告开始时间')->format("YYYY-MM-DD HH:mm:ss");
            $form->datetime("endTime", '广告结束时间')->format("YYYY-MM-DD HH:mm:ss");
            $form->image("url", "图片链接")->uniqueName();
            $form->text("link", '广告链接');
            $form->radio('status', '是否启用')->options(['0' => '启用', '1' => '禁用'])->default("0");
            $form->editor('content', '广告内容');
            $form->saved(function (Form $form) {
                if (!$form->model()->is_save || !$form->model()->is_add) {
                    $message = isset($form->model()->errorMessage) && !empty($form->model()->errorMessage) ? $form->model()->errorMessage : '操作失败';
                    $error = new MessageBag([
                        'title' => '操作返回信息',
                        'message' => $message,
                    ]);
                    session()->flash('error', $error);
                    return back()->with(compact('error'))->withInput();
                }
            });
        });
    }

    public function ChangeStatus(Request $request)
    {
        $params['adId'] = $request->post('id');
        $params['status'] = $request->post('status') === "SHOW" ? "HIDE" : "SHOW";
        $result = java_get('/advertise/onlineOrOffline', $params, []);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $arr = ['status' => false, 'message' => '修改失败'];
        }
        return response()->json($arr);
    }

    public function release(Request $request)
    {
        $params = $request->only(['id', 'action']);
        $params['id'] = implode(',', $params['id']);
        $result = java_delete($params['action'], (array)$params['id'], array('Content-Type:application/json'));
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $arr = ['status' => false, 'message' => '修改失败'];
        }
        return response()->json($arr);
    }

    public function destroy(Request $request)
    {
        $param['id'] = $request->post('id');
        $result = java_delete('advertise', (array)$param['id'], $header = array('Content-Type:application/json'));
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '删除成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : '删除失败 ';
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }
}
