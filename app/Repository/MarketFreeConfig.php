<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/8/20
 * Time: 上午11:41
 */

namespace App\Repository;


class MarketFreeConfig extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'marketGroupConfigId';

    protected $list_url = 'marketGroupConfig/get-market-group-config-list';

    protected $detail_column = 'marketGroupConfigId';

    protected $detail_url = 'marketGroupConfig/get-market-group-config-byId';

    protected $put_url = 'marketGroupConfig/update-market-group-config';

    protected $save_column = ['marketGroupConfigId','buyConValue', 'groupId', 'id', 'marketId', 'remark', 'sellConValue','coinName','settlementCurrency'];

    protected $add_url = 'marketGroupConfig/add-market-group-config';

    public $is_save = true;

    public $is_add = true;
}