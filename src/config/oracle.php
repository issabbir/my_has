<?php

return [
    'cpa_security' => [
        'driver'         => 'oracle',
        'tns'            => env('DB_TNS', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=192.168.78.26)(PORT=1999)))
		(CONNECT_DATA=(SERVER=DEDICATED)(SERVICE_NAME=cpadb)))'),
        'host'           => env('DB_HOST', ''),
        'port'           => env('DB_PORT', '1999'),
        'database'       => env('DB_DATABASE', ''),
        'username'       => env('DB_USERNAME', ''),
        'password'       => env('DB_PASSWORD', ''),
        'charset'        => env('DB_CHARSET', 'AL32UTF8'),
        'prefix'         => env('DB_PREFIX', ''),
        'prefix_schema'  => env('DB_SCHEMA_PREFIX', ''),
        'edition'        => env('DB_EDITION', 'ora$base'),
        'server_version' => env('DB_SERVER_VERSION', '11g'),
    ],
    'has' => [
        'driver'         => 'oracle',
        //'tns'            => env('DB_TNS', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=192.168.78.26)(PORT=1999))) (CONNECT_DATA=(SERVER=DEDICATED)(SID=cpadb)))'),
        'tns'            => env('DB_TNS', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=192.168.78.26)(PORT=1999)))
		(CONNECT_DATA=(SERVER=DEDICATED)(SERVICE_NAME=cpadb)))'),
        'host'           => env('DB_HOST', ''),
        'port'           => env('DB_PORT', '1999'),
        'database'       => env('DB_DATABASE', ''),
        'username'       => env('DB_USERNAME', ''),
        'password'       => env('DB_PASSWORD', ''),
        'charset'        => env('DB_CHARSET', 'AL32UTF8'),
        'prefix'         => env('DB_PREFIX', ''),
        'prefix_schema'  => env('DB_SCHEMA_PREFIX', ''),
        'edition'        => env('DB_EDITION', 'ora$base'),
        'server_version' => env('DB_SERVER_VERSION', '11g'),
    ],
    'pmis' => [
        'driver'         => 'oracle',
        'tns'            => env('DB_TNS', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=192.168.78.26)(PORT=1999)))
		(CONNECT_DATA=(SERVER=DEDICATED)(SERVICE_NAME=cpadb)))'),
        'host'           => env('DB_HOST', ''),
        'port'           => env('DB_PORT', '1999'),
        'database'       => env('DB_DATABASE', ''),
        'username'       => env('DB_USERNAME', ''),
        'password'       => env('DB_PASSWORD', ''),
        'charset'        => env('DB_CHARSET', 'AL32UTF8'),
        'prefix'         => env('DB_PREFIX', ''),
        'prefix_schema'  => env('DB_SCHEMA_PREFIX', ''),
        'edition'        => env('DB_EDITION', 'ora$base'),
        'server_version' => env('DB_SERVER_VERSION', '11g'),
    ]
];
