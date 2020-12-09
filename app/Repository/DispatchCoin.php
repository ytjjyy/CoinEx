<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/24
 * Time: 下午5:43
 */

namespace App\Repository;


class DispatchCoin extends CmdModel
{
    protected $table='';

    protected $primaryKey='id';

    protected $list_url='dispatch/get-dispatch';

    protected $params = ['userName','realName','coinName'];
}