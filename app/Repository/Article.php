<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/6/22
 * Time: 下午5:09
 */

namespace App\Repository;

use Request;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class Article extends CmdModel
{
    protected $table = 't_article';
    /*
     * 获取列表的链接
     */
    protected $list_url = 'article';
    /*
     *  列表查询用到的相关字段
     */
    protected $params = ['locale', 'type'];


    /*
     * 定义获取详情的url
     */
    protected $detail_url = 'article/detail';
    /*
     * 定义获取详情的参数
     */
    protected $detail_column = 'articleId';
    /*
     * 定义保存的字段
     */
    protected $save_column = ['id', 'title', 'titleEn', 'type', 'locale', 'contentEn', 'content', 'status', 'sort','displayTime'];
    /*
     * 定义修改的链接
     */
    protected $put_url = 'article';
    /*
     * 定义添加文章的url
     */
    protected $add_url = 'article';
    public $is_save = true;
    public $is_add = true;

}