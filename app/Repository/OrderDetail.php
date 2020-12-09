<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/26
 * Time: 下午3:11
 */

namespace App\Repository;


class OrderDetail extends CmdModel
{
    protected $table = '';

    protected $primaryKey = '';

    protected $method = 'POST';
    protected $list_url = 'otc/order-detail';

    protected $params = ['id'];
}