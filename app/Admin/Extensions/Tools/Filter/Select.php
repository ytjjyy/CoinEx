<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/6/26
 * Time: 下午3:50
 */

namespace App\Admin\Extensions\Tools\Filter;


class Select
{
    protected $label = '';
    protected $options = array();
    protected $name = '';
    protected $value = '';

    public function __construct($params)
    {
        $this->label = $params['lable'];
        $this->options = $params['options'];
        $this->name = $params['name'];
        $this->value = request($this->name);
    }

    public function render()
    {
        $view = view('admin.filter.select')->with([
            'name' => $this->name,
            'options' => $this->options,
            'lable' => $this->label,
            'value' => $this->value,
        ]);
        return response($view)->getContent();
    }
}