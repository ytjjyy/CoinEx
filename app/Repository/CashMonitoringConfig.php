<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/9/21
 * Time: 下午4:32
 */

namespace App\Repository;


class CashMonitoringConfig extends CmdModel
{
    public $table = '';
    public $primaryKey = 'id';

    public $list_url = 'cash_monitoring_config/get_cash_monitoring_config_list';

    protected $detail_url = '/cash_monitoring_config/get_cash_monitoring_config_byId';
    protected $detail_column = 'id';
    protected $put_url = '/cash_monitoring_config/update_cash_monitoring_config';
    /*
     * var save_clounm 添加需要修改的字段
     */
    protected $save_column = ['coinName', 'id', 'lastRefreshTime', 'rollInNumber', 'rollOutNumber'];
    /*
     * 设置修改提交主键id
     */
    protected $save_primaryKey = 'id';

    protected $add_url = 'cash_monitoring_config/add_cash_monitoring_config';

    public $is_add = true;

    public $is_save = true;
}