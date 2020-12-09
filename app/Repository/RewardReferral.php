<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/9
 * Time: 下午12:07
 */

namespace App\Repository;


class RewardReferral extends CmdModel
{
    protected $table = '';
    protected $list_url = 'reward/referral';
    protected $params = ['endTime', 'mobile', 'referrerMobile', 'startTime'];

}