<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/6/23
 * Time: 上午10:36
 */

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class ChangeStatus
{
    protected $articleDetail;

    public function __construct($articleDetail, string $url)
    {
        $this->articleDetail = $articleDetail;
        $this->url = $url;
    }

    protected function script()
    {
        return <<<SCRIPT
        $('.change-status').unbind('click').click(function () {
            let id=$(this).data('id');
            let status=$(this).data('status');
            swal({
              title: "你确定要修改状态吗?",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "确认",
              closeOnConfirm: false,
              cancelButtonText: "取消"
            },function(){
                   $.ajax({
                        url:'{$this->url}',
                        method:'post',
                        type:'json',
                        data:{id:id,status:status},
                        headers: {
                            'X-CSRF-TOKEN': LA.token, 
                        },
                        success:function(res){
                         $.pjax.reload('#pjax-container');
                         if(res.status==true){
                             swal('修改成功', '', 'success');
                         }else{
                              swal('修改失败', '', 'error');
                         }
                        }
                   });
                
            })
        });
SCRIPT;

    }

    protected function render()
    {
        Admin::script($this->script());
        $desc = $this->articleDetail->status == 1 ? '启用' : '禁用';
        $style = $this->articleDetail->status === 1 ? 'btn-adn' : 'btn-bitbucket';
        return "<span><a class='btn btn-xs {$style} change-status' data-id='{$this->articleDetail->id}' data-status='{$this->articleDetail->status}'>
                " . $desc . "
              </a>&nbsp;&nbsp;</span>";
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->render();
    }
}