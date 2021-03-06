<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/9/27
 * Time: 上午10:06
 */

namespace App\Repository;


class FundTradeLog extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';
    protected $detail_column = 'id';

    protected $list_url = 'fundraising/trade-log-list';

    protected $params=['coinName','settlementCurrency','fundRaisingId','type','beginCreateTime', 'endCreateTime'];
}