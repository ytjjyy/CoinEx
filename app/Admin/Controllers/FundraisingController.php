<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/23
 * Time: 15:49
 */
namespace App\Admin\Controllers;

use App\Admin\Extensions\DeleteButton;
use App\Admin\Extensions\Reset;
use App\Admin\Extensions\Tools\Filter;
use App\Repository\FundInvest;
use App\Repository\FundRaising;
use App\Repository\FundTrade;
use App\Repository\FundTradeLog;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class FundraisingController extends Controller
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

            $content->header('资金托管项目列表');
            $content->description('资金托管项目列表');

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

            $content->header('修改资金托管项目');
            $content->description('修改资金托管项目');

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

            $content->header('添加资金托管项目');
            $content->description('添加资金托管项目');

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
        return Admin::grid(FundRaising::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->userId("操盘用户ID");
            $grid->name("项目名称");
            $grid->coinName('币种名称');
            $grid->raisingBalance('总募集资产');
            $grid->serviceFeeRate('手续费，0.1表示10%');
            $grid->minInvestBalance('用户每次最低投资');
            $grid->maxInvestBalance('用户每次最高投资');
            $grid->createTime("创建时间");
            $grid->investCount("用户总投资次数");
            $grid->finishRaisingTime("募集完成时间");
            $grid->finishRaisingBalance("完成时募集的资产数");
            $grid->returnTime("返还收益的时间");
            $grid->returnBalance("返还收益的总资产数");
            $grid->platProfitRate("平台分成收益比例");
            $grid->platProfit("平台收益");
            $grid->status("当前状态")->display(function ($status) {
                switch ($status) {
                    case '0':
                        return '募集资金';
                    case '1':
                        return '操盘中';
                    case '2':
                        return '返还收益';
                    case '10':
                        return '取消';
                }
            });
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                if($actions->row->status != 0) {
                    $actions->disableEdit();
                }
                if($actions->row->status == 0) {
                    // 取消项目
                    $actions->append(new Reset($actions->row, '/admin/fundraising/cancel-fundraising', '取消项目', 'cancel', ''));
                }
                else if($actions->row->status == 1) {
                    // 操盘
                    $actions->append("<a href='fundraising-operate?id=" . $actions->row['id'] .  "'>进行操盘</a>");
                    // 分配收益
                    $actions->append(new Reset($actions->row, '/admin/fundraising/profit-fundraising', '结束分配收益', 'profit', ''));
                }
            });
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $filter->SetParams(array('lable' => '币种名称', 'options' => getCoin(), 'name' => 'coinName'))->select();
                $filter->SetParams(array('lable' => '状态', 'options' => [ '0' => '募集资金', '1' => '操盘中','2'=>'返还收益','10'=>'取消'], 'name' => 'status'))->select();
                $filter->SetParams(array('lable' => '创建开始时间 ', 'name' => 'beginCreateTime'))->datetime();
                $filter->SetParams(array('lable' => '创建结束时间 ', 'name' => 'endCreateTime'))->datetime();
                $tools->append($filter->render());
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
        return Admin::form(FundRaising::class, function (Form $form) {
            $form->text('name', '项目名称');
            $form->select('coinName', '币种名称')->options(getCoin());
            $form->text('raisingBalance', '总募集资产');
            $form->text('serviceFeeRate', '手续费，0.1表示10%');
            $form->text('minInvestBalance', '用户每次最低投资的资产');
            $form->text('maxInvestBalance', '用户每次最高投资的资产');
            $form->text('platProfitRate', '平台分成利润比例，0.1表示10%');
            $form->editor('description', '项目介绍');
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


    /*
     * 取消资金托管项目
     */
    public function cancelFundraising(Request $request)
    {
        $params['id'] = $request->post('id');
        $result = java_post('fundraising/cancel-fundraising', $params);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : "确认失败";
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }

    /*
     * 资金托管项目返回收益
     */
    public function profitFundraising(Request $request)
    {
        $params['id'] = $request->post('id');
        $result = java_post('fundraising/profit-fundraising', $params);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : "确认失败";
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }

    protected function operateView($id, $errorMsg = null) {
        $params['id'] = $id;
        $fundraisingContent = java_get("fundraising/fundraising-info", $params);
        if (isset($fundraisingContent['statusCode']) && $fundraisingContent['statusCode'] == 0) {
            $fundraising = $fundraisingContent['content'];
        }

        $market = java_get('market', ['pageNo' => 1, 'pageSize' => 200]);
        if (isset($market['statusCode']) && $market['statusCode'] == 0) {
            foreach ($market['content'] as $vo) {
                $markets[$vo['coinName'] . '/' . $vo['settlementCurrency']] = $vo['coinName'] . '/' . $vo['settlementCurrency'];
            }
        }

        // 获取资金
        $balances = null;
        $balancesContent = $fundraisingContent = java_get("fundraising/fund-raising-balance", ['fundRaisingId' => $id]);
        if (isset($balancesContent['statusCode'])) {
            if($balancesContent['statusCode'] != 0) {
                $errorMsg = "\r\n获取资金失败，statusCode=" . $balancesContent['statusCode'] . ",msg=" . $balancesContent['errorMessage'];
            } else {
                $balances = $balancesContent['content'];
            }
        } else {
            $errorMsg = "\r\n获取资金失败:" . $balancesContent;
        }

        return Admin::content(function (Content $content) use ($fundraising, $markets, $errorMsg, $balances) {
            $content->body(view('admin.fundraising.operate', ['fundraising' => $fundraising, 'markets' => $markets,
                'errorMsg' => $errorMsg, 'balances' => $balances]));
        });
    }

    /*
     * 操盘
     */
    public function operate(Request $request)
    {
        return $this->operateView($request->post('id'), null);
    }

    /**
     * 下单
     */
    public function createTrade(Request $request)
    {
        $errorMsg = null;
        $params = $request->post();
        $tradeResult = java_post("fundraising/create-trade", $params);
        if (isset($tradeResult['statusCode'])) {
            if($tradeResult['statusCode'] != 0) {
                $errorMsg = "下单失败，statusCode=" . $tradeResult['statusCode'] . ",msg=" . $tradeResult['errorMessage'];
            }
        } else {
            $errorMsg = "下单失败:" . $tradeResult;
        }
        return $this->operateView($request->post('fundRaisingId'), $errorMsg);
    }


    /**
     * Index interface.
     *
     * @return Content
     */
    public function investList()
    {
        return Admin::content(function (Content $content) {

            $content->header('普通用户资金托管记录');
            $content->description('普通用户资金托管记录');

            $content->body($this->investGrid());
        });
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function investGrid()
    {
        return Admin::grid(FundInvest::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->userId('用户ID');
            $grid->userName('用户名');
            $grid->raisingId('项目ID');
            $grid->raisingName("项目名称");
            $grid->coinName('币种名称');
            $grid->balance('投资数量');
            $grid->fee('手续费（平台币）');
            $grid->createTime("投资时间");
            $grid->returnBalance('总回馈');
            $grid->returnTime("回馈时间");
            $grid->disableActions();
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $filter->SetParams(array('lable' => '币种名称', 'options' => getCoin(), 'name' => 'coinName'))->select();
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '托管项目ID', 'name' => 'fundRaisingId'))->text();
                $filter->SetParams(array('lable' => '投资开始时间 ', 'name' => 'beginCreateTime'))->datetime();
                $filter->SetParams(array('lable' => '投资结束时间 ', 'name' => 'endCreateTime'))->datetime();
                $tools->append($filter->render());
            });
            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableCreateButton();
        });
    }

    //// 挂单列表
    public function openTradeList() {
        return Admin::content(function (Content $content) {
            $content->header('当前挂单');
            $content->description('当前挂单');
            $content->body($this->openTradeListGrid());
        });
    }

    protected function openTradeListGrid() {
        return Admin::grid(FundTrade::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->type('类型');
            $grid->coinName('交易币种');
            $grid->settlementCurrency('结算币种');
            $grid->price("价格");
            $grid->amount('委托数量');
            $grid->dealAmount('已成交交易币种数量');
            $grid->dealCurrency('已成交结算币种数量');
            $grid->createdAt('下单时间');
            $grid->disableFilter();
            $grid->disableExport();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->append(new Reset($actions->row, '/admin/fundraising/cancel-trade', '取消订单', 'cancel', $actions->row['userId']));
            });
            $grid->disableCreateButton();
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }

    // 取消订单
    protected function cancelTrade(Request $request) {
        $params['id'] = $request->post('id');
        $params['userId'] = $request->post('action');
        $result = java_post('fundraising/cancel-trade', $params);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '取消成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : "取消失败";
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }


    //// 历史成交列表
    public function tradeLogList() {
        return Admin::content(function (Content $content) {
            $content->header('成交记录');
            $content->description('成交记录');
            $content->body($this->tradeLogListGrid());
        });
    }

    protected function tradeLogListGrid() {
        return Admin::grid(FundTradeLog::class, function (Grid $grid) {
            //$grid->id('ID');
            $grid->type('类型');
            $grid->coinName('交易币种');
            $grid->settlementCurrency('结算币种');
            $grid->price("价格");
            $grid->amount('成交数量');
            $grid->createTime('成交时间');
            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }
}