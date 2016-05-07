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
 * Team Channel methods.
 *
 * @author Morgan Touverey-Quilling <mtouverey@methodinthemadness.eu>
 */
class ChannelMethods extends SlackObjectMethods
{    
    public function configure() : array
    {
        return [
            'slackObjectClass' => Channel::class,
            'apiPrefix'        => 'channels',
            'apiName'          => 'channel',
            'apiNamePlural'    => 'channels'
        ];
    }

    /**
     * Archives a channel.
     * @link https://api.slack.com/methods/channels.archive
     *
     * @param string|Channel $channel
     *
     * @return Promise
     */
    public function archiveAsync($channel) : Promise
    {
        return $this->callMethodAsync('archive', $channel);
    }

    /**
     * Creates a channel.
     * @link https://api.slack.com/methods/channels.create
     *
     * @param string $name
     *
     * @return Promise<Channel>
     */
    public function createAsync(string $name) : Promise
    {
        return $this->callMethodWithObjectResultAsync('create', ['name' => $name]);
    }

    /**
     * Returns a portion of message events from the specified channel.
     * Warning: The results could be paged. See the docs for more informations.
     * @link https://api.slack.com/methods/channels.history
     *
     * @param string|Channel $channel
     * @param array          $criterion
     *
     * @return Promise<array>
     */
    public function historyAsync($channel, array $criterion = []) : Promise
    {
        return $this->callMethodAsync('history', $channel, $criterion, function($result) {
            return $result['messages'];
        });
    }

    /**
     * Gets a channel by ID.
     * @link https://api.slack.com/methods/channels.info
     *
     * @param string $id
     *
     * @return Promise<Channel>
     */
    public function infoAsync(string $id) : Promise
    {
        return $this->callMethodWithObjectResultAsync('info', ['channel' => $id]);
    }

    /**
     * Invites a user to a channel. The calling user must be a member of the channel.
     * @link https://api.slack.com/methods/channels.invite
     *
     * @param string|Channel $channel
     * @param string|User    $user
     *
     * @return Promise
     */
    public function inviteAsync($channel, $user) : Promise
    {
        return $this->callMethodAsync('invite', $channel, ['user' => $user]);
    }

    /**
     * Joins a channel (/!\ by name).
     * If the channel does not exist, it is created.
     * @link https://api.slack.com/methods/channels.join
     *
     * @param string $channelName
     *
     * @return Promise
     *
     * @TODO Handle Slack responses that vary whether the channel exists or not.
     */
    public function joinAsync(string $channelName) : Promise
    {
        return $this->callMethodAsync('join', null, ['name' => $channelName]);
    }

    /**
     * Removes a user from a channel.
     * @link https://api.slack.com/methods/channels.kick
     *
     * @param string|Channel $channel
     * @param string|User    $user
     *
     * @return Promise
     */
    public function kickAsync($channel, $user) : Promise
    {
        return $this->callMethodAsync('kick', $channel, ['user' => $user]);
    }

    /**
     * Leaves a channel.
     * @link https://api.slack.com/methods/channels.leave
     *
     * @param string|Channel $channel
     *
     * @return Promise
     */
    public function leaveAsync($channel) : Promise
    {
        return $this->callMethodAsync('leave', $channel);
    }

    /**
     * This method returns a list of all channels in the team.
     * This includes channels the caller is in, channels they are not currently in,
     * and archived channels but does not include private channels.
     * @link https://api.slack.com/methods/channels.list
     *
     * @param array $options
     *
     * @return Promise<Channel[]>
     */
    public function listAsync(array $options = []) : Promise
    {
        return $this->callMethodWithCollectionResultAsync('list', $options);
    }

    /**
     * Moves the read cursor in a channel.
     * @link https://api.slack.com/methods/channels.mark
     *
     * @param string|Channel $channel
     * @param \DateTime      $lastRead
     *
     * @return Promise
     */
    public function markAsync($channel, \DateTime $lastRead) : Promise
    {
        return $this->callMethodAsync('mark', $channel, ['ts' => $lastRead]);
    }

    /**
     * Renames a team channel.
     * The only people who can rename a channel are Team Admins, or the person that originally created the channel.
     * Returns the new name, that could have been slugified by Slack to follow their naming conventions.
     * @link https://api.slack.com/methods/channels.rename
     *
     * @param string|Channel $channel
     * @param string         $name
     *
     * @return Promise<string>
     */
    public function renameAsync($channel, string $name) : Promise
    {
        return $this->callMethodAsync('rename', $channel, ['name' => $name], function($result) {
            return $result['channel']['name'];
        });
    }

    /**
     * Changes the purpose of a channel.
     * The calling user must be a member of the channel.
     * @link https://api.slack.com/methods/channels.setPurpose
     * 
     * @param string|Channel $channel
     * @param string         $purpose
     *
     * @return Promise
     */
    public function setPurposeAsync($channel, string $purpose) : Promise
    {
        return $this->callMethodAsync('setPurpose', $channel, ['purpose' => $purpose]);
    }

    /**
     * Changes the topic of a channel.
     * The calling user must be a member of the channel.
     * @link https://api.slack.com/methods/channels.setTopic
     *
     * @param string|Channel $channel
     * @param string         $topic
     *
     * @return Promise
     */
    public function setTopicAsync($channel, string $topic) : Promise
    {
        return $this->callMethodAsync('setPurpose', $channel, ['topic' => $topic]);
    }

    /**
     * Unarchives a channel.
     * @link https://api.slack.com/methods/channels.unarchive
     * 
     * @param string|Channel $channel
     *
     * @return Promise
     */
    public function unarchiveAsync($channel) : Promise
    {
        return $this->callMethodAsync('unarchive', $channel);
    }
}