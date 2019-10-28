<?php
namespace Swango\Aliyun\Acm\Exception;
class ACMException extends \Exception {
    public function __construct(string $message = "", $code = 0) {
        parent::__construct($message, $code);
    }
}