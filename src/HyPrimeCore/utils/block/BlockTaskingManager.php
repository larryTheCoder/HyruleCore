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


use HyPrimeCore\CoreMain;
use HyPrimeCore\utils\block\task\BlockUpdateTask;
use HyPrimeCore\utils\block\task\ProgressiveScheduler;
use pocketmine\block\Block;
use pocketmine\block\Diamond;
use pocketmine\block\Emerald;
use pocketmine\block\Gold;
use pocketmine\block\Iron;
use pocketmine\entity\Entity;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\Position;

class BlockTaskingManager {

	const BLOCK_DIAMOND = 0;
	const BLOCK_IRON = 1;
	const BLOCK_EMERALD = 2;
	const BLOCK_GOLD = 4;

	private $plugin;
	private $list = [];

	public function __construct(CoreMain $plugin){
		$this->plugin = $plugin;
	}

	public function registerTemporary(Position $pos, int $blockId){
		if($blockId < self::BLOCK_DIAMOND || $blockId > self::BLOCK_GOLD){
			$this->plugin->getServer()->getLogger()->notice("ERROR: Attempt to register block out of bounds");

			return;
		}

		$level = $pos->getLevel();
		$posit = $pos->add(0.5, 2.5, 0.5);
		$nbt = Entity::createBaseNBT($posit, null, 0, 0);
		// The shit ArmorStand
		$block = new ArmorStand($level, $nbt, $this->getBlockById($blockId));
		$block->spawnToAll();

		$this->plugin->getServer()->getLogger()->debug("Registering blockEntity: " . $block);

		$pos1 = $posit->add(0, 1.2, 0);
		$pos2 = $posit->add(0, 1.6, 0);
		$pos3 = $posit->add(0, 2.0, 0);

		// This text message will not be executed first load
		$text1 = new FloatingTextParticle($pos1, "");
		$text2 = new FloatingTextParticle($pos2, $this->getNameById($blockId));
		$text3 = new FloatingTextParticle($pos3, "");

		$this->list[$blockId][] = [$text1, $text2, $text3];
		$task = new BlockUpdateTask([$text1, $text2, $text3], $level); // The update text
		$task2 = new ProgressiveScheduler($block);                       // The Yaw up and down function
		$this->plugin->getSchedulerForce()->scheduleRepeatingTask($task, 20);
		$this->plugin->getSchedulerForce()->scheduleRepeatingTask($task2, 1);
	}

	public function getBlockById(int $blockId): ?Block{
		if($blockId < self::BLOCK_DIAMOND || $blockId > self::BLOCK_GOLD){
			$this->plugin->getServer()->getLogger()->notice("ERROR: Attempt to register block out of bounds");

			return null;
		}
		switch($blockId){
			case self::BLOCK_DIAMOND:
				return new Diamond();
			case self::BLOCK_IRON:
				return new Iron();
			case self::BLOCK_EMERALD:
				return new Emerald();
			case self::BLOCK_GOLD:
				return new Gold();
			default:
				return null;
		}
	}

	public function getNameById(int $blockId){
		if($blockId < self::BLOCK_DIAMOND || $blockId > self::BLOCK_GOLD){
			$this->plugin->getServer()->getLogger()->notice("ERROR: Attempt to register block out of bounds");

			return "";
		}
		switch($blockId){
			case self::BLOCK_DIAMOND:
				return "§9§lDiamond";
			case self::BLOCK_IRON:
				return "§lIron";
			case self::BLOCK_EMERALD:
				return "§6§2Emerald";
			case self::BLOCK_GOLD:
				return "§6§lGold";
			default:
				return "§eUnknown";
		}
	}
}