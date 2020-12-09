<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ChangeStatus;
use App\Admin\Extensions\DeleteButton;
use App\Admin\Extensions\Status;
use App\Admin\Extensions\Tools\Filter;
use App\Admin\Extensions\Tools\UserTools;
use App\Repository\AdminUser;
use App\Repository\Article;

use App\Services\OSS;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Qcloud\Cos\Client;

class ArticleController extends Controller
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
            $content->header('文章管理');
            $content->description('添加/修改文章');

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

            $content->header('文章管理');
            $content->description('修改文章');

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

            $content->header('文章管理');
            $content->description('添加文章');

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
        return Admin::grid(Article::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->title('标题');
            $grid->type('类型')->display(function (string $type) {
                switch ($type) {
                    default :
                    case 'NOTICE':
                        return '公告';
                    case 'NEWS' :
                        return '资讯';
                    case 'AGREEMENT':
                        return '注册协议';
                    case 'FEE':
                        return '费用说明';
                    case 'SERVICE' :
                        return '服务条款';
                    case 'PRIVACY' :
                        return '隐私说明';
                    case 'JOIN_US':
                        return '加入我们';
                    case 'PLATFORM':
                        return '平台说明';
                    case 'MERCHANT':
                        return '商家认证公告';
                    case 'CONTACT':
                        return '联系客服';

                }
            });
            $grid->status('状态')->display(function ($status) {
                return $status === 'SHOW' ? '启用' : '禁用';
            });

            $grid->createTime('添加时间');
            $grid->updatedAt('编辑时间');
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->append(new Status($actions->row, 'article/ChangeStatus', 'changeStatus'));
                $actions->append(new DeleteButton($actions->row, 'article/destroy', 'article_destroy', 'id'));
            });
            /*
             * 取消默认批量删除的按钮
             */
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
            $grid->disableExport(); //去掉导出按钮
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
//                    $batch->disableExporter();
                    $batch->add('禁用', new UserTools('article/disable'));
                    $batch->add('启用', new UserTools('article/enable'));
                });
                $filter->SetParams(array('lable' => '类型',
                    'options' => ['NOTICE' => '公告', 'NEWS' => '资讯', 'AGREEMENT' => '注册协议', 'FEE' => '费用说明', 'SERVICE' => '服务条款',
                        'PRIVACY' => '隐私说明', 'JOIN_US' => '加入我们', 'PLATFORM' => '平台说明', 'MERCHANT' => '商家认证公告', 'CONTACT'=> '联系客服'],
                    'name' => 'type'))
                    ->select();
                $filter->SetParams(array('lable' => '文章状态', 'options' => ['SHOW' => '启用', 'HIDE' => '禁用'], 'name' => 'status'))->select();
                $filter->SetParams(array('lable' => '语言', 'options' => ['zh_CN' => '中文', 'en_US' => '英文'], 'name' => 'locale'))->select();
                $tools->append($filter->render());
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
        return Admin::form(Article::class, function (Form $form) {
//            $form->display('id', 'ID');
            $form->text('title', '中文标题')->rules('required', ['required' => '币种名称不能为空']);
//            $form->text('titleEn', '英文名称');
            $form->radio('status', '状态')->options(['SHOW' => '启用', 'HIDE' => '禁用'])->default('status');
            $form->datetime('displayTime', '前台显示时间');
            $form->select('type', '类型')->options(['NOTICE' => '公告', 'NEWS' => '资讯', 'AGREEMENT' => '注册协议',
                'FEE' => '费用说明', 'SERVICE' => '服务条款', 'PRIVACY' => '隐私说明', 'JOIN_US' => '加入我们', 'PLATFORM' => '平台说明',
                'MERCHANT' => '商家认证公告', 'CONTACT'=>'联系客服']);
            $form->radio('locale', '语言选择')->options(['zh_CN' => '中文', 'en_US' => '英文'])->default('zh_CN');
            $form->editor('content', '内容');
            $form->hidden('sort')->default('0');
//            $form->editor('contentEn', '英文内容');
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

    /*
     * 修改文章的状态
     */
    public function ChangeStatus(Request $request)
    {
        $params = (array)$request->post("id");
        $status = $request->post('status');
        $status === 'HIDE' ? $url = 'article/enable' : $url = 'article/disable';
        $result = java_post($url, $params, $header = array('Content-Type:application/json'));
        if ($result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $arr = ['status' => false, 'message' => '修改失败'];
        }
        return response()->json($arr);
    }

    /*
     * 批量操作文章类型
     */
    public function release(Request $request)
    {
        $params = $request->only(['id', 'action']);
        $result = java_post($params['action'], $params['id'], $header = array('Content-Type:application/json'));
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $arr = ['status' => false, 'message' => '修改失败'];
        }
        return response()->json($arr);
    }

    /*
     * 富文本上传图片
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension(); //获取文件名的后缀
        $key = md5(time() . random_int(1, 5)) . '.' . $extension;
        $path = $file->getRealPath();
        $res = OSS::upload($key, $path);
        if ($res) {
            $arr = ['error' => 0, 'data' => [env('AliossUrl') . $key]];
        } else {
            $arr = ['error' => 1, 'message' => '上传失败'];
        }
        return response()->json($arr);
    }
    /**
     * 删除文章
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $param['id'] = $request->post('id');
        $result = java_post('article/delete', (array)$param['id'], $header = array('Content-Type:application/json'));
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '删除成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : '删除失败 ';
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }
}
