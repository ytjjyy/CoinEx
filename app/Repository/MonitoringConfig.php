<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/8/29
 * Time: 下午6:30
 */

namespace App\Repository;


class MonitoringConfig extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $list_url = 'timeMonitoring/get-timeMonitoring-page-list';

    protected $detail_url = 'timeMonitoring/get-timeMonitoring-detail-ById';

    protected $detail_column = 'id';

    protected $add_url = 'timeMonitoring/add-timeMonitoring';

    public $is_save = true;

    public $is_add = true;

    protected $put_url ='timeMonitoring/update-timeMonitoring';

    protected $save_column = ['monitoringName','id','monitoringType','numMinutes'];
}