<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/25
 * Time: 下午5:36
 */

namespace App\Admin\Extensions;

use Encore\Admin\Admin;


class C2CButton
{
    protected $articleDetail;
    protected $className;

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
              title: "你确定要撤销吗?",
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
                              swal(res.message, '', 'error');
                         }
                        }
                   });
                
            })
        });
SCRIPT;

    }

    protected function render()
    {
        if($this->articleDetail->status != 2) {
            return "";
        }
        $desc = $this->articleDetail->status != 2 ? '无法撤销' : '撤销';
        $style = $this->articleDetail->status != 2 ? 'btn-bitbucket' : 'btn-adn';
        $this->className = $this->articleDetail->status != 2 ? '' : 'change-status';
        Admin::script($this->script());
        return "<span><a class='btn btn-xs {$style} {$this->className}' data-id='{$this->articleDetail->id}' data-status='{$this->articleDetail->status}'>
                " . $desc . "
              </a>&nbsp;&nbsp;</span>";
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->render();
    }
}