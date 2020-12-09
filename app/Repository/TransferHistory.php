<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/9
 * Time: 上午11:35
 */

namespace App\Repository;


class TransferHistory extends CmdModel
{
    protected $table = '';

    protected $list_url = '/finance/transfer-history';

    protected $params = ['coinName', 'endTime', 'mobile', 'sourceAddress', 'startTime', 'targetAddress', 'type','userName','startTime','endTime','realName'];

    static public $status = [
        'APPLYING' => '正在申请',
        'PASSED' =>'成功',
        'FAILED' =>'失败',
        'CONFIRM' =>'节点确认',
    ];
}