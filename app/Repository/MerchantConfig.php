<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/8/9
 * Time: 下午2:50
 */

namespace App\Repository;


class MerchantConfig extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $list_url = 'merchant/coins';

    protected $save_column = ['coinName', 'cnyPrice', 'dollarPrice', 'hkdollarPrice', 'orderMinAmount', 'orderMaxAmount', 'id','feeRate'];

    protected $detail_url = 'merchant/get-coin';

    protected $detail_column = 'id';

    protected $put_url = 'merchant/update-coin';

    protected $add_url = 'merchant/add-coin';

    public $is_add = true;

    public $is_save = true;

    /*
     * 隐藏表单字段
     */
    public $hidden_form_column = ['status' => 0];
}