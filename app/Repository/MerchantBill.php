<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/8/8
 * Time: 下午3:26
 */

namespace App\Repository;


class MerchantBill extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $list_url = 'merchant/bill';


    protected $params = ['coinName','merchantId'];
}