<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/26
 * Time: 下午12:13
 */

namespace App\Repository;


class C2COrder extends CmdModel
{
    protected $list_url = 'otc/orders';

    protected $params = ['buyName','sellName','status','coinName','startTime','endTime'];
}