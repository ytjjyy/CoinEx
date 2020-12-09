<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/6/27
 * Time: 上午11:13
 */

namespace App\Admin\Extensions\Export;


use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Illuminate\Database\Eloquent\Model;

class CsvExport extends AbstractExporter
{
    protected $model;

    public function __construct(Grid $grid, Model $model)
    {
//        dd(\Request::all());
        $this->model = $model;
        parent::__construct($grid);

    }

    public function export()
    {
        $param = \Request::only($this->model->filter_export_column);
        if (isset($this->model->handle_offerSet)) {
            foreach ($this->model->handle_offerSet as $column) {
                if (array_key_exists($column, $param)) {
                    if ($this->model->handle_flag) {
                        $param[$column] = explode($this->model->handle_flag, $param[$column]);
                        if (is_array($param[$column])) {
                            foreach ($param[$column] as $key => $vo) {
                                $param[$this->model->handle_replace_column[$key]] = $vo;
                            }
                        }
                    }
                }
                unset($param[$column]);
            }
        }
        download_file($this->model->export_url, $param);
        exit;
    }
}