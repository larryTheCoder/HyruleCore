<?php
/**
 * BSD 3-Clause License
 *
 * Copyright (c) 2018, Adam Matthew, Hyrule Minigame Division
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * - Neither the name of the copyright holder nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace HyPrimeCore\player;

use pocketmine\entity\Entity;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\level\particle\Particle;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\PlayerSkinPacket;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\Player;
use pocketmine\utils\UUID;

class FakePlayer extends Particle {

    public $entityId = -1;
    public $invisible = false;
    /** @var Skin */
    public $skin;
    /** @var float */
    public $yaw = 0, $pitch = 0;
    /** @var Level */
    public $level;
    /** @var UUID */
    public $uuid;
    /** @var Player */
    private $player;
    private $title;

    /**
     * @param Location $loc
     * @param Player $player
     * @param Level $level
     */
    public function __construct(Location $loc, Player $player, Level $level) {
        parent::__construct($loc->x, $loc->y, $loc->z);
        $this->level = $level;
        $this->player = $player;
        $this->uuid = UUID::fromRandom();
        $this->title = "";
        $this->yaw = $loc->getYaw();
        $this->pitch = $loc->getPitch();
    }

    public function getLocation(): Location {
        return new Location($this->x, $this->y, $this->z, $this->yaw, $this->pitch, $this->level);
    }

    /**
     * Get the entity runtime ID
     *
     * @return int
     */
    public function getRuntimeId() {
        return $this->entityId;
    }

    /**
     * Get the main player for this NPC
     *
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * Set the title for the entity
     * (Need to be spawned by yourself
     *
     * @param $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return DataPacket|DataPacket[]
     */
    public function encode() {
        $p = [];

        if ($this->entityId === -1) {
            $this->entityId = Entity::$entityCount++;
        } else {
            $pk0 = new RemoveEntityPacket();
            $pk0->entityUniqueId = $this->entityId;

            $p[] = $pk0;
        }

        $pk = new AddPlayerPacket();
        $pk->uuid = is_null($this->uuid) ? $this->uuid = UUID::fromRandom() : $this->uuid;
        $pk->username = $this->title;
        $pk->yaw = $this->yaw;
        $pk->pitch = $this->pitch;
        $pk->entityRuntimeId = $this->entityId;
        $pk->position = $this->asVector3(); // TODO: check offset
        $pk->item = ItemFactory::get(Item::AIR, 0, 0);
        $flags = (
            1 << Entity::DATA_FLAG_IMMOBILE
        );
        $pk->metadata = [
            Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
            Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 1],
        ];
        $p[] = $pk;

        $skinPk = new PlayerSkinPacket();
        $skinPk->uuid = $this->uuid;
        $skinPk->skin = $this->player->getSkin();
        $p[] = $skinPk;

        return $p;
    }

    public function getLevel(): Level {
        return $this->level;
    }
}