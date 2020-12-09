<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/9
 * Time: 下午4:39
 */

namespace App\Repository;


class UserRecord extends CmdModel
{
    protected $table = '';
    protected $list_url = 'finance/bill-list';
    protected $params = ['coinName', 'userId','userName'];
}