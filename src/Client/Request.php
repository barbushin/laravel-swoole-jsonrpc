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

use Ramsey\Uuid\Uuid;

class Request
{
    /**
     * @var string
     */
    protected $jsonrpc = '2.0';

    /**
     * @var string
     */
    protected $method;

    /**
     * @var mixed
     */
    protected $params;

    /**
     * @var string
     */
    protected $id;

    /**
     * Request constructor.
     *
     * @param string $method
     * @param array|null $params
     * @param mixed $id
     */
    public function __construct($method, array $params = null, $id = null)
    {
        $this->method = $method;
        $this->params = $params;

        $this->setId($id);
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id = null)
    {
        if (is_null($id)) {
            $id = Uuid::uuid4()->toString();
        }

        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        $data = [
            'jsonrpc' => $this->jsonrpc,
            'method' => $this->method,
            'id' => $this->id,
        ];

        if (is_array($this->params)) {
            $data['params'] = $this->params;
        }

        return json_encode($data);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
