<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/8/6
 * Time: 下午4:50
 */

namespace App\Repository;


class MerchantHistory extends CmdModel
{
    protected $table='';
    protected $primaryKey='id';

    protected $list_url = 'merchant/user-merchant-order-list';

    protected $params = ['merchantId','status','type'];

    protected $detail_url = 'merchant/complaint';

    protected $detail_column = 'orderId';
}