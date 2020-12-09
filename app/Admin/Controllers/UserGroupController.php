<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DeleteButton;
use App\Repository\UserGroup;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class UserGroupController extends Controller
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

            $content->header('用户分组');
            $content->description('用户分组');

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

            $content->header('用户分组设置');
            $content->description('用户分组设置');

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

            $content->header('添加用户分组');
            $content->description('添加用户分组');

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
        return Admin::grid(UserGroup::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->groupName('名字')->editable('text');

            $grid->actions(function ($actions) {
                $actions->disableDelete();
            });
            $grid->disableExport();
            $grid->disableFilter();
//            $grid->disableActions();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->append(new DeleteButton($actions->row, '/admin/userGroup/destroy', 'destory-user', 'id'));
            });
//            $grid->disableCreateButton();
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
        return Admin::form(UserGroup::class, function (Form $form) {
            $form->text('groupName', '分组名称');
            $form->saved(function (Form $form) {
                if (!$form->model()->is_save || !$form->model()->is_add) {
                    $error = new MessageBag([
                        'title' => '操作返回信息',
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
        $result = java_get('userConfig/getUserGroupConfigById', $param);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $param['groupType'] = $result['content']['groupType'];
        }
        $result = java_get('userConfig/delUserGroupConfigById', $param);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '删除成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : '删除失败';
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }
}
