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

use Slamp\SlackObject;

/**
 * User
 *
 * @author Morgan Touverey-Quilling <mtouverey@methodinthemadness.eu>
 */
class User extends SlackObject
{
    public function getId() : string
    {
        return $this['id'];
    }

    public function getName() : string
    {
        return $this['name'];
    }

    public function isDeleted() : bool
    {
        return $this['deleted'];
    }

    public function getColor() : string
    {
        return $this['color'];
    }

    public function getFirstName()
    {
        return $this['profile']['first_name'] ?? null;
    }

    public function getLastName()
    {
        return $this['profile']['last_name'] ?? null;
    }

    public function getRealName()
    {
        return $this['profile']['real_name'] ?? null;
    }

    public function getEmail()
    {
        return $this['profile']['email'] ?? null;
    }

    public function getSkype()
    {
        return $this['profile']['skype'] ?? null;
    }

    public function getPicture(int $size = 512) : string
    {
        if(!$image = $this['profile']["image_${size}"]) {
            throw new \DomainException("No images have a size of ${size}px. Choose between 24, 32, 48, 72, 192 and 512.");
        }

        return $image;
    }

    public function isAdmin() : bool
    {
        return $this['is_admin'];
    }

    public function isOwner() : bool
    {
        return $this['is_owner'];
    }

    public function isPrimaryOwner() : bool
    {
        return $this['is_primary_owner'];
    }

    public function isRestricted() : bool
    {
        return $this['is_restricted'];
    }

    public function isUltraRestricted() : bool
    {
        return $this['is_ultra_restricted'];
    }

    public function hasTwoFactor() : bool
    {
        return $this['has_2fa'];
    }

    public function getTwoFactorType()
    {
        return $this['has_2fa'] ? $this['two_factor_type'] : null;
    }

    public function hasFiles() : bool
    {
        return $this['has_files'];
    }
}