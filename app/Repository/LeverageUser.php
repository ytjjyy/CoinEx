<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/9/27
 * Time: 上午10:06
 */

namespace App\Repository;


class LeverageUser extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $list_url = 'leverage/user-leverages';

    protected $params=['coinName','coin_name','loanCoinName','settlementCurrency','status','userName'];

    /*
     * 设置搜索需要处理的字段
     */
    protected $handle_offerSet = ['coin_name'];
    protected $handle_flag = '/';
    protected $handle_replace_column = ['coinName', 'settlementCurrency'];

}