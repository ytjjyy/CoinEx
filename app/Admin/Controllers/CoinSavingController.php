<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/6
 * Time: 17:01
 */

namespace App\Admin\Controllers;

use App\Admin\Extensions\ChangeStatus;
use App\Admin\Extensions\DetailButton;
use App\Admin\Extensions\Export\CsvExport;
use App\Admin\Extensions\Reset;
use App\Admin\Extensions\Tools\Filter;
use App\Admin\Extensions\Tools\Modal;
use App\Admin\Extensions\Tools\ReleasePost;
use App\Admin\Extensions\Tools\UserTools;
use App\Repository\User;

use App\Repository\CoinSaving;
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


class CoinSavingController extends Controller
{
    use ModelForm;


    /*
     * 获取用户锁仓信息/分润宝信息
     */
    public function user_saving_coin()
    {
        return Admin::content(function (Content $content) {
            $content->header('用户分润宝信息');
            $content->description('用户分润宝信息');
            $content->body($this->saving_coin_grid());
        });
    }

    protected function saving_coin_grid()
    {
        return Admin::grid(CoinSaving::class, function (Grid $grid) {
            $grid->id('ID');
            $grid->userId('用户ID');
            $grid->userName('用户名');
            $grid->balance('存储资产')->display(function ($value){
                return sprintf('%.8f', $value);
            });
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableFilter();
            $grid->disableExport();
            //$grid->exporter((new CsvExport($grid, new UserCoin())));
//            $grid->disableExport();
            $filter = new Filter();
            $grid->tools(function ($tools) use ($filter) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
                $filter->SetParams(array('lable' => '用户名', 'name' => 'userName'))->text();
                $tools->append($filter->render());
            });
        });
    }
}