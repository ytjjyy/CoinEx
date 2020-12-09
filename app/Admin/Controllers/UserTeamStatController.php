<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/19
 * Time: 15:52
 */

namespace App\Admin\Controllers;


use App\Admin\Extensions\Tools\Filter;
use App\Http\Controllers\Controller;
use App\Repository\UserTeamBalanceStat;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Grid;

class UserTeamStatController extends Controller
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

            $content->header('团队资产统计');
            $content->description('团队资产统计');
            $content->body($this->grid());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(UserTeamBalanceStat::class, function (Grid $grid) {
            //$grid->id('ID');
            $grid->statTime('统计时间');
            $grid->userId('用户ID');
            $grid->userName('用户名');
            $grid->coinName('币种名称');
            $grid->transferIn('外部总转入');
            $grid->transferOut('外部总转出');
            $grid->availableBalance('可用余额总额');
            $grid->freezeBalance('冻结金总额');
            $grid->investBalance('托管总额');
            $grid->otcBuy('OTC总买入');
            $grid->otcSell('OTC总卖出');
            $grid->disableActions();
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                //$filter->SetParams(array('lable' => '币种名称', 'name' => 'coinName'))->text();
                $tools->append($filter->render());
            });
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
            });
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
        });
    }
}