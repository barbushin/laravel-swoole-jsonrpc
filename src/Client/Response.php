<?php

namespace HuangYi\JsonRpc\Client;

use HuangYi\JsonRpc\Exceptions\InvalidResponseException;
use HuangYi\JsonRpc\Server\Request;

class Response
{
    /**
     * JSON-RPC version.
     *
     * @var string
     */
    protected $jsonrpc;

    /**
     * Result.
     *
     * @var array
     */
    protected $result;

    /**
     * Error.
     *
     * @var array
     */
    protected $error;

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
    protected $requiredMembers = ['jsonrpc', 'id'];

    /**
     * Optional members in JSON-RPC 2.0.
     *
     * @var array
     */
    protected $optionalMembers = ['result', 'error'];

    /**
     * @param string $payload
     * @return static
     */
    public static function make($payload)
    {
        $attributes = Request::parseJson($payload);

        return new static($attributes);
    }

    /**
     * Response constructor.
     * @param array $attributes
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
                throw new InvalidResponseException;
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
     * @param string $key
     * @param mixed $default
     * @return array|mixed
     */
    public function getResult($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->result;
        }

        return array_get($this->result, $key, $default);
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return array|mixed
     */
    public function getError($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->error;
        }

        return array_get($this->error, $key, $default);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return !! $this->getError();
    }
}
