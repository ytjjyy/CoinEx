<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/6
 * Time: 下午4:43
 */

namespace App\Repository;


class TradeManager extends CmdModel
{
    protected $table = '';
    protected $list_url = '/trade';

    protected $params = ['coinName', 'settlementCurrency', 'mobile', 'realName', 'type', 'coin_name','userName'];

    /*
     * 设置搜索需要处理的字段
     */
    protected $handle_offerSet = ['coin_name'];
    protected $handle_flag = '/';
    protected $handle_replace_column = ['coinName', 'settlementCurrency'];
}