<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/11/8
 * Time: 上午10:07
 */

namespace App\Repository;


class LockDetail extends CmdModel
{
    protected $table = '';

    protected $list_url ='lock-coin/lock-return-list';

    protected $params = ['coinName','endTime','startTime','userName'];
}