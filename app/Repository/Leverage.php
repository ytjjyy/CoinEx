<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/9/26
 * Time: 下午4:59
 */

namespace App\Repository;


class Leverage extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $list_url = 'leverage/config-ls';

    protected $detail_column = 'id';

    protected $detail_url ='leverage/get-config';

    protected $put_url ='leverage/update-config';

    protected $save_column = ['bondholderUserId','coinDayRate','coinMinLoan','coinName','explosionRiskRate','id','leverageMultiple','settlementCurrency','settlementDayRate','settlementMinLoan','warnRiskRate'];

    public $is_save = true;

    public $is_add = true;

    protected $add_url ='leverage/add-config';

}