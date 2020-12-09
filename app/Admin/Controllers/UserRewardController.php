<?php

namespace App\Admin\Controllers;


use App\Admin\Extensions\Tools\Filter;
use App\Http\Controllers\Controller;
use App\Repository\LastTrade;
use App\Repository\RewardReferral;
use App\Repository\RewardRegister;
use App\Repository\UserTradeStatus;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class UserRewardController extends Controller
{
    use ModelForm;

    /*
     * 获取用户的注册奖励
     */
    public function reward_register()
    {
        return Admin::content(function (Content $content) {
            $content->header('用户注册奖励');
            $content->description('用户注册奖励');
            $content->body($this->grid());
        });
    }

    /*
     * 获取用户的推荐奖励
     */
    public function reward_referral()
    {
        return Admin::content(function (Content $content) {
            $content->header('用户推荐奖励');
            $content->description('用户推荐奖励');
            $content->body($this->referral_grid());
        });
    }

    /*
     * 获取用户挖矿奖励列表
     */
    public function mining_reward()
    {
        return Admin::content(function (Content $content) {
            $content->header('挖矿奖励');
            $content->description('挖矿奖励');
            $content->body($this->mining_grid());
        });
    }

    /*
     * 获取用户分红奖励列表
     */
    public function share_reward()
    {
        return Admin::content(function (Content $content) {
            $content->header('分红奖励');
            $content->description('分红奖励');
            $content->body($this->share_grid());
        });
    }

    /**
     * 用户盈亏情况
     */
    public function user_tradeStatus()
    {
        return Admin::content(function (Content $content) {
            $content->header('用户交易盈亏情况');
            $content->description('用户交易盈亏情况');
            $content->body($this->user_tradeStatusGrid());
        });
    }

    /**
     * 统计用户某一个交易对的盈亏情况
     */
    public function last_trade()
    {
        if (empty(request('coin_name'))) {
            $market = java_get('market', ['pageNo' => 1, 'pageSize' => 20]);
            if (isset($market['statusCode']) && $market['statusCode'] == 0) {
                request()->offsetSet('coin_name', $market['content'][0]['coinName'] . '/' . $market['content'][0]['settlementCurrency']);
            }
        }
        return Admin::content(function (Content $content) {
            $content->header('交易对实时盈亏');
            $content->description('交易对实时盈亏');
            $content->body($this->last_trade_grid());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(RewardRegister::class, function (Grid $grid) {
            $grid->id("ID");
            $grid->mobile('手机号');
            $grid->realName('真实姓名');
            $grid->coinName('币种');
            $grid->rewardAmount('奖励数量');
            $grid->rewardTime('注册时间');
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $filter->SetParams(array('lable' => '手机号', 'name' => 'mobile'))->text();
                $filter->SetParams(array('lable' => '推荐人手机号', 'name' => 'referrerMobile'))->text();
                $filter->SetParams(array('lable' => '开始时间', 'name' => 'startTime'))->datetime();
                $filter->SetParams(array('lable' => '结束时间', 'name' => 'endTime'))->datetime();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
            });
            $grid->disableActions();
            $grid->disableExport();
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function referral_grid()
    {
        return Admin::grid(RewardReferral::class, function (Grid $grid) {
            $grid->id("ID");
            $grid->mobile('手机号');
            $grid->referrerMobile('推荐人电话');
            $grid->coinName('币种');
            $grid->rewardAmount('奖励数量');
            $grid->rewardTime('推荐时间');
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $filter->SetParams(array('lable' => '手机号', 'name' => 'mobile'))->text();
                $filter->SetParams(array('lable' => '推荐人手机号', 'name' => 'referrerMobile'))->text();
                $filter->SetParams(array('lable' => '开始时间', 'name' => 'startTime'))->datetime();
                $filter->SetParams(array('lable' => '结束时间', 'name' => 'endTime'))->datetime();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
            });
            $grid->disableActions();
            $grid->disableExport();
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function mining_grid()
    {
        return Admin::grid(RewardRegister::class, function (Grid $grid) {
            $grid->id("ID");
            $grid->realName('真实姓名');
            $grid->mobile('手机号');
            $grid->recommendBonus('推荐人奖励');
            $grid->referrerMobile('推荐人电话');
            $grid->coinName('币种');
            $grid->settlementCurrency('结算币种');
            $grid->tradeBonus('奖励数量');
            $grid->tradeFee('推荐时间');
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $filter->SetParams(array('lable' => '手机号', 'name' => 'mobile'))->text();
                $filter->SetParams(array('lable' => '推荐人手机号', 'name' => 'referrerMobile'))->text();
                $filter->SetParams(array('lable' => '开始时间', 'name' => 'startTime'))->datetime();
                $filter->SetParams(array('lable' => '结束时间', 'name' => 'endTime'))->datetime();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
            });
            $grid->disableActions();
            $grid->disableExport();
        });
    }

    protected function share_grid()
    {
        return Admin::grid(RewardRegister::class, function (Grid $grid) {
            $grid->id("ID");
            $grid->realName('真实姓名');
            $grid->mobile('手机号');
            $grid->userBaseCoin('持有平台币数量');
            $grid->userBonus('分红数量');
            $grid->coinName('币种');
            $grid->createTime('分红时间');
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $filter->SetParams(array('lable' => '手机号', 'name' => 'mobile'))->text();
                $filter->SetParams(array('lable' => '推荐用户姓名', 'name' => 'realName'))->text();
                $filter->SetParams(array('lable' => '开始时间', 'name' => 'startTime'))->datetime();
                $filter->SetParams(array('lable' => '结束时间', 'name' => 'endTime'))->datetime();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
            });
            $grid->disableActions();
            $grid->disableExport();
        });
    }

    protected function user_tradeStatusGrid()
    {
        return Admin::grid(UserTradeStatus::class, function (Grid $grid) {
            $grid->id("ID");
            $grid->userName('用户名称');
            $grid->market('交易对')->display(function () {
                return $this->coinName . '/' . $this->settlementCurrency;
            });
            $grid->coinBuyTotal('买入总量');
            $grid->coinSellTotal('卖出总量');
            $grid->coinFee('总买入币种手续费');
            $grid->holdCoin('持币数量');
            $grid->holdCoinPrice('持仓价格')->sortable();
            $grid->settlementBuyTotal('花费的结算货币总量');
            $grid->settlementSellTotal('卖单收入总量(含手续费)');
            $grid->settlementCostTotal('总成本');
            $grid->settlementFee('结算货币总手续费');
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
//               $options = [];
                $market = java_get('market', ['pageNo' => 1, 'pageSize' => 20]);
                if (isset($market['statusCode']) && $market['statusCode'] == 0) {
                    foreach ($market['content'] as $vo) {
                        $options[$vo['coinName'] . '/' . $vo['settlementCurrency']] = $vo['coinName'] . '/' . $vo['settlementCurrency'];
                    }
                }
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '交易市场', 'options' => $options, 'name' => 'coin_name'))->select();
//                $filter->SetParams(array('lable' => '开始时间', 'name' => 'startTime'))->datetime();
//                $filter->SetParams(array('lable' => '结束时间', 'name' => 'endTime'))->datetime();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
            });
            $grid->disableActions();
            $grid->disableExport();
        });
    }

    protected function last_trade_grid()
    {
        return Admin::grid(LastTrade::class, function (Grid $grid) {
            $grid->id("ID");
            $grid->userName('用户名称');
            $grid->market('交易对')->display(function () {
                return $this->coinName . '/' . $this->settlementCurrency;
            });
            $grid->profit('浮动盈亏')->sortable()->display(function(){
                return sprintf('%.8f',$this->profit);
            });;
            $grid->coinBuyTotal('买入总量');
            $grid->coinSellTotal('卖出总量');
            $grid->coinFee('总买入币种手续费');
            $grid->holdCoin('持币数量');
            $grid->holdCoinPrice('持仓价格')->sortable()->display(function(){
                return $this->holdCoinPrice;
            });
            $grid->settlementBuyTotal('花费的结算货币总量');
            $grid->settlementSellTotal('卖单收入总量(含手续费)');
            $grid->settlementCostTotal('总成本');
            $grid->settlementFee('结算货币总手续费');
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
//               $options = [];
                $market = java_get('market', ['pageNo' => 1, 'pageSize' => 20]);
                if (isset($market['statusCode']) && $market['statusCode'] == 0) {
                    foreach ($market['content'] as $vo) {
                        $options[$vo['coinName'] . '/' . $vo['settlementCurrency']] = $vo['coinName'] . '/' . $vo['settlementCurrency'];
                    }
                }
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '交易市场', 'options' => $options, 'name' => 'coin_name'))->select();
//                $filter->SetParams(array('lable' => '开始时间', 'name' => 'startTime'))->datetime();
//                $filter->SetParams(array('lable' => '结束时间', 'name' => 'endTime'))->datetime();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
            });
            $grid->disableActions();
            $grid->disableExport();
        });
    }
}
