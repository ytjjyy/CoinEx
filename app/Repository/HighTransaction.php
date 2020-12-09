<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/9/17
 * Time: 上午10:33
 */

namespace App\Repository;


class HighTransaction extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $list_url = 'hfmonitor/get_high_frequency_list';

    protected $params = ['coinName','settlementCurrency','coin_name'];

    /*
     * 设置搜索需要处理的字段
     */
    protected $handle_offerSet = ['coin_name'];
    protected $handle_flag = '/';
    protected $handle_replace_column = ['coinName', 'settlementCurrency'];
}