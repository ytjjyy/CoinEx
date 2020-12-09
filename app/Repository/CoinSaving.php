<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/30
 * Time: 下午5:47
 */

namespace App\Repository;


class CoinSaving extends CmdModel
{
    protected $table='';

    protected $list_url = 'finance/user-coin-saving';

    protected $params = ['userName'];

    //public $filter_export_column = ['coinName', 'userName', 'groupType', 'realName'];
    //public $export_url = 'finance/user-coin-info-export';
}