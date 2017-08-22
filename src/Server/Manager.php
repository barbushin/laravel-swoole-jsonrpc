<?php

/*
 * This file is part of the huang-yi/laravel-swoole-http package.
 *
 * (c) Huang Yi <coodeer@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HuangYi\JsonRpc\Server;

use Exception;
use HuangYi\JsonRpc\Contracts\ExceptionHandlerContract;
use HuangYi\JsonRpc\Contracts\KernelContract;
use HuangYi\JsonRpc\Exceptions\Handler;
use HuangYi\JsonRpc\Exceptions\InternalErrorException;
use HuangYi\JsonRpc\Exceptions\ResponseException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Log;
use Swoole\Server;

class Manager
{
    /**
     * @var \Swoole\Server
     */
    protected $server;

    /**
     * Container.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Server events.
     *
     * @var array
     */
    protected $events = [
        'start', 'shutDown', 'workerStart', 'workerStop', 'connect', 'receive',
        'packet', 'close', 'bufferFull', 'bufferEmpty', 'task', 'finish',
        'pipeMessage', 'workerError', 'managerStart', 'managerStop',
    ];

    /**
     * JSON-RPC server constructor.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->initialize();
    }

    /**
     * Run swoole_jsonrpc_server.
     */
    public function run()
    {
        $this->server->start();
    }

    /**
     * Stop swoole_jsonrpc_server.
     */
    public function stop()
    {
        $this->server->shutdown();
    }

    /**
     * Initialize.
     */
    protected function initialize()
    {
        $this->setProcessName('manager process');

        $this->createSwooleServer();
        $this->configureSwooleServer();
        $this->setSwooleServerListeners();
    }

    /**
     * Creates swoole_jsonrpc_server.
     */
    protected function createSwooleServer()
    {
        $host = $this->container['config']['jsonrpc.server.host'];
        $port = $this->container['config']['jsonrpc.server.port'];

        $this->server = new Server($host, $port);
    }

    /**
     * Sets swoole_jsonrpc_server configurations.
     */
    protected function configureSwooleServer()
    {
        $config = $this->container['config']['jsonrpc.server.options'];

        $this->server->set($config);
    }

    /**
     * Sets swoole_jsonrpc_server listeners.
     */
    protected function setSwooleServerListeners()
    {
        foreach ($this->events as $event) {
            $listener = 'on' . ucfirst($event);

            if (method_exists($this, $listener)) {
                $this->server->on($event, [$this, $listener]);
            } else {
                $this->server->on($event, function () use ($event) {
                    $event = sprintf('jsonrpc.%s', $event);

                    $this->container['events']->fire($event, func_get_args());
                });
            }
        }
    }

    /**
     * "onStart" listener.
     */
    public function onStart()
    {
        $this->setProcessName('master process');
        $this->createPidFile();

        $this->container['events']->fire('jsonrpc.start', func_get_args());
    }

    /**
     * "onWorkerStart" listener.
     */
    public function onWorkerStart()
    {
        $this->clearCache();
        $this->setProcessName('worker process');

        $this->container['events']->fire('jsonrpc.workerStart', func_get_args());

        $this->container->singleton(KernelContract::class, Kernel::class);

        $this->container->singleton(ExceptionHandlerContract::class, Handler::class);
    }

    /**
     * Set onReceive listener.
     *
     * @param \Swoole\Server $server
     * @param int $connectionId
     * @param int $reactorId
     * @param string $payload
     */
    public function onReceive($server, $connectionId, $reactorId, $payload)
    {
        if ($this->container['config']['app.debug']) {
            $connectionInfo = $server->connection_info($connectionId);
            Log::debug(sprintf('Received request from [%s] with [%s]', $connectionInfo['remote_ip'], $payload));
        }

        $kernel = $this->container->make(KernelContract::class);

        try {

            $response = $kernel->handle(
                $request = Request::make($payload)
            );

        } catch (ResponseException $exception) {

            $response = $this->container[ExceptionHandlerContract::class]->render($request, $exception);

        } catch (Exception $exception) {

            $this->container[ExceptionHandlerContract::class]->report($exception);

            $response = $this->container[ExceptionHandlerContract::class]->render($request, new InternalErrorException);

        }

        $response->send($server, $connectionId);

        $kernel->terminate($request, $response);
    }

    /**
     * Set onShutdown listener.
     */
    public function onShutdown()
    {
        $this->removePidFile();

        $this->container['events']->fire('jsonrpc.showdown', func_get_args());
    }

    /**
     * Gets pid file path.
     *
     * @return string
     */
    protected function getPidFile()
    {
        return $this->container['config']->get('jsonrpc.server.options.pid_file');
    }

    /**
     * Create pid file.
     */
    protected function createPidFile()
    {
        $pidFile = $this->getPidFile();
        $pid = $this->server->master_pid;

        file_put_contents($pidFile, $pid);
    }

    /**
     * Remove pid file.
     */
    protected function removePidFile()
    {
        unlink($this->getPidFile());
    }

    /**
     * Clear APC or OPCache.
     */
    protected function clearCache()
    {
        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
        }

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * Sets process name.
     *
     * @param $process
     */
    protected function setProcessName($process)
    {
        $serverName = 'swoole_jsonrpc_server';
        $appName = $this->container['config']->get('app.name', 'Laravel');

        $name = sprintf('%s: %s for %s', $serverName, $process, $appName);

        swoole_set_process_name($name);
    }
}
