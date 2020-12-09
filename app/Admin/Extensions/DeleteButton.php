<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/4
 * Time: 上午10:25
 */

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class DeleteButton
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
            $(this).addClass('disabled')
            let id=$(this).data('id');
            swal({
              title: "你确定要删除该条记录吗?",
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
                        data:{id:id},
                        headers: {
                            'X-CSRF-TOKEN': LA.token, 
                        },
                        success:function(res){
                         $.pjax.reload('#pjax-container');
                         $('.{$this->className}').removeClass('disabled');
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
        return "<span><a class='btn btn-xs btn-adn {$this->className}' data-id='{$this->Detail->{$this->column}}' >
                  删除  
              </a>&nbsp;&nbsp;</span>";
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->render();
    }
}