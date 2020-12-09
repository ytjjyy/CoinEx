<?php

namespace Encore\Admin\Controllers;

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;
use Illuminate\Support\MessageBag;

class UserController extends Controller
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
            $content->header(trans('admin.administrator'));
            $content->description(trans('admin.list'));
            $content->body($this->grid()->render());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header(trans('admin.administrator'));
            $content->description(trans('admin.edit'));
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
            $content->header(trans('admin.administrator'));
            $content->description(trans('admin.create'));
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
        return Administrator::grid(function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->username(trans('admin.username'));
            $grid->name(trans('admin.name'));
            $grid->roles(trans('admin.roles'))->pluck('name')->label();
            $grid->created_at(trans('admin.created_at'));
            $grid->updated_at(trans('admin.updated_at'));

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if ($actions->getKey() == 1) {
                    $actions->disableDelete();
                }
            });

            $grid->tools(function (Grid\Tools $tools) {
                $tools->batch(function (Grid\Tools\BatchActions $actions) {
                    $actions->disableDelete();
                });
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Administrator::form(function (Form $form) {
            $form->display('id', 'ID');
            $form->text('username', trans('admin.username'));
            $form->text('name', trans('admin.name'))->rules('required');
            $form->image('avatar', trans('admin.avatar'));
            $form->password('password', trans('admin.password'))->rules('required|confirmed');
            $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
                ->default(function ($form) {
                    return $form->model()->password;
                });
            $form->hidden('id');
            $form->ignore(['password_confirmation']);

            $form->multipleSelect('roles', trans('admin.roles'))->options(Role::all()->pluck('name', 'id'));
            $form->multipleSelect('permissions', trans('admin.permissions'))->options(Permission::all()->pluck('name', 'id'));

            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));
            $form->saving(function (Form $form) {
                $data['password'] = $form->password;
                $data['position'] = $form->name;
                $data['userName'] = $form->username;
                $data['roleId'] = 1;
                $form->username = $form->model()->username;
                $id = $form->id;
                if ($id) {
                    if ($form->password && $form->model()->password != $form->password) {
                        $updateData['userName'] = $form->model()->username;
                        $updateData['newPassword'] = $form->password;
                        $result = java_post('admin/user/update-password', $updateData);
                    } else {
                        $result['statusCode'] = 0;
                    }
                } else {
                    $result = java_post('admin/user', $data, ['Content-Type:application/json']);
                }
                if (isset($result['statusCode']) && $result['statusCode'] == 0) {
                    if ($form->password && $form->model()->password != $form->password) {
                        $form->password = bcrypt($form->password);
                    }
                } else {
                    $message = isset($result['errorMessage']) ? $result['errorMessage'] : '操作失败';
                    $error = new MessageBag([
                        'title' => '操作提示',
                        'message' => $message,
                    ]);
                    session()->flash('error', $error);
                    return back()->with(compact('error'));
                }

            });
        });
    }
}
