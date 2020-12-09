<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/30
 * Time: 下午6:22
 */

namespace App\Repository;


class FinanceBill extends CmdModel
{
    protected $table = '';

    protected $list_url = 'finance/bill-list';

    protected $params = ['coinName', 'userName', 'groupType', 'startTime', 'endTime','subType','reason','realName'];
}