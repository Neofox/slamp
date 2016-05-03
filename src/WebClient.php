<?php
/*
 * This file is part of the Slamp library.
 *
 * (c) Morgan Touverey-Quilling <mtouverey@methodinthemadness.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slamp;

use Amp\Artax\{Client, Request, Response};
use Amp\{Promise, Deferred};
use Slamp\{Exception\SlackException, SlackObject};

/**
 * WebClient
 *
 * @author Morgan Touverey-Quilling <mtouverey@methodinthemadness.eu>
 */
class WebClient
{
    const BASE_URL = 'https://slack.com/api';

    protected $httpClient;
    
    protected $token;


    public function __construct(string $token)
    {
        $this->httpClient = new Client;
        $this->token = $token;
    }

    public function getChannelAsync(string $id) : Promise
    {
        return $this->objectifyAsync(
            false, SlackObject\Channel::class, 'channel',
            $this->callAsync('channels.info', ['channel' => $id])
        );
    }

    public function getUserAsync(string $id) : Promise
    {
        return $this->objectifyAsync(
            false, SlackObject\User::class, 'user',
            $this->callAsync('users.info', ['user' => $id])
        );
    }

    public function callAsync(string $method, array $arguments = []) : Promise
    {
        $promisor = new Deferred;
        
        $this->doHttpRequest($method, $arguments)->when(
            function(\Throwable $err = null, Response $res = null) use($promisor) {
                try {
                    if($err) throw $err;

                    if(!is_array($content = json_decode($res->getBody(), true))) {
                        throw new \InvalidArgumentException('Slack returned unexpected response format - expecting JSON object/array.');
                    } elseif(($content['ok'] ?? false) !== true) {
                        throw SlackException::fromSlackCode($content['error'] ?? 'unknown_error');
                    }

                    $promisor->succeed($content);
                } catch(\Throwable $err) {
                    $promisor->fail($err);
                    return;
                }
            }
        );
        
        return $promisor->promise();
    }

    protected function doHttpRequest(string $method, array $arguments) : Promise
    {
        $arguments['token'] = $this->token;
        
        $request = (new Request)
            ->setUri(static::BASE_URL.'/'.$method)
            ->setMethod('POST')
            ->setAllHeaders(['Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8'])
            ->setBody(http_build_query($arguments));
        
        return $this->httpClient->request($request);
    }

    protected function objectifyAsync(bool $isCollection, string $class, string $property, Promise $futureRawData) : Promise
    {
        /** @var SlackObject $class */

        $promisor = new Deferred;

        $futureRawData->when(
            function(\Throwable $err = null, array $rawData = null) use($isCollection, $class, $property, $promisor) {
                try {
                    if($err) throw $err;

                    if($isCollection) {
                        $objects = [];
                        foreach($rawData[$property] as $rawObject) {
                            $objects[] = $class::fromClientAndArray($this, $rawObject);
                        }

                        $promisor->succeed($objects);
                    } else {
                        $promisor->succeed($class::fromClientAndArray($this, $rawData[$property]));
                    }
                } catch(\Throwable $err) {
                    $promisor->fail($err);
                }
            }
        );

        return $promisor->promise();
    }
}