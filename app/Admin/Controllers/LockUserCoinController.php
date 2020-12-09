<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/11/7
 * Time: 上午9:55
 */

namespace App\Admin\Controllers;


use App\Admin\Extensions\Tools\Filter;
use App\Http\Controllers\Controller;
use App\Repository\LockDetail;
use App\Repository\LockUserCoin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class LockUserCoinController extends Controller
{
    public function setLock()
    {
        $corn = [];
        $result = java_post('dispatch/get-cron-config', []);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $corn = $result['content'];
        }
        return Admin::content(function (Content $content) use ($corn) {
            $content->body(view('admin.system.setLock', compact('corn')));
        });

    }

    public function lockCoin(Request $request)
    {
        $params = $request->all();
        if ($params['type'] == 1) {
            $data['coinName'] = $params['coinName'];
            $data['cronId'] = $params['cronId'];
            $data['lockRate'] = $params['lockRate'];
            $data['releaseRate'] = $params['releaseRate'];
            $data['userNames'] = $params['userNames'];
            $result = java_post('lock-coin/lock-user-coin', $data);
        } else {
            $data['coinName'] = $params['coinName'];
            $data['cronId'] = $params['cronId'];
            $data['lockRate'] = $params['lockRate'];
            $data['releaseRate'] = $params['releaseRate'];
            $data['groupType'] = $params['groupType'];
            $result = java_post('lock-coin/lock-group-coin', $data);
        }
        if (!isset($result['statusCode']) || $result['statusCode'] != 0) {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : '操作失败';
            $error = new MessageBag([
                'title' => '操作提示',
                'message' => $message,
            ]);
            session()->flash('error', $error);
            return back()->withInput()->with(compact('error'));
        }
        $error = new MessageBag([
            'title' => '操作提示',
            'message' => '操作成功',
        ]);
        session()->flash('success', $error);
        return redirect('/admin/lockUserCoin');
    }

    public function lockHistory()
    {
        return Admin::content(function (Content $content) {

            $content->header('用户锁定资产释放记录');
            $content->description('用户锁定资产释放记录');

            $content->body($this->grid());
        });
    }

    protected function grid()
    {
        return Admin::grid(LockUserCoin::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->userName('用户名称');
            $grid->coinName('币种名称');
            $grid->cronName('释放策略名称');
            $grid->lockNo("锁仓批次");
            $grid->lockAmount('锁仓币种个数');
            $grid->lockRate('锁仓比例');
            $grid->releaseAmount('每次释放个数');
            $grid->releaseRate('每次释放比例');
            $grid->createTime('创建时间');
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '币种名称', 'options' => getCoin(), 'name' => 'coinName'))->select();
                $filter->SetParams(array('lable' => '开始时间', 'options' => [], 'name' => 'startTime'))->datetime();
                $filter->SetParams(array('lable' => '结束时间', 'options' => [], 'name' => 'endTime'))->datetime();
                $tools->append($filter->render());
            });
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
            });
        });
    }

    public function lockDetail()
    {
        return Admin::content(function (Content $content) {

            $content->header('用户锁定资产返还明细');
            $content->description('用户锁定资产返还明细');

            $content->body($this->gridlockDetail());
        });
    }

    protected function gridlockDetail()
    {
        return Admin::grid(LockDetail::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->realName('真实姓名');
            $grid->userName('用户名称');
//            $grid->groupName('分组类型');
            $grid->coinName('币种名称');
            $grid->comment('交易详情');
//            $grid->cronName('释放策略名称');
            $grid->subType("账户特性")->display(function ($subType){
                return $subType ? '冻结余额' : '可用余额';
            });
            $grid->changeAmount('交易数量');
            $grid->lastTime('时间');
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '币种名称', 'options' => getCoin(), 'name' => 'coinName'))->select();
                $filter->SetParams(array('lable' => '开始时间', 'options' => [], 'name' => 'startTime'))->datetime();
                $filter->SetParams(array('lable' => '结束时间', 'options' => [], 'name' => 'endTime'))->datetime();
                $tools->append($filter->render());
            });
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
            });
        });
    }
}