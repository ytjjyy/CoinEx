<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/4
 * Time: 上午10:25
 */

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class DetailButton
{
    protected $Detail;
    protected $url;
    protected $className;
    protected $column;
    protected $desc;
    public function __construct($Detail, string $url, string $className, $column = 'status',$desc)
    {
        $this->Detail = $Detail;
        $this->url = $url;
        $this->className = $className;
        $this->column = $column;
        $this->desc =$desc;
    }

    protected function script()
    {
        return <<<SCRIPT
        $('.{$this->className}').unbind('click').click(function () {
            let id=$(this).data('id');
            window.location.href='{$this->url}/'+id;
        });
SCRIPT;

    }

    protected function render()
    {
        Admin::script($this->script());
        return "<span><a class='btn btn-xs btn-bitbucket {$this->className}' data-id='{$this->Detail->{$this->column}}' >
                  {$this->desc}  
              </a>&nbsp;&nbsp;</span>";
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->render();
    }
}