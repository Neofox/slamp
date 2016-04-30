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
 * InvalidFormDataException
 *
 * @author Morgan Touverey-Quilling <mtouverey@methodinthemadness.eu>
 */
class InvalidPostTypeException extends SlackException
{
    public $message = 'The method was called via a POST request, but the specified Content-Type was invalid. Valid types are: application/json application/x-www-form-urlencoded multipart/form-data text/plain.';
}