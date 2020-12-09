<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/8/31
 * Time: 下午12:24
 */

namespace App\Repository;


class UserLog extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $list_url= 'userLog';

    protected $params = ['logType', 'userName'];
}