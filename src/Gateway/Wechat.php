<?php
namespace Payment\Gateway;
use Symfony\Component\HttpFoundation\Request;
use Payment\Gateway\Wechat\Support;
use Payment\Support\Collection;
class Wechat{
    /**
     * 普通模式.
     */
    const MODE_NORMAL = 'normal';

    /**
     * 沙箱模式.
     */
    const MODE_DEV = 'dev';

    /**
     * 香港钱包 API.
     */
    const MODE_HK = 'hk';

    /**
     * 境外 API.
     */
    const MODE_US = 'us';

    /**
     * 服务商模式.
     */
    const MODE_SERVICE = 'service';

    /**
     * Const url.
     */
    const URL = [
        // self::MODE_NORMAL=>'https://api.mch.weixin.qq.com/',
        self::MODE_DEV=>'https://api.mch.weixin.qq.com/sandboxnew/',
        // self::MODE_HK=>'https://apihk.mch.weixin.qq.com/',
        // self::MODE_SERVICE=>'https://api.mch.weixin.qq.com/',
        // self::MODE_US=>'https://apius.mch.weixin.qq.com/',
    ];

    private $payload;

    public function __construct($config){
        Support::create($config);
        $this->payload=[
            'appid'=>$config->get('app_id', ''),
            'mch_id'=>$config->get('mch_id', ''),
            // 'nonce_str'=>\Str::random(),
            'nonce_str'=>strtoupper(uniqid()),
            'notify_url' => $config->get('notify_url', ''),
            'sign' => '',
            'trade_type' => '',
            'spbill_create_ip'=>Request::createFromGlobals()->getClientIp(),
        ];
        // $this->payload=$config;
    }

    public function __call($method,$param){
        return self::payment($method,...$param);
    }

    public function payment($method,$param=[]){
        $this->payload=array_merge($this->payload,$param);
        $gateway = get_class($this).'\\'.ucwords($method).'Gateway';
        if(class_exists($gateway)){
            return self::makePayment($gateway);
        }
        var_dump(1234);
    }

    public function makePayment($gateway){
        $app=new $gateway($this->payload);
        return $app->payment();
    }

    public function refund(array $order){
        $this->payload = Support::filterPayload($this->payload, $order, false);
        
        // Events::dispatch(new Events\MethodCalled('Wechat', 'Refund', $this->gateway, $this->payload));
        return Support::requestApi(
            'secapi/pay/refund',
            $this->payload,
            true
        );
    }

    public function verify($content = null,$refund = false){
        $content = $content??Request::createFromGlobals()->getContent();
        $data = Support::fromXml($content);
        // if ($refund) {
        //     $decrypt_data = Support::decryptRefundContents($data['req_info']);
        //     $data = array_merge(Support::fromXml($decrypt_data), $data);
        // }

        if ($refund || Support::generateSign($data) === $data['sign']) {
            return new Collection($data);
        }
        var_dump(456);
    }
}