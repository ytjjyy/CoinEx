<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/9
 * Time: 上午11:48
 */

namespace App\Admin\Extensions\Tools\Filter;


class DateTime
{
    protected $label = '';
    protected $options = array();
    protected $name = '';
    protected $value = '';

    public function __construct($params)
    {
        $this->label = $params['lable'];
        $this->name = $params['name'];
        $this->value = request($this->name);
    }

    public function render()
    {
        $view = view('admin.filter.datetime')->with([
            'name' => $this->name,
            'options' => $this->options,
            'label' => $this->label,
            'value' => $this->value,
        ]);
        return response($view)->getContent();
    }
}