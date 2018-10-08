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

namespace HyPrimeCore\buttonInterface\menu;

use HyPrimeCore\kits\types\NormalKit;
use HyPrimeCore\player\FakePlayer;
use larryTheCoder\SkyWarsPE;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\Player;

class KitMenu extends Menu {

	/** @var NormalKit[] */
	private $types;
	/** @var int */
	private $count = 0;
	/** @var Player */
	private $player;

	public function __construct(Player $p){
		$this->types = SkyWarsPE::getInstance()->kit->getKits();
		$this->player = $p;
	}

	public function getInteractId(): int{
		return self::INTERACT_KIT_MENU;
	}

	/**
	 * Get the next menu for player
	 */
	public function getNextMenu(){
		if($this->count >= count($this->types) - 1){
			$this->count = 0;
		}else{
			$this->count++;
		}
	}

	/**
	 * Get the previous menu for player
	 */
	public function getPrevMenu(){
		if($this->count <= 0){
			$this->count = count($this->types) - 1;
		}else{
			$this->count--;
		}
	}

	/**
	 * Executed when a player select the button
	 */
	public function onPlayerSelect(){
		SkyWarsPE::getInstance()->kit->setPlayerKit($this->player, $this->types[$this->count]);
	}

	/**
	 * Get the data for a menu
	 *
	 * @return array
	 */
	public function getMenuData(): array{
		$data['kit'] = true;
		$pd = SkyWarsPE::getInstance()->getDatabase()->getPlayerData($this->player->getName());
		$data['name'] = $this->types[$this->count]->getKitName();
		if(!in_array(strtolower($this->types[$this->count]->getKitName()), $pd->kitId)){
			$data['payment'] = $this->types[$this->count]->getKitPrice();
		}else{
			$data['payment'] = 0;
		}

		return $data;
	}

	/**
	 * Update the NPC interface with player
	 * Only send the packets or interactions entity to player.
	 * Cleanup is used to clean all the interface between
	 * the player to make sure the next interface are clean.
	 *
	 * @param FakePlayer $player
	 * @param bool $cleanup
	 * @return void
	 */
	public function updateNPC(FakePlayer $player, bool $cleanup){
		if($cleanup){
			$pk = new MobArmorEquipmentPacket();
			$pk->entityRuntimeId = $player->getRuntimeId();
			$pk->slots = $this->getContents(false);
			$pk->encode();
		}else{
			$pk = new MobArmorEquipmentPacket();
			$pk->entityRuntimeId = $player->getRuntimeId();
			$pk->slots = $this->getContents(true);
			$pk->encode();
		}

		$this->player->dataPacket($pk);
	}

	/**
	 * @param bool $includeEmpty
	 *
	 * @return Item[]
	 */
	public function getContents(bool $includeEmpty = false): array{
		$contents = [];
		$air = null;
		$kit = $this->types[$this->count];

		if($includeEmpty){
			foreach($kit->getArmourContents() as $id => $slot){
				if($slot !== null){
					$contents[$id] = clone $slot;
				}elseif($includeEmpty){
					$contents[$id] = $air ?? ($air = ItemFactory::get(Item::AIR, 0, 0));
				}
			}
		}else{
			$contents[0] = $air ?? ($air = ItemFactory::get(Item::AIR, 0, 0));
			$contents[1] = $air ?? ($air = ItemFactory::get(Item::AIR, 0, 0));
			$contents[2] = $air ?? ($air = ItemFactory::get(Item::AIR, 0, 0));
			$contents[3] = $air ?? ($air = ItemFactory::get(Item::AIR, 0, 0));
		}


		return $contents;
	}
}