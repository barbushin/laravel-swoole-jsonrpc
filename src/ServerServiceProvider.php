<?php

namespace HuangYi\JsonRpc;

use HuangYi\JsonRpc\Commands\JsonRpcCommand;
use HuangYi\JsonRpc\Server\Manager;
use HuangYi\JsonRpc\Routing\Router;

class ServerServiceProvider extends JsonRpcServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerRouter();
        $this->registerServer();
        $this->registerCommands();
    }

    /**
     * Register router.
     */
    protected function registerRouter()
    {
        $this->app->singleton('swoole.jsonrpc.router', function ($app) {
            return new Router($app);
        });

        $this->app->alias('swoole.jsonrpc.router', Router::class);
    }

    /**
     * Register server.
     */
    protected function registerServer()
    {
        $this->app->singleton('swoole.jsonrpc', function ($app) {
            return new Manager($app);
        });

        $this->app->alias('swoole.jsonrpc', Manager::class);
    }

    /**
     * Register commands.
     */
    protected function registerCommands()
    {
        $this->commands([
            JsonRpcCommand::class,
        ]);
    }
}
