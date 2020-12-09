<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/9/8
 * Time: 下午12:11
 */

namespace App\Repository;


class Balance extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $list_url = 'balance-stat/list';

    protected $params = ['name','timeBegin','timeEnd'];

    protected $strtotime= ['timeBegin','timeEnd'];
}