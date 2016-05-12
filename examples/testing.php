<?php

require __DIR__.'/../vendor/autoload.php';

Amp\run(function() {
    $slack = new Slamp\WebClient(getenv('SLACK_TOKEN'));

    $me = yield $slack->users->getMeAsync();

    var_dump($me->getName());

    Amp\stop();
});

