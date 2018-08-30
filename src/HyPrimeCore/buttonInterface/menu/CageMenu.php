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

namespace HyPrimeCore\buttonInterface\menu;

use HyPrimeCore\player\FakePlayer;
use larryTheCoder\cages\Cage;
use larryTheCoder\SkyWarsPE;
use pocketmine\Player;

class CageMenu extends Menu {

	/** @var Cage[] */
	private $types;
	/** @var int */
	private $count = 0;
	/** @var Player */
	private $player;

	public function __construct(Player $p){
		foreach(SkyWarsPE::getInstance()->cage->getCages() as $cage){
			$this->types[] = $cage;
		}
		$this->player = $p;
	}

	public function getInteractId(): int{
		return Menu::INTERACT_CAGES_MENU;
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
		SkyWarsPE::getInstance()->cage->setPlayerCage($this->player, $this->types[$this->count]);
	}

	/**
	 * Get the data for a menu
	 *
	 * @return array
	 */
	public function getMenuData(): array{
		var_dump($this->count);
		$data['cage'] = true;
		$data['name'] = $this->types[$this->count]->getCageName();
		$pd = SkyWarsPE::getInstance()->getDatabase()->getPlayerData($this->player->getName());
		if(!in_array(strtolower($this->types[$this->count]->getCageName()), $pd->cages)){
			$data['payment'] = $this->types[$this->count]->getPrice();
		}else{
			$data['payment'] = 0;
		}

		return $data;
	}

	/**
	 * Update the NPC interface with player
	 *
	 * @param FakePlayer $player
	 * @param bool $cleanup
	 * @return void
	 */
	public function updateNPC(FakePlayer $player, bool $cleanup){
		// TODO: Implement updateNPC() method.
	}
}