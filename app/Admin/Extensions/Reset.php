<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/6/26
 * Time: 下午7:51
 */

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class Reset
{
    protected $title;
    protected $articleDetail;
    protected $url;
    protected $className;
    protected $action_url;

    public function __construct($articleDetail, string $url, string $title, string $className, $action_url )
    {
        $this->articleDetail = $articleDetail;
        $this->title = $title;
        $this->url = $url;
        $this->className = $className;
        $this->action_url = $action_url;
    }

    public function script()
    {
        return <<<SCRIPT
        $('.{$this->className}').unbind('click').click(function () {
            let id=$(this).data('id');
            let url=$(this).data('url');
            swal({
              title: "你确定要{$this->title}吗?",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "确认",
              closeOnConfirm: false,
              cancelButtonText: "取消"
            },function(){
                   $.ajax({
                        url:"$this->url",
                        method:'post',
                        type:'json',
                        data:{id:id, action:url},
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
        return "<a class='btn btn-xs btn-bitbucket {$this->className}' data-id='{$this->articleDetail->id}' data-url='{$this->action_url}' >
                " . $this->title . "
              </a>&nbsp;&nbsp;";
    }

    public function __toString()
    {
        return $this->render();
    }
}