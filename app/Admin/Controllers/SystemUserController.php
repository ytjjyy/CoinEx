<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ChangeStatus;
use App\Admin\Extensions\ChangeVip;
use App\Admin\Extensions\CommInput;
use App\Admin\Extensions\DetailButton;
use App\Admin\Extensions\Export\CsvExport;
use App\Admin\Extensions\Reset;
use App\Admin\Extensions\Tools\Filter;
use App\Admin\Extensions\Tools\Modal;
use App\Admin\Extensions\Tools\ReleasePost;
use App\Admin\Extensions\Tools\UserTools;
use App\Repository\User;

use App\Repository\UserCoin;
use App\Repository\UserLevel;
use App\Repository\UserRecord;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;


class SystemUserController extends Controller
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

            $content->header('用户管理');
            $content->description('用户列表');
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
            $content->header('用户管理');
            $content->description('修改用户');

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

            $content->header('header');
            $content->description('description');

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
        return Admin::grid(User::class, function (Grid $grid) {
            $grid->setView('admin.grid.userTable');
            $grid->id('ID');
            $grid->email('用户邮箱');
            $grid->userName('用户名');
            //$grid->areaCode('区号');
            $grid->userType('用户类型')->display(function ($userType) {
                if($userType == 1) return "超级节点";
                return "普通用户";
            });
            $grid->mobile('手机号');
            $grid->inviteCode('分享码');
            $grid->realName('真实姓名');
            $grid->groupName('分组');
            $grid->idCardTime('上传认证时间');
            $grid->referrerMobile('推荐人手机号');
//            $grid->realNameStatus('实名认证状态')->display(function ($status) {
//                switch ($status) {
//                    case 0:
//                        return '未验证';
//                    case 1:
//                        return '等待验证';
//                    case 2:
//                        return '审核通过';
//                    default:
//                        return '未验证';
//                }
//            });
            $grid->idCardStatus('身份认证状态')->display(function ($status) {
                switch ($status) {
                    case 0:
                        return '待上传';
                    case 1:
                        return '审核通过';
                    case 2:
                        return '待验证';
                    case 3:
                        return '审核失败';
                    default:
                        return '未验证';
                }
            });
            $grid->registerTime('注册时间');
            $grid->status('状态')->display(function ($status) {
                return $status ? '禁用' : '正常';
            });
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->append(new DetailButton($actions->row, 'user/record', 'detail', 'id', '明细'));
                $actions->append(new ChangeStatus($actions->row, 'systemUser/ChangeStatus'));
                $actions->append(new Reset($actions->row, '/admin/systemUser/resetPassword', '重置支付密码', 'reset_pay_password', '/user/pay-password'));
                $actions->append(new Reset($actions->row, '/admin/systemUser/resetPassword', '重置登录密码', 'reset_password', '/user/password'));
            });
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                    $batch->add('禁用', new UserTools('user/disable'));
                    $batch->add('启用', new UserTools('user/enable'));
                    $batch->add('实名认证审核通过', new UserTools('user/id-card-pass'));
                    $batch->add('实名认证审核不通过', new UserTools('user/id-card-deny'));
                    $batch->add('分组', new Modal());
                });
                $filter->SetParams(array('lable' => '用户真实姓名', 'name' => 'realName'))->text();
                $filter->SetParams(array('lable' => '身份认证状态', 'options' => ['0' => '待上传', '1' => '审核通过', '2' => '等待身份验证', '3' => '审核失败'], 'name' => 'authStatus'))->select();
                $filter->SetParams(array('lable' => '手机号', 'name' => 'mobile'))->text();
                $filter->SetParams(array('lable'=>'邮箱','name'=>'email'))->text();
                $filter->SetParams(array('lable' => '推荐人手机号', 'name' => 'referrerMobile'))->text();
                $filter->SetParams(array('lable' => '用户状态', 'options' => ['0' => '启用', '1' => '禁用'], 'name' => 'status'))->select();
                $options = java_get('userConfig/getUserGroupConfigList', []);
                $arr = [];
                if (isset($options['statusCode']) && $options['statusCode'] == 0) {
                    foreach ($options['content'] as $vo) {
                        $arr[$vo['groupType']] = $vo['groupName'];
                    }
                }
                $filter->SetParams(array('lable' => '用户分组', 'options' => $arr, 'name' => 'groupType'))->select();
//                $filter->SetParams(array('lable' => '是否上传过实名图片', 'options' => ['true' => '是', 'false' => '否'], 'name' => 'uploadIdCard'))->select();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableCreateButton();
            /*
             * 自定义数据导出
             */
            $grid->exporter((new CsvExport($grid, new User())));
