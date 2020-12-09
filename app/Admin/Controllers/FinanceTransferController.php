<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Button;
use App\Admin\Extensions\CheckButton;
use App\Admin\Extensions\Tools\Filter;
use App\Http\Controllers\Controller;
use App\Repository\FinanceBill;
use App\Repository\TransferHistory;
use App\Repository\TransferList;
use App\Repository\TransferSecondList;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

class FinanceTransferController extends Controller
{
    use ModelForm;

    /*
     * 转出审核列表
     */
    public function index()
    {
//        if (empty(request()->get('coinName'))) {
//            $coin = java_get('coin', ['pageNo' => 1, 'pageSize' => 100]);
//            if (isset($coin['statusCode']) && $coin['statusCode'] == 0) {
//                request()->offsetSet('coinName', $coin['content'][0]['displayName']);
//            }
//        }
        if (empty(request()->get('status'))) {
            request()->offsetSet('status', 'APPLYING');
        }
        return Admin::content(function (Content $content) {
            $content->header('转出审核');
            $content->description('转出审核');
            $content->body($this->grid());
        });
    }

    public function second_index()
    {
        if (empty(request()->get('status'))) {
            request()->offsetSet('status', 'APPLYING');
        }
        return Admin::content(function (Content $content) {
            $content->header('二次转出审核');
            $content->description('二次转出审核');
            $content->body($this->second_grid());
        });
    }

