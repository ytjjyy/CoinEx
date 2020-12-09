<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/6
 * Time: 下午6:28
 */

namespace App\Repository;


class TradeDetail extends CmdModel
{
    protected $table='';
    protected $list_url = 'trade/deal';
    protected $params = ['tradeId'];

}