<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/6/26
 * Time: 下午4:40
 */

namespace App\Admin\Extensions\Tools\Filter;


class Text
{
    protected $label = '';
    protected $name = '';
    protected $value = '';
    protected $type = 'text';
    protected $placeholder = '请输入您想要搜索的内容';

    public function __construct($params)
    {
        $this->label = $params['lable'];
        $this->name = $params['name'];
        $this->value = request($this->name);
    }

    public function render()
    {
        $view = view('admin.filter.text')->with([
            'name' => $this->name,
            'lable' => $this->label,
            'value' => $this->value,
            'type' => $this->type,
            'placeholder' => $this->placeholder
        ]);
        return response($view)->getContent();
    }
}