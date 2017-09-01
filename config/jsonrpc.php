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

    'debug' => env('JSONRPC_DEBUG', false),

    'white_list' => env('JSONRPC_WHITE_LIST', base_path('.whitelist')),

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

            'worker_num' => env('JSONRPC_SERVER_OPTIONS_WORKER_NUM', 1),

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

        'auto_reconnect' => env('JSONRPC_CLIENT_AUTO_RECONNECT', false),

        'timer_tick' => env('JSONRPC_CLIENT_TIMER_TICK', 60000),

        'connections' => [

            'server' => [
                'host' => '127.0.0.1',
                'port' => '1216',
            ],

        ],

    ],
];
