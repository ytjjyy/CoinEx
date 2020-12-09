<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/9
 * Time: 上午10:44
 */

namespace App\Repository;


class TransferSecondList extends CmdModel
{
    //转出审核记录
    protected $table = '';
    protected $list_url = 'finance/transfer-second-list';  //获取转账列表
    protected $params = ['coinName', 'status','userName','groupType','startTime','endTime'];
}