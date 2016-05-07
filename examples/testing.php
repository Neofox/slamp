<?php

require __DIR__.'/../vendor/autoload.php';

$client = new \Slamp\WebClient(getenv('SLACK_TOKEN'));

Amp\run(function() use($client) {
    $chan = yield $client->channels->infoAsync('C16P4T620');
    var_dump($chan->getName());

    Amp\stop();
});

