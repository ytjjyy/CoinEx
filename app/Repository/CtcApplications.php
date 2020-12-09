<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/25
 * Time: 下午3:26
 */

namespace App\Repository;


class CtcApplications extends CmdModel
{
    protected $list_url = 'otc/applications';

    protected $params = ['coinName','orderNo','status','type','user','startTime','endTime'];
}