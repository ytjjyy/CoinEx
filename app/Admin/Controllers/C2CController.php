<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Button;
use App\Admin\Extensions\C2CButton;
use App\Admin\Extensions\DetailButton;
use App\Admin\Extensions\Reset;
use App\Admin\Extensions\Tools\Filter;
use App\Http\Controllers\Controller;
use App\Repository\C2COrder;
use App\Repository\CtcApplications;
use App\Repository\OrderDetail;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use App\Repository\OtcPrivilege;

class C2CController extends Controller
{
    use ModelForm;

    /*
     * 获取c2c挂单列表
     */
    public function otc_applications()
    {
        if (empty(request()->get('type'))) {
            request()->offsetSet('type', '100');
        }
        if (empty(request()->get('status'))) {
            request()->offsetSet('status', '10');
        }
        return Admin::content(function (Content $content) {
            $content->header('C2C挂单列表');
            $content->description('C2C挂单列表');

            $content->body($this->otc_applications_grid());
        });
    }

    protected function otc_applications_grid()
    {
        return Admin::grid(CtcApplications::class, function (Grid $grid) {
            $grid->id('订单号');
            $grid->orderNo('订单号');
            $grid->coinName('币种名称');
            $grid->createUser('用户');
            $grid->amount('总数量');
            $grid->ramount('剩余数量');
            $grid->mamount('匹配数量');
            $grid->samount('交易成功数量');
            $grid->price('价格');
            $grid->type('挂单类型')->display(function ($type) {
                return $type == 1 ? '买' : '卖';
            });
            $grid->status('状态')->display(function ($status) {
                switch ($status) {
                    default:
                    case 1:
                        return '匹配完成';
                    case 2:
                        return '匹配中';
                    case 100:
                        return '已取消';
                }
            });
            $grid->createTime('挂单时间');
            $grid->disableCreateButton();
            $grid->disableExport();
//            $grid->disableActions();
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
                $filter->SetParams(array('lable' => '订单号', 'name' => 'orderNo'))->text();
                $filter->SetParams(array('lable' => '用户', 'name' => 'user'))->text();
                $filter->SetParams(array('lable' => '状态', 'options' => ['10' => '所有', '2' => '匹配中', '1' => '已完成', '100' => '取消'], 'name' => 'status'))->select();
                $filter->SetParams(array('lable' => '挂单类型', 'options' => ['100' => '所有', '1' => '买', '2' => '卖'], 'name' => 'type'))->select();
                $filter->SetParams(array('lable' => '开始时间', 'name' => 'startTime'))->datetime();
                $filter->SetParams(array('lable' => '结束时间', 'name' => 'endTime'))->datetime();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });

            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->append(new C2CButton($actions->row, '/admin/cancel/application'));
//                $actions->append(new Status($actions->row, 'article/ChangeStatus', 'changeStatus'));
            });
        });
    }

    /*
     * 取消挂单
     */
    public function cancel_application(Request $request)
    {
        $params['id'] = $request->post('id');
        $result = java_put('otc/application-cancel', $params);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : '操作失败';
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }

    /*
     * c2c订单列表
     */
    public function c2c_order(Request $request)
    {
        if (empty($request->get('status'))) {
            $request->offsetSet('status', '10');
        }
        return Admin::content(function (Content $content) {
            $content->header('C2C订单列表');
            $content->description('C2C订单列表');
            $content->body($this->c2c_order_grid());
        });
    }

    /*
     * 获取c2c订单详情
     */
    public function order_detail(Request $request, $id)
    {
        $request->offsetSet('id', $id);
        return Admin::content(function (Content $content) {
            $content->header('C2C订单详情');
            $content->description('C2C订单详情');
            $content->body($this->order_detail_grid());
        });
    }

    /*
     * 确认完成c2c订单
     */
    public function finish_order(Request $request)
    {
        $params['id'] = $request->post('id');
        $result = java_post('otc/order-deal', $params);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '修改成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : "确认失败";
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }

    public function cancel_order(Request $request)
    {
        $params['id'] = $request->post('id');
        $result = java_put('otc/order-cancel', $params);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '取消成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : "取消失败";
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }

    protected function c2c_order_grid()
    {
        return Admin::grid(C2COrder::class, function (Grid $grid) {
            $grid->id('id');
            $grid->orderNo('订单号');
            $grid->buyUser('买家账号');
            $grid->sellUser('卖家账号');
            $grid->amount('总额')->display(function ($amount) {
                return round($amount, 2);
            });
            $grid->number('数量');
            $grid->price('单价');
            $grid->fee('手续费')->display(function ($fee) {
                return round($fee, 2);
            });
            $grid->createTime('撮合时间');
            $grid->coinName('币种名称');
            $grid->legalName('交易货币');
            $grid->finishTime('成交时间');
            $grid->uploadCredentialTime('上传凭证时间');
            $grid->status('状态')->display(function ($status) {
                switch ($status) {
                    default:
                    case 1:
                        return '交易成功';
                    case 2:
                        return '匹配中';
                    case 3:
                        return '匹配完成';
                    case 4:
                        return '已接单';
                    case 5:
                        return '已经付款';
                    case 6:
                        return '申诉中';
                    case 7:
                        return '冻结，后台管理员可取消';
                    case 100:
                        return '已经取消';
                }
            });
            $grid->tag('订单完成标识')->display(function ($tag) {
                switch ($tag) {
                    default:
                    case 0:
                        return '未标识';
                    case 1:
                        return '用户确认';
                    case 2:
                        return '后台确认';
                }
            });
            $grid->disableCreateButton();
            $grid->disableExport();
//            $grid->disableActions();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->append(new DetailButton($actions->row, '/admin/order/detail', 'order_detail', 'id', '详情'));
                if ($actions->row->status == 6 || $actions->row->status == 7) {
                    $actions->append(new Reset($actions->row, '/admin/finish/order', '确认完成', 'finish', ''));
                    $actions->append(new Reset($actions->row, '/admin/cancel/order', '取消订单', 'cancel', ''));
                }
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
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
//                    $batch->disableExporter();
                });
                $filter->SetParams(array('lable' => '买家姓名', 'name' => 'buyName'))->text();
                $filter->SetParams(array('lable' => '卖家姓名', 'name' => 'sellName'))->text();
                $filter->SetParams(array('lable' => '状态', 'options' => ['10' => '所有', '1' => '交易成功', '2' => '匹配中', '3' => '匹配成功', '4' => '已接单', '5' => '已付款', '6' => '申诉中', '7' => '冻结', '100' => '已取消'], 'name' => 'status'))->select();
                $filter->SetParams(array('lable' => '开始时间', 'name' => 'startTime'))->datetime();
                $filter->SetParams(array('lable' => '结束时间', 'name' => 'endTime'))->datetime();
                $tools->append($filter->render());
            });
        });
    }

    protected function order_detail_grid()
    {
        return Admin::grid(OrderDetail::class, function (Grid $grid) {
            $grid->id('id');
            $grid->orderNo('订单号');
            $grid->appeal('申诉人角色')->display(function ($appeal) {
                return $appeal == 1 ? '卖家' : '卖家';
            });
            $grid->appealDesc('申诉说明');
            $grid->buyEmail('买家邮箱');
            $grid->buyIdCard('买家身份证');
            $grid->buyMobile('买家手机号');
            $grid->buyName('买家名字');
            $grid->buyRealName('买家真实姓名');
            $grid->sellIdCard('卖家身份证');
            $grid->sellMobile('卖家手机号');
            $grid->sellName('卖家用户名');
            $grid->sellRealName('卖家真实姓名');
            $grid->status('状态')->display(function ($status) {
                switch ($status) {
                    default:
                    case 1:
                        return '交易成功';
                    case 2:
                        return '匹配中';
                    case 3:
                        return '匹配成功';
                    case 4:
                        return '已经接单';
                    case 5:
                        return '已经付款';
                    case 6:
                        return '申诉中';
                    case 7:
                        return '冻结，后台管理员可取消';
                    case 100:
                        return '已取消';
                }
            });
            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }



    /*
     * 获取c2c权限列表
     */
    public function otc_privileges()
    {
        return Admin::content(function (Content $content) {
            $content->header('C2C权限列表');
            $content->description('C2C权限列表');

            $content->body($this->otc_privileges_grid());
        });
    }

    protected function otc_privileges_grid()
    {
        return Admin::grid(OtcPrivilege::class, function (Grid $grid) {
            $grid->id('订单号');
            $grid->userId('用户ID');
            $grid->userName('用户名');
            $grid->publishEnable('是否允许挂卖单')->display(function ($publishEnable) {
                return $publishEnable == 1 ? '允许' : '不允许';
            });;
            $grid->status('申请状态')->display(function ($status) {
                switch ($status) {
                    case 0:
                        return '无申请';
                    case 1:
                        return '申请挂单';
                    case 2:
                        return '申请退款';
                }
                return '';
            });;
            $grid->applyTime('申请时间');
            $grid->updateTime('修改时间');
            $grid->createTime('挂单时间');
            $grid->disableCreateButton();
            $grid->disableExport();
//            $grid->disableActions();
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $filter->SetParams(array('lable' => '申请状态', 'options' => ['0' => '无申请', '1' => '申请挂单', '2' => '申请退款'], 'name' => 'status'))->select();
                $filter->SetParams(array('lable' => '当前是否允许挂卖单', 'options' => ['0' => '不允许', '1' => '允许'], 'name' => 'publishEnable'))->select();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });

            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                if($actions->row['status'] != 0) {
                    $actions->append(new Reset($actions->row, '/admin/c2c/approve-privilege', '审批通过', 'approve', "1"));
                    $actions->append(new Reset($actions->row, '/admin/c2c/approve-privilege', '拒绝', 'reject', "0"));
                } else if($actions->row['publishEnable'] == 1) {
                    $actions->append(new Reset($actions->row, '/admin/c2c/approve-privilege', '撤销权限', 'cancel', "2"));
                }
            });
        });
    }

    public function approve_privilege(Request $request) {
        $params['id'] = $request->post('id');
        $params['type'] = $request->post('action');
        $result = java_post('otc/approve-privilege', $params);
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $arr = ['status' => true, 'message' => '操作成功'];
        } else {
            $message = isset($result['errorMessage']) ? $result['errorMessage'] : "操作失败";
            $arr = ['status' => false, 'message' => $message];
        }
        return response()->json($arr);
    }
}
