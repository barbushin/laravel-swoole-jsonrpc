<?php

namespace HuangYi\JsonRpc\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \HuangYi\JsonRpc\Server\ResponseFactory
 */
class Response extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'swoole.jsonrpc.response';
    }
}
