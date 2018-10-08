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

			$this->task = CoreMain::get()->getScheduler()->scheduleRepeatingTask(new GadgetTask($this), 1);
			$this->listener = new GadgetListener($this);
			CoreMain::get()->getServer()->getPluginManager()->registerEvents($this->listener, CoreMain::get());
		}
	}

	public abstract function getPermission(): String;

	public static function getGadgetById($p, $type): Gadget{
		if($p === null){
			return;
		}

//        if (pManager == null) {
//            return;
//        }
//        final int slot = GadgetsMenu.getGadgetsMenuData().getGadgetSlot();
//        if (player.getInventory().getItem(slot) != null) {
//            CategoryManager.removeHotbarCosmetic(player);
//            if (player.getInventory().getItem(slot) != null) {
//                if (CategoryManager.checkEquipRequirement(player, MessageType.REMOVE_ITEM_FROM_SLOT_TO_EQUIP_GADGET.getFormatMessage().replace("{SLOT}", String.valueOf(slot)).replace("{ITEM}", player.getInventory().getItem(slot).getType().name()))) {
//                    return;
//                }
//                if (GadgetsMenu.getGadgetsMenuData().getEquipCosmeticItemToSlotAction() == EnumEquipType.DROP) {
//                    player.getWorld().dropItemNaturally(player.getLocation(), player.getInventory().getItem(slot).clone());
//                    player.getInventory().setItem(slot, (ItemStack)null);
//                    player.updateInventory();
//                }
//            }
//        }
//        player.getInventory().setItem(slot, type.getItemStack());
//        player.updateInventory();
//        type.equip(player);
//        pManager.setSelectedCategoryGadget(GadgetCategoryType.valueOf(type.getGroup()));
//        pManager.setSelectedGadget(type);
	}

	public function clear(){
		if(!is_null($this->task)){
			CoreMain::get()->getScheduler()->cancelTask($this->task->getTaskId());
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