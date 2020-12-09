<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/9
 * Time: 下午3:38
 */

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class CheckButton
{
    protected $Detail;
    protected $url;

    public function __construct($Detail, string $url, $action = 'finance/transfer-check-pass', $fail_action = 'finance/transfer-check-fail')
    {
        $this->Detail = $Detail;
        $this->url = $url;
        $this->action = $action;
        $this->fail_action = $fail_action;
    }

    protected function script()
    {
        return <<<SCRIPT
        $('.check-pass').unbind('click').click(function () {
            let id=$(this).data('id');
            let action=$(this).data('action');
            swal({
              title: "你确定要审核吗?",
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
                        data:{id:id,action:action},
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
         $('.check-fail').unbind('click').click(function () {
                    let id=$(this).data('id');
                    let action=$(this).data('action');
                    swal({
                      title: "你确定要审核吗?",
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
                                data:{id:id,action:action},
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
        Admin::script($this->script());
        return "<span><a class='btn btn-xs btn-bitbucket  check-pass' data-id='{$this->Detail->id}' data-action='{$this->action}'>
                 审核通过 
              </a>&nbsp;&nbsp;</span>
              <span><a class='btn btn-xs btn-adn  check-fail' data-id='{$this->Detail->id}' data-action='{$this->fail_action}'>
                 审核不通过 
              </a>&nbsp;&nbsp;</span>
              ";
    }

    public function __toString()
    {
        return $this->render();
    }
}