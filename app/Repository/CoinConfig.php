<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/6/29
 * Time: 下午4:46
 */

namespace App\Repository;


class CoinConfig extends CmdModel
{
    protected $table = '';
    protected $primaryKey = 'id';
    /*
     * 定义获取币种列表
     */
    protected $list_url = '/coin';
    /*
     * 定义添加数据的链接
     */
    protected $add_url = 'coin';
    /*
     * 表单提交 判断是否操作成功
     */
    public $is_add = true;
    public $is_save = true;
    /*
     * 隐藏表单字段
     */
    public $hidden_form_column = ['sort' => 0, 'status' => 0];
    /*
     * 设置获取详情url
     */
    protected $detail_url = 'coin/coin-info';
    /*
     * 设置获取详情传的参数
     */
    protected $detail_column = 'name';
    /*
     * 设置上传图片到cos字段
     */
    protected $image_column = ['image', 'icon'];

    /*
     * 设置修改时提交的字段
     */
    protected $save_column = ['name', 'symbol', 'category', 'displayName', 'displayNameAll', 'image', 'icon', 'sort', 'status',
        'serverAddress', 'serverPort', 'serverUser', 'serverPassword', 'contractAddress', 'coinSelfParameter', 'transferMaxAmount',
        'transferMinAmount', 'transferFeeRate','transferFeeSelect','transferFeeStatic','maximumAmountDay','maximumNumberDay',
        'coinBase', 'receivedFreeze', 'revFreezeRate', 'releaseConf', 'vipReleaseConf', 'maxDayRelNum', 'maxMonthRel', 'maxMonthRelNum', 'maxMonthRelRec',
        'maxDayRel', 'releasePolicy', 'sellReleaseConf', 'sellVipReleaseConf'];

    protected $put_url = '/coin';
    /*
     * 传过来的值转化为大小写
     */
    protected $upper_column = ['name'];

}