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

namespace HyPrimeCore\cosmetics\gadgets;


use HyPrimeCore\CoreMain;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class GadgetListener implements Listener {

	private $gadget;

	public function __construct(Gadget $gadget){
		$this->gadget = $gadget;
	}

	public function onPlayerActivateGadget(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		if($event->getAction() != PlayerInteractEvent::RIGHT_CLICK_AIR && $event->getAction() != PlayerInteractEvent::RIGHT_CLICK_BLOCK){
			return;
		}
		if(!$player->getInventory()->getItemInHand()->hasCustomName()){
			return;
		}
		if(!$player->getInventory()->getItemInHand()->getCustomName() === $this->gadget->getItem()->getCustomName() ||
			$player->getInventory()->getItemInHand()->getId() != $this->gadget->getItem()->getId()){
			return;
		}
		$data = CoreMain::get()->getPlayerData($player);
		if(isset($data->getCooldown()[$this->gadget->getType()]) && !$player->isOp()){
			if($data->getCooldown()[$this->gadget->getType()] - microtime() > 0){
				$player->sendMessage("Cooldown dude");
				$event->setCancelled();

				return;
			}
			$data->unsetCooldownData($this->gadget->getType());
		}

		if(!$player->hasPermission($this->gadget->getPermission()) && !$player->isOp()){
			$player->sendMessage("You don't have permission to do this");
			$data->save($player);

			return;
		}

		if($this->gadget->checkRequirements()){
			$this->gadget->onClick();
			if(!$player->isOp()){
				$data->addCooldownData($this->gadget->getType(), microtime() + (5 * 1000)); // 5 Seconds
				$data->save($player);
			}
		}

		$event->setCancelled();
	}
}