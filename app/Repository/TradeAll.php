<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/9/3
 * Time: 下午5:51
 */

namespace App\Repository;


class TradeAll extends CmdModel
{
    protected $table = '';
    protected $list_url = '/trade/getTradeListAll';

    protected $params = ['coinName', 'settlementCurrency', 'mobile', 'realName', 'type', 'coin_name','userName','amount','addEndTime','addStartTime','endLastTradeTime','price','startLastTradeTime','status'];

    /*
     * 设置搜索需要处理的字段
     */
    protected $handle_offerSet = ['coin_name'];
    protected $handle_flag = '/';
    protected $handle_replace_column = ['coinName', 'settlementCurrency'];

    static public $tradeStatus=[
        'OPEN' => '未完全成交',
        'DEAL' => '完全成交',
        'CANCELED' => '已经撤单',
        'ALL' => '所有',
        'EXCEPTION' => '订单异常,无法完成交易',
    ];
}