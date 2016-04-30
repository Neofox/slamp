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
 * JsonPayload
 *
 * @author Morgan Touverey-Quilling <mtouverey@methodinthemadness.eu>
 */
class JsonPayload implements \JsonSerializable
{
    public $data;


    public static function fromJson(string $json) : self
    {
        $data = json_decode($json, true);

        if(!is_array($data)) {
            throw new \UnexpectedValueException('Invalid JSON provided (should be valid JSON, and not a scalar representation).');
        }

        return new self($data);
    }

    public function __construct(array $data)
    {
        $this->data = $data;
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