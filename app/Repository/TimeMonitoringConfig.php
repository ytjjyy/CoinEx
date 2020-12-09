<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/9/3
 * Time: 下午7:03
 */

namespace App\Repository;


class TimeMonitoringConfig extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $list_url = 'timeMonitoringConfig/get-timeMonitoringConfig-page-list';

    protected $params = ['TIME_MONITORING_H_F_TRADE', 'monitoringType'];

    protected $detail_url = 'timeMonitoringConfig/get-timeMonitoringConfig-detail-ById';

    protected $detail_column = 'id';

    protected $add_url = 'timeMonitoringConfig/add-timeMonitoring-config';

    protected $save_column = ['id', 'buyNumber', 'coinName', 'monitoringType', 'numMinutes', 'sellNumber', 'settlementCurrency'];

    protected $put_url = 'timeMonitoringConfig/update-timeMonitoringConfig-byId';

    public $is_add = true;

    public $is_save = true;

    public $errorMessage;
}