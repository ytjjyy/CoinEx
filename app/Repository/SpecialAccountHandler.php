<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/8/21
 * Time: 上午10:01
 */

namespace App\Repository;


class SpecialAccountHandler extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $params = ['userName','noWarningType'];

    protected $list_url = 'tradeNowarningUser/get-no-warning-user-list';

    protected $add_url = 'tradeNowarningUser/add-no-warning-user';

    public $is_add = true;

    public $errorMessage;


}