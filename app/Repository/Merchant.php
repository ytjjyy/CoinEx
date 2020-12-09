<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/8/6
 * Time: 上午11:13
 */

namespace App\Repository;


class Merchant extends CmdModel
{
    protected $table = '';

    protected $primaryKey='id';

    protected $list_url = 'merchant/list';

    protected $params = ['merchantName', 'status','type'];

    protected $detail_url = 'merchant/merchant-detail';

    protected $detail_column = 'id';

    protected $save_column = ['name', 'phone', 'email', 'bankType', 'bankUser', 'bankName', 'bankNo', 'status', 'id','reason'];

    protected $put_url = 'merchant';
}