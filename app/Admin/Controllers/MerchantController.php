<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ChangeStatus;
use App\Admin\Extensions\DetailButton;
use App\Admin\Extensions\Modal\MerchantModal;
use App\Admin\Extensions\Reset;
use App\Admin\Extensions\Tools\Filter;
use App\Repository\Merchant;

use App\Repository\MerchantBalance;
use App\Repository\MerchantBill;
use App\Repository\MerchantHistory;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;


class MerchantController extends Controller
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

            $content->header('兑换商管理');
            $content->description('兑换商列表');

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

            $content->header('兑换商管理');
            $content->description('兑换商修改');

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

            $content->header('兑换商管理');
            $content->description('创建兑换商');

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
        return Admin::grid(Merchant::class, function (Grid $grid) {

            $grid->id('ID');
            $grid->name('兑换商名称');
            $grid->phone('兑换商电话');
            $grid->type('兑换商类型')->display(function ($type) {
                return $type == 1 ? '普通兑换商' : '超级兑换商';
            });
            $grid->bankUser('兑换商银行账户名');
            $grid->bankType('银行卡类型')->display(function ($bankType) {
                switch ($bankType) {
                    default:
                    case 'BANK':
                        return '银行卡';
                    case 'ALIPAY':
                        return '支付宝';
                    case 'WEIXIN':
                        return '微信';
                }
            });
            $grid->email('兑换商邮箱地址');
            $grid->workingStatus('兑换商工作状态')->display(function ($workingStatus) {
                return $workingStatus == 1 ? '正常工作' : '不工作不接单';
            });
            $grid->status('状态')->display(function ($status) {
                switch ($status) {
                    default:
                    case 0:
                        return '正常';
                    case 1:
                        return '已禁用';
                    case 2:
                        return '申请中';
                    case 3:
                        return '审核失败';
                }
            });
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->append(new ChangeStatus($actions->row, 'merchant/ChangeStatus?_pjax=%23pjax-container'));
                $actions->append(new DetailButton($actions->row, 'merchant/balance', 'balance-detail', 'id', '资产'));
                $actions->append(new DetailButton($actions->row, 'merchant/history', 'detail', 'id', '明细'));
                if ($actions->row->type == 2) {
                    $actions->append(new MerchantModal($actions->row, 'dispatch/balance'));
                }
            });
            $grid->disableFilter();
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $filter->SetParams(array('lable' => '推荐人手机号', 'name' => 'merchantName'))->text();
                $filter->SetParams(array('lable' => '状态', 'options' => ['0' => '正常', '1' => '禁用', '2' => '申请中'], 'name' => 'status'))->select();
                $filter->SetParams(array('lable' => '兑换商级别', 'options' => ['1' => '普通兑换商', '2' => '超级兑换商'], 'name' => 'type'))->select();
                $tools->append($filter->render());
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
        return Admin::form(Merchant::class, function (Form $form) {

            $form->text('name', '兑换商名称');
            $form->mobile('phone', '联系电话');
            $form->email('email', '联系邮箱');
            $form->radio('bankType', '银行卡类型')->options(['BANK' => '银行卡', 'ALIPAY' => '支付宝', 'WEIXIN' => '微信']);
            $form->text('bankUser', '银行卡开户人');
            $form->text('bankName', '开户行');
            $form->text('bankNo', '银行卡号');
            $form->radio('status', '审核状态')->options(['0' => '审核成功', '1' => '禁用', '2' => '审核中', '3' => '审核失败']);
            $form->textarea('reason','失败原因');
        });
    }

    public function ChangeStatus(Request $request)
    {
        $params['id'] = $request->post("id");
        $status = $request->post('status');
        $params['status'] = $status == 1 ? 0 : 1;
        $result = java_get('merchant/change-status', $params);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $arr = ['status' => false, 'message' => '修改失败'];
        }
        return response()->json($arr);
    }

    public function transfer_application(Request $request)
    {
        return Admin::content(function (Content $content) {
            $content->header('B2C交易订单');
            $content->description('B2C交易订单');

            $content->body($this->application_grid());
        });
    }

    public function release(Request $request)
    {
        $params['orderId'] = $request->post('id');
        $action_url = $request->post('action');
        $result = java_get($action_url, $params);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : '修改失败';
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }

    /**
     * 超级兑换商拨币
     * @param coin_name  string 币种名称
     * @param available string 信用金
     * @param freeze string 冻结金
     * @param merchant_id int 兑换商id
     * @return json
     */
    public function dispatch_balance(Request $request)
    {
        $params['merchant_id'] = $request->post('id');
        $params['coinName'] = $request->post('coinName');
        $params['available'] = $request->post('available');
        $params['freeze'] = $request->post('freeze');
        $params['reason'] = '管理后台拨币';
        $result = java_post('merchant/dispatch-balance', $params);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : '拨币失败';
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }

    /**
     * 查看兑换商用户明细
     * @param Request $request
     * @param $id 兑换商id
     */
    public function merchant_balance(Request $request, $id)
    {

        return Admin::content(function (Content $content) use ($id) {
            $content->header('兑换商资产明细');
            $content->description('兑换商资产明细');

            $content->body($this->merchant_balance_grid($id));
        });
    }

    protected function merchant_balance_grid(int $id): string
    {
        $params['merchantId'] = $id;
        $result = java_get('merchant/merchant-balance', $params);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $header = ['ID', '币种名称', '可用资产', '冻结资产'];
            $content = [];
            if ($result['content']) {
                foreach ($result['content'] as $key => $item) {
                    $content[$key] = [$key + 1, $item['coinName'], $item['availableBalance'], $item['freezeBalance']];
                }
            }
        }
        $table = new Table($header, $content);
        return $table->render();
    }

    /**
     * @param Request $request
     * @param $id 兑换商id
     */
    public function transfer_history(Request $request, $id)
    {
        $request->offsetSet('merchantId', $id);
        return Admin::content(function (Content $content) {
            $content->header('兑换商明细');
            $content->description('兑换商明细');
            $content->body($this->transfer_history_grid());
        });
    }

    /**
     * 获取申诉中订单详情
     * @param Request $request
     * @param $id  订单id
     */
    public function complaint(Request $request, $id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('申诉详情');
            $content->description('申诉详情');

            $content->body($this->complaint_form()->edit($id));
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function complaint_form()
    {
        return Admin::form(MerchantHistory::class, function (Form $form) {
            $form->display('id', '申诉id');
            $form->select('type', '申诉类型')->options(['1' => '用户申诉', '2' => '兑换商申诉'])->readOnly();
            $form->textarea('credentialComment', '凭证说明')->readOnly();
            $form->display('credentialUrls', '凭证图片')->with(function ($value) {
                $str = '';
                if($value){
                    if (strpos($value, ',')) {
                        $image = explode(',', $value);
                        foreach ($image as $vo) {
                            $str .= "<a href='{$vo}' target='_blank'><img src='{$vo}' style='width: 160px;height: 160px;'/></a>&nbsp;&nbsp;&nbsp;&nbsp;";
                        }
                    } else {
                        $str = "<a href='{$value}' target='_blank'><img src='{$value}' style='width: 160px;height: 160px;'/></a>";
                    }
                    return $str;
                }
                return '暂无图片';
            });
//            $form->image('credentialUrls', '凭证图片')->readOnly();
            $form->disableSubmit();
            $form->disableReset();
            $form->tools(function (Form\Tools $tools) {

                // 去掉返回按钮
//                $tools->disableBackButton();

                // 去掉跳转列表按钮
                $tools->disableListButton();

                // 添加一个按钮, 参数可以是字符串, 或者实现了Renderable或Htmlable接口的对象实例
                $tools->add('<div class="btn-group pull-right" style="margin-right: 10px">
    <a href="/admin/merchant/application" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;列表</a>
</div>');
            });
        });
    }

    /**
     * @return Grid
     */
    protected function transfer_history_grid()
    {
        return Admin::grid(MerchantBill::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->coinName('币种名称');
            $grid->changeAmount('变化金额');
            $grid->subType('变化类型')->display(function ($type) {
                return $type ? '冻结金变化' : '可用基金变化';
            });
            $grid->reason('原因')->display(function ($reason) {
                switch ($reason) {
                    case 'USellC':
                        return '普通用户卖数字货币给兑换商，兑换商买入普通用户的数字货币';
                    case 'UBuyC':
                        return '普通用户买兑换商的数字货币，兑换商卖出数字货币';
                    case 'MSellC':
                        return '普通兑换商卖数字货币给超级兑换商，超级兑换商买入普通兑换商的数字货币';
                    case 'MBuyC':
                        return '普通兑换商买超级兑换商的数字货币，超级兑换商卖出数字货币';
                    case  'Cancel':
                        return '兑换商取消订单';
                    case 'MgrCc':
                        return '管理员取消订单';
                    default:
                        return $reason;
                }
            });
            $grid->lastTime('变化时间');
            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
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
                $filter->SetParams(array('lable' => '币种名称', 'options' => $options, 'name' => 'coinName'))->select();
                $tools->append($filter->render());
            });
        });
    }

    protected function application_grid()
    {
        return Admin::grid(MerchantHistory::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->userName('用户名');
            $grid->merchantName('兑换商名称');
            $grid->orderNo('订单号');
            $grid->settlementCurrency('结算币种');
            $grid->coinName('币种名称');
            $grid->amount('总量');
            $grid->fee('手续费');
            $grid->status('订单状态')->display(function ($status) {
                switch ($status) {
                    default:
                    case 'COMPLETE':
                        return '<span style="color: #00a65a">已完成</span>';
                    case 'WAIT_ACCEPT':
                        return '<span style="color: #2ea8e5">待接单</span>';
                    case 'WAIT_PAY':
                        return '<span style="color: sandybrown">待付款</span>';
                    case 'PAYED':
                        return '<span style="color: #8a6d3b">待收款</span>';
                    case 'COMPLAIN':
                        return '<span style="color: #6f42c1">申诉中</span>';
                    case 'CANCEL':
                        return '<span style="color: red">已取消</span>';
                }
            });
            $grid->type('类型')->display(function ($type) {
                return $type == 'BUY' ? '<span style="color: red">充值</span>' : '<span style="color: sandybrown">提现</span>';
            });
            $grid->addTime('订单创建时间')->display(function ($time) {
                return date('Y-m-d H:i:s', $time);
            });
            $grid->proofTime('上传凭证时间')->display(function ($time) {
                if (is_null($time)) {
                    return '';
                }
                return date('Y-m-d H:i:s', $time);
            });
            $grid->finishTime('结束时间')->display(function ($time) {
                if ($time) {
                    return date('Y-m-d H:i:s', $time);
                }
                return '';
            });
            $grid->disableFilter();
            $grid->disableCreateButton();
            $grid->disableExport();
            $filter = new Filter();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                if ($actions->row->status == 'COMPLAIN') {
                    $actions->append(new DetailButton($actions->row, '/admin/merchant/complaint', 'merchant-detail', 'id', '申诉详情'));
                }
                if ($actions->row->status == 'PAYED' || $actions->row->status == 'COMPLAIN') {
                    $actions->append(new Reset($actions->row, '/admin/merchant/release', '确认完成', 'finish', 'merchant/complete-order'));
                    $actions->append(new Reset($actions->row, '/admin/merchant/release', '取消订单', 'cancel', 'merchant/cancel-order'));
                }
            });
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
//                $filter->SetParams(array('lable' => '推荐人手机号', 'name' => 'merchantName'))->text();
                $filter->SetParams(array('lable' => '状态', 'options' => ['COMPLETE' => '已完成', 'WAIT_ACCEPT' => '待接单', 'WAIT_PAY' => '待付款', 'PAYED' => '待收款', 'COMPLAIN' => '申诉中', 'CANCEL' => '已取消'], 'name' => 'status'))->select();
                $filter->SetParams(array('lable' => '类型', 'options' => ['BUY' => '充值', 'SELL' => '提现'], 'name' => 'type'))->select();
                $tools->append($filter->render());
            });
        });
    }
}
