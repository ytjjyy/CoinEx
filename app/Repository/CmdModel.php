<?php

namespace App\Repository;

use App\Services\OSS;
use Encore\Admin\Widgets\Form;
use Illuminate\Pagination\LengthAwarePaginator;
use Qcloud\Cos\Client;
use Request;
use Illuminate\Database\Eloquent\Model;

class CmdModel extends Model
{
    /*
     * 自定义来自外部的分页数据
     */
    public function paginate()
    {
        $perPage = Request::get('per_page', 20);
        $page = Request::get('page', 1);
        $params['pageNo'] = $page;
        $params['pageSize'] = $perPage;
        $modelParams = Request::only($this->params);
        $conditionColumn = request()->get('column');
        $sort = request()->get('sort');
//        if (!empty($conditionColumn)) {
//            $conditionColumn = strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $conditionColumn));
//        }
        $modelParams = $this->handle_param($modelParams);
        $params = array_merge($params, $modelParams);
        $params['column'] = $conditionColumn;
        $params['sort'] = $sort;
        $result = java_get($this->list_url, $params, $header = array());
        $data = array();
        $total = 0;
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            if (empty($result['content'])) {
                $data = [];
            } else {
                if (count($result['content']) == count($result['content'], 1) && !empty($result['content'])) {   // 判断是否是二维数组
                    $data[] = $result['content'];
                } else {
                    $data = $result['content'];
                }
            }

            $total = isset($result['total']) ? $result['total'] : count($data);
        }
        $paginator = new LengthAwarePaginator($data, $total, $perPage);
        $paginator->setPath(url()->current());
        return $paginator;
    }

    protected function handle_param($params)
    {

        if (isset($this->handle_offerSet)) {
            foreach ($this->handle_offerSet as $column) {
                if (array_key_exists($column, $params)) {
                    if ($this->handle_flag) {
                        $params[$column] = explode($this->handle_flag, $params[$column]);
                        if (is_array($params[$column])) {
                            foreach ($params[$column] as $key => $vo) {
                                $params[$this->handle_replace_column[$key]] = $vo;
                            }
                        }
                    }
                }
            }
        }
        return $params;
    }

    /*
     * 覆盖form 数据源
     */
    public function findOrFail($id)
    {
        $result = java_get($this->detail_url, [$this->detail_column => $id], []);
        $detail = array();
        if (isset($result['statusCode']) && $result['statusCode'] == 0) {
            $detail = $result['content'];
        } else {
            admin_log($this->list_url, ['userId' => $id], [], $result, 'get');
        }
        return static::newFromBuilder($detail);
    }

    // 自定义提交的form数据
    public function save(array $options = [])
    {
        $attributes = $this->getAttributes();
        $attributes = $this->upper($attributes);
        $attributes = $this->handle_image($attributes);
        if (isset($attributes[$this->primaryKey])) {   //修改数据
            $attributes = $this->handle_attributes($attributes);
            $result = java_put($this->put_url, $attributes, ['Content-Type:application/json']);
            if (!isset($result['statusCode']) || !$result['statusCode'] == 0) {
                $this->is_save = false;
                isset($result['errorMessage']) && $result['errorMessage'] ? $this->errorMessage = $result['errorMessage'] : '';
            }
        } else {
            $this->addData($attributes);
        }

    }

    /*
     * 添加数据
     */
    public function addData($attributes)
    {
        if (isset($this->hidden_form_column) && !empty($this->hidden_form_column)) {
            $attributes = array_merge($attributes, $this->hidden_form_column);
        }
        $result = java_post($this->add_url, $attributes, ['Content-Type:application/json']);
        if (!$result['statusCode'] == 0) {
            admin_log($this->add_url, $attributes, [], $result, 'post');
            $this->is_add = false;
            isset($result['errorMessage']) && !empty($result['errorMessage']) ? $this->errorMessage = $result['errorMessage'] : '';
        }
    }

    /*
     * 处理添加java端 表单数据
     */
    protected function handle_attributes($attributes)
    {
        if ($this->save_primaryKey) {
            if (!array_key_exists($this->save_primaryKey, $attributes)) {
                $attributes[$this->save_primaryKey] = $attributes['id'];
            }
        }
        foreach ($attributes as $key => $vo) {
            if (!in_array($key, $this->save_column)) {
                unset($attributes[$key]);
            }
        }
        /*
         * 替换form提交过来的字段
         */
        if (isset($this->form_replace_column)) {
            $replace_keys = array_keys($this->form_replace_column);
            foreach ($replace_keys as $vo) {
                if (isset($attributes[$vo])) {
                    $attributes[$this->form_replace_column[$vo]] = $attributes[$vo];
                    unset($attributes[$vo]);
                }
            }
        }

        return $attributes;
    }

    protected function upper($attributes)
    {
        if (isset($this->upper_column)) {
            foreach ($this->upper_column as $vo) {
                if (array_key_exists($vo, $attributes)) {
                    $attributes[$vo] = strtoupper($attributes[$vo]);
                }
            }
        }
        return $attributes;
    }

    public static function with($relations)
    {
        return new static;
    }

    /*
     * 上传图片到oss
     */
    protected function handle_image($attributes)
    {
        if ($this->image_column) {
            foreach ($this->image_column as $key => $item) {
                if (isset($attributes[$item]) && $attributes[$item]) {
                    $imagePath = public_path('uploads/' . $attributes[$item]);
                    if (file_exists($imagePath)) {
                        $key = basename($imagePath);
                        $is_success = OSS::upload($key, $imagePath);
                        if ($is_success) {
                            $attributes[$item] = env('AliossUrl') . $key;
                            @unlink($imagePath);
                        }
                    }

                }
            }
        }
        return $attributes;
    }
}
