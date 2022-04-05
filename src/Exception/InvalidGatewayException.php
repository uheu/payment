<?php
namespace Payment\Exception;

class InvalidGatewayException extends Exception
{
    /**
     * @param string       $message
     * @param array|string $raw
     */
    public function __construct($message, $raw = [])
    {
        parent::__construct('INVALID_GATEWAY: '.$message, $raw, self::INVALID_GATEWAY);
    }
}
