<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DeleteButton;
use App\Admin\Extensions\Tools\Filter;
use App\Repository\Leverage;

use App\Repository\LeverageLoan;
use App\Repository\LeverageLend;
use App\Repository\LeverageUser;
use App\Repository\LeverageUserBalance;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class LeverageController extends Controller
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

            $content->header('杠杆交易配置');
            $content->description('杠杆交易配置');

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

            $content->header('修改杠杆交易配置');
            $content->description('修改杠杆交易配置');

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

            $content->header('添加');
            $content->description('添加');

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
        return Admin::grid(Leverage::class, function (Grid $grid) {
            $grid->id('ID');
            //$grid->bondholderUserId('债权人id');
            //$grid->coinDayRate("交易币种日利率");
            $grid->coinMinLoan("交易货币最少借款数量");
            $grid->coinName('币种名称');
            $grid->settlementCurrency('结算货币');
            $grid->explosionRiskRate('爆仓风险率');
            $grid->leverageMultiple('杠杆倍数');
            //$grid->settlementDayRate('结算币种日利率');
            $grid->settlementMinLoan("结算货币最少借款数量");
            $grid->warnRiskRate("预警风险率");
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->append(new DeleteButton($actions->row, '/admin/leverage/destroy', 'destroy', 'id'));
            });
            $grid->disableFilter();
            $grid->disableExport();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Leverage::class, function (Form $form) {

            //$form->text('coinDayRate', '交易币种日利率');
            //$form->text('bondholderUserId', '债权人id');
            $form->text('coinMinLoan', '交易货币最少借款数量');
            $form->select('coinName', '币种名称')->options(getCoin());
            $form->select('settlementCurrency', '结算货币')->options(getCoin());
            $form->text('explosionRiskRate', '爆仓风险率');
            $form->text('leverageMultiple', '杠杆倍数');
            //$form->text('settlementDayRate', '结算币种日利率');
            $form->text('settlementMinLoan', '结算货币最少借款数量');
            $form->text('warnRiskRate', '预警风险率');
            $form->saved(function (Form $form) {
                if (!$form->model()->is_save || !$form->model()->is_add) {
                    $message = isset($form->model()->errorMessage) ? $form->model()->errorMessage : '操作失败';
                    $error = new MessageBag([
                        'title' => '操作提示',
                        'message' => $message,
                    ]);
                    session()->flash('error', $error);
                    return back()->with(compact('error'))->withInput();
                }
            });
        });
    }

    public function destroy(Request $request)
    {
        $param['id'] = $request->post('id');
        $result = java_get('leverage/del-config', $param);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '删除成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : '删除失败';
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }

    /**
     * 获取借款记录
     */
    public function loan()
    {
        if(empty(\request()->get('type'))){
            \request()->offsetSet('type','0');
        }
        return Admin::content(function (Content $content) {

            $content->header('借款记录');
            $content->description('借款记录');

            $content->body($this->loan_grid());
        });
    }

    protected function loan_grid()
    {
        return Admin::grid(LeverageLoan::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->userId("借款用户ID");
            $grid->userName("借款用户名");
            $grid->loanCoinName('借款币种');
            $grid->settlementCurrency('结算币种');
            $grid->coinName('币种名称');
            $grid->lendingId('放贷记录ID');
            $grid->lendingUserId('放贷用户ID');
            $grid->interest("当前所欠利息");
            $grid->repaymentInterest("已还利息总和");
            $grid->loanAmount("本次借款总数");
            $grid->repaymentAmount('已还款数量');
            $grid->status('状态')->display(function ($status) {
                switch ($status) {
                    case '0':
                        return '未完成';
                    case '1':
                        return '已完成';
                }
            });
            $grid->coinDayRate('日利率');
            $grid->loanTime("借款时间");
            $grid->interestTime("最后算息时间");
//            $grid->actions(function ($actions) {
//                $actions->disableDelete();
//                $actions->append(new DeleteButton($actions->row, '/admin/leverage/destroy', 'destroy', 'id'));
//            });
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableExport();
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $options = [];
                $market = java_get('market', ['pageNo' => 1, 'pageSize' => 200]);
                if (isset($market['statusCode']) && $market['statusCode'] == 0) {
                    foreach ($market['content'] as $vo) {
                        $options[$vo['coinName'] . '/' . $vo['settlementCurrency']] = $vo['coinName'] . '/' . $vo['settlementCurrency'];
                    }
                }
                $filter->SetParams(array('lable' => '交易市场', 'options' => $options, 'name' => 'coin_name'))->select();
                $filter->SetParams(array('lable' => '借款币种名称', 'options' => getCoin(), 'name' => 'loanCoinName'))->select();
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '放贷ID', 'name' => 'lendId'))->text();

