<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\DeleteButton;
use App\Admin\Extensions\Status;
use App\Repository\TradeMarket;

use App\Services\OSS;
use Encore\Admin\Exception\Handler;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use \Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Qcloud\Cos\Client;

class TradeMarketController extends Controller
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

            $content->header('交易市场设置');
            $content->description('市场列表');

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

            $content->header('交易市场设置');
            $content->description('修改市场');

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

            $content->header('交易市场');
            $content->description('添加交易市场');

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
        return Admin::grid(TradeMarket::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->coinName('交易币种');
            $grid->settlementCurrency('结算货币');
            $grid->dayExchangeBegin('交易开始时间');
            $grid->dayExchangeEnd('交易结束时间');
            $grid->feeCoin("买入手续费,0.1代表10%");
            $grid->feeCurrency("卖出手续费0.1代表10%");
            $grid->maxExchangeNum('单笔下单最大数量');
            $grid->minExchangeNum('单笔下单最小数量');
            $grid->maxIncrease('最大涨幅');
            $grid->minDecrease('最大跌幅');
            $grid->maxCurrency('最高挂单总价');
            $grid->closed('是否开放市场')->display(function ($closed){
                return $closed=='SHOW'?'已开放':'未开放';
            });
            $grid->disableExport(); //去掉导出按钮


            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->append(new Status($actions->row, 'tradeMarket/changeStatus', 'changeStatus', 'closed'));
//                $actions->append(new DeleteButton($actions->row, 'tradeMarket/deleteRows', 'deleteRows', 'id'));
            });

            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
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
        return Admin::form(TradeMarket::class, function (Form $form) {
            $options = [];
            $coin = java_get('coin', ['pageNo' => 1, 'pageSize' => 100]);
            if (isset($coin['statusCode']) && $coin['statusCode'] == 0) {
                foreach ($coin['content'] as $vo) {
                    $options[$vo['displayName']] = $vo['displayName'];
                }
            }
            $form->select('coinName', '名称')->options($options);
            $form->select('settlementCurrency', '结算货币')->options($options);
            $form->radio('closed','是否开放市场')->options(['SHOW'=>'开放','HIDE'=>'关闭']);
            $form->datetime("dayExchangeBegin", '交易开始时间')->format("HH:mm:ss");
            $form->datetime('dayExchangeEnd', '交易结束时间')->format("HH:mm:ss");
            $form->image('coinUrl', '交易市场图片');
            $form->text('feeCoin', "买入手续费,0.1代表10%");
            $form->text("feeCurrency", "卖出手续费,0.1代表10%");
            $form->number("maxExchangeNum", '单笔下单最大数量');
            $form->number("minExchangeNum", '单笔下单最小数量');
            $form->text("maxIncrease", '最大涨幅');
            $form->text('minDecrease', '最大跌幅');
            $form->text('maxCurrency', '最高挂单总价（0表示不限制）');
            $form->text('minSellPrice', '最低卖单单价（0表示不限制）');
            $form->text('minBuyPrice', '最低买单单价（0表示不限制）');
            $form->radio('preArea','是否属于ETH区')->options(['1'=>'是','0'=>'否']);
            $form->radio('mineArea','是否属于USDT区')->options(['1'=>'是','0'=>'否']);
            $form->radio('mainArea','是否属于待上线区')->options(['1'=>'是','0'=>'否']);
            $form->radio('ukzArea','是否属于DT区')->options(['1'=>'是','0'=>'否']);
            //$form->number("buyRequireLockNum", '在该市场中挂买单必须具有的最小锁仓币个数');
            //$form->radio('rewardPolicy','奖励/释放币策略')->options(['1'=>'大于上次买入价格','0'=>'大于等于上次买入价格', '10'=>'不考虑价格']);
            $form->radio("del", '是否删除')->options(['0' => '不删除', '1' => '删除'])->default(0);
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

    public function deleteRows(Request $request)
    {
        $params = (array)$request->post('id');
        $result = java_delete('market', $params, ['Content-Type:application/json']);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '删除成功'];
        } else {
            $arr = ['status' => false, 'message' => '删除失败'];
        }
        return response()->json($arr);
    }

    public function config()
    {
        $result = java_get('/config/system');
        $config = [];
        if (!isset($result['statusCode']) || $result['statusCode'] != 0) {
            return view('admin.errors.errors');
        } else {
            $config = $result['content'];
        }
        return Admin::content(function (Content $content) use ($config) {
            $content->body(view('admin.system.config', ['config' => $config]));
        });
    }

    public function system_config(Request $request)
    {
        $params = $request->only(['bcnPrice','recUserFeeRate','rec2UserFeeRate', 'referrerReward', 'registerReward', 'tradeReward',
            'firstRewardAmount', 'firstRewardPhone', 'secondRewardAmount', 'secondRewardPhone', 'thirdRewardAmount', 'thirdRewardPhone',
             'qqLink', 'wxImageLink', /*'receivedReleaseRate', 'vipReceivedReleaseRate',*/ 'superUserGetFeeRate', 'coinSavingUserGetFeeRate',
            'sameTradeRewardMaxTimes','otcSellFreeze','otcCancelPriFeeRate']);
        $file = $request->file('wxImageLink');
        if (!isset($params['wxImageLink']) || empty($params['wxImageLink'])) {
            $params['wxImageLink'] = '';
        }
        if ($file) {
            $params['wxImageLink'] = $this->upload($file);
        }
        $result = java_post('config/system', $params, ['Content-Type:application/json']);
        if (!isset($result['statusCode']) || $result['statusCode'] != 0) {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : '操作失败';
            $error = new MessageBag([
                'title' => '操作提示',
                'message' => $message,
            ]);
            session()->flash('error', $error);
            return back()->with(compact('error'));
        }
        $error = new MessageBag([
            'title' => '操作提示',
            'message' => '修改成功',
        ]);
        session()->flash('success', $error);
        return redirect('/admin/config/system');
    }

    public function upload($file)
    {
        $extension = $file->getClientOriginalExtension(); //获取文件名的后缀
        $key = md5(time() . random_int(1, 5)) . '.' . $extension;
        $path = $file->getRealPath();
        $res = OSS::upload($key, $path);
        if ($res) {
            return env('AliossUrl') . $key;
        }
    }

    public function ChangeStatus(Request $request)
    {
        $params['marketId'] = $request->post("id");
        $url = 'market/close_or_open_market';
        $result = java_get($url, $params);
        if ($result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '操作成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : '操作失败';
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }
}
