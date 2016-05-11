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

use Amp\{Deferred, Promise, function all};
use Slamp\SlackObject;

/**
 * Channel
 *
 * @author Morgan Touverey-Quilling <mtouverey@methodinthemadness.eu>
 */
class Channel extends SlackObject
{
    /**
     * Gets channel ID.
     *
     * @return string
     */
    public function getId() : string
    {
        return $this['id'];
    }

    /**
     * Gets channel name.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this['name'];
    }

    /**
     * Gets the channel's creation date.
     *
     * @return \DateTime
     */
    public function getCreatedAt() : \DateTime
    {
        return (new \DateTime())->setTimestamp($this['created']);
    }

    /**
     * Gets the user that created the channel.
     *
     * @return Promise<User>
     */
    public function getCreatorAsync() : Promise
    {
        return $this->webClient->getUserAsync($this['creator']);
    }

    /**
     * Gets whether the channel is archived or not.
     *
     * @return bool
     */
    public function isArchived() : bool
    {
        return $this['is_archived'];
    }

    /**
     * Gets whether the channel is the "general" channel or not.
     * Some teams may have changed the default name "general", so you should use this instead of a string match.
     *
     * @return bool
     */
    public function isGeneral() : bool
    {
        return $this['is_general'];
    }

    /**
     * Gets whether the connected user is member of that channel
     *
     * @return bool
     */
    public function isMember() : bool
    {
        return $this['is_member'];
    }

    /**
     * Gets the last time the connected user marked the channel.
     *
     * @return \DateTime
     */
    public function getLastReadAt() : \DateTime
    {
        return (new \DateTime())->setTimestamp((int) $this['last_read']);
    }

    /**
     * Gets the number of unread messages the connected user has (posted after the last mark).
     *
     * @return int
     */
    public function getUnreadCount() : int
    {
        return $this['unread_count'];
    }

    /**
     * Gets the channel topic.
     *
     * @return string
     */
    public function getTopic() : string
    {
        return $this['topic']['value'];
    }

    /**
     * Gets the channel purpose.
     *
     * @return string
     */
    public function getPurpose() : string
    {
        return $this['purpose']['value'];
    }

    /**
     * Gets the number of members in the channel.
     *
     * @return int
     */
    public function getMembersCount() : int
    {
        return count($this['members']);
    }

    /**
     * Gets all the channel members, in User objects.
     * As this may require a lot of queries (one per member), requests are not all started
     * at the same time and there is a concurrency of 5 requests max. at the same time.
     *
     * @TODO Improve the algorithm, I was very tired when I wrote it.
     *
     * @return Promise<Channel[]>
     */
    public function getMembersAsync() : Promise
    {
        $promisor = new Deferred;

        $members = [];
        $chunksFunctions = [];
        # explode all members IDs into blocks of five IDs
        $chunks = array_chunk($this['members'], 5);

        foreach($chunks as $chunk) {
            # for each of that blocks, make a function
            $chunksFunctions[] = function() use($promisor, &$members, $chunk, &$chunksFunctions) {
                $promises = [];
                foreach($chunk as $userId) {
                    # fetch all users in the blocks
                    $promises[] = $this->webClient->getUserAsync($userId);
                }

                # when all the five users have been loaded...
                all($promises)->when(
                    function(\Throwable $err = null, array $fiveUsers = null) use($promisor, &$members, &$chunksFunctions) {
                        if($err) {
                            $promisor->fail($err);
                            return;
                        }

                        $members = array_merge($members, $fiveUsers);

                        # Continue with the next chunk or return async-ily?
                        array_shift($chunksFunctions);
                        switch(count($chunksFunctions) > 0) {
                            case true: $chunksFunctions[0](); break;  # fetch next chunk
                            case false: $promisor->succeed($members); # make master promise succeed
                        }
                    }
                );
            };
        }

        $chunksFunctions[0]();

        return $promisor->promise();
    }

    /**
     * @see ChannelMethods::historyAsync()
     * 
     * @param array $criterion
     *
     * @return Promise<array>
     */
    public function getHistoryAsync(array $criterion = []) : Promise
    {
        return $this->webClient->channels->historyAsync($this, $criterion);
    }

    /**
     * @see ChannelMethods::archiveAsync()
     * 
     * @return Promise
     */
    public function archiveAsync() : Promise
    {
        return $this->webClient->channels->archiveAsync($this);
    }

    /**
     * @see ChannelMethods::unarchiveAsync()
     * 
     * @return Promise
     */
    public function unarchiveAsync() : Promise
    {
        return $this->webClient->channels->unarchiveAsync($this);
    }

    /**
     * @see ChannelMethods::inviteAsync()
     * 
     * @param string|User $user
     *
     * @return Promise
     */
    public function inviteAsync($user) : Promise
    {
        return $this->webClient->channels->inviteAsync($this, $user);
    }

    /**
     * @see ChannelMethods::joinAsync()
     *
     * @return Promise
     */
    public function joinAsync() : Promise
    {
        return $this->webClient->channels->joinAsync($this->getName());
    }

    /**
     * @see ChannelMethods::leaveAsync()
     *
     * @return Promise
     */
    public function leaveAsync() : Promise
    {
        return $this->webClient->channels->leaveAsync($this);
    }

    /**
     * @see ChannelMethods::kickAsync()
     * 
     * @param string|User $user
     *
     * @return Promise
     */
    public function kickAsync($user) : Promise
    {
        return $this->webClient->channels->kickAsync($this, $user);
    }

    /**
     * @see ChannelMethods::markAsync()
     * 
     * @param \DateTime $lastRead
     *
     * @return Promise
     */
    public function markAsync(\DateTime $lastRead = null) : Promise
    {
        return $this->webClient->channels->markAsync($this, $lastRead);
    }

    /**
     * @see ChannelMethods::renameAsync()
     * 
     * @param string $name
     *
     * @return Promise<string>
     */
    public function renameAsync(string $name) : Promise
    {
        return $this->webClient->channels->renameAsync($this, $name);
    }

    /**
     * @see ChannelMethods::setPurposeAsync()
     * 
     * @param string $purpose
     *
     * @return Promise
     */
    public function setPurposeAsync(string $purpose) : Promise
    {
        return $this->webClient->channels->setPurposeAsync($this, $purpose);
    }

    /**
     * @see ChannelMethods::setTopicAsync()
     * 
     * @param string $topic
     *
     * @return Promise
     */
    public function setTopicAsync(string $topic) : Promise
    {
        return $this->webClient->channels->setTopicAsync($this, $topic);
    }
}