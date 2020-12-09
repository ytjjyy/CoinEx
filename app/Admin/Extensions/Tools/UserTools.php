<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/6/23
 * Time: 下午4:06
 */

namespace App\Admin\Extensions\Tools;


use Encore\Admin\Grid\Tools\BatchAction;

class UserTools extends BatchAction
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

    public function __construct(string $action_url)
    {
        $this->action_url = $action_url;
    }

    public function script()
    {
        return <<<EOT
            $('{$this->getElementClass()}').on('click', function() {
                swal({
                  title: "你确定要执行该操作吗?",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "确认",
                  closeOnConfirm: false,
                  cancelButtonText: "取消"
                },function(){
                  $.ajax({
                        method: 'post',
                        url: '{$this->resource}/release',
                        data: {
                            _token:LA.token,
                            id: selectedRows(),
                            action:'{$this->action_url}',
                        },
                        success: function (res) {
                            $.pjax.reload('#pjax-container');
                            if(res.status==true){
                                swal('操作成功', '', 'success');
                             }else{
                                  swal('操作失败', '', 'error');
                             }
                        }
                  });
                })
                
            });
EOT;
    }
}