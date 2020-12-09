<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/19
 * Time: 上午9:35
 */

namespace App\Repository;


class UserApi extends CmdModel
{
    protected $table = '';

    protected $list_url = 'user-api';

    protected $params = ['endTime', 'mobile', 'status', 'startTime','realName','userName'];
}