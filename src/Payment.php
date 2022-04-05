<?php
namespace Payment;
use Payment\Exception\InvalidGatewayException;
use Payment\Support\Config;
class Payment{
    private $config;

    /**
     * 初始化
     */
    public function __construct(array $config){
       $this->config=new Config($config);
    }

    
    public static function __callStatic($method,$params){
        $app=new self(...$params);
        return $app->create($method);
    }

    protected function create($method){
        $gateway = __NAMESPACE__."\\Gateway\\".ucwords($method);
        if(class_exists($gateway)){
            return self::makePayment($gateway);
        }
        throw new InvalidGatewayException("Gateway [{$method}] Not Exists");
    }
    protected function makePayment($gateway){
        return $app = new $gateway($this->config);
        // if ($app instanceof GatewayApplicationInterface) {
        //     return $app;
        // }
        // throw new InvalidGatewayException("Gateway [{$gateway}] Must Be An Instance Of GatewayApplicationInterface");
    }
}