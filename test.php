<?php
require __DIR__ . '/vendor/autoload.php';
use Payment\Payment;

$config=[
    'appid' 				=>'xxxx',// APP APPID
    'mini_id'			    =>'wx6e301696cc179a6b',// 小程序 APPID
    'app_id' 				=>'wx5fcbaca16581e16a',// 公众号 APPID
    'mch_id'	    		=>'1596092201',
    'key'                   =>'89c383df1bd197f2d398b7a0f07db030',
    // 'cert_client'  			=>'/home/wwwroot/api.bnschoolbus.com/apiclient/apiclient_cert.pem',// optional，退款等情况时用到
    // 'cert_key'   			=>'/home/wwwroot/api.bnschoolbus.com/apiclient/apiclient_key.pem',// optional，退款等情况时用到
    'notify_url'			=>'http://testapi.zhaodaolo.com/wechat/notify',
    'mode'                  =>'dev', // optional, dev/hk;当为 `hk` 时，为香港 gateway。普通模式:normal,
];
$order=[
    'out_trade_no'=>uniqid(),
    'total_fee'=>1011,
    'type'=>'mini'
];
var_dump(Payment::wechat($config)->refund($order));exit;
