<?php

namespace HuangYi\JsonRpc;

use HuangYi\JsonRpc\Client\ConnectionManager;

class ClientServiceProvider extends JsonRpcServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerClient();
    }

    /**
     * Register client.
     */
    protected function registerClient()
    {
        $this->app->singleton('swoole.jsonrpc.client', function ($app) {
            return new ConnectionManager($app);
        });
    }
}
