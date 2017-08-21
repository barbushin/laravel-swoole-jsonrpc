<?php

/*
 * This file is part of the huang-yi/laravel-swoole-http package.
 *
 * (c) Huang Yi <coodeer@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HuangYi\JsonRpc\Exceptions;

class InvalidRequestException extends ResponseException
{
    /**
     * InvalidRequestException constructor.
     *
     * @param string $message
     * @param int $code
     */
    public function __construct($message = "Invalid Request", $code = -32600)
    {
        parent::__construct($message, $code);
    }
}
