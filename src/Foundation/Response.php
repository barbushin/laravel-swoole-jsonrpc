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

class Response
{
    /**
     * @var string
     */
    protected $jsonrpc = '2.0';

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @var array
     */
    protected $error;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * Codes translation table.
     *
     * The list of codes is complete according to the
     * {@link http://www.jsonrpc.org/specification#error_object JSON-RPC 2.0 Specification for error object}
     * (last updated 2013-01-04).
     *
     * @var array
     */
    public static $errorCodes = [
        -32700 => 'Parse error',
        -32600 => 'Invalid Request',
        -32601 => 'Method not found',
        -32602 => 'Invalid params',
        -32603 => 'Internal error',
        -32000 => 'Server error',
    ];

    /**
     * @return string
     */
    public function getJsonrpc()
    {
        return $this->jsonrpc;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param int $code
     * @param string $message
     * @param mixed $data
     * @return $this
     */
    public function setError($code, $message = null, $data = null)
    {
        $this->error = [];

        if (is_null($message) && array_key_exists($code, self::$errorCodes)) {
            $message = self::$errorCodes[$code];
        }

        $this->error = [
            'code' => (int) $code,
            'message' => (string) $message,
        ];

        if (! is_null($data)) {
            $this->error['data'] = $data;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->toJson();
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return is_array($this->getError());
    }

    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [
            'jsonrpc' => $this->getJsonrpc(),
            'id' => $this->getId(),
        ];

        if ($this->hasError()) {
            $array['error'] = $this->getError();
        } else {
            $array['result'] = $this->getResult();
        }

        return $array;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @param \HuangYi\JsonRpc\Foundation\Request $request
     * @return $this
     */
    public function prepare(Request $request)
    {
        $this->setId($request->getId());

        return $this;
    }
}
