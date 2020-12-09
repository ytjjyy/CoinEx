<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/10
 * Time: 下午2:59
 */

namespace App\Repository;


class Mining extends CmdModel
{
    protected $table = '';
    protected $list_url = 'reward/mining-bonus';

    protected $params = ['endTime', 'mobile', 'referrerId', 'referrerMobile', 'startTime'];
}