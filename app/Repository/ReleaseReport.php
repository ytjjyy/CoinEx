<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/24
 * Time: 下午2:19
 */

namespace App\Repository;


class ReleaseReport extends CmdModel
{
    protected $table='';
    protected $primaryKey = 'id';
    /*
     * 定义获取列表的链接
     */
    protected $list_url='dispatch/dispatch-log';

    protected $params = ['userId'];

    protected $detail_url = 'dispatch/get-dispatch-info-by-id';

    protected $detail_column = 'id';

    public $is_save = true;

    public $is_add = true;

    protected $put_url='dispatch//update-dispatch-config';

    protected $save_column = ['id','lockName','lockRate','freeRate','status','cronName'];

    protected $add_url = 'dispatch/admin-add-dispatch-config';
}