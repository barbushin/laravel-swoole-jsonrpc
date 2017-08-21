<?php

/*
 * This file is part of the huang-yi/laravel-swoole-http package.
 *
 * (c) Huang Yi <coodeer@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HuangYi\JsonRpc;

use HuangYi\JsonRpc\Commands\JsonRpcCommand;
use HuangYi\JsonRpc\Foundation\Server;
use HuangYi\JsonRpc\Routing\Router;
use Illuminate\Support\ServiceProvider;

class JsonRpcServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
        $this->registerRouter();
        $this->registerServer();
        $this->registerCommands();
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/jsonrpc.php' => base_path('config/jsonrpc.php')
        ], 'config');
    }

    /**
     * Merge configurations.
     */
    protected function mergeConfig()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/jsonrpc.php', 'jsonrpc');
    }

    /**
     * Register router.
     */
    protected function registerRouter()
    {
        $this->app->singleton('swoole.jsonrpc.router', function ($app) {
            return new Router($app);
        });
    }

    /**
     * Register server.
     */
    protected function registerServer()
    {
        $this->app->singleton('swoole.jsonrpc', function ($app) {
            return new Server($app);
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
