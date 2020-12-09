<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/2
 * Time: 下午2:27
 */

namespace App\Admin\Extensions;


use Encore\Admin\Form\Field;

class WangEditor extends Field
{
    protected $view = 'admin.wang-editor';

    protected static $css = [
        '/vendor/wangEditor-3.1.1/release/wangEditor.min.css',
    ];

    protected static $js = [
        '/vendor/wangEditor-3.1.1/release/wangEditor.min.js',
    ];

    public function render()
    {
        $name = $this->formatName($this->column);
        $this->script = <<<EOT
            var E = window.wangEditor
            var editor = new E('#{$this->id}');
            editor.customConfig.zIndex = 0;
            editor.customConfig.debug=true;
            editor.customConfig.onchange = function (html) {
                $('input[name=\'$name\']').val(html);
            }
            editor.customConfig.uploadFileName = 'file'
            editor.customConfig.uploadImgServer = '/admin/article/upload'
            editor.customConfig.uploadImgHeaders = {
                'X-CSRF-TOKEN': LA.token
             }
             editor.customConfig.uploadImgHooks = {
                before: function (xhr, editor, files) {
                    // 图片上传之前触发
                    // xhr 是 XMLHttpRequst 对象，editor 是编辑器对象，files 是选择的图片文件
                    
                    // 如果返回的结果是 {prevent: true, msg: 'xxxx'} 则表示用户放弃上传
                    // return {
                    //     prevent: true,
                    //     msg: '放弃上传'
                    // }
                },
                success: function (xhr, editor, result) {
                    // 图片上传并返回结果，图片插入成功之后触发
                    // xhr 是 XMLHttpRequst 对象，editor 是编辑器对象，result 是服务器端返回的结果
                },
                fail: function (xhr, editor, result) {
                    // 图片上传并返回结果，但图片插入错误时触发
                    // xhr 是 XMLHttpRequst 对象，editor 是编辑器对象，result 是服务器端返回的结果
                },
                error: function (xhr, editor) {
                    // 图片上传出错时触发
                    // xhr 是 XMLHttpRequst 对象，editor 是编辑器对象
                },
                timeout: function (xhr, editor) {
                  
                },
                customInsert: function (insertImg, result, editor) {
                    if(result.error==0){
                        var url = result.data
                        insertImg(url)
                    }else{
                        alert(result.message);
                    }
                    
                }
             
            }
            editor.create()
EOT;
        return parent::render();
    }
}