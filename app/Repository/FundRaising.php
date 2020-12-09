<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/9/27
 * Time: 上午10:06
 */

namespace App\Repository;


class FundRaising extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';
    protected $detail_column = 'id';

    protected $list_url = 'fundraising/fundraising-list';
    protected $add_url = 'fundraising/create-fundraising';
    protected $put_url = 'fundraising/update-fundraising';
    protected $detail_url = 'fundraising/fundraising-info';

    protected $params=['coinName','status','beginCreateTime', 'endCreateTime'];
    protected $save_column = ['id','name','coinName','raisingBalance','serviceFeeRate','minInvestBalance','maxInvestBalance','platProfitRate','description'];

    public $is_save = true;

    public $is_add = true;
}