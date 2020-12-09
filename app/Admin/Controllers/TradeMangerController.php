<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DetailButton;
use App\Admin\Extensions\Export\CsvExport;
use App\Admin\Extensions\Tools\Filter;
use App\Http\Controllers\Controller;
use App\Repository\TradeAll;
use App\Repository\TradeDetail;
use App\Repository\TradeLog;
use App\Repository\TradeManager;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use App\Admin\Extensions\Reset;

class TradeMangerController extends Controller
{
    use ModelForm;

    /*
     * 获取交易管理列表
     */
    public function index()
    {
        if (empty(request('type'))) {
            request()->offsetSet('type', 'BUY');
        }
        $market = java_get('market', ['pageNo' => 1, 'pageSize' => 100]);
        if (isset($market['statusCode']) && $market['statusCode'] == 0) {
            if (empty(request('coin_name'))) {
                request()->offsetSet('coin_name', $market['content'][0]['coinName'] . '/' . $market['content'][0]['settlementCurrency']);
            }
        }
        return Admin::content(function (Content $content) {
            $minute = 1;
            $result = java_get('/timeMonitoring/get-timeMonitoring-detail-byType', ['monitoringType' => 'TIME_MONITORING_TRADE']);
            if (isset($result['statusCode']) && $result['statusCode'] == 0) {
                $minute = $result['content']['numMinutes'];
            }
            $content->header('挂单监控页面');
            $content->description('挂单监控页面');
            $content->body($this->grid(true, $minute));
        });
    }

    public function tradeAll()
    {
        if (empty(request('type'))) {
            request()->offsetSet('type', 'ALL');
        }
        if (empty(request('status'))) {
            request()->offsetSet('status', 'ALL');
        }
        $market = java_get('market', ['pageNo' => 1, 'pageSize' => 100]);
        if (isset($market['statusCode']) && $market['statusCode'] == 0) {
            if (empty(request('coin_name'))) {
                request()->offsetSet('coin_name', $market['content'][0]['coinName'] . '/' . $market['content'][0]['settlementCurrency']);
            }
        }
//        if(empty(request('addStartTime'))){
//            \request()->offsetSet('addStartTime',date('Y-m-d'." 00:00:00"));
//        }
//        if(empty(request('addEndTime'))){
//            \request()->offsetSet('addEndTime',date('Y-m-d'." 23:59:59"));
//        }
        return Admin::content(function (Content $content) {
            $content->header('币币交易委托列表');
            $content->description('币币交易委托列表');
            $content->body($this->tradeAll_grid());
        });
    }

    /*
     * 获取撮合明细
     */
    public function trade_detail(Request $request, $id)
    {
        $request->offsetSet('tradeId', $id);
        return Admin::content(function (Content $content) {

            $content->header('交易管理');
            $content->description('撮合明细');
            $content->body($this->trade_grid());
        });
    }

