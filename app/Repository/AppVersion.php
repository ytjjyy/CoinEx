<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/8/26
 * Time: 下午12:31
 */

namespace App\Repository;


class AppVersion extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $list_url = 'config/app-version';

    protected $detail_url = 'config/app-version-detail';

    protected $detail_column = 'id';

    protected $put_url = 'config/app-version';

    protected $save_column = ['force', 'platform', 'url', 'versionName', 'id', 'content','code'];

    public $is_save = true;

//    protected $image_column = ['url'];
}