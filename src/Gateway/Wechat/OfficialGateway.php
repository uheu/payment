<?php
namespace Payment\Gateway\Wechat;
use Payment\Gateway\Wechat\Support;
use Payment\Support\Collection;
class OfficialGateway extends Gateway{
    protected function openapi(){
        $merge=array_merge($this->params,['trade_type'=>$this->getTradeType()]);

        $pay_request = [
            'appId' => $this->params['appid'],
            'timeStamp' => strval(time()),
            'nonceStr' => uniqid(),
            'package' => 'prepay_id='.$this->preOrder($merge)->get('prepay_id'),
            'signType' => 'MD5',
        ];
        $pay_request['paySign'] = Support::generateSign($pay_request);
        return new Collection($pay_request);
    }

    protected function getTradeType(){
        return 'JSAPI';
    }
}