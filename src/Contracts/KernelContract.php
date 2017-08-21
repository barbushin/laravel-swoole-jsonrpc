<?php

/*
 * This file is part of the huang-yi/laravel-swoole-http package.
 *
 * (c) Huang Yi <coodeer@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HuangYi\JsonRpc\Contracts;

interface KernelContract
{
    /**
     * @param \HuangYi\JsonRpc\Foundation\Request $request
     * @return \HuangYi\JsonRpc\Foundation\Response
     */
    public function handle($request);

    /**
     * @param \HuangYi\JsonRpc\Foundation\Request $request
     * @param \HuangYi\JsonRpc\Foundation\Response $response
     */
    public function terminate($request, $response);
}
