<?php

require __DIR__.'/../vendor/autoload.php';

$client = new \Slamp\WebClient('xoxp-11211955940-11217530880-39195492418-704ba15dea');

Amp\run(function() use($client) {
    $general = yield $client->getChannelAsync('C0B67RUE7');
    $members = yield $general->getMembersAsync();

    var_dump(count($members));
    foreach($members as $member) {
        var_dump($member->getName());
    }
});

