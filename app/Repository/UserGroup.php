<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/8/17
 * Time: 上午10:54
 */

namespace App\Repository;


class UserGroup extends CmdModel
{
    protected $table = '';

    protected $primaryKey = 'id';

    protected $list_url = 'userConfig/getUserGroupConfigList';

    protected $detail_url = 'userConfig/getUserGroupConfigById';

    protected $detail_column = 'id';

    protected $put_url = 'userConfig/updateUserGroupConfigNameById';

    protected $save_column = ['groupName', 'groupType', 'id', 'status'];

    protected $add_url = 'userConfig/addUserGroupConfig';

    public $is_add = true;

    public $is_save = true;
}