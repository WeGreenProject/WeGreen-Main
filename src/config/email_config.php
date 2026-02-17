<?php

return [

    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls',
        'auth' => true,
        'username' => 'jmssgames@gmail.com',
        'password' => 'cgme tjee hnjv trtl',
    ],


    'from' => [
        'email' => 'jmssgames@gmail.com',
        'name' => 'WeGreen Marketplace',
    ],


    'admin' => [
        'email' => 'admin@wegreen.pt',
        'notify_new_orders' => true,
    ],


    'options' => [
        'charset' => 'UTF-8',
        'timeout' => 10,
        'debug' => 0,
        'enable_logging' => true,
    ],


    'limits' => [
        'daily_limit' => 300,
        'retry_attempts' => 3,
        'retry_delay' => 5,
        'enable_queue' => false,
    ],


    'templates' => [
        'base_path' => __DIR__ . '/../views/email_templates/',
        'cache_enabled' => false,
    ],


    'base_url' => 'http://localhost/WeGreen-Main',
];
