<?php

namespace App\Repository;

use Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class User extends CmdModel
{
    protected $table = 't_user';
    protected $primaryKey = 'id';
    public $timestamps = false;
    /*
     * @var list_url 获取用户列表的URl
     */
    protected $list_url = '/user';
    /*
     * @var params 列表查询用到的相关字段
     */
    protected $params = ['mobile', 'referrerMobile', 'status', 'uploadIdCard', 'authStatus','groupType','email','realName'];
    /*
     * var detail_url 获取用户详情接口
     */
    protected $detail_url = '/user/detail';
    protected $detail_column = 'userId';
    protected $put_url = '/user';
    /*
     * var save_clounm 添加需要修改的字段
     */
    protected $save_column = ['areaCode', 'mobile', 'status', 'userId', 'idCardStatus', 'groupType', 'userType', 'isPublishOtc', 'userPrivilege', 'idCard', 'realName'];
    /*
     * 设置修改提交主键id
     */
    protected $save_primaryKey = 'userId';
    /*
     * form 表单提交是要替换的字段
     */
    public $form_replace_column = ['idCardStatus' => 'realNameStatus'];
    public $is_save = true;
    public $is_add = true;
    public $filter_export_column = ['authStatus', 'mobile', 'referrerMobile', 'status', 'uploadIdCard'];
//    public $idCardStatus = [
//        '0' => '未上传',
//        '1' => '审核通过',
//        '2' => '等待验证',
//        '3' => '验证失败'
//    ];
    /*
     * 导出表格的链接
     */
    public $export_url = 'user/export';

}