//
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(User::class, function (Form $form) {

            $form->display('userName', '用户名');
            $options = [
                '01' => '+01美国',
                '86' => '+86中国',
                '61' => '+61澳大利亚',
                '33' => '+33法国',
                '62' => '+62印度尼亚',
                '81' => '+81日本',
                '60' => '+60马来西亚',
                '82' => '+82韩国',
                '66' => '+66泰国',
                '44' => '+44英国',
                '84' => '+84越南',
                '219' => '+219香港',
                '220' => '+220新加坡'
            ];
            $form->select('areaCode', '选择地区')->options($options);
            $form->display('mobile', '手机号');
            $form->text('realName', '真实姓名');
            $form->text('idCard', '证件号码');
            $form->image('idCardImg1', '身份证');
            $form->image('idCardImg2', '身份证');
            $form->image('idCardImg3', '身份证');
            $form->radio('idCardStatus', '身份证审核状态')->options(['1' => '审核通过', '3' => '审核失败'])->default('1');
            //$form->radio('userType', '用户类型')->options(['0' => '普通用户', '1' => '超级节点']);
            //$form->radio('isPublishOtc', 'C2C挂单权限')->options(['0' => '关闭', '1' => '开启']);
            //$form->radio('userPrivilege', '特权类型')->options(['0' => '普通用户', '1' => '特权用户']);
            $options = java_get('userConfig/getUserGroupConfigList', []);
            $arr = [];
            if (isset($options['statusCode']) && $options['statusCode'] == 0) {
                foreach ($options['content'] as $vo) {
                    $arr[$vo['groupType']] = $vo['groupName'];
                }
            }
            $form->select('groupType', '分组')->options($arr);
//            $form->saving(function (Form $form) {
//                dd(\Storage::disk('s3')->putFile('',request()->file('idCardImg1'),'public'));
//            });
            $form->saved(function (Form $form) {
                if (!$form->model()->is_save || !$form->model()->is_add) {
                    $error = new MessageBag([
                        'title' => '操作返回信息',
                        'message' => '操作失败',
                    ]);
                    session()->flash('error', $error);
                    return back()->with(compact('error'));
                }
            });
        });
    }

    public function ChangeStatus(Request $request)
    {

        $params = (array)$request->post("id");
        $status = $request->post('status');
        $status ? $url = 'user/enable' : $url = 'user/disable';
        $result = java_post($url, $params, $header = array('Content-Type:application/json'));
        if ($result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $arr = ['status' => false, 'message' => '修改失败'];
        }
        return response()->json($arr);
    }

    /**
     * 批量设置用户分组
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setUserGroup(Request $request)
    {
        $params['userIds']=implode(",",$request->post('id'));
        $params['groupType'] = $request->post('group');
        $result = java_get('user/update-group-type', $params);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $arr = ['status' => false, 'message' => '修改失败'];
        }
        return response()->json($arr);
    }

    /*
     * 批量修改用户的状态
     */
    public function release(Request $request)
    {
        $params = $request->only(['id', 'action']);
        $result = java_post($params['action'], $params['id'], $header = array('Content-Type:application/json'));
        if ($result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $arr = ['status' => false, 'message' => '修改失败'];
//            admin_log($params['action'], $params['id'], $result, 'post');
        }
        return response()->json($arr);
    }

    public function resetPassword(Request $request)
    {
        $params = $request->only(['id', 'action']);
        $java_param['userId'] = $params['id'];
        $result = java_post($params['action'], $java_param);
        if ($result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $arr = ['status' => false, 'message' => '修改失败'];
        }
        return response()->json($arr);
    }

    /*
     * 获取用户的交易明细
     */
    public function user_record(Request $request, $id)
    {
        $request->offsetSet('userId', $id);
        $userInfo = java_get('/user/detail', ['userId' => $id]);
        if (isset($userInfo['statusCode']) && $userInfo['statusCode'] == 0) {
            $request->offsetSet('userName', $userInfo['content']['userName']);
        }
        if (empty($request->get('coinName'))) {
            $coin = java_get('coin', ['pageNo' => 1, 'pageSize' => 100]);
            if (isset($coin['statusCode']) && $coin['statusCode'] == 0) {
                request()->offsetSet('coinName', $coin['content'][0]['displayName']);
            }
        }
        return Admin::content(function (Content $content) {

            $content->header('用户明细');
            $content->description('用户明细');
            $content->body($this->record_grid());
        });
    }

    protected function record_grid()
    {
        return Admin::grid(UserRecord::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->changeAmount('交易数量');
            $grid->coinName('币种名称');
            $grid->subType('账户特性')->display(function ($type) {
                return $type ? '冻结余额' : '可用余额';
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
                        return '交易费用分红返还';
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
                    default : return '';
                }
            });
            $grid->lastTime('交易时间');
            $grid->actions(function ($actions) {
                $actions->disableDelete();
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
                $filter->SetParams(array('lable' => '币种名称', 'options' => $options, 'name' => 'coinName'))->select();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableActions();
            $grid->disableExport();
            $grid->disableCreateButton();
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
        });
    }

    /*
     * 获取用户资产信息
     */
    public function user_coin()
    {
        return Admin::content(function (Content $content) {
            $desc = '';
            if(!empty(request()->get('coinName'))){
                $coinName = request()->get('coinName');
                $groupType = request()->get('groupType');
                $result = java_get('/finance/user-coin-total',['coinName'=>$coinName,'groupType'=>$groupType]);
                if(isset($result['statusCode']) && $result['statusCode'] ==0){
                    $desc = $coinName.'总和为:'.$result['content'];
                }
            }
            $amountDesc='';
            $params = request()->all();
            $perPage = request()->get('per_page', 20);
            $page = request()->get('page', 1);
            $params['pageNo'] = $page;
            $params['pageSize'] = $perPage;
            $data = java_get('finance/user-coin-info',$params);
            $amount = 0;
            if(isset($data['statusCode']) && $data['statusCode'] ==0){
                if(!empty($data['content']) && is_array($data['content'])){
                    foreach ($data['content'] as $vo){
                        $amount += $vo['availableBalance'];
                    }
                    $amountDesc='  当前页面总和:'.$amount;
                }
            }
            $content->header('用户资产信息');
            $content->description('用户资产信息'.$desc.$amountDesc);
            $content->body($this->user_coin_grid());
        });
    }

    protected function user_coin_grid()
    {
        return Admin::grid(UserCoin::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->userName('用户名');
            //$grid->groupName('用户分组');
            /*$grid->vipType('是否VIP')->display(function ($value){
                if($value == 1) return "是";
                return "否";
            });*/
            $grid->realName('真实姓名');
            $grid->coinName('币种名称');
            $grid->bindAddress('钱包地址');
            $grid->availableBalance('可用资产')->sortable()->display(function ($value){
                return sprintf('%.8f', $value);
            });
            $grid->freezeBalance('冻结资产')->sortable()->display(function ($freeze){
                return sprintf('%.8f', $freeze);
            });
            $grid->receiveFreezeBalance('锁定资产')->sortable()->display(function ($receivedFreeze){
                return sprintf('%.8f', $receivedFreeze);
            });
            $grid->disableCreateButton();
            $grid->disableActions();
            /*$grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->append(new ChangeVip($actions->row, '/admin/systemUser/ChangeCoinVip'));
            });*/
            $grid->disableFilter();
            $grid->exporter((new CsvExport($grid, new UserCoin())));
//            $grid->disableExport();
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
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '用户真实姓名', 'name' => 'realName'))->text();
                $filter->SetParams(array('lable' => '币种名称', 'options' => $options, 'name' => 'coinName'))->select();
                $filter->SetParams(array('lable' => '用户分组', 'options' => getGroup(), 'name' => 'groupType'))->select();
                $tools->append($filter->render());
            });
        });
    }


    public function ChangeCoinVip(Request $request)
    {
        $userId = $request->post("userId");
        $coinName = $request->post("coinName");
        $vipType = $request->post('vipType');
        $data = [
            "userId" => $userId,
            "coinName" => $coinName,
            "vipType" => $vipType
        ];
        $result = java_post("finance/set-coin-vip-type", $data);
        if ($result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $arr = ['status' => false, 'message' => '修改失败'];
        }
        return response()->json($arr);
    }

    public function userLevels()
    {
        return Admin::content(function (Content $content) {

            $content->header('用户等级');
            $content->description('用户等级列表');
            $content->body($this->userLevelGrid());
        });
    }

    protected function userLevelGrid()
    {
        return Admin::grid(UserLevel::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->userId('用户ID');
            $grid->userName('用户名');
            $grid->userLevel('用户等级');
            $grid->referrer('推荐用户ID');
            $grid->referrerName('推荐用户名');
            $grid->tradeAmount('用户交易量总和(USDT)');
            $grid->teamAmount('团队交易量总和(USDT)');
            $grid->tradeTodayAmount('今天交易量(USDT)');
            $grid->tradeYesterdayAmount('昨天天交易量(USDT)');
            $grid->teamYesterdayAmount('团队昨天天交易量(USDT)');
            $grid->recCount('有效直推人数');
            $grid->teamCount('有效团队人数');
            //$grid->allRecCount('所有直推人数');
            //$grid->allTeamCount('所有团队人数');
            $grid->yesterdayProfit('昨日收益');
            $grid->todayProfit('今日收益');
            $grid->totalProfit('总收益');
            $grid->teamTotalProfit('团队总收益');

            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->append(new CommInput('/admin/systemUser/set-user-level', 'userId', $actions->row['userId'],
                    'level', '修改等级', '修改用户等级', '新的用户等级'));
            });
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '推荐用户名', 'name' => 'refererName'))->text();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableCreateButton();
        });
    }

    public function updateUserLevel(Request $request)
    {
        $userId = $request->post("userId");
        $level = $request->post("level");
        $data = [
            "userId" => $userId,
            "level" => $level,
        ];
        $result = java_post("user/set-user-level", $data);
        if ($result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $arr = ['status' => false, 'message' => '修改失败'];
        }
        return response()->json($arr);
    }
}
