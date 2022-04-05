<?php
namespace Payment\Gateway\Wechat;
class MiniGateway extends OfficialGateway{
    protected function openapi(){
        $this->params['appid'] = Support::getInstance()->mini_id;
        return parent::openapi();
    }
}