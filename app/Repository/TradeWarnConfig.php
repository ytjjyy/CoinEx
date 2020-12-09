<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/8/20
 * Time: 上午9:50
 */

namespace App\Repository;


class TradeWarnConfig extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $list_url = 'trade-warning/get-trade-warning-list';

    protected $detail_url = 'trade-warning/get-trade-warning-byId';

    protected $detail_column = 'id';

    protected $put_url = 'trade-warning/update-trade-warning';

    protected $save_column = ['amount', 'id', 'marketId', 'remark','type'];

    public $is_save = true;

    public $is_add = true;

    protected $add_url ='trade-warning/add-trade-warning';

    static public $type = [
        '1' =>'买入',
        '2' => '卖出'
    ];
}