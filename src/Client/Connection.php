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
use Swoole\Client;

class Connection
{
    /**
     * @var \Swoole\Client
     */
    protected $client;

    /**
     * JsonRpcClient constructor.
     *
     * @param $host
     * @param $port
     * @throws \HuangYi\JsonRpc\Exceptions\ConnectionException
     */
    public function __construct($host, $port)
    {
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
     * @return string
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
        $this->client->send($content);
    }

    /**
     * Receive response.
     *
     * @return string
     */
    public function receive()
    {
        return $this->client->recv();
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
}
