<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/6/23
 * Time: 上午10:36
 */

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class ChangeVip
{
    protected $userCoin;

    public function __construct($articleDetail, string $url)
    {
        $this->userCoin = $articleDetail;
        $this->url = $url;
    }

    protected function script()
    {
        return <<<SCRIPT
        $('.change-status').unbind('click').click(function () {
            let userId=$(this).data('userid');
            let coinName=$(this).data('coinname');
            let vipType=$(this).data('viptype');
            swal({
              title: "你确定要修改VIP状态吗?",
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
                        //type:'json',
                        data:{userId:userId,coinName:coinName,vipType:vipType},
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
        $desc = $this->userCoin->vipType == 1 ? '禁用VIP' : '启用VIP';
        $style = $this->userCoin->vipType === 1 ? 'btn-adn' : 'btn-bitbucket';
        $newVipType = ($this->userCoin->vipType == 1) ? 0 : 1;
        return "<span><a class='btn btn-xs {$style} change-status' data-userid='{$this->userCoin->userId}' data-coinname='{$this->userCoin->coinName}' data-viptype='{$newVipType}'>
                " . $desc . "
              </a>&nbsp;&nbsp;</span>";
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->render();
    }
}