<?php

namespace HuangYi\JsonRpc\Server;

use HuangYi\JsonRpc\Exceptions\InternalErrorException;
use HuangYi\JsonRpc\Exceptions\InvalidParamsException;
use Illuminate\Contracts\Support\Arrayable;

class ResponseFactory
{
    /**
     * Return an item.
     *
     * @param mixed $item
     * @param Transformable|null $transformer
     * @return \HuangYi\JsonRpc\Server\Response
     */
    public function item($item, Transformable $transformer = null)
    {
        if (! is_null($transformer)) {
            $item = $transformer->transform($item);
        }

        if ($item instanceof Arrayable) {
            $item = $item->toArray();
        }

        return (new Response)->setResult($item);
    }

    /**
     * Return a collection.
     *
     * @param array|\Illuminate\Support\Collection $collection
     * @param Transformable|null $transformer
     * @return \HuangYi\JsonRpc\Server\Response
     */
    public function collection($collection, Transformable $transformer = null)
    {
        if (! is_null($transformer)) {
            foreach ($collection as &$item) {
                $item = $transformer->transform($item);
            }
        }

        if ($collection instanceof Arrayable) {
            $collection = $collection->toArray();
        }

        return (new Response)->setResult($collection);
    }

    /**
     * Return a paginator.
     *
     * @param array|\Illuminate\Support\Collection $collection
     * @param int $total
     * @param \HuangYi\JsonRpc\Server\Transformable|null $transformer
     * @return \HuangYi\JsonRpc\Server\Response
     */
    public function paginator($collection, $total, Transformable $transformer = null)
    {
        if (! is_null($transformer)) {
            foreach ($collection as &$item) {
                $item = $transformer->transform($item);
            }
        }

        if ($collection instanceof Arrayable) {
            $collection = $collection->toArray();
        }

        return (new Response)->setResult(['items' => $collection, 'total' => $total]);
    }

    /**
     * Return an invalid params error.
     *
     * @param array|null $errors
     */
    public function invalidParams(array $errors = null)
    {
        throw new InvalidParamsException("Invalid params", -32602, $errors);
    }

    /**
     * Return an internal error.
     *
     * @param array|null $errors
     */
    public function internalError(array $errors = null)
    {
        throw new InternalErrorException("Internal error", -32603, $errors);
    }
}
