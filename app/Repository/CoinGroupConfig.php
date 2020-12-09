<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/8/18
 * Time: 下午2:10
 */

namespace App\Repository;


class CoinGroupConfig extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $list_url = 'coinGroup/coin-group-config-list';

    protected $detail_url = 'coinGroup/get-coin-group-config-detail';

    protected $detail_column = 'id';

    protected $put_url = 'coinGroup/update-coin-group-configs';

    protected $save_column = ['conValue', 'id', 'remark'];


}