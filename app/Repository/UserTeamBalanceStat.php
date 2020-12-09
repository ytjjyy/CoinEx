<?php

namespace App\Repository;

use Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class UserTeamBalanceStat extends CmdModel
{
    /*
     * @var list_url 获取用户列表的URl
     */
    protected $list_url = '/team-stat';
    /*
     * @var params 列表查询用到的相关字段
     */
    protected $params = ['userName', 'coinName'];

}
