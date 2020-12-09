<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/10/8
 * Time: 上午11:49
 */

namespace App\Repository;


class LastTrade extends CmdModel
{
    protected $table = '';

    protected  $primaryKey = 'id';

    protected $list_url = 'user-trade-stat/stat-latest-trade';

    protected $params = ['coinName','settlementCurrency','coin_name','userName','orderType'];

    /*
     * 设置搜索需要处理的字段
     */
    protected $handle_offerSet = ['coin_name'];
    protected $handle_flag = '/';
    protected $handle_replace_column = ['coinName', 'settlementCurrency'];
}