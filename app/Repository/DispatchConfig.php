<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/24
 * Time: 下午2:52
 */

namespace App\Repository;


class DispatchConfig extends CmdModel
{
    // 锁仓配置信息

    protected $table = '';

    protected $list_url = 'dispatch/get-dispatch-config';


}