<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ChangeStatus;
use App\Admin\Extensions\CoinConfigEdit;
use App\Admin\Extensions\Tools\Filter;
use App\Repository\CoinConfig;


use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Qcloud\Cos\Client;

class CoinController extends Controller
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

            $content->header('币种配置');
            $content->description('币种列表');

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

            $content->header('币种配置');
            $content->description('修改币种');

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

            $content->header('币种设置');
            $content->description('添加币种');

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
        return Admin::grid(CoinConfig::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->name('币种名称');
            $grid->category('币种种类')->display(function (string $category) {
                switch ($category) {
                    case 'real' :
                        return '真实货币';
                    case 'btc' :
                        return '比特币';
                    case 'usdt':
                        return 'usdt数字货币';
                    case 'eth' :
                        return '以太币';
                    case 'token':
                        return '以太坊代币';
                    case 'eos':
                        return 'EOS';
                    default:
                        return 'etc';
                }
            });
            $grid->serverAddress('钱包服务器');
            $grid->serverPort('钱包服务器端口');
            $grid->status('状态')->display(function (int $status) {
                return $status ? '禁用' : '启用';
            });
            $grid->actions(function ($actions) {
                $actions->setKey($actions->row->name);  //设置编辑的参数
                $actions->disableDelete();
                $actions->append(new CoinConfigEdit($actions->row, 'coinConfig/ChangeStatus'));
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });


            $grid->disableExport(); //去掉导出按钮
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
        return Admin::form(CoinConfig::class, function (Form $form) {

            $form->text('name', '币种名称')->rules('required|max:6', ['required' => '币种名称不能为空', 'max' => '长度不能操作6位']);
            $form->text('symbol', '币种符号')->rules('required', ['required' => '币种符号不能为空']);
            $form->select('category', '币种类型')->options(['real' => '真实货币', 'btc' => '比特币', 'usdt' => 'usdt数字货币', 'eth' => '以太币', 'token' => '以太坊代币', 'etc' => '以太坊经典', 'eos' => 'EOS'])->rules('required', ['required' => '请选择币种类型']);
            $form->text('displayName', '默认显示币种名称')->rules('required', ['required' => '默认显示币种名称不能为空']);
            $form->text('displayNameAll', '英文币种名称')->rules('required', ['required' => '英文币种名称不能为空']);
            $form->image('image', '图标')->uniqueName()->rules('required', ['required' => '图标不能为空']);
            $form->image('icon', '币种小图标')->uniqueName()->rules('required', ['required' => '币种小图标不能为空']);
            $form->text('serverAddress', '钱包服务器地址')->rules('required', ['required' => '钱包服务器地址不能为空']);
            $form->text('serverPort', '币种服务器端口')->rules('required', ['required' => '币种服务器端口不能为空']);
            $form->text('serverUser', '连接服务器用户名');
            $form->text('serverPassword', '连接服务器名密码');
            $form->text('contractAddress', '以太坊合约地址');
            $form->text('coinSelfParameter', '币种本身参数');
            $form->text("transferMaxAmount", '提现最大金额')->rules('required', ['required' => '提现最大金额不能为空']);
            $form->text("transferMinAmount", '提现最小金额')->rules('required', ['required' => '提现最小金额不能为空']);
            $form->radio('transferFeeSelect', '手续费方式')->options(['0' => '百分比', '1' => '固定值']);
            $form->text('transferFeeRate', '转出手续费，0.1代表10%');
            $form->text('transferFeeStatic', '固定手续费数量(个)');
//            $form->number('nodeConfirmCount', '节点确认次数');
//            $form->text('maximumAmountDay', '当天最大提币数量');
//            $form->text('maximumNumberDay', '当前最大提币次数');
            $form->text('coinBase', '主账户地址设置(区块链地址/本平台地址)');
            //$form->radio('receivedFreeze', '转入币种时冻结')->options(['0' => '否', '1' => '是']);
            //$form->text('revFreezeRate', '转入锁仓比例，0.1代表10%');
            //$form->text("maxDayRelNum", '每天释放的最多次数')->rules('required', ['required' => '每天释放的最多次数']);
            //$form->text("maxMonthRel", '每月最大释放的量')->rules('required', ['required' => '每月最大释放的量']);
            //$form->text("maxMonthRelNum", '每月最大释放的量推荐人数配置')->rules('required', ['required' => '每月最大释放的量推荐人数配置']);
            //$form->text("maxMonthRelRec", '每月最大释放的量(推荐用户达到配置数量)')->rules('required', ['required' => '每月最大释放的量(推荐用户达到配置数量)']);
            //$form->text("releaseRate", '转入锁仓币种买入时释放比例(0.1代表10%)')->rules('required', ['required' => '转入锁仓币种买入时释放比例']);
            //$form->text("releaseRateVip", 'VIP转入锁仓币种买入时释放比例(0.1代表10%)')->rules('required', ['required' => 'VIP转入锁仓币种买入时释放比例']);
            //$form->text("maxDayRel", '每日最大释放的量')->rules('required', ['required' => '每日最大释放的量']);
            //$form->radio('releasePolicy', '交易时释放策略')->options(['0' => '不释放', '1' => '买入释放', '2' => '卖出释放', '3' => '买入卖出都释放']);
            //$form->textarea("releaseConf", '转入锁仓币种买入时根据价格释放比例(0.1代表10%)，')->rules('required', ['required' => '转入锁仓币种买入时释放比例']);
            //$form->textarea("vipReleaseConf", 'VIP转入锁仓币种买入时根据价格释放比例(0.1代表10%)')->rules('required', ['required' => 'VIP转入锁仓币种买入时释放比例']);
            //$form->textarea("sellReleaseConf", '转入锁仓币种卖出时根据价格释放比例(0.1代表10%)，')->rules('required', ['required' => '转入锁仓币种卖出时释放比例']);
            //$form->textarea("sellVipReleaseConf", 'VIP转入锁仓币种卖出时根据价格释放比例(0.1代表10%)')->rules('required', ['required' => 'VIP转入锁仓币种卖出时释放比例']);
            //$form->html("根据价格释放比例格式如下：<br>0:0.1<br>1.5:0.2<br>10:0.3<br>表示价格在0-1.5（&gt;=0,&lt;1.5）之间释放比例是10%,1.5-10(&gt;=1.5,&lt;10)之间比例是20%，大于等于10比例是30%，支持1到多行");
            $form->saving(function (Form $form) {
                if ($form->transferFeeSelect == '0') {
                    $form->transferFeeStatic = $form->model()->transferFeeStatic;
                }
                if ($form->transferFeeSelect == '1') {
                    $form->transferFeeRate = $form->model()->transferFeeRate;
                }

//                if(request()->file('image')){
//                if ($form->image) {
//                    $imageName = \Storage::disk('s3')->putFile($form->image('image'), request()->file('image'), 'public');
//                }
//                }
//                dd(request()->all());
            });
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

    public function ChangeStatus(Request $request)
    {
        $params['name'] = $request->post("name");
        $status = $request->post('status');
        $params['status'] = $status == 0 ? 1 : 0;
        $url = 'coin/enableOrDisable';
        $result = java_get($url, $params, $header = array());
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $arr = ['status' => false, 'message' => '修改失败'];
        }
        return response()->json($arr);
    }



    public function usdtBalance()
    {
        $result = java_get('/wallet/btc-balance-of-usdt');
        $balance = 0;
        if (!isset($result['statusCode']) || $result['statusCode'] != 0) {
            return view('admin.errors.errors');
        } else {
            $balance = $result['content'];
        }
        return Admin::content(function (Content $content) use ($balance) {
            $content->body(view('admin.system.usdt_balance', ['balance' => $balance]));
        });
    }

    public function collectUsdtBalance(Request $request)
    {
        //$params = $request->only(['amount']);
        $params['amount'] = $request->input()['amount'];
        $result = java_post('wallet/collect-btc-of-usdt', $params);
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
        return redirect('/admin/coin/usdtBalance');
    }
}
