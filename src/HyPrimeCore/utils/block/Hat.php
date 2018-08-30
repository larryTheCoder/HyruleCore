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

namespace HyPrimeCore\utils\block;


use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\{
	AddEntityPacket, RemoveEntityPacket
};
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;

class Hat {

	/** @var ArmorStand */
	private $player;

	/** @var Block */
	private $block;

	/** @var int */
	private $hatId;

	/** @var AddEntityPacket */
	private $spawnPacket;

	public function __construct(ArmorStand $player){
		$this->player = $player;
	}

	public function getBlock(): Block{
		return $this->block;
	}

	public function setBlock(Block $block, bool $render = true): void{
		$this->block = $block;
		if($render){
			$this->render();
		}
	}

	public function render(): void{
		if($this->block === null){
			return;
		}

		if($this->spawnPacket === null){
			$pk = new AddEntityPacket();
			$pk->entityRuntimeId = $this->hatId = Entity::$entityCount++;
			$pk->type = Entity::FALLING_BLOCK;
			$pk->position = new Vector3();

			//these are needed for combat - you cannot hit the hat!
			$pk->metadata[Entity::DATA_BOUNDING_BOX_WIDTH] = [Entity::DATA_TYPE_FLOAT, 0];
			$pk->metadata[Entity::DATA_BOUNDING_BOX_HEIGHT] = [Entity::DATA_TYPE_FLOAT, 0];

			$pk->metadata[Entity::DATA_RIDER_SEAT_POSITION] = [Entity::DATA_TYPE_VECTOR3F, new Vector3(0, 0.4, 0)];
			$this->spawnPacket = $pk;
		}

		$this->spawnPacket->metadata[Entity::DATA_VARIANT] = [Entity::DATA_TYPE_INT, $this->block->getId() | ($this->block->getDamage() << 8)];

		if(count($this->spawnPacket->links) === 0){
			$link = new EntityLink();
			$link->fromEntityUniqueId = $this->player->getId();
			$link->toEntityUniqueId = $this->hatId;
			$link->type = 2;
			$link->immediate = true;
			$this->spawnPacket->links[] = $link;
		}
	}

	/**
	 * @param $players Player[]|Player...
	 */
	public function send($players): void{
		if($this->spawnPacket !== null){
			$pk = clone $this->spawnPacket;
			foreach($players as $player){
				$player->dataPacket($pk);
			}
		}
	}

	public function unsend(Player $player): void{
		if($this->spawnPacket !== null){
			$pk = new RemoveEntityPacket();
			$pk->entityUniqueId = $this->hatId;
			$player->dataPacket($pk);
		}
	}
}