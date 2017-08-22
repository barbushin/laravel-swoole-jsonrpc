<?php

namespace HuangYi\JsonRpc\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \HuangYi\JsonRpc\Client\ConnectionManager
 */
class JsonRpcClient extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'swoole.jsonrpc.client';
    }
}
