<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/11/16
 * Time: 下午2:32
 */

namespace App\Repository;


class TradeLog extends CmdModel
{
    protected $table = '';

    protected $list_url = 'trade/trade-reward-ls';

    protected $params = ['buyUserName','coinName','endLastTradeTime','sellUserName','settlementCurrency','startLastTradeTime','type','coin_name'];

    /*
     * 设置搜索需要处理的字段
     */
    public $handle_offerSet = ['coin_name'];
    public $handle_flag = '/';
    public $handle_replace_column = ['coinName', 'settlementCurrency'];

    public $filter_export_column = ['buyUserName', 'coinName', 'endLastTradeTime', 'sellUserName', 'settlementCurrency','startLastTradeTime','type','coin_name'];
    /*
     * 导出表格的链接
     */
    public $export_url = 'trade/export-trade-reward';
}