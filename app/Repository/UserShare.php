<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/10
 * Time: 下午3:07
 */

namespace App\Repository;


class UserShare extends CmdModel
{
    protected $table = '';
    protected $list_url = 'reward/share-out-bonus';
    protected $params = ['endTime', 'mobile', 'realName', 'startTime'];
}