    /*
     * 获取转入转出历史记录
     */
    public function transfer_history()
    {
        if (empty(request()->get('coinName'))) {   //设置比传参数
            $coin = java_get('coin', ['pageNo' => 1, 'pageSize' => 100]);
            if (isset($coin['statusCode']) && $coin['statusCode'] == 0) {
                request()->offsetSet('coinName', $coin['content'][0]['displayName']);
            }
        }
        if (empty(request()->get('type'))) {  //设置比传参数
            request()->offsetSet('type', 'RECEIVED');
        }
        return Admin::content(function (Content $content) {
            $content->header('转入转出记录');
            $content->description('转入转出记录');
            $content->body($this->history_grid());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(TransferList::class, function (Grid $grid) {
            $grid->id("ID");
            $grid->amount('数量');
            $grid->userName('用户名');
            $grid->realName('用户真实姓名');
            $grid->groupName('用户分组名');
            $grid->coinName('币种名称');
            $grid->address('目标地址');
            $grid->sendTime('转账时间');
            $grid->lastTime('操作时间');
            $grid->status('交易状态')->display(function ($status) {
                switch ($status) {
                    case "APPLYING":
                        return "待审核";
                    case "PASSED":
                        return "审核通过";
                    case "FAILED":
                        return "审核失败";
                    case 'CONFIRM':
                        return '节点确认';
                    default :
                        return '待审核';
                }
            });

            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $options = [];
                $coin = java_get('coin', ['pageNo' => 1, 'pageSize' => 100]);
                if (isset($coin['statusCode']) && $coin['statusCode'] == 0) {
                    foreach ($coin['content'] as $vo) {
                        $options[$vo['displayName']] = $vo['displayName'];
                    }
                }
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '币种名称', 'options' => $options, 'name' => 'coinName'))->select();
                $filter->SetParams(array('lable' => '审核状态', 'options' => ['APPLYING' => '待审核', 'PASSED' => '审核通过', 'CONFIRM' => '节点确认', 'FAILED' => '审核失败'], 'name' => 'status'))->select();
                $filter->SetParams(array('lable' => '用户分组', 'options' => getGroup(), 'name' => 'groupType'))->select();
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
                if ($actions->row->status == 'APPLYING') {
                    $actions->append(new CheckButton($actions->row, '/admin/finance/changeStatus'));
                }
            });
            $grid->disableExport();
        });
    }

    protected function second_grid()
    {
        return Admin::grid(TransferSecondList::class, function (Grid $grid) {
            $grid->id("ID");
            $grid->amount('数量');
            $grid->userName('用户名');
            $grid->realName('用户真实姓名');
            $grid->coinName('币种名称');
            $grid->address('目标地址');
            $grid->sendTime('转账时间');
            $grid->lastTime('操作时间');
            $grid->status('交易状态')->display(function ($status) {
                switch ($status) {
                    case "0":
                        return "待审核";
                    case "1":
                        return "审核通过";
                    case "2":
                        return "审核失败";
                    case '3':
                        return '节点确认';
                    default :
                        return '待审核';
                }
            });

            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $options = [];
                $coin = java_get('coin', ['pageNo' => 1, 'pageSize' => 100]);
                if (isset($coin['statusCode']) && $coin['statusCode'] == 0) {
                    foreach ($coin['content'] as $vo) {
                        $options[$vo['displayName']] = $vo['displayName'];
                    }
                }
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '币种名称', 'options' => $options, 'name' => 'coinName'))->select();
                $filter->SetParams(array('lable' => '审核状态', 'options' => ['APPLYING' => '待审核', 'PASSED' => '审核通过', 'CONFIRM' => '节点确认', 'FAILED' => '审核失败'], 'name' => 'status'))->select();
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
                if ($actions->row->status == 'APPLYING') {
                    $actions->append(new CheckButton($actions->row, '/admin/finance/changeStatus', 'finance/transfer-sencond-check-pass', 'finance/transfer-sencond-check-fail'));
                }
            });
            $grid->disableExport();
        });
    }

    protected function history_grid()
    {
        return Admin::grid(TransferHistory::class, function (Grid $grid) {
            $grid->id("ID");
            $grid->mobile('手机号');
            $grid->userName('用户名');
            $grid->realName('真实姓名');
            $grid->groupName('用户分组名');
            $grid->type('类型')->display(function ($type) {
                switch ($type) {
                    case 'SEND':
                        return '转出';
                    case 'RECEIVED' :
                        return '转入';
                    default:
                        return '转出';
                }
            });
            $grid->amount('数量');
            $grid->fee('手续费');
            $grid->innerTransfer('转账类型')->display(function ($innerTransfer) {
                return $innerTransfer ? '内部转账' : '外部转账';
            });
            $grid->status('转入/转出状态')->display(function ($status) {
                if (isset(TransferHistory::$status[$status])) {
                    return TransferHistory::$status[$status];
                } else {
                    return '未知状态';
                }
            });
            $grid->coinName('币种名称');
            $grid->txid('交易hash');
//            $grid->sourceAddress('来源地址');
            $grid->targetAddress('目标地址');
            $grid->transferTime('转账时间');
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $options = [];
                $coin = java_get('coin', ['pageNo' => 1, 'pageSize' => 100]);
                if (isset($coin['statusCode']) && $coin['statusCode'] == 0) {
                    foreach ($coin['content'] as $vo) {
                        $options[$vo['displayName']] = $vo['displayName'];
                    }
                }
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '用户真实姓名', 'name' => 'realName'))->text();
                $filter->SetParams(array('lable'=>'目标地址','name'=>'targetAddress'))->text();
                $filter->SetParams(array('lable' => '币种名称', 'options' => $options, 'name' => 'coinName'))->select();
                $filter->SetParams(array('lable' => '类型', 'options' => ['SEND' => '转出', 'RECEIVED' => '转入'], 'name' => 'type'))->select();
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
            $grid->disableExport();

        });
    }

    public function changeStatus(Request $request)
    {
        $params['id'] = $request->post('id');
        $action = $request->post('action');
        $result = java_post($action, (array)$params);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $arr = ['status' => false, 'message' => isset($result['errorMessage']) ? $result['errorMessage'] : '修改失败'];
        }
        return response()->json($arr);
    }

    /*
     * 获取全栈流水信息
     */
    public function finance_bill()
    {
        if (empty(request()->get('startTime'))) {
            request()->offsetSet('startTime', date('Y-m-d') . " 00:00:00");
        }
        if (empty(request()->get('endTime'))) {
            request()->offsetSet('endTime', date('Y-m-d') . " 23:59:59");
        }
        return Admin::content(function (Content $content) {

            $content->header('用户的资产流水列表');
            $content->description('用户的资产流水列表');
            $content->body($this->finance_bill_grid());
        });
    }

    protected function finance_bill_grid()
    {
        return Admin::grid(FinanceBill::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->userName('用户名');
            $grid->realName('用户真实姓名');
            $grid->groupName('分租名称');
            $grid->coinName('币种名称');
            $grid->subType('账户特性')->display(function ($type) {
                return $type == 0 ? '可用余额' : ($type == 1 ? '冻结资产' : '锁定资产');
            });
            $grid->reason('变动类型')->display(function ($reason) {
                switch ($reason) {
                    case 'TxMatch':
                        return '交易撮合';
                    case 'TxTrade':
                        return '交易市场下单';
                    case 'TxMatFee':
                        return '交易撮合费用';
                    case 'TxMatRet':
                        return '交易撮合完成后返回的冻结金';
                    case 'TxBgCanc':
                        return '后台取消交易，因为交易超时';
                    case 'TxUsrCc':
                        return '用户取消交易';
                    case 'TxMgrCc':
                        return '管理员取消交易';
                    case 'TxBonus':
                        return '交易费用奖励（挖矿）';
                    case 'TxRecBo':
                        return '交易费用推荐奖励（挖矿）（返回给推荐人的奖励）';
                    case 'ShareOut':
                        return '超级节点分红';
                    case 'BcRev':
                        return '从区块链中收到转账（外部进入）';
                    case 'Register':
                        return '注册赠送';
                    case 'Referrer':
                        return '推荐奖励';
                    case 'Freeze':
                        return '冻结';
                    case 'UnFreeze':
                        return '解冻';
                    case 'Transfer':
                        return '内部转账';
                    case 'Withdraw':
                        return '外部提现';
                    case 'C2Freeze':
                        return 'c2c冻结';
                    case 'C2Free':
                        return 'c2c手续费';
                    case 'C2Succes':
                        return 'c2c交易成功';
                    case 'C2UnFree':
                        return 'c2c解冻';
                    case 'ConToPl':
                        return '将数字货币转换成平台币（针对私募）';
                    case 'ToPlRel':
                        return '释放数字货币转换成平台币的冻结金（针对私募）';
                    case 'disRel':
                        return '拨币释放';
                    case 'SellCoin':
                        return '普通用户卖出数字货币冻结';
                    case 'CcSell':
                        return '普通用户取消卖出数字货币';
                    case 'BuyCoin':
                        return '普通用户买入数字货币';
                    case 'SavBonus':
                        return '分润宝分润';
                    case 'TxRel':
                        return '交易释放';
                    case 'AddSav':
                        return '转入分润宝';
                    case 'SubSav':
                        return '转从出分润宝';
                    case 'ToRecFee':
                        return '用户锁仓';
                    case 'LevAdd':
                        return '杠杆账号充值';
                    case 'LevSub':
                        return '从杠杆账号提币';
                    case 'LevLoan':
                        return '杠杆借款';
                    case 'LevRepay':
                        return '杠杆还款';
                    case 'LevInter':
                        return '杠杆还利息';
                    case 'TxLevCc':
                        return '杠杆爆仓取消交易';
                    case 'LevLend':
                        return '杠杆放贷';
                    case 'LevCcLe':
                        return '杠杆取消放贷';
                    case 'FundCc':
                        return '取消资金托管';
                    case 'FundProf':
                        return '托管结束返还收益和本金';
                    case 'FundRet':
                        return '返还收益和资金';
                    case 'FundInv':
                        return '用户托管资金';
                    case 'FundFee':
                        return '托管资金手续费';
                    case 'FeeLev':
                        return '等级分红';
                    case 'FeeLev7':
                        return '股东分红';
                    default :
                        return '';
                }
            });
//            $grid->comment('交易详情');
            $grid->changeAmount('变化数量');
            $grid->lastTime('交易时间');
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableFilter();
            $grid->disableExport();
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $coin = java_get('coin', ['pageNo' => 1, 'pageSize' => 100]);
                if (isset($coin['statusCode']) && $coin['statusCode'] == 0) {
                    foreach ($coin['content'] as $vo) {
                        $options[$vo['displayName']] = $vo['displayName'];
                    }
                }
                $arr = ['TxMatch' => '交易撮合', 'TxTrade' => '交易市场下单', 'TxMatFee' => '交易撮合费用', 'TxMatRet' => '交易撮合完成后返回的冻结金', 'TxBgCanc' => '后台取消交易，因为交易超时',
                    'TxUsrCc' => '用户取消交易', 'TxMgrCc' => '管理员取消交易', 'ShareOut' => '超级节点分红', 'BcRev' => '从区块链中收到转账（外部进入）', 'Register' => '注册赠送', 'Referrer' => '推荐奖励', 'Freeze' => '冻结',
                    'UnFreeze' => '解冻', 'Transfer' => '内部转账', 'Withdraw' => '外部提现', 'SellCoin' => '普通用户卖出数字货币冻结', 'CcSell' => '普通用户取消卖出数字货币', 'BuyCoin' => '普通用户买入数字货币',
                    'SavBonus' => '分润宝分润', 'TxRel' => '交易释放', 'AddSav' => '转入分润宝', 'SubSav' => '转从出分润宝', 'ToRecFee' => '用户锁仓'];
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '用户真实姓名', 'name' => 'realName'))->text();
                $filter->SetParams(array('lable' => '账户特性', 'name' => 'subType', 'options' => ['0' => '可用余额', '1' => '冻结资产', '2' => '锁定资产']))->select();
                $filter->SetParams(array('lable' => '变动类型', 'name' => 'reason', 'options' => $arr))->select();
                $filter->SetParams(array('lable' => '币种名称', 'options' => $options, 'name' => 'coinName'))->select();
                $filter->SetParams(array('lable' => '用户分组', 'options' => getGroup(), 'name' => 'groupType'))->select();
                $filter->SetParams(array('lable' => '开始时间', 'name' => 'startTime'))->datetime();
                $filter->SetParams(array('lable' => '结束时间', 'name' => 'endTime'))->datetime();
                $tools->append($filter->render());
            });
        });
    }
}
