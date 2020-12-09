<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/6/26
 * Time: 下午3:10
 */

namespace App\Admin\Extensions\Tools;

use App\Admin\Extensions\Tools\Filter\DateTime;
use App\Admin\Extensions\Tools\Filter\Select;
use App\Admin\Extensions\Tools\Filter\Text;
use Encore\Admin\Admin;
use URL;
use Encore\Admin\Grid\Tools\AbstractTool;

class Filter extends AbstractTool
{
    protected $html = '';
    protected $params = [];

    public function script()
    {
        
    }

    public function select()
    {
        $select = new Select($this->params);
        $this->html .= $select->render();
    }

    public function SetParams($params)
    {
        $this->params = $params;
        return $this;
    }

    public function text()
    {
        $text = new Text($this->params);
        $this->html .= $text->render();
    }

    public function datetime()
    {
        $datetime = new DateTime($this->params);
        $this->html .= $datetime->render();
    }

    public function render($uri='')
    {
        if(!$uri){
            if (strpos(request()->getRequestUri(), '?')) {
                $uri = explode('?', request()->getRequestUri())[0];
            } else {
                $uri = request()->getRequestUri();
            }
        }

        Admin::script($this->script());
        return view('admin.tools.filter')->with(['html' => $this->html, 'uri' => $uri]);
    }

    public function __destruct()
    {
        unset($this->html);
    }

    public function __call($method, $arg)
    {

    }
}