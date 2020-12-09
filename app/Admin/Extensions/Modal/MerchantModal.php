<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/8/6
 * Time: 下午4:00
 */

namespace App\Admin\Extensions\Modal;

use Encore\Admin\Admin;

class MerchantModal
{
    protected $articleDetail;

    protected $url;

    public function __construct($articleDetail, string $url)
    {
        $this->articleDetail = $articleDetail;
        $this->url = $url;
    }

    protected function script()
    {
        return <<<SCRIPT
            $('.dispatch-button').click(function(){
                let merchant_id = $(this).data('id');
                $('#myModal').find('.merchant_id').val(merchant_id);
            });
            $('.add-coin').click(function(){
              $('#myModal').modal('hide');
              let id = $('#myModal').find('.merchant_id').val();
              let coinName = $('#myModal').find('#coinName').val();
              let available = $('#myModal').find('#available').val();
              let freeze = $('#myModal').find('#freeze').val();
              swal({
              title: "你确定要拨币吗?",
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
                        data:{id:id,coinName:coinName,available:available,freeze:freeze},
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
                    $('#myModal').find('#coinName').val('');
                    $('#myModal').find('#available').val('');
                    $('#myModal').find('#freeze').val('');
                
               })
            });  
SCRIPT;

    }

    protected function render()
    {
        Admin::script($this->script());
        $html = '<button type="button" class="btn btn-primary btn-sm dispatch-button" data-toggle="modal" data-target="#myModal" data-id=' . $this->articleDetail->id . '>
                拨币
        </button>';

        return $html;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->render();
    }
}