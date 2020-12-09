<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/9/13
 * Time: 下午3:28
 */

namespace Encore\Admin\Grid\Tools;


use Encore\Admin\Admin;
use Encore\Admin\Grid;

class BackButton extends AbstractTool
{
    /**
     * Create a new CreateButton instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Render CreateButton.
     *
     * @return string
     */
    public function render()
    {
        if (!$this->grid->allowBack()) {
            return '';
        }
        $script = <<<'EOT'
$('.form-history-back').on('click', function (event) {
    event.preventDefault();
    history.back(1);
});
EOT;
        Admin::script($script);
        $text = trans('admin.back');
        return <<<EOT

<div class="btn-group pull-right" style="margin-right: 10px">
    <a class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;$text</a>
</div>

EOT;
    }
}