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

namespace HyPrimeCore\cosmetics\gadgets;

use HyPrimeCore\CoreMain;
use HyPrimeCore\cosmetics\gadgets\type\GadgetTrampoline;
use HyPrimeCore\tasks\GadgetTask;
use pocketmine\event\HandlerList;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\scheduler\TaskHandler;

abstract class Gadget implements Listener {

	const TRAMPOLINE = 0;

	/** @var bool */
	private $showCooldownBar;
	/** @var int */
	private $type;
	/** @var Player */
	private $player;
	/** @var Listener */
	private $listener;
	/** @var TaskHandler */
	private $task;

	public function __construct(Player $player, int $type){
		$this->showCooldownBar = true;
		$this->type = $type;
		if($player !== null){
			$this->player = $player;
			if(!$player->hasPermission($this->getPermission())){
				return;
			}

			$this->task = CoreMain::get()->getSchedulerForce()->scheduleRepeatingTask(new GadgetTask($this), 1);
			$this->listener = new GadgetListener($this);
			CoreMain::get()->getServer()->getPluginManager()->registerEvents($this->listener, CoreMain::get());
		}
	}

	public abstract function getPermission(): String;

	public static function getGadgetById(Player $p, int $type): Gadget{
		// Get the gadget type
		switch($type){
			case self::TRAMPOLINE:
				$gadget = new GadgetTrampoline($p);
				break;
			default:
				$gadget = null;
				break;
		}
		// Put them in the inventory
		if($p->getInventory()->contains($gadget->getItem())){
			$p->getInventory()->remove($gadget->getItem());
		}
		$p->getInventory()->setHeldItemIndex(0);
		$p->getInventory()->setItemInHand($gadget->getItem());
	}

	public function clear(){
		if(!is_null($this->task)){
			CoreMain::get()->getSchedulerForce()->cancelTask($this->task->getTaskId());
			HandlerList::unregisterAll($this->listener);
		}

		if(is_null($this->player)){
			return;
		}
		$data = CoreMain::get()->getPlayerData($this->player);
		if($data->getGadgetData() != null){
			$data->setCurrentGadget(null);
		}

		$this->player = null;
	}

	public function getType(): int{
		return $this->type;
	}

	public abstract function getItem(): Item;

	public abstract function checkRequirements(): bool;

	public abstract function onClick(): void;

	public abstract function onUpdate(): void;

	public abstract function onClear(): void;

	public function getPlayer(): ?Player{
		return $this->player;
	}

}