<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\Filter;
use App\Repository\DispatchCoin;
use App\Repository\DispatchConfig;
use App\Repository\ReleaseReport;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use App\Repository\AdminChangeRfBill;

class ReleaseController extends Controller
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

            $content->header('锁仓配置列表');
            $content->description('锁仓配置列表');

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

            $content->header('锁仓配置修改');
            $content->description('锁仓配置修改');

            $content->body($this->form()->edit($id));
        });
    }

    public function dispatch_view()
    {
        $dispatch_config = array();
        $result = java_get('dispatch/get-dispatch-config');
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $dispatch_config = $result['content'];
        }
        $coinName = [];
        $coin = java_get('coin');
        if (isset($coin['statusCode']) && $coin['statusCode'] == 0) {
            $coinName = $coin['content'];
        }
        return Admin::content(function (Content $content) use ($dispatch_config, $coinName) {
            $content->body(view('admin.system.dispatch_view', compact('dispatch_config', 'coinName')));
        });
    }

    public function dispatch_post(Request $request)
    {
        $params = $request->only(['dispatchId', 'amount', 'comment', 'phone', 'coinName']);
        if (is_null($params['dispatchId']) || empty($params['dispatchId'])) {
            $error = new MessageBag([
                'title' => '操作提示',
                'message' => '请选择锁仓配置',
            ]);
            session()->flash('error', $error);
            return back()->withInput();
        }
        if (is_null($params['coinName']) || empty($params['coinName'])) {
            $error = new MessageBag([
                'title' => '操作提示',
                'message' => '请选择币种',
            ]);
            session()->flash('error', $error);
            return back()->withInput();
        }
        if (is_null($params['amount']) || empty($params['amount'])) {
            $error = new MessageBag([
                'title' => '操作提示',
                'message' => '请填写拨币的数量',
            ]);
            session()->flash('error', $error);
            return back()->withInput();
        }
        $params['phone'] = explode(',', $params['phone']);
        $userInfo = java_post("user/get-user-by-user-name", $params['phone'], ['Content-Type:application/json']);
        if (isset($userInfo['statusCode']) && $userInfo['statusCode'] == 0) {
            if (empty($userInfo['content'])) {
                $error = new MessageBag([
                    'title' => '操作提示',
                    'message' => '未找到用户',
                ]);
                session()->flash('error', $error);
                return back()->withInput();
            }
        } else {
            $message = isset($userInfo['errorMessage']) ? $userInfo['errorMessage'] : '操作接口错误';
            $error = new MessageBag([
                'title' => '操作提示',
                'message' => $message,
            ]);
            session()->flash('error', $error);
            return back()->withInput();
        }
        $data = [];
        $data['dispatchId'] = $params['dispatchId'];
        $data['comment'] = $params['comment'];
        $data['list'] = [];
        foreach ($userInfo['content'] as $key => $item) {
            $data['list'][$key]['amount'] = $params['amount'];
            $data['list'][$key]['coinName'] = $params['coinName'];
            $data['list'][$key]['mobile'] = $item['mobile'];
            $data['list'][$key]['userId'] = $item['id'];
        }
        $result = java_post('dispatch/dispatch', $data, ['Content-Type:application/json']);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $success = new MessageBag([
                'title' => '操作提示',
                'message' => '拨币成功',
            ]);
            session()->flash('success', $success);
            return redirect('/admin/dispatch');
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : "拨币失败";
            $error = new MessageBag([
                'title' => '操作提示',
                'message' => $message,
            ]);
            session()->flash('error', $error);
            return back()->withInput();
        }

    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('锁仓配置添加');
            $content->description('锁仓配置添加');

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
        return Admin::grid(DispatchConfig::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->cronName('定时任务名称');
            $grid->lockName('锁仓名称');
//            $grid->coinName("币种名称");
            $grid->lockRate('锁仓比例');
            $grid->freeRate('释放比例');
            $grid->status('状态')->display(function ($status) {
                return $status === 'SHOW' ? '开启' : '关闭';
            });
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
//            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
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
        return Admin::form(ReleaseReport::class, function (Form $form) {
            $corn = [];
            $result = java_post('dispatch/get-cron-config', []);
            if (isset($result['statusCode']) && $result['statusCode'] == 0) {
                foreach ($result['content'] as $key => $vo) {
                    $corn[$vo['id']] = $vo['cronName'];
                }
            }
            $form->text('lockName', '锁仓名称');
            $form->radio('status', '状态')->options(['SHOW' => '开启', 'HIDE' => '关闭']);
            $form->select('cronId', '任务名称')->options($corn)->rules('required', ['required' => '任务名称不能为空']);
            $form->text('lockRate', '锁仓比例(0.1为10%)');
            $form->text('freeRate', '释放比例(0.1为10%)');
            $form->hidden("cronName");
            $form->saving(function (Form $form) {
                $result = java_post('dispatch/get-cron-config', []);
                if (isset($result['statusCode']) && $result['statusCode'] == 0) {
                    foreach ($result['content'] as $key => $vo) {
                        if ($vo['id'] == $form->cronId) {
                            $form->cronName = $vo['cronName'];
                        }
                    }
                } else {
                    $form->cronName = '';
                }
            });
            $form->saved(function (Form $form) {
                if (!$form->model()->is_save || !$form->model()->is_add) {
                    $error = new MessageBag([
                        'title' => '操作提示',
                        'message' => '操作失败',
                    ]);
                    session()->flash('error', $error);
                    return back()->with(compact('error'))->withInput();
                }
            });
        });
    }

    /**
     * Index interface.
     *
     * @return Content
     */
    public function release_report()
    {
        return Admin::content(function (Content $content) {

            $content->header('释放报告');
            $content->description('释放报告');

            $content->body($this->report_grid());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function report_grid()
    {
        return Admin::grid(ReleaseReport::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->mobile('手机号');
            $grid->coinName('币种名称');
            $grid->amount('数量');
            $grid->createTime('创建时间');
            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }

    /*
     * 获取拨币记录
     */
    public function dispatch_coin()
    {
        return Admin::content(function (Content $content) {

            $content->header('拨币记录');
            $content->description('拨币记录');

            $content->body($this->dispatch_grid());
        });
    }

    protected function dispatch_grid()
    {
        return Admin::grid(DispatchCoin::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->userName('用户名');
            $grid->realName('用户真实姓名');
            $grid->coinName('币种名称');
            $grid->dispatch_no('拨币编号');
            $grid->freeRate('释放比例');
            $grid->lockRate('锁仓比例');
            $grid->amountAll('拨币总量');
            $grid->amount('剩余数量');
            $grid->createTime('拨币时间');
            $grid->comment('备注信息');
            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '用户真实姓名', 'name' => 'realName'))->text();
                $filter->SetParams(array('lable' => '币种名称', 'options' => getCoin(), 'name' => 'coinName'))->select();
                $tools->append($filter->render());
            });
        });
    }

    public function global_monitor()
    {
        $config = [];
        $result = java_get('global-monitor/get-global-monitor-status');
        if(isset($result['statusCode']) && $result['statusCode'] == 0){
            $config = $result['content'];
            $config['hfTradeListDetail'] = '';
            $config['tradeListDetail']='';
            if(!empty($config['tradeList'])){
                foreach($config['tradeList'] as $v){
                    $config['tradeListDetail'].=$v.' ';
                }
            }
            if(!empty($config['tradeList'])){
                foreach($config['tradeList'] as $v){
                    $config['hfTradeListDetail'].=$v.' ';
                }
            }
        }
        return Admin::content(function (Content $content) use ($config) {
            $content->body(view('admin.system.monitor',compact('config')));
        });
    }

    //////////////////////////////////////////////////////////////////////////
    /// 直接修改锁定资产的数量（转入锁定）
    public function changeReceiveFreeze()
    {
        $coinName = [];
        $coin = java_get('coin');
        if (isset($coin['statusCode']) && $coin['statusCode'] == 0) {
            $coinName = $coin['content'];
        }
        return Admin::content(function (Content $content) use ($coinName) {
            $content->body(view('admin.system.change_received_freeze_view', compact('coinName')));
        });
    }

    public function changeReceiveFreeze_post(Request $request)
    {
        $params = $request->only([ 'amount', 'phone', 'coinName']);
        if (is_null($params['coinName']) || empty($params['coinName'])) {
            $error = new MessageBag([
                'title' => '操作提示',
                'message' => '请选择币种',
            ]);
            session()->flash('error', $error);
            return back()->withInput();
        }
        if (is_null($params['amount']) || empty($params['amount'])) {
            $error = new MessageBag([
                'title' => '操作提示',
                'message' => '请填写拨币的数量',
            ]);
            session()->flash('error', $error);
            return back()->withInput();
        }
        $params['phone'] = explode(',', $params['phone']);
        $userInfo = java_post("user/get-user-by-user-name", $params['phone'], ['Content-Type:application/json']);
        if (isset($userInfo['statusCode']) && $userInfo['statusCode'] == 0) {
            if (empty($userInfo['content'])) {
                $error = new MessageBag([
                    'title' => '操作提示',
                    'message' => '未找到用户',
                ]);
                session()->flash('error', $error);
                return back()->withInput();
            }
        } else {
            $message = isset($userInfo['errorMessage']) ? $userInfo['errorMessage'] : '操作接口错误';
            $error = new MessageBag([
                'title' => '操作提示',
                'message' => $message,
            ]);
            session()->flash('error', $error);
            return back()->withInput();
        }
        $data = [];
        $data['list'] = [];
        foreach ($userInfo['content'] as $key => $item) {
            $data['list'][$key]['amount'] = $params['amount'];
            $data['list'][$key]['coinName'] = $params['coinName'];
            $data['list'][$key]['mobile'] = $item['mobile'];
            $data['list'][$key]['userId'] = $item['id'];
        }
        $result = java_post('dispatch/change-rf-coin', $data, ['Content-Type:application/json']);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $success = new MessageBag([
                'title' => '操作提示',
                'message' => '拨币成功',
            ]);
            session()->flash('success', $success);
            return redirect('/admin/changeReceiveFreeze');
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : "拨币失败";
            $error = new MessageBag([
                'title' => '操作提示',
                'message' => $message,
            ]);
            session()->flash('error', $error);
            return back()->withInput();
        }

    }



    /*
     * 获取锁定资产拨币流水信息
     */
    public function rf_bill()
    {
        /*if (empty(request()->get('startTime'))) {
            request()->offsetSet('startTime', date('Y-m-d') . " 00:00:00");
        }
        if (empty(request()->get('endTime'))) {
            request()->offsetSet('endTime', date('Y-m-d') . " 23:59:59");
        }*/
        return Admin::content(function (Content $content) {

            $content->header('冻结资产拨币记录');
            $content->description('冻结资产拨币记录');
            $content->body($this->rf_bill_grid());
        });
    }

    protected function rf_bill_grid()
    {
        return Admin::grid(AdminChangeRfBill::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->userName('用户名');
            //$grid->realName('用户真实姓名');
            //$grid->groupName('分租名称');
            $grid->coinName('币种名称');
            /*$grid->subType('账户特性')->display(function ($type) {
                return $type == 0 ? '可用余额' : ($type == 1 ? '冻结资产' : '锁定资产');
            });*/
//            $grid->comment('交易详情');
            $grid->changeAmount('变化数量');
            $grid->lastTime('拨币时间');
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableFilter();
            $grid->disableExport();
        });
    }
}
