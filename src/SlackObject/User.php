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

use Amp\Promise;
use Slamp\SlackObject;

/**
 * User
 *
 * @author Morgan Touverey-Quilling <mtouverey@methodinthemadness.eu>
 */
class User extends SlackObject
{
    /**
     * Gets user ID.
     *
     * @return string
     */
    public function getId() : string
    {
        return $this['id'];
    }

    /**
     * Gets user name (username).
     *
     * @return string
     */
    public function getName() : string
    {
        return $this['name'];
    }

    /**
     * Gets whether the user is deleted or not.
     *
     * @return bool
     */
    public function isDeleted() : bool
    {
        return $this['deleted'];
    }

    /**
     * Gets a color that can be used in graphical clients.
     *
     * @return string
     */
    public function getColor() : string
    {
        return $this['color'];
    }

    /**
     * Gets user first name. This value is not necessarily given by the user and may be null.
     *
     * @return string|null
     */
    public function getFirstName()
    {
        return $this['profile']['first_name'] ?? null;
    }

    /**
     * Gets user last name. This value is not necessarily given by the user and may be null.
     *
     * @return string|null
     */
    public function getLastName()
    {
        return $this['profile']['last_name'] ?? null;
    }

    /**
     * Gets user real name. This value is not necessarily given by the user and may be null.
     *
     * @return string|null
     */
    public function getRealName()
    {
        return $this['profile']['real_name'] ?? null;
    }

    /**
     * Gets user email. This value is not necessarily given by the user and may be null.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this['profile']['email'] ?? null;
    }

    /**
     * Gets user Skype username. This value is not necessarily given by the user and may be null.
     *
     * @return string|null
     */
    public function getSkype()
    {
        return $this['profile']['skype'] ?? null;
    }

    /**
     * Gets user picture. If the user didn't uploaded a picture, it'll be a default Slack avatar.
     *
     * @param int $size Picture size, between 24, 32, 48, 72, 192, 512.
     *
     * @return string
     */
    public function getPicture(int $size = 512) : string
    {
        if(!$image = $this['profile']["image_${size}"]) {
            throw new \DomainException("No images have a size of ${size}px. Choose between 24, 32, 48, 72, 192 and 512.");
        }

        return $image;
    }

    /**
     * Gets whether the user is a team admin or not.
     *
     * @return bool
     */
    public function isAdmin() : bool
    {
        return $this['is_admin'];
    }

    /**
     * Gets whether the user is a team owner.
     *
     * @return bool
     */
    public function isOwner() : bool
    {
        return $this['is_owner'];
    }

    /**
     * Gets whether the user is the team's primary owner.
     *
     * @return bool
     */
    public function isPrimaryOwner() : bool
    {
        return $this['is_primary_owner'];
    }

    /**
     * Gets whether the user has restricted permissions.
     *
     * @return bool
     */
    public function isRestricted() : bool
    {
        return $this['is_restricted'];
    }

    /**
     * Gets whether the user has ultra-restricted permissions.
     *
     * @return bool
     */
    public function isUltraRestricted() : bool
    {
        return $this['is_ultra_restricted'];
    }

    /**
     * Gets whether two-step verification is enabled for this user.
     *
     * @return bool
     */
    public function hasTwoFactor() : bool
    {
        return $this['has_2fa'];
    }

    /**
     * Gets the two-step verification type ("app", "sms"), if enabled.
     * Returns null if two-step verification is not enabled at all for this user.
     *
     * @return string|null
     */
    public function getTwoFactorType()
    {
        return $this['has_2fa'] ? $this['two_factor_type'] : null;
    }

    /**
     * @return bool
     */
    public function hasFiles() : bool
    {
        return $this['has_files'];
    }

    /**
     * @see UserMethods::getPresenceAsync()
     *
     * @return Promise<string>
     */
    public function getPresenceAsync() : Promise
    {
        return $this->webClient->users->getPresenceAsync($this);
    }
}