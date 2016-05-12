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
            'apiNamePlural'    => 'members'
        ];
    }

    /**
     * Gets informations about a user's presence.
     * @link https://api.slack.com/methods/users.getPresence
     *
     * @param string|User $user
     *
     * @return Promise<string>
     */
    public function getPresenceAsync($user) : Promise
    {
        return $this->callMethodAsync('getPresence', $user, [], function(array $result) {
            return $result['presence'];
        });
    }

    /**
     * After your Slack app is awarded an identity token through "Sign in with Slack",
     * use this method to retrieve a user's identity.
     * The returned fields depend on any additional authorization scopes you've requested.
     * @link https://api.slack.com/methods/users.identity
     *
     * @return Promise
     *
     * @TODO Implement this.
     */
    public function identityAsync() : Promise
    {
        throw new \Exception('Not implemented yet.');
    }

    /**
     * Gets an user by ID.
     * @link https://api.slack.com/methods/users.info
     *
     * @param string $id
     *
     * @return Promise<User>
     */
    public function infoAsync(string $id) : Promise
    {
        return $this->callMethodWithObjectResultAsync('info', ['user' => $id]);
    }

    /**
     * This method returns a list of all users in the team. This includes deleted/deactivated users.
     * @link https://api.slack.com/methods/users.list
     *
     * @param array $options
     *
     * @return Promise<User[]>
     */
    public function listAsync(array $options = []) : Promise
    {
        return $this->callMethodWithCollectionResultAsync('list', $options);
    }

    /**
     * Lets the Slack messaging server know that the authenticated user is currently active.
     * @link https://api.slack.com/methods/users.setActive
     *
     * @return Promise
     */
    public function setActiveAsync() : Promise
    {
        return $this->callMethodAsync('setActive');
    }

    /**
     * Lets you set the calling user's manual presence.
     * @link https://api.slack.com/methods/users.setPresence
     *
     * @param string $presence
     *
     * @return Promise
     */
    public function setPresenceAsync(string $presence) : Promise
    {
        return $this->callMethodAsync('setPresence', null, ['presence' => $presence]);
    }
}