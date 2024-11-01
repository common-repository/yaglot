<?php

namespace Yaglot\Exceptions;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Throwable;

class ServerErrorException extends \Exception {

    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}