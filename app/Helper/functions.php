<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/6/25
 * Time: 下午7:05
 */

/*
 * 后台用户登录
 */
if (!function_exists('admin_login')) {
    function admin_login(string $url, array $param, $header = array()): array
    {
        $res = java_post($url, $param, $header);
        return $res;
    }
}
/**
 * 获取币种
 * @return array
 */
if (!function_exists('getCoin')) {
    function getCoin(): array
    {
        $options = [];
        $coin = java_get('coin', ['pageNo' => 1, 'pageSize' => 100]);
        if (isset($coin['statusCode']) && $coin['statusCode'] == 0) {
            foreach ($coin['content'] as $vo) {
                $options[$vo['displayName']] = $vo['displayName'];
            }
        }
        return $options;
    }
}

/**
 * 获取用户分组列表
 * @return array
 */
if (!function_exists('getGroup')) {
    function getGroup(): array
    {
        $group = java_get('userConfig/getUserGroupConfigList', []);
        $arr = [];
        if (isset($group['statusCode']) && $group['statusCode'] == 0) {
            foreach ($group['content'] as $vo) {
                $arr[$vo['groupType']] = $vo['groupName'];
            }
        }
        return $arr;
    }
}
if (!function_exists('admin_log')) {
    /*
     * 调用java接口 记录错误日志
     * @param url 请求链接
     * @param params 请求参数
     * @param header 请求头
     * @message 请求返回信息
     */
    function admin_log($url, $params, $header, $message, $method = 'get')
    {
        $header = array_merge(array('token:' . cache()->get('admin_token')), $header);
        $desc = '';
        $desc .= 'url:' . $url;
        $desc .= ' method:' . strtoupper($method);
        if (is_array($params)) {
            $params = json_encode($params);
        }
        $desc .= ' params:' . $params;
        if (is_array($header)) {
            $header = json_encode($header);
        }
        $desc .= ' header:' . $header;
        if (is_array($message)) {
            $message = urldecode(json_encode(url_encode($message)));
        }
        $desc .= ' message:' . $message;
        app('log')->error($desc);
    }
}
if (!function_exists('url_encode')) {
    /*
     * json_encode 编码转换成中文
     */
    function url_encode($str)
    {
        if (is_array($str)) {
            foreach ($str as $key => $value) {
                $str[urlencode($key)] = url_encode($value);
            }
        } else {
            if ($str && strlen($str) > 0) {
                if (is_numeric($str))
                    $str = $str;
                else $str = urlencode($str);
            }
        }

        return $str;
    }
}


if (!function_exists("java_post")) {
    /*
     * post url
     */
    function java_post(string $url, array $param, $header = array()): array
    {
        $url = env('JAVA_URL') . $url;
        if ($header) {
            $param = json_encode($param);
        } else {
            if (is_array($param)) {
                $param = http_build_query($param);
            }
        }
        $header = array_merge(array('token:' . cache()->get('admin_token')), $header);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $out_put = curl_exec($ch);
        if (!$out_put) {
            return array();
        }
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $body = substr($out_put, $headerSize);
        $json = json_decode($body, true);
        app('log')->info("post " . $url . ",data:" . $param . ",result:" . $body);
        if (isset($json['statusCode']) && $json['statusCode'] != 0) {
            admin_log($url, $param, $header, $json, 'post');
        }
        if (is_null($json)) {
            return array();
        }
        return $json;
    }
}
if (!function_exists('java_put')) {
    function java_put(string $url, array $param, $header = array()): array
    {
        $url = env('JAVA_URL') . $url;
        if ($header) {
            $param = json_encode($param);
        } else {
            if (is_array($param)) {
                $param = http_build_query($param);
            }
        }
        $header = array_merge(array('token:' . cache()->get('admin_token'), 'Expect:'), $header);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $out_put = curl_exec($ch);
        if (!$out_put) {
            return array();
        }
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $body = substr($out_put, $headerSize);
        $json = json_decode($body, true);
        if (isset($json['statusCode']) && $json['statusCode'] != 0) {
            admin_log($url, $param, $header, $json, 'PUT');
        }
        return $json;
    }
}
if (!function_exists('java_get')) {
    /**
     * 发送java get请求
     *
     * @param string $url
     * @param array $query
     * @param array $header
     * @return mixed|null
     */
    function java_get($url, $query = [], $header = array())
    {
        $url = env('JAVA_URL') . $url;
        if (is_array($query)) {
            $query = http_build_query($query);
            if ($query != '') {
                $url = $url . '?' . $query;
            }
        }
        $header = array_merge(array('token:' . cache()->get('admin_token')), $header);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        app('log')->info("get url:" . $url . ",token:" . cache()->get('admin_token'));
        $out_put = curl_exec($ch);
        if (!$out_put) {
            return array();
        }
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $body = substr($out_put, $headerSize);
        $json = json_decode($body, true);
        if (isset($json['statusCode']) && $json['statusCode'] != 0) {
            admin_log($url, $query, [], $json, 'get');
        }
        return $json;
    }
}
if (!function_exists('java_delete')) {
    function java_delete(string $url, array $param, $header = array()): array
    {
        $url = env('JAVA_URL') . $url;
        if ($header) {
            $param = json_encode($param);
        } else {
            if (is_array($param)) {
                $query = http_build_query($param);
                if ($query != '') {
                    $url = $url . '?' . $query;
                }
            }
        }
        $header = array_merge(array('token:' . cache()->get('admin_token'), 'Expect:'), $header);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $out_put = curl_exec($ch);
        if (!$out_put) {
            return array();
        }
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $body = substr($out_put, $headerSize);
        $json = json_decode($body, true);
        if (isset($json['statusCode']) && $json['statusCode'] != 0) {
            admin_log($url, $param, $header, $json, 'delete');
        }
        return $json;
    }
}
if (!function_exists('download_file')) {
    function download_file($url, $query = [], $header = [])
    {
        $url = env('JAVA_URL') . $url;
        if (is_array($query)) {
            $query = http_build_query($query);
            if ($query != '') {
                $url = $url . '?' . $query;
            }
        }
        $header = array_merge(array('token:' . cache()->get('admin_token')), $header);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        $out_put = curl_exec($ch);
        if (!$out_put) {
            return array();
        }
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $header = substr($out_put, 0, $headerSize);
        $header = explode("\n", $header);
        foreach ($header as $vo) {
            if (strncasecmp($vo, 'Content-disposition:', strlen('Content-disposition:')) == 0) {
                header($vo);
            }
            if (strncasecmp($vo, 'Content-Type:', strlen('Content-Type:')) == 0) {
                header($vo);
            }
        }
        $body = substr($out_put, $headerSize);
        echo $body;
    }
}
