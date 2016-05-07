<?php
/*
 * This file is part of the Slamp library.
 *
 * (c) Morgan Touverey-Quilling <mtouverey@methodinthemadness.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slamp\SlackObject;

use Slamp\SlackObjectMethods;

/**
 * UserMethods
 *
 * @author Morgan Touverey-Quilling <mtouverey@methodinthemadness.eu>
 */
class UserMethods extends SlackObjectMethods
{
    public function configure() : array
    {
        return [
            'slackObjectClass' => User::class,
            'apiPrefix'        => 'users',
            'apiName'          => 'user',
            'apiNamePlural'    => 'users'
        ];
    }
}