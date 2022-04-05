<?php
namespace Payment\Gateway\Wechat;
abstract class Gateway{
    protected $params;

    public function __construct(array $params){
        $this->params=$params;
    }

    public function payment(){
        return $this->openapi();
    }

    abstract protected function openapi();
    
    abstract protected function getTradeType();

    protected function preOrder($data){
        $data['sign'] = Support::generateSign($data);
        return Support::requestApi('pay/unifiedorder',$data);
    }
}