    protected function tradeAll_grid()
    {
        return Admin::grid(TradeAll::class, function (Grid $grid) {
            $grid->id("ID");
            $grid->userName('用户名');
            $grid->realName('真实姓名');
//            $grid->mobile('手机号');
            $grid->coinName('币种名称');
            $grid->type('交易类型')->display(function ($type) {
                return $type == "BUY" ? '买单' : '卖单';
            });
            $grid->price('单价');
            $grid->amount('委托数量');
            $grid->dealAmount('成交数量');
            $grid->dealPrice('成交价格');
            $grid->status('交易状态')->display(function ($status) {
                if (isset(TradeAll::$tradeStatus[$status])) {
                    return TradeAll::$tradeStatus[$status];
                } else {
                    return '未知状态';
                }
            });
            $grid->finishTradeTme('交易完全成交时间');
            $grid->createdAt('委托时间');

            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $options = [];
                $market = java_get('market', ['pageNo' => 1, 'pageSize' => 100]);
                if (isset($market['statusCode']) && $market['statusCode'] == 0) {
                    foreach ($market['content'] as $vo) {
                        $options[$vo['coinName'] . '/' . $vo['settlementCurrency']] = $vo['coinName'] . '/' . $vo['settlementCurrency'];
                    }
                }
                $filter->SetParams(array('lable' => '成交开始时间 ', 'name' => 'startLastTradeTime'))->datetime();
                $filter->SetParams(array('lable' => '成交结束时间 ', 'name' => 'endLastTradeTime'))->datetime();
                $filter->SetParams(array('lable' => '委托开始时间 ', 'name' => 'addStartTime'))->datetime();
                $filter->SetParams(array('lable' => '委托结束时间 ', 'name' => 'addEndTime'))->datetime();
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '手机号', 'name' => 'mobile'))->text();
                $filter->SetParams(array('lable' => '交易市场', 'options' => $options, 'name' => 'coin_name'))->select();
                $filter->SetParams(array('lable' => '数量', 'name' => 'amount'))->text();
                $filter->SetParams(array('lable' => '单价', 'name' => 'price'))->text();
                $filter->SetParams(array('lable' => '成交状态', 'options' => TradeAll::$tradeStatus, 'name' => 'status'))->select();
                $filter->SetParams(array('lable' => '交易类型', 'options' => ['BUY' => '买入', 'SELL' => '卖出'], 'name' => 'type'))->select();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->append(new DetailButton($actions->row, 'trade/detail', 'detail', 'id', '撮合明细'));
                if($actions->row->status == 'OPEN'){
                    $actions->append(new Reset($actions->row, 'cancel/tradeManager', '撤销', 'cancel_trade', 'trade/cancel-trade'));
                }
            });

        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    public function grid($is_reload = false, $minute = 1)
    {
        return Admin::grid(TradeManager::class, function (Grid $grid) use ($is_reload, $minute) {
            $grid->minute = $minute;
            $grid->is_reload = $is_reload;
            $grid->setView('admin.grid.tradeManagerTable');
            $grid->id("ID");
            $grid->userName('用户名');
            $grid->realName('真实姓名');
            $grid->mobile('手机号');
            $grid->marketName('交易对')->display(function () {
                return $this->coinName . '/' . $this->settlementCurrency;
            });
            $grid->type('交易类型')->display(function ($type) {
                return $type == "BUY" ? '买单' : '卖单';
            });
            $grid->price('单价');
            $grid->amount('委托数量');
            $grid->dealAmount('成交数量');
            $grid->dealPrice('成交价格');
            $grid->status('交易状态')->display(function ($status) {
                switch ($status) {
                    case "OPEN":
                        return "未完全成交";
                    case "DEAL":
                        return "完全成交";
                    case "CANCELED":
                        return "已经撤单";
                    default :
                        return '未完成';
                }
            });
            $grid->createdAt('委托时间');

            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $options = [];
                $market = java_get('market', ['pageNo' => 1, 'pageSize' => 100]);
                if (isset($market['statusCode']) && $market['statusCode'] == 0) {
                    foreach ($market['content'] as $vo) {
                        $options[$vo['coinName'] . '/' . $vo['settlementCurrency']] = $vo['coinName'] . '/' . $vo['settlementCurrency'];
                    }
                }
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '交易市场', 'options' => $options, 'name' => 'coin_name'))->select();
                $filter->SetParams(array('lable' => '手机号', 'name' => 'mobile'))->text();
                $filter->SetParams(array('lable' => '交易类型', 'options' => ['BUY' => '买入', 'SELL' => '卖出'], 'name' => 'type'))->select();
                $tools->append($filter->render('/admin/tradeManager'));
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableExport();
            $grid->disableCreateButton();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->append(new DetailButton($actions->row, 'trade/detail', 'detail', 'id', '撮合明细'));
            });

        });
    }

    public function grid_html(Request $request)
    {
        $params = $request->only(['coin_name', 'mobile', 'realName', 'type', 'coin_name']);
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);
        $params['pageNo'] = $page;
        $params['pageSize'] = $perPage;
        if (!isset($params['coin_name']) || empty($params['coin_name'])) {
            $market = java_get('market', ['pageNo' => 1, 'pageSize' => 100]);
            if (isset($market['statusCode']) && $market['statusCode'] == 0) {
                if (empty(request('coin_name'))) {
                    $params['coin_name'] = $market['content'][0]['coinName'] . '/' . $market['content'][0]['settlementCurrency'];
                }
            }
        }
        if (!isset($params['type']) || empty($params['type'])) {
            $params['type'] = 'BUY';
        }
        $params['coinName'] = explode('/', $params['coin_name'])[0];
        $params['settlementCurrency'] = explode('/', $params['coin_name'])[1];
        unset($params['coin_name']);
        $result = java_get('/trade', $params);
        $data = [];
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $data = $result['content'];
        }
        return view('admin.grid.html.tradeManager')->with(compact('data'));
    }

    protected function trade_grid()
    {
        return Admin::grid(TradeDetail::class, function (Grid $grid) {
            $grid->id("ID");
            $grid->realName('真实姓名');
            $grid->mobile('手机号');
            $grid->coinName('币种名称');
            $grid->type('交易类型')->display(function ($type) {
                return $type == "BUY" ? '买单' : '卖单';
            });
            $grid->amount('委托数量');
            $grid->fee('手续费');
            $grid->price('单价');
            $grid->createdAt('委托时间');

            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableCreateButton();
//            $grid->actions(function ($actions) {
//                $actions->disableDelete();
//                $actions->disableEdit();
//                $actions->append(new DetailButton($actions->row, 'trade/detail', 'detail', 'id', '撮合明细'));
//            });
            $grid->enableBack();
            $grid->disableExport();
            $grid->disableActions();
        });
    }

    public function tradeLog()
    {
        return Admin::content(function (Content $content) {

            $content->header('币币交易手续费');
            $content->description('币币交易手续费');
            $content->body($this->tradeLog_grid());
        });
    }

    protected function  tradeLog_grid()
    {
        return Admin::grid(TradeLog::class, function (Grid $grid) {
            $grid->id("ID");
            $grid->buyTradeId('挂单ID');
            $grid->buyUserName('买方姓名');
            $grid->sellUserName('卖方姓名');
            $grid->coinName('交易市场')->display(function (){
                return $this->coinName . '/' . $this->settlementCurrency;
            });
            $grid->amount('数量');
            $grid->price('价格');
            $grid->addTime('成交时间')->display(function ($addtime){

                return date('Y-m-d H:i:s',$addtime);
            });
            $grid->buyFeeCoin('买方手续费')->display(function($buyFeeCoin){
                return sprintf('%.8f', $buyFeeCoin);
            });;
            $grid->sellFeeCurrency('卖方手续费')->display(function($sellFeeCurrency){
                return sprintf('%.8f', $sellFeeCurrency);
            });
            /*$grid->buyRecUserName('1代买方用户名');
            $grid->buyRecRet('1代买方返佣')->display(function($buyRecRet){
                return sprintf('%.8f', $buyRecRet);
            });

            $grid->buyRec2UserName('2代买方用户名');
            $grid->buyRec2Ret('2代买方返佣')->display(function($buyRec2Ret){
                return sprintf('%.8f', $buyRec2Ret);
            });*/
//            $grid->recUserRate('手续费返还推荐用户的比例');
//            $grid->rec2UserRate('手续费返还二级推荐用户的比例');
            /*$grid->sellRecUserName('1代卖方用户名');
            $grid->sellRecRet('1代卖方返佣')->display(function($sellRecRet){
                return sprintf('%.8f', $sellRecRet);
            });
            $grid->sellRec2UserName('2代卖方用户名');
            $grid->sellRec2Ret('2代卖方返佣')->display(function($sellRec2Ret){
                return sprintf('%.8f', $sellRec2Ret);
            });*/
//            $grid->buyRetCoin('买方返还币种')->display(function (){
//                if(!$this->buyRetCoin){
//                    return $this->coinName;
//                }else{
//                    return $this->buyRetCoin;
//                }
//            });
//            $grid->sellRetCoin('卖方返还币种')->display(function (){
//                if(!$this->sellRetCoin){
//                    return $this->settlementCurrency;
//                }else{
//                    return $this->sellRetCoin;
//                }
//            });
//            $grid->retTime('返还时间');
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $options = [];
                $market = java_get('market', ['pageNo' => 1, 'pageSize' => 100]);
                if (isset($market['statusCode']) && $market['statusCode'] == 0) {
                    foreach ($market['content'] as $vo) {
                        $options[$vo['coinName'] . '/' . $vo['settlementCurrency']] = $vo['coinName'] . '/' . $vo['settlementCurrency'];
                    }
                }
                $filter->SetParams(array('lable' => '买方姓名 ', 'name' => 'buyUserName'))->text();
                $filter->SetParams(array('lable' => '卖方姓名 ', 'name' => 'sellUserName'))->text();
                $filter->SetParams(array('lable' => '交易市场', 'options' => $options, 'name' => 'coin_name'))->select();
                $filter->SetParams(array('lable' => '是否只显示返佣', 'options' => ['1'=>'只显示返佣'], 'name' => 'type'))->select();
                $filter->SetParams(array('lable' => '成交开始时间 ', 'name' => 'startLastTradeTime'))->datetime();
                $filter->SetParams(array('lable' => '成交结束时间 ', 'name' => 'endLastTradeTime'))->datetime();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableCreateButton();
//            $grid->actions(function ($actions) {
//                $actions->disableDelete();
//                $actions->disableEdit();
//                $actions->append(new DetailButton($actions->row, 'trade/detail', 'detail', 'id', '撮合明细'));
//            });
//            $grid->disableExport();
            $grid->exporter((new CsvExport($grid, new TradeLog())));
            $grid->disableActions();
        });
    }

    public function cancelTradeManager(Request $request)
    {
        $params['tradeId'] = $request->post('id');
        $action = $request->post('action');
        $result = java_get($action, $params);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '撤销成功'];
        } else {
            $arr = ['status' => false, 'message' => isset($result['errorMessage']) ? $result['errorMessage'] : '撤销失败'];
        }
        return response()->json($arr);
    }
}
