<?php
namespace Payment\Gateway\Wechat;
use Payment\Support\Traits\HasHttpRequest;
use Payment\Exception\InvalidArgumentException;
use Payment\Exception\InvalidSignException;
use Payment\Exception\BusinessException;
use Payment\Exception\GatewayException;
use Payment\Support\Collection;
use Payment\Gateway\Wechat;
class Support{
    use HasHttpRequest;

    protected $baseUri;

    protected $config;

    private static $instance;

    public function __construct($config){
        $this->baseUri =Wechat::URL[$config->get('mode',Wechat::MODE_NORMAL)];
        $this->config=$config;
        $this->setHttpOptions();
    }

    public function __get($key){
        return $this->getConfig($key);
    }

    public function getConfig($key = null, $default = null){
        if (is_null($key)) {
            return $this->config->all();
        }
        
        if($this->config->has($key)) {
            return $this->config->get($key);
        }
        return $default;
    }

    public static function filterPayload($payload, $params, $preserve_notify_url = false){
        $type = self::getTypeName($params['type'] ?? '');
        
        $payload = array_merge($payload,is_array($params)?$params:['out_trade_no' => $params]);
        $payload['appid'] = self::$instance->getConfig($type, '');
    
        unset($payload['trade_type'], $payload['type']);
        if(!$preserve_notify_url){
            unset($payload['notify_url']);
        }
        
        $payload['sign'] = self::generateSign($payload);
        return $payload;
    }

    public static function getTypeName($type = ''){
        switch ($type) {
            case '':
                $type = 'app_id';
                break;
            case 'app':
                $type = 'appid';
                break;
            default:
                $type = $type.'_id';
        }
        return $type;
    }

    public static function getInstance(){
        if (is_null(self::$instance)) {
            throw new InvalidArgumentException('You Should [Create] First Before Using');
        }

        return self::$instance;
    }
    
    public static function create($config){
        if ('cli' === php_sapi_name() || !(self::$instance instanceof self)) {
            self::$instance = new self($config);
            self::setDevKey();
        }
        return self::$instance;
    }

    /**
     * @return mixed
     */
    private static function setDevKey(){
        if (Wechat::MODE_DEV == self::$instance->mode) {
            $data = [
                'mch_id' => self::$instance->mch_id,
                // 'nonce_str' => \Str::random(),
                'nonce_str' => strtoupper(uniqid()),
            ];
            $data['sign'] = self::generateSign($data);
            $result=self::requestApi('pay/getsignkey',$data);
            self::$instance->config->set('key', $result['sandbox_signkey']);
        }
        return self::$instance;
    }

     /**
     * @param array $attributes
     * @return string
     * 生成签名
     */
    public static function generateSign($attributes){
        $key = self::$instance->key;
        ksort($attributes);
        $string = md5(self::getSignContent($attributes).'&key='.$key);
        return strtoupper($string);
    }

    public static function getSignContent($attributes){
        $buff = '';
        foreach ($attributes as $k => $v) {
            $buff .= ('sign' != $k && '' != $v && !is_array($v)) ? $k.'='.$v.'&' : '';
        }
        return trim($buff, '&');
    }

    public static function requestApi($endpoint,$data,$cert = false){
        $result = self::$instance->post(
            $endpoint,
            self::toXml($data),
            $cert ? [
                'cert' => self::$instance->cert_client,
                'ssl_key' => self::$instance->cert_key,
            ] : []
        );
        $result = is_array($result) ? $result : self::fromXml($result);
        return self::processingApiResult($endpoint, $result);
    }

    protected static function processingApiResult($endpoint, array $result){
        if (!isset($result['return_code']) || 'SUCCESS' != $result['return_code']) {
            throw new GatewayException('Get Wechat API Error:'.($result['return_msg'] ?? $result['retmsg'] ?? ''), $result);
        }

        if (isset($result['result_code']) && 'SUCCESS' != $result['result_code']) {
            throw new BusinessException('Wechat Business Error: '.$result['err_code'].' - '.$result['err_code_des'], $result);
        }
        if ('pay/getsignkey' === $endpoint ||
            false !== strpos($endpoint, 'mmpaymkttransfers') ||
            self::generateSign($result) === $result['sign']) {
            return new Collection($result);
        }
        //Events::dispatch(new Events\SignFailed('Wechat', '', $result));
        throw new InvalidSignException('Wechat Sign Verify FAILED', $result);
    }

    /**
     * @param array $data 
     * @return string
     * 数组转换成xml格式
     */
    public static function toXml($data){
        if (!is_array($data) || count($data) <= 0) {
             throw new InvalidArgumentException('Convert To Xml Error! Invalid Array!');
         }
        
        $xml = '<xml>';
        foreach($data as $key => $val){
            $xml .= is_numeric($val)?'<'.$key.'>'.$val.'</'.$key.'>':'<'.$key.'><![CDATA['.$val.']]></'.$key.'>';
        }
        $xml .= '</xml>';
        return $xml;
    }

    /**
     * @param string $xml 
     * @return array
     * xml格式转换成数组
     */
    public static function fromXml($xml): array{
        if (!$xml) {
            throw new InvalidArgumentException('Convert To Array Error! Invalid Xml!');
        }

        //判断当前的php版本是否小于8 如70405代表7.45
        if (\PHP_VERSION_ID < 80000) {
            libxml_disable_entity_loader(true);
        }
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE), true);
    }

    // public static function decryptRefundContents($contents){
    //     return openssl_decrypt(
    //         base64_decode($contents),
    //         'AES-256-ECB',
    //         md5(self::$instance->key),
    //         OPENSSL_RAW_DATA
    //     );
    // }

    /**
     * @return mixed
     */
    public function getBaseUri(){
        return $this->baseUri;
    }

    private function setHttpOptions(){
        if ($this->config->has('http') && is_array($this->config->get('http'))) {
            $this->config->forget('http.base_uri');
            $this->httpOptions = $this->config->get('http');
        }
        return $this;
    }
}