<?php
return array(
    //'配置项'=>'配置值'
    define('WEB_HOST', 'http://www.airenxiao.com'),
    /*微信支付配置*/
    'WxPayConf_pub'=>array(
        'APPID' => 'wxd12a95234827bea8',
        'MCHID' => '1335685201',
        'KEY' => '34dsafkdsfkdslsdfeiquresadffewfa',
        'APPSECRET' => 'ec404c70cbb5808c466317a4857800d7',
        'JS_API_CALL_URL' => WEB_HOST.'/Weixin/Weixin/jsApiCall',
        'SSLCERT_PATH' => WEB_HOST.'/ThinkPHP/Library/Vendor/WxPayPubHelper/cacert/apiclient_cert.pem',
        'SSLKEY_PATH' => WEB_HOST.'/ThinkPHP/Library/Vendor/WxPayPubHelper/cacert/apiclient_key.pem',
        'NOTIFY_URL' =>  WEB_HOST.'/Weixin/Weixin/notify',
        'CURL_TIMEOUT' => 30
    )
);