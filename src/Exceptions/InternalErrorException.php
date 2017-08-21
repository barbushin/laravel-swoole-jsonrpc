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

class InternalErrorException extends ResponseException
{
    /**
     * NotFoundException constructor.
     *
     * @param string $message
     * @param int $code
     * @param array|null $data
     */
    public function __construct($message = "Internal error", $code = -32603, array $data = null)
    {
        parent::__construct($message, $code, $data);
    }
}
