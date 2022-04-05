<?php
namespace Payment\Exception;
class BusinessException extends GatewayException
{
    /**
     * @param string       $message
     * @param array|string $raw
     */
    public function __construct($message, $raw = [])
    {
        parent::__construct('ERROR_BUSINESS: '.$message, $raw, self::ERROR_BUSINESS);
    }
}
