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
    public function getId() : string
    {
        return $this['id'];
    }

    public function getName() : string
    {
        return $this['name'];
    }

    public function getCreatedAt() : \DateTime
    {
        return (new \DateTime())->setTimestamp($this['created']);
    }

    public function getCreatorAsync() : Promise
    {
        return $this->webClient->getUserAsync($this['creator']);
    }

    public function isArchived() : bool
    {
        return $this['is_archived'];
    }

    public function isGeneral() : bool
    {
        return $this['is_general'];
    }

    public function isMember() : bool
    {
        return $this['is_member'];
    }

    public function getLastReadAt() : \DateTime
    {
        return (new \DateTime())->setTimestamp((int) $this['last_read']);
    }

    public function getUnreadCount() : int
    {
        return $this['unread_count'];
    }

    public function getTopic() : string
    {
        return $this['topic']['value'];
    }

    public function getPurpose() : string
    {
        return $this['purpose']['value'];
    }
    
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
}