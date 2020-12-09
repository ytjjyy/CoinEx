<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/3
 * Time: 下午2:26
 */

namespace App\Repository;


class TradeMarket extends CmdModel
{
    /*
     * 定义列表链接
     */
    protected $list_url = 'market';
    /*
    * 定义获取详情的url
    */
    protected $detail_url = 'market/detail';
    /*
     * 定义获取详情字段
     */
    protected $detail_column = 'marketId';

    /*
     * 定义修改链接
     */
    protected $put_url = "market";
    /*
     * 定义上传图片字段
     */
    protected $image_column = ['coinUrl'];
    /*
     * 定义修改的字段
     */
    protected $save_column = ['id', 'closed', 'coinName', 'dayExchangeBegin', 'dayExchangeEnd', 'feeCoin', 'feeCurrency', 'maxExchangeNum',
        'maxIncrease', 'minDecrease', 'minExchangeNum', 'settlementCurrency', 'coinUrl','del', 'maxCurrency', 'minSellPrice', 'minBuyPrice',
        'preArea', 'mineArea', 'mainArea', 'ukzArea', 'buyRequireLockNum', 'rewardPolicy'];

    protected $add_url = "market";
    protected $primaryKey = 'id';
    public $is_save = true;
    public $is_add = true;

    /*
    * 传过来的值转化为大小写
    */
    protected $upper_column = ['coinName', 'settlementCurrency'];
}