<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/5
 * Time: 上午10:15
 */

namespace App\Repository;


class Advertise extends CmdModel
{
    protected $table = 't_advertise';
    /*
     * 定义获取广告列表的
     */
    protected $list_url = "advertise";
    protected $primaryKey = 'id';
    /*
     * 设置获取详情的接口
     */
    protected $detail_url = "advertise/detail";
    protected $detail_column ='adId';
    /*
     * 设置修改链接
     */
    protected $put_url = 'advertise';
    /*
     * 设置修改表单的字段
     */
    protected $save_column = ['content', 'id', 'createTime', 'lastTime', 'link', 'name', 'status', 'type', 'startTime', 'endTime','locale','url','clientType'];
    /*
     * 设置添加URl
     */
    protected $add_url = 'advertise';
    public $is_save = true;
    public $is_add = true;
    /*
     * 设置上传图片字段
     */
    protected $image_column = ['url'];
}