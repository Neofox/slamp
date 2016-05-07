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

use Amp\{Deferred, Promise};

/**
 * SlackObjectMethods
 *
 * @author Morgan Touverey-Quilling <mtouverey@methodinthemadness.eu>
 */
abstract class SlackObjectMethods
{
    /** @var string Slack Object FQDN */
    public $slackObjectClass;

    /** @var string API methods prefix */
    public $apiPrefix;

    /** @var string In the API, object types are referred by a lowercase name */
    public $apiName;

    /** @var string Same as $apiName, pluralized (collections) */
    public $apiNamePlural;
    
    /** @var WebClient */
    protected $webClient;


    public function __construct(WebClient $webClient)
    {
        $this->webClient = $webClient;
        
        $conf = $this->configure();

        foreach(['slackObjectClass', 'apiPrefix', 'apiName', 'apiNamePlural'] as $requiredProp) {
            if(!isset($conf[$requiredProp])) {
                throw new \LogicException("Missing ${requiredProp} configuration property");
            } else {
                $this->$requiredProp = $conf[$requiredProp];
            }
        }
    }
    
    abstract public function configure() : array;

    final protected function callMethodAsync(string $method, $slackObject = null, array $arguments = [], callable $transformer = null) : Promise
    {
        $promisor = new Deferred;

        $slackObject && ($arguments[$this->apiName] = $slackObject);
        
        $arguments = $this->serializeSlackArguments($arguments);

        $this->webClient->callAsync($this->apiPrefix.'.'.$method, $arguments)->when(
            function(\Throwable $err = null, array $response = null) use($transformer, $promisor) {
                try {
                    if($err) throw $err;
                    
                    // $transformer() may fail, that's why we use a try block.
                    $promisor->succeed($transformer ? $transformer($response) : null);
                } catch(\Throwable $err) {
                    $promisor->fail($err);
                }
            }
        );

        return $promisor->promise();
    }

    final protected function callMethodWithObjectResultAsync(string $method, array $arguments = []) : Promise
    {
        return $this->apiCallToSlackObjectAsync(
            $this->callMethodAsync($method, null, $arguments, function($d) { return $d; }),
            false, $this->slackObjectClass, $this->apiName
        );
    }

    final protected function callMethodWithCollectionResultAsync(string $method, array $arguments = []) : Promise
    {
        return $this->apiCallToSlackObjectAsync(
            $this->callMethodAsync($method, null, $arguments, function($d) { return $d; }),
            true, $this->slackObjectClass, $this->apiNamePlural
        );
    }
    
    final protected function apiCallToSlackObjectAsync(Promise $futureResponse, bool $isCollection, string $class, string $property) : Promise
    {
        /** @var SlackObject $class */

        $promisor = new Deferred;

        $futureResponse->when(
            function(\Throwable $err = null, array $response = null) use($isCollection, $class, $property, $promisor) {
                try {
                    if($err) throw $err;
                    
                    $data = $property ? $response[$property] : $response;

                    if($isCollection) {
                        $objects = [];
                        foreach($data as $rawObject) {
                            $objects[] = $class::fromClientAndArray($this->webClient, $rawObject);
                        }

                        $promisor->succeed($objects);
                    } else {
                        $promisor->succeed($class::fromClientAndArray($this->webClient, $data));
                    }
                } catch(\Throwable $err) {
                    $promisor->fail($err);
                }
            }
        );

        return $promisor->promise();
    }

    final protected function serializeSlackArguments(array $arguments) : array
    {
        foreach($arguments as &$value) {
            if($value instanceof SlackObject) {
                $value = $value['id'];
            } elseif($value instanceof \DateTime) {
                $value = (string) $value->getTimestamp();
            }
        }
        
        return $arguments;
    }
}