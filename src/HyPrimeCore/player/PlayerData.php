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


use HyPrimeCore\CoreMain;
use HyPrimeCore\cosmetics\cloaks\ParticleCloak;
use HyPrimeCore\cosmetics\gadgets\Gadget;
use pocketmine\Player;

class PlayerData {

	/** @var ParticleCloak */
	private $cloakData = null;
	/** @var Gadget */
	private $gadget = null;
	/** @var int[] */
	private $cooldown = [];
	/**
	 * @return ParticleCloak
	 */
	public function getCloakData(){
		return $this->cloakData;
	}

	/**
	 * @param ParticleCloak $cloakData
	 */
	public function setCurrentCloak(?ParticleCloak $cloakData): void{
		$this->removeCloak();
		$this->cloakData = $cloakData;
	}

	public function removeCloak(){
		if(!is_null($this->cloakData)){
			$this->cloakData->clear();
		}
		$this->cloakData = null;
	}

	public function getGadgetData(){
		return $this->gadget;
	}

	public function removeGadget(){
		if(!is_null($this->gadget)){
			$this->gadget->clear();
		}
		$this->gadget = null;
	}

	/**
	 * @param Gadget $cloakData
	 */
	public function setCurrentGadget(?Gadget $cloakData): void{
		$this->removeGadget();
		$this->cloakData = $cloakData;
	}

	public function getCooldown(){
		return $this->cooldown;
	}

	public function unsetCooldownData(int $data){
		unset($this->cooldown[$data]);
	}

	public function addCooldownData(int $data, int $fromTo){
		$this->cooldown[$data] = $fromTo;
	}

	public function save(Player $p){
		CoreMain::get()->savePlayerData($p, $this);
	}
}