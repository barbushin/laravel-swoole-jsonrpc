<?php

/*
 * This file is part of the huang-yi/laravel-swoole-http package.
 *
 * (c) Huang Yi <coodeer@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HuangYi\JsonRpc\Client;

use HuangYi\JsonRpc\Exceptions\ConnectionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Log;
use Swoole\Client;

class Connection
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $port;

    /**
     * @var \Swoole\Client
     */
    protected $client;

    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @var int
     */
    public $timer;

    /**
     * Connection constructor.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @param string $host
     * @param string $port
     * @throws \HuangYi\JsonRpc\Exceptions\ConnectionException
     */
    public function __construct(Container $container, $host, $port)
    {
        $this->container = $container;
        $this->host = $host;
        $this->port = $port;

        $this->createSwooleClient();
        $this->connect();
        $this->registerListeners();
    }

    /**
     * Initialize.
     */
    protected function createSwooleClient()
    {
        $this->client = new Client(SWOOLE_TCP | SWOOLE_KEEP);
    }

    /**
     * Connect.
     *
     * @return $this
     * @throws \HuangYi\JsonRpc\Exceptions\ConnectionException
     */
    public function connect()
    {
        if (! $this->client->connect($this->host, $this->port, -1)) {
            throw new ConnectionException(
                sprintf(
                    'Connect JSON-RPC Server [%s:%s] failed. Error code: %s.',
                    $this->host,
                    $this->port,
                    $this->client->errCode
                )
            );
        }

        return $this;
    }

    /**
     * Register listeners.
     */
    protected function registerListeners()
    {
        $this->client->onClose(function () {
            $this->clearTimer();
        });
    }

    /**
     * @return bool
     */
    public function ping()
    {
        $this->send('ping');

        return $this->receive() === 'pong';
    }

    /**
     * Send a request.
     *
     * @param string $method
     * @param array|null $params
     * @param mixed $id
     * @return \HuangYi\JsonRpc\Client\Response
     */
    public function request($method, array $params = null, $id = null)
    {
        $request = new Request($method, $params, $id);

        $this->send($request->toJson());

        return Response::make($this->receive());
    }

    /**
     * Send a notification.
     *
     * @param string $method
     * @param array|null $params
     */
    public function notify($method, array $params = null)
    {
        $notification = new Notification($method, $params);

        $this->send($notification->toJson());
    }

    /**
     * Send request.
     *
     * @param $content
     * @return string
     */
    public function send($content)
    {
        if ($this->isDebug()) {
            Log::debug(sprintf('Send request to [%s:%s] with \'%s\'', $this->host, $this->port, $content));
        }

        $this->client->send($content);
    }

    /**
     * Receive response.
     *
     * @return \HuangYi\JsonRpc\Client\Response
     */
    public function receive()
    {
        $payload = $this->client->recv();

        if ($this->isDebug()) {
            Log::debug(sprintf('Received response \'%s\'', $payload));
        }

        return $payload;
    }

    /**
     * Close connection.
     *
     * @return $this
     */
    public function disconnect()
    {
        $this->client->close();

        return $this;
    }

    /**
     * Clear timer.
     */
    public function clearTimer()
    {
        if ($this->timer) {
            swoole_timer_clear($this->timer);
        }
    }

    /**
     * @return bool
     */
    protected function autoConnect()
    {
        return $this->container['config']['jsonrpc.client.auto_reconnect'];
    }

    /**
     * @return bool
     */
    protected function isDebug()
    {
        return $this->container['config']['jsonrpc.debug'];
    }
}
