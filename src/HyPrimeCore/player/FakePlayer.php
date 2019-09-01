<?php
/**
 * BSD 3-Clause License
 *
 * Copyright (c) 2017-2018, larryTheCoder, Hyrule Minigame Division
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
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\MoveEntityAbsolutePacket;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\UUID;

/**
 * This is an NPC class that used by Particles
 * Which is a hack to force the server to NOT
 * SAVE THE ENTITY to disk
 *
 * @package larryTheCoder\npc
 */
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

	/**
	 * @param Vector3 $pos
	 * @param Player $p
	 * @param Level $level
	 */
	public function __construct(Vector3 $pos, Player $p, Level $level){
		parent::__construct($pos->x, $pos->y, $pos->z);
		$this->level = $level;
		$this->uuid = UUID::fromRandom();
		$this->skin = $p->getSkin();
		$this->player = $p;
	}

	/**
	 * Changes the entity's yaw and pitch to make it look at the specified Vector3 position. For mobs, this will cause
	 * their heads to turn.
	 */
	public function lookAt(): void{
		$horizontal = sqrt(($this->player->x - $this->x) ** 2 + ($this->player->z - $this->z) ** 2);
		$vertical = ($this->player->y - $this->y) + 0.5; // 0.6 is the player offset.
		$this->pitch = -atan2($vertical, $horizontal) / M_PI * 180; //negative is up, positive is down

		$xDist = $this->player->x - $this->x;
		$zDist = $this->player->z - $this->z;
		$this->yaw = atan2($zDist, $xDist) / M_PI * 180 - 90;
		if($this->yaw < 0){
			$this->yaw += 360.0;
		}
		$this->updateMovement();
	}

	public function updateMovement(){
		$pk = new MoveEntityAbsolutePacket();

		$pk->entityRuntimeId = $this->entityId;
		$pk->position = $this->asVector3()->add(0, 1.6);

		$pk->xRot = $this->pitch;
		$pk->yRot = $this->yaw; //TODO: head yaw
		$pk->zRot = $this->yaw;

		$this->player->dataPacket($pk);
	}

	/**
	 * @return DataPacket|DataPacket[]
	 */
	public function encode(){
		$p = [];

		if($this->entityId === -1){
			$this->entityId = Entity::$entityCount++;
		}else{
			$pk0 = new RemoveEntityPacket();
			$pk0->entityUniqueId = $this->entityId;

			$p[] = $pk0;
		}

		$name = "";

		$add = new PlayerListPacket();
		$add->type = PlayerListPacket::TYPE_ADD;
		$add->entries = [PlayerListEntry::createAdditionEntry($this->uuid, $this->entityId, $name, $this->skin)];
		$p[] = $add;

		$pk = new AddPlayerPacket();
		$pk->uuid = $this->uuid;
		$pk->username = $name;
		$pk->entityRuntimeId = $this->entityId;
		$pk->position = $this->asVector3(); // TODO: check offset
		$pk->item = ItemFactory::get(Item::AIR, 0, 0);

		$flags = (
			1 << Entity::DATA_FLAG_IMMOBILE
		);
		$pk->metadata = [
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
			Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 0.85],
		];

		$p[] = $pk;

		$remove = new PlayerListPacket();
		$remove->type = PlayerListPacket::TYPE_REMOVE;
		$remove->entries = [PlayerListEntry::createRemovalEntry($this->uuid)];
		$p[] = $remove;

		return $p;
	}

	public function remove(){
		$pk0 = new RemoveEntityPacket();
		$pk0->entityUniqueId = $this->entityId;

		Server::getInstance()->broadcastPacket(Server::getInstance()->getOnlinePlayers(), $pk0);
	}

	public function getPlayer(): Player{
		return $this->player;
	}

	public function getLevel(): Level{
		return $this->level;
	}

	public function getLocation(): Location{
		return new Location($this->x, $this->y, $this->z, $this->yaw, $this->pitch, $this->level);
	}
}