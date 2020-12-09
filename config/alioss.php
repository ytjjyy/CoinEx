<?php
/**
 * Created by PhpStorm.
 * User: blaine
 * Date: 2018/7/31
 * Time: 下午3:28
 */

return [
    'ossServer' => '',                      // 外网
    'ossServerInternal' => '',       // 内网
    'AccessKeyId' => env('AliossAccessKeyId','LTAI9JefBY2yoOnp'),                     // key
    'AccessKeySecret' => env('AliossAccessKeySecret','3bKVZEC6RNlAUTQ0CE49SZW87f4NOf'),             // secret
    'BucketName' => env('AliossBucket','bishengoss')
];