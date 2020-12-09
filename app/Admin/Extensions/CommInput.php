<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/6/23
 * Time: 上午10:36
 */

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class CommInput
{
    protected $url;
    protected $idName;    // 标识记录的id名称,用于标识某个记录
    protected $idValue;   // 标识记录的id值,用于标识某个记录
    protected $valueName; // 修改的值的名称
    protected $title;
    protected $valueDesc;
    protected $buttonName;

    public function __construct(string $url, $idName, $idValue, $valueName, $buttonName, $title, $valueDesc)
    {
        $this->url = $url;
        $this->idName = $idName;
        $this->idValue = $idValue;
        $this->valueName = $valueName;
        $this->title = $title;
        $this->valueDesc = $valueDesc;
        $this->buttonName = $buttonName;
    }

    protected function script()
    {
        return <<<SCRIPT
        $('.change-status').unbind('click').click(function () {
            let {$this->idName}=$(this).data('idvalue');
            swal({
              title: "{$this->title}",
              type: "input",
              text: "{$this->valueDesc}",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "确认",
              closeOnConfirm: false,
              cancelButtonText: "取消"
            },function(inputValue){
                if(inputValue) {
                   $.ajax({
                        url:'{$this->url}',
                        method:'post',
                        data:{{$this->idName}:{$this->idName},{$this->valueName}:inputValue},
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
                }
            })
        });
SCRIPT;

    }

    protected function render()
    {
        Admin::script($this->script());
        return "<span><a class='btn btn-xs btn-adn change-status' data-idvalue='{$this->idValue}'>" . $this->buttonName . "</a>&nbsp;&nbsp;</span>";
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->render();
    }
}