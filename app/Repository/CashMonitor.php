<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/9/21
 * Time: 下午4:54
 */

namespace App\Repository;


class CashMonitor extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $list_url = '/cash_monitoring/get_cash_monitoring_list';
}