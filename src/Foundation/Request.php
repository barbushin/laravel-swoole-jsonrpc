<?php

/*
 * This file is part of the huang-yi/laravel-swoole-http package.
 *
 * (c) Huang Yi <coodeer@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HuangYi\JsonRpc\Foundation;

use HuangYi\JsonRpc\Exceptions\InvalidRequestException;
use HuangYi\JsonRpc\Exceptions\ParseErrorException;
use HuangYi\JsonRpc\Routing\Route;

class Request
{
    /**
     * JSON-RPC version.
     *
     * @var string
     */
    protected $jsonrpc = '2.0';

    /**
     * Request method.
     *
     * @var string
     */
    protected $method;

    /**
     * Request parameters.
     *
     * @var mixed
     */
    protected $params;

    /**
     * Request id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Required members in JSON-RPC 2.0.
     *
     * @var array
     */
    protected $requiredMembers = ['jsonrpc', 'method'];

    /**
     * Optional members in JSON-RPC 2.0.
     *
     * @var array
     */
    protected $optionalMembers = ['params', 'id'];

    /**
     * Current route.
     *
     * @var \HuangYi\JsonRpc\Routing\Route
     */
    protected $route;

    /**
     * Makes JSON-RPC request.
     *
     * @param $payload
     * @return static
     * @throws \HuangYi\JsonRpc\Exceptions\ParseErrorException
     * @throws \HuangYi\JsonRpc\Exceptions\InvalidRequestException
     */
    public static function make($payload)
    {
        $attributes = static::parseJson($payload);

        return new static($attributes);
    }

    /**
     * Request constructor.
     *
     * @param array $attributes
     * @throws \HuangYi\JsonRpc\Exceptions\InvalidRequestException
     */
    public function __construct(array $attributes)
    {
        $this->initialize($attributes);
    }

    /**
     * Sets the parameters for JSON-RPC request.
     *
     * @param array $attributes
     * @throws \HuangYi\JsonRpc\Exceptions\InvalidRequestException
     */
    protected function initialize(array $attributes)
    {
        foreach ($this->requiredMembers as $member) {
            if (! array_key_exists($member, $attributes)) {
                throw new InvalidRequestException;
            }

            $this->{$member} = $attributes[$member];
        }

        foreach ($this->optionalMembers as $member) {
            if (array_key_exists($member, $attributes)) {
                $this->{$member} = $attributes[$member];
            }
        }
    }

    /**
     * @return string
     */
    public function getJsonrpc()
    {
        return $this->jsonrpc;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get parameter value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        return array_get($this->params, $key, $default);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the route handling the request.
     *
     * @return \HuangYi\JsonRpc\Routing\Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set the route resolver callback.
     *
     * @param  \HuangYi\JsonRpc\Routing\Route $route
     * @return $this
     */
    public function setRoute(Route $route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Parse json.
     *
     * @param string $payload
     * @return array
     * @throws \HuangYi\JsonRpc\Exceptions\ParseErrorException
     */
    public static function parseJson($payload)
    {
        $array = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParseErrorException;
        }

        return $array;
    }
}
