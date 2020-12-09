<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/11/7
 * Time: 上午10:38
 */

namespace App\Repository;


class LockUserCoin extends CmdModel
{
    protected $list_url = 'lock-coin/lock-history';
    protected $params = ['coinName', 'endTime', 'startTime', 'userName'];
}