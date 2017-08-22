<?php

/*
 * This file is part of the huang-yi/laravel-swoole-http package.
 *
 * (c) Huang Yi <coodeer@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | JSON-RPC server configurations.
    |--------------------------------------------------------------------------
    |
    | @see https://wiki.swoole.com/wiki/page/274.html
    |
    */

    'server' => [

        'host' => env('JSONRPC_SERVER_HOST', '127.0.0.1'),

        'port' => env('JSONRPC_SERVER_PORT', '1216'),

        'options' => [

            'pid_file' => env('JSONRPC_SERVER_OPTIONS_PID_FILE', storage_path('logs/jsonrpc.pid')),

            'log_file' => env('JSONRPC_SERVER_OPTIONS_LOG_FILE', storage_path('logs/jsonrpc.log')),

            'daemonize' => env('JSONRPC_SERVER_OPTIONS_DAEMONIZE', 1),

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | JSON-RPC client configurations.
    |--------------------------------------------------------------------------
    |
    | @see https://wiki.swoole.com/wiki/page/274.html
    |
    */

    'client' => [

        'default' => 'server',

        'connections' => [

            'server' => [
                'host' => '127.0.0.1',
                'port' => '1216',
            ],

        ],

    ],
];
