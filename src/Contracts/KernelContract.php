<?php

namespace HuangYi\JsonRpc\Contracts;

use HuangYi\JsonRpc\Foundation\Request;
use HuangYi\JsonRpc\Foundation\Response;

interface KernelContract
{
    /**
     * @param \HuangYi\JsonRpc\Foundation\Request $request
     * @return \HuangYi\JsonRpc\Foundation\Response
     */
    public function handle(Request $request);

    /**
     * @param \HuangYi\JsonRpc\Foundation\Request $request
     * @param \HuangYi\JsonRpc\Foundation\Response $response
     */
    public function terminal(Request $request, Response $response);
}
