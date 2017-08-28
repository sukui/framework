<?php

namespace ZanPHP\Framework\Foundation\Exception\Handler;

use ZanPHP\Contracts\Foundation\ExceptionHandler;

class ExceptionLogger extends BaseExceptionHandler implements ExceptionHandler
{
    public function handle(\Exception $e)
    {
        if (!isset($e->logLevel)) {
            return false;
        }
        if (null === $e->logLevel) {
            return false;
        }

        //TODO: logging
    }
}
