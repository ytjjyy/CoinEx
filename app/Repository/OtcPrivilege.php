<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/9/27
 * Time: 上午10:06
 */

namespace App\Repository;


class OtcPrivilege extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $list_url = 'otc/otc-privileges';

    protected $params=['userName','status','publishEnable'];

}