<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/8/3
 * Time: 下午3:30
 */

namespace App\Repository;


class C2CConfig extends CmdModel
{
    protected $table = '';
    protected $primaryKey = 'id';
    protected $list_url = 'otc/config-list';

    protected $params = [];

    protected $detail_url = 'otc/config-detail';

    protected $save_column = ['coinName', 'legalName', 'expiredTimeFreeze', 'expiredTimeCancel', 'feeRate','maxApplBuyCount','maxApplSellCount'];

    protected $put_url = '/otc/config';

    protected $detail_column = 'coinName';
}