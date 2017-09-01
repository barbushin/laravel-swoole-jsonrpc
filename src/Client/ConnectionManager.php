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

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class ConnectionManager
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The active connection instances.
     *
     * @var array
     */
    protected $connections = [];

    /**
     * ClientManager constructor.
     * @param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get a connection instance.
     *
     * @param string $name
     * @param bool $reconnect
     * @return \HuangYi\JsonRpc\Client\Connection
     */
    public function connection($name = null, $reconnect = false)
    {
        $name = $name ?: $this->getDefaultConnection();

        if ($reconnect || ! isset($this->connections[$name])) {
            $this->connections[$name] = $this->createConnection($name);
        }

        return $this->connections[$name];
    }

    /**
     * Create a connection.
     *
     * @param string $name
     * @return \HuangYi\JsonRpc\Client\Connection
     */
    public function createConnection($name)
    {
        $config = $this->configuration($name);

        $connection = new Connection($this->container, $name, $config['host'], $config['port']);

        return $connection;
    }

    /**
     * Get the configuration for a connection.
     *
     * @param  string  $name
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function configuration($name)
    {
        $name = $name ?: $this->getDefaultConnection();

        $connections = $this->container['config']['jsonrpc.client.connections'];

        if (is_null($config = Arr::get($connections, $name))) {
            throw new InvalidArgumentException("JSON-RPC Server [$name] not configured.");
        }

        return $config;
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return $this->container['config']['jsonrpc.client.default'];
    }

    /**
     * Disconnect from the given server.
     *
     * @param  string  $name
     * @return void
     */
    public function disconnect($name = null)
    {
        $name = $name ?: $this->getDefaultConnection();

        if (isset($this->connections[$name])) {
            $this->connections[$name]->disconnect();
        }
    }

    /**
     * Reconnect to the given server.
     *
     * @param  string  $name
     * @return \HuangYi\JsonRpc\Client\Connection
     */
    public function reconnect($name = null)
    {
        $name = $name ?: $this->getDefaultConnection();

        $this->disconnect($name);

        return $this->connection($name, true);
    }

    /**
     * @return bool
     */
    protected function autoConnect()
    {
        return $this->container['config']['jsonrpc.client.auto_reconnect'];
    }
}
