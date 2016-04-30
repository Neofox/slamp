<?php

require __DIR__.'/../vendor/autoload.php';

$client = new \Slamp\WebClient('xoxp-11211955940-11217530880-39195492418-704ba15dea');

Amp\run(function() use($client) {
    $res = yield $client->call('auth.test');

    var_dump($res);
});

