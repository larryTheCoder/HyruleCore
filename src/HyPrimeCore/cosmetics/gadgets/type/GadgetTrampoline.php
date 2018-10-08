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

namespace HyPrimeCore\cosmetics\gadgets\type;

use HyPrimeCore\CoreMain;
use HyPrimeCore\cosmetics\gadgets\Gadget;
use HyPrimeCore\utils\Cuboid;
use HyPrimeCore\utils\Utils;
use pocketmine\block\Block;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\HandlerList;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class GadgetTrampoline extends Gadget {

	public $activated = false;
	/** @var Position */
	public $location;
	/** @var Cuboid */
	public $cuboid;
	/** @var Block[] */
	private $blocks;

	public function __construct(Player $player){
		parent::__construct($player, self::TRAMPOLINE);
		$this->activated = false;
		$this->blocks = []; // spl_object_hash
		Server::getInstance()->getPluginManager()->registerEvents($this, CoreMain::get());
	}

	public function getPermission(): String{
		return "core.gadget.trampoline";
	}

	public function getItem(): Item{
		return Item::get(154, 0);
	}

	public function checkRequirements(): bool{
		if($this->activated){
			$this->getPlayer()->sendMessage("§cGadget already used");

			return false;
		}
		if(!$this->getPlayer()->isOnGround()){
			$this->getPlayer()->sendMessage("§cYou must be on the ground to use this!");

			return false;
		}
		$level = $this->getPlayer()->getLevel();
		$loc1 = $this->getPlayer()->add(-3, 0, -3);
		$loc2 = $this->getPlayer()->add(3, 20, 3);

		$stair = $this->getPlayer()->getLevel()->getBlock($this->getPlayer()->add(-4, 1, 0));
		$stair2 = $this->getPlayer()->getLevel()->getBlock($this->getPlayer()->add(-5, 0, 0));

		$this->cuboid = new Cuboid(Position::fromObject($loc1, $level), Position::fromObject($loc2, $level));
		if(!$this->cuboid->isEmpty() || $stair->getId() !== 0 || $stair2->getId() !== 0){
			$this->getPlayer()->sendMessage("§cNot enough space around you or above you to use this gadget!");

			return false;
		}

		return true;
	}

	public function onClick(): void{
		$this->location = $this->getPlayer()->getPosition();
		$this->getPlayer()->teleport($this->getPlayer()->add(0.0, 5.0, 0.0));
		$this->generateTrampoline();
		CoreMain::get()->getSchedulerForce()->scheduleDelayedTask(new class($this) extends Task {
			/** @var GadgetTrampoline */
			private $gadget;

			public function __construct(GadgetTrampoline $gadget){
				$this->gadget = $gadget;
			}

			public function onRun(int $tick){
				$data = CoreMain::get()->getPlayerData($this->gadget->getPlayer());
				if(!$this->gadget->getPlayer()->isOnline()
					|| $data->getGadgetData() == null
					|| $data->getGadgetData()->getType() != Gadget::TRAMPOLINE
					|| !$this->gadget->activated){
					return;
				}
				$this->gadget->clearAll();
			}
		}, 300);
		$this->activated = true;
	}

	public function generateTrampoline(): void{
		$this->setBlock($this->getLocation(3, 0, 3), Item::FENCE, 0);
		$this->setBlock($this->getLocation(-3, 0, 3), Item::FENCE, 0);
		$this->setBlock($this->getLocation(3, 0, -3), Item::FENCE, 0);
		$this->setBlock($this->getLocation(-3, 0, -3), Item::FENCE, 0);
		$this->setBlock($this->getLocation(3, 1, 3), Item::WOOL, 11);
		$this->setBlock($this->getLocation(2, 1, 3), Item::WOOL, 11);
		$this->setBlock($this->getLocation(1, 1, 3), Item::WOOL, 11);
		$this->setBlock($this->getLocation(0, 1, 3), Item::WOOL, 11);
		$this->setBlock($this->getLocation(-1, 1, 3), Item::WOOL, 11);
		$this->setBlock($this->getLocation(-2, 1, 3), Item::WOOL, 11);
		$this->setBlock($this->getLocation(-3, 1, 3), Item::WOOL, 11);
		$this->setBlock($this->getLocation(3, 1, 2), Item::WOOL, 11);
		$this->setBlock($this->getLocation(3, 1, 1), Item::WOOL, 11);
		$this->setBlock($this->getLocation(3, 1, 0), Item::WOOL, 11);
		$this->setBlock($this->getLocation(3, 1, -1), Item::WOOL, 11);
		$this->setBlock($this->getLocation(3, 1, -2), Item::WOOL, 11);
		$this->setBlock($this->getLocation(-3, 1, 2), Item::WOOL, 11);
		$this->setBlock($this->getLocation(-3, 1, 1), Item::WOOL, 11);
		$this->setBlock($this->getLocation(-3, 1, 0), Item::WOOL, 11);
		$this->setBlock($this->getLocation(-3, 1, -1), Item::WOOL, 11);
		$this->setBlock($this->getLocation(-3, 1, -2), Item::WOOL, 11);
		$this->setBlock($this->getLocation(3, 1, -3), Item::WOOL, 11);
		$this->setBlock($this->getLocation(2, 1, -3), Item::WOOL, 11);
		$this->setBlock($this->getLocation(1, 1, -3), Item::WOOL, 11);
		$this->setBlock($this->getLocation(0, 1, -3), Item::WOOL, 11);
		$this->setBlock($this->getLocation(-1, 1, -3), Item::WOOL, 11);
		$this->setBlock($this->getLocation(-2, 1, -3), Item::WOOL, 11);
		$this->setBlock($this->getLocation(-3, 1, -3), Item::WOOL, 11);
		$this->setBlock($this->getLocation(2, 1, 2), Item::WOOL, 15);
		$this->setBlock($this->getLocation(1, 1, 2), Item::WOOL, 15);
		$this->setBlock($this->getLocation(0, 1, 2), Item::WOOL, 15);
		$this->setBlock($this->getLocation(-1, 1, 2), Item::WOOL, 15);
		$this->setBlock($this->getLocation(-2, 1, 2), Item::WOOL, 15);
		$this->setBlock($this->getLocation(2, 1, 1), Item::WOOL, 15);
		$this->setBlock($this->getLocation(1, 1, 1), Item::WOOL, 15);
		$this->setBlock($this->getLocation(0, 1, 1), Item::WOOL, 15);
		$this->setBlock($this->getLocation(-1, 1, 1), Item::WOOL, 15);
		$this->setBlock($this->getLocation(-2, 1, 1), Item::WOOL, 15);
		$this->setBlock($this->getLocation(2, 1, 0), Item::WOOL, 15);
		$this->setBlock($this->getLocation(1, 1, 0), Item::WOOL, 15);
		$this->setBlock($this->getLocation(0, 1, 0), Item::WOOL, 15);
		$this->setBlock($this->getLocation(-1, 1, 0), Item::WOOL, 15);
		$this->setBlock($this->getLocation(-2, 1, 0), Item::WOOL, 15);
		$this->setBlock($this->getLocation(2, 1, -1), Item::WOOL, 15);
		$this->setBlock($this->getLocation(1, 1, -1), Item::WOOL, 15);
		$this->setBlock($this->getLocation(0, 1, -1), Item::WOOL, 15);
		$this->setBlock($this->getLocation(-1, 1, -1), Item::WOOL, 15);
		$this->setBlock($this->getLocation(-2, 1, -1), Item::WOOL, 15);
		$this->setBlock($this->getLocation(2, 1, -2), Item::WOOL, 15);
		$this->setBlock($this->getLocation(1, 1, -2), Item::WOOL, 15);
		$this->setBlock($this->getLocation(0, 1, -2), Item::WOOL, 15);
		$this->setBlock($this->getLocation(-1, 1, -2), Item::WOOL, 15);
		$this->setBlock($this->getLocation(-2, 1, -2), Item::WOOL, 15);
		$this->setBlock($this->getLocation(-4, 1, 0), Item::WOODEN_STAIRS, 0);
		$this->setBlock($this->getLocation(-5, 0, 0), Item::WOODEN_STAIRS, 0);
	}

	private function setBlock(Position $block, int $id, int $data){
		$hash = spl_object_hash(Position::fromObject($block, $block->getLevel()));
		if(isset($this->blocks[$hash])){
			$this->blocks[$hash] = $id . ":" . $data;
		}
		$this->location->getLevel()->setBlock($block, Block::get($id, $data), true, true);
	}

	private function getLocation(int $x, int $y, int $z){
		$vec = $this->location->add($x, $y, $z);

		return $this->location->getLevel()->getBlock($vec);
	}

	public function clearAll(): void{
		foreach($this->blocks as $loc){
			$loc->getLevel()->setBlock($loc, Block::get(0));
		}
		unset($this->blocks);
		$this->activated = false;
	}

	public function onUpdate(): void{
		if($this->activated){
			CoreMain::get()->getSchedulerForce()->scheduleTask(new class($this) extends Task {

				/** @var GadgetTrampoline */
				private $gadget;

				public function __construct(GadgetTrampoline $gadget){
					$this->gadget = $gadget;
				}

				/**
				 * Actions to execute when run
				 *
				 * @param int $currentTick
				 *
				 * @return void
				 */
				public function onRun(int $currentTick){
					foreach(Utils::getNearbyLivingEntities($this->gadget->location, 4) as $entity){
						$block = $entity->getLevel()->getBlock($entity->add(0, -1, 0));
						if($block->getId() == Block::WOOL && $block->getDamage() == 15 && isset($this->gadget->cuboid[spl_object_hash($block)])){
							$entity->setMotion(new Vector3(0, 3, 0));
						}
					}
				}
			});
		}
	}

	public function onClear(): void{
		$this->clearAll();
		HandlerList::unregisterAll($this);
	}

	/**
	 * @param EntityDamageEvent $event
	 * @priority MONITOR
	 */
	public function checksDamage(EntityDamageEvent $event){
		if($event->getEntity() instanceof Player){
			$p = $event->getEntity();
			if($this->getPlayer() === $p){
				if($event->getCause() === EntityDamageEvent::CAUSE_FALL){
					$event->setCancelled();
				}
			}
		}
	}
}