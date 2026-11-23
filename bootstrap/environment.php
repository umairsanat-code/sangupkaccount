<?php

$envPath = realpath(dirname(__DIR__ ));
$repository = Dotenv\Repository\RepositoryBuilder::createWithNoAdapters()
    ->addAdapter(Dotenv\Repository\Adapter\EnvConstAdapter::class)
    ->addWriter(Dotenv\Repository\Adapter\PutenvAdapter::class)
    ->immutable()
    ->make();

$env = get_current_user();
if ($env == 'staging') {
    $envFile = ".staging.env";
} elseif ($env == "acceptance") {
    $envFile = ".acceptance.env";
}  elseif ($env == "production") {
    $envFile = ".production.env";
} else {
    $envFile = ".local.env";
}

Dotenv\Dotenv::create($repository,$envPath, $envFile)->load();
