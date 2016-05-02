<?php
/*
 * This file is part of the Slamp library.
 *
 * (c) Morgan Touverey-Quilling <mtouverey@methodinthemadness.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slamp\Exception;

/**
 * SlackException
 *
 * @author Morgan Touverey-Quilling <mtouverey@methodinthemadness.eu>
 */
class SlackException extends \Exception
{
    const KNOWN_CODES = [
        'invalid_post_type' => InvalidPostTypeException::class
    ];

    protected $slackCode;
    
    
    final public static function fromSlackCode(string $slackCode) : SlackException
    {
        $class = self::KNOWN_CODES[$slackCode] ?? self::class;

        return new $class($slackCode);
    }

    public function __construct(string $slackCode)
    {
        $this->slackCode = $slackCode;
        
        if(self::class == static::class) {
            $this->message = ucfirst(str_replace('_', ' ', $slackCode));
        }
    }
    
    public function getSlackCode() : string
    {
        return $this->slackCode;
    }
}