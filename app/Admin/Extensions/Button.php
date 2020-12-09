<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/9
 * Time: 下午4:01
 */

namespace App\Admin\Extensions;


class Button
{
    protected $Detail;
    protected $url;

    public function __construct($Detail, string $url)
    {
        $this->Detail = $Detail;
        $this->url = $url;
    }

    protected function script()
    {


    }

    protected function render()
    {
        if ($this->Detail->status == 'APPLYING') {
            $desc = "待审核";
        } else if ($this->Detail->status == 'PASSED') {
            $desc = "审核通过";
        } else if ($this->Detail->status == 'FAILED') {
            $desc = "审核失败";
        } else {
            $desc = '待审核';
        }
        $style = $this->Detail->status == 'PASSED' ? 'btn-default ' : 'btn-adn';
        return "<span><a class='btn btn-xs {$style}}  check-pass'>
                 " . $desc . "
              </a>&nbsp;&nbsp;</span>";
    }

    public function __toString()
    {
        return $this->render();
    }
}