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
     * JsonRpcClient constructor.
     *
     * @param string $host
     * @param string $port
     * @throws \HuangYi\JsonRpc\Exceptions\ConnectionException
     */
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;

        $this->createSwooleClient();
        $this->connect($host, $port);
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
     * @param string $host
     * @param string $port
     * @return $this
     * @throws \HuangYi\JsonRpc\Exceptions\ConnectionException
     */
    public function connect($host, $port)
    {
        if (! $this->client->connect($host, $port, -1)) {
            throw new ConnectionException(
                sprintf('Connect JSON-RPC Server [%s:%s] failed. Error code: %s.', $host, $port, $this->client->errCode)
            );
        }

        return $this;
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

        return $this->receive();
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

        return Response::make($payload);
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
     * @param \Illuminate\Contracts\Container\Container $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @return bool
     */
    protected function isDebug()
    {
        return $this->container['config']['jsonrpc.debug'];
    }
}
