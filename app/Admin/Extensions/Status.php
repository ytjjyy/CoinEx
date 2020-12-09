<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/6/23
 * Time: 上午10:36
 */

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class Status
{
    protected $Detail;
    protected $url;
    protected $className;
    protected $column;

    public function __construct($Detail, string $url, string $className, $column = 'status')
    {
        $this->Detail = $Detail;
        $this->url = $url;
        $this->className = $className;
        $this->column = $column;
    }

    protected function script()
    {
        return <<<SCRIPT
        $('.{$this->className}').unbind('click').click(function () {
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
                        url:'{$this->url}?_pjax=%23pjax-container',
                        method:'post',
                        type:'json',
                        data:{id:id,status:status},
                        headers: {
                            'X-CSRF-TOKEN': LA.token, 
                        },
                        success:function(res){
                         $.pjax.reload('#pjax-container');
                         if(res.status==true){
                             swal(res.message, '', 'success');
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
        $desc = $this->Detail->{$this->column} == 'HIDE' ? '启用' : '禁用';
        $style = $this->Detail->{$this->column} === 'HIDE' ? 'btn-adn' : 'btn-bitbucket';
        return "<span><a class='btn btn-xs {$style}  {$this->className}' data-id='{$this->Detail->id}' data-status='{$this->Detail->{$this->column}}'>
                " . $desc . "
              </a>&nbsp;&nbsp;</span>";
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->render();
    }
}