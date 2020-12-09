<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/8/28
 * Time: 下午4:14
 */

namespace App\Admin\Extensions\Tools;


use Encore\Admin\Grid\Tools\BatchAction;

class Modal extends BatchAction
{
    /*
         * @var string
         * @param 用户操作的type  对应数据表的字段
         */
    protected $action_url;

    /*
     * @var int
     * @param 用户操作状态的值
     */

    public function __construct(string $action_url = '')
    {
        $this->action_url = $action_url;
    }

    public function script()
    {
        return <<<EOT
            $('{$this->getElementClass()}').on('click', function() {
                   $("#userGroup").modal('show');
            });
EOT;
    }
}