<?php

namespace HuangYi\JsonRpc;

use HuangYi\JsonRpc\Commands\JsonRpcCommand;
use HuangYi\JsonRpc\Server\Manager;
use HuangYi\JsonRpc\Routing\Router;
use HuangYi\JsonRpc\Server\ResponseFactory;
use HuangYi\JsonRpc\Server\WhiteList;

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

        $this->registerWhiteList();
        $this->registerRouter();
        $this->registerServer();
        $this->registerCommands();
    }

    /**
     * Register white list.
     */
    protected function registerWhiteList()
    {
        $this->app->singleton('swoole.jsonrpc.whitelist', function ($app) {
            return new WhiteList($app);
        });

        $this->app->alias('swoole.jsonrpc.whitelist', WhiteList::class);
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
        // jsonrpc server
        $this->app->singleton('swoole.jsonrpc', function ($app) {
            return new Manager($app);
        });

        $this->app->alias('swoole.jsonrpc', Manager::class);

        // jsonrpc response
        $this->app->singleton('swoole.jsonrpc.response', function () {
            return new ResponseFactory;
        });
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
