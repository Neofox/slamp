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

/**
 * SlackObject
 *
 * @author Morgan Touverey-Quilling <mtouverey@methodinthemadness.eu>
 */
class SlackObject implements \ArrayAccess, \JsonSerializable
{
    /** @var WebClient */
    protected $webClient;

    private $data;


    public static function fromArray(array $data) : SlackObject
    {
        $object = new static;
        $object->data = $data;

        return $object;
    }

    public static function fromClientAndArray(WebClient $webClient, array $data) : SlackObject
    {
        $object = new static;
        $object->webClient = $webClient;
        $object->data = $data;

        return $object;
    }

    public function offsetExists($offset) : bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        if(!isset($this->data[$offset])) {
            throw new \DomainException("Unknown property ${offset}.");
        }

        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new \LogicException('SlackObjects are read-only.');
    }

    public function offsetUnset($offset)
    {
        throw new \LogicException('SlackObjects are read-only.');
    }

    public function jsonSerialize()
    {
        return $this->data;
    }

    public function __toString() : string
    {
        return json_encode($this->data);
    }
}