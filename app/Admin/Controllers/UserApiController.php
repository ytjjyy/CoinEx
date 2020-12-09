<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DeleteButton;
use App\Admin\Extensions\Reset;
use App\Admin\Extensions\Tools\Filter;
use App\Admin\Extensions\Tools\UserTools;
use App\Repository\UserApi;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;

class UserApiController extends Controller
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

            $content->header('用户api审核列表');
            $content->description('用户api审核列表');

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

            $content->header('header');
            $content->description('description');

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
        return Admin::grid(UserApi::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->userName('用户名');
            $grid->realName('用户真实姓名');
            $grid->mobile('用户手机号');
            $grid->areaCode('区号');
            $grid->status('状态')->display(function ($status) {
                switch ($status) {
                    case 'DISABLED':
                        return '禁用';
                    case 'PASS':
                        return '审核通过';
                    case 'DENY':
                        return '审核失败';
                    default:
                    case 'PENDING':
                        return '审核中';
                }
            });
            $grid->ipList('绑定ip')->display(function ($list) {
                $str = '';
                if (is_array($list)) {
                    foreach ($list as $vo) {
                        $str .= $vo . ',';
                    }
                } else {
                    $str = $list;
                }
                return trim($str, ',');
            });
            $grid->apiSecret('访问密匙apiSecret');
            $grid->whiteIpList('白名单');
            $grid->createTime('创建时间');

            $grid->disableCreateButton();
            $grid->disableExport();
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                    $batch->add('审核通过', new UserTools('user-api/pass'));
                    $batch->add('审核不通过', new UserTools('user-api/deny'));
                });
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '用户真实姓名', 'name' => 'realName'))->text();
                $filter->SetParams(array('lable' => '手机号', 'name' => 'mobile'))->text();
                $filter->SetParams(array('lable' => '状态', 'options' => ['PASS' => '审核通过', 'DENY' => '审核失败', 'PENDING' => '审核中', 'DISABLED' => '禁用'], 'name' => 'status'))->select();
                $tools->append($filter->render());
            });
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
//                if ($actions->row->status == 'DISABLED') {
//                    $actions->append(new Reset($actions->row, '/admin/userApi/release', '启用', 'able','user-api/deny'));
//                }
                if ($actions->row->status != 'DISABLED') {
                    $actions->append(new Reset($actions->row, '/admin/userApi/release', '禁用', 'disable', 'user-api/disable'));
                }
                if($actions->row->status == 'PENDING'){
                    $actions->append(new Reset($actions->row, '/admin/userApi/release', '审核通过', 'pass', 'user-api/pass'));
                    $actions->append(new Reset($actions->row, '/admin/userApi/release', '审核不通过', 'deny', 'user-api/deny'));
                }
                $actions->append(new DeleteButton($actions->row, '/admin/userApi/destroy', 'destroy', 'id'));
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
        return Admin::form(UserApi::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function release(Request $request)
    {
        $id = $request->post('id');
        $action = $request->post('action');
        $result = java_post($action, (array)$id, ['Content-Type:application/json']);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $arr = ['status' => false, 'message' => '修改失败'];
        }
        return response()->json($arr);
    }

    public function destroy(Request $request)
    {
        $id = $request->post('id');
        $result = java_post('user-api/del', (array)$id, ['Content-Type:application/json']);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '操作成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] :'操作失败';
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }
}