//                $filter->SetParams(array('lable' => '手机号', 'name' => 'mobile'))->text();
                $filter->SetParams(array('lable' => '状态', 'options' => [ '1' => '未完成','2'=>'已完成'], 'name' => 'type'))->select();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
        });
    }

    /**
     * 获取放贷记录
     */
    public function lend()
    {
        if(empty(\request()->get('type'))){
            \request()->offsetSet('type','-1');
        }
        return Admin::content(function (Content $content) {

            $content->header('放贷记录');
            $content->description('放贷记录');

            $content->body($this->lend_grid());
        });
    }

    protected function lend_grid()
    {
        return Admin::grid(LeverageLend::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->userId('用户ID');
            $grid->userName('用户名');
            $grid->coinName('币种名称');
            $grid->coinDayRate('交易币种日利率');
            $grid->amount("剩余可借数量");
            $grid->amountComplete("已经借出的数量");
            $grid->amountRepayment('已经还款数量');
            $grid->totalInterest('应收利息');
            $grid->interest('已收利息');
            $grid->createTime('创建时间');
            $grid->status('状态')->display(function ($status) {
                switch ($status) {
                    case '0':
                        return '借款中';
                    case '1':
                        return '借完';
                    case '2':
                        return '取消';
                    default:
                        return '状态：' . $status;
                }
            });
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableExport();
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '币种名称', 'options' => getCoin(), 'name' => 'coinName'))->select();
                $filter->SetParams(array('lable' => '状态', 'options' => [ '0' => '借款中', '1' => '借完','2'=>'取消'], 'name' => 'type'))->select();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
        });
    }

    /**
     * 获取杠杆用户信息
     */
    public function user()
    {
        if(empty(\request()->get('type'))){
            \request()->offsetSet('type','-1');
        }
        return Admin::content(function (Content $content) {

            $content->header('杠杆用户信息');
            $content->description('杠杆用户信息');

            $content->body($this->user_grid());
        });
    }

    protected function user_grid()
    {
        return Admin::grid(LeverageUser::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->userId('用户ID');
            $grid->userName('用户名');
            $grid->leverageUserId('杠杆用户ID');
            $grid->coinName('交易币种名称');
            $grid->settlementCurrency('结算币种名称');
            $grid->loanCoin("交易货币借款总数");
            $grid->loanSettlement("结算货币的借款总数");
            $grid->status('状态')->display(function ($status) {
                switch ($status) {
                    case '0':
                        return '正常';
                    case '1':
                        return '爆仓冻结';
                    default:
                        return '状态：' . $status;
                }
            });
            $grid->disableCreateButton();
            //$grid->disableActions();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->append("<a href='leverage-user-balance?userId=" . $actions->row['userId'] .  "' target='_blank'>查看杠杆资产</a>");
            });
            $grid->disableExport();
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $leverageConfigs = [];
                $market = java_get('leverage/config-ls', ['pageNo' => 1, 'pageSize' => 200]);
                if (isset($market['statusCode']) && $market['statusCode'] == 0) {
                    foreach ($market['content'] as $vo) {
                        $leverageConfigs[$vo['coinName'] . '/' . $vo['settlementCurrency']] = $vo['coinName'] . '/' . $vo['settlementCurrency'];
                    }
                }
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '状态', 'options' => [ '0' => '正常', '1' => '爆仓冻结'], 'name' => 'status'))->select();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
        });
    }

    /**
     * 查看杠杆用户资产信息
     */
    public function user_balance()
    {
        return Admin::content(function (Content $content) {

            $content->header('杠杆用户资产信息');
            $content->description('杠杆用户资产信息');

            $content->body($this->user_balance_grid());
        });
    }

    protected function user_balance_grid()
    {
        return Admin::grid(LeverageUserBalance::class, function (Grid $grid) {
            //$grid->id('ID');
            $grid->userId('用户ID');
            //$grid->userName('用户名');
            $grid->coinName('交易币种名称');
            $grid->settlementCurrency('结算币种名称');
            $grid->coinAvailable("交易货币可用资金");
            $grid->coinFreeze("交易货币冻结资金");
            $grid->loanCoin("交易货币借款数");
            $grid->interestCoin("交易货币欠利息总数");
            $grid->settlementAvailable("结算货币可用资金");
            $grid->settlementFreeze("结算货币冻结资金");
            $grid->loanSettlement("结算货币借款数");
            $grid->interestSettlement("结算货币的欠利息总数");
            $grid->riskRate("当前风险率");
            $grid->explosionPrice("爆仓价");
            $grid->amountToCny("折合资产");
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableRowSelector();
        });
    }
}
