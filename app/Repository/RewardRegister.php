<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/9
 * Time: 下午12:06
 */

namespace App\Repository;


class RewardRegister extends CmdModel
{
    protected $table = '';
    protected $list_url = 'reward/register';
    protected $params = ['endTime', 'mobile', 'startTime'];
}