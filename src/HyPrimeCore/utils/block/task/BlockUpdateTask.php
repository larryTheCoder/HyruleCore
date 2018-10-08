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

namespace HyPrimeCore\utils\block\task;

use pocketmine\level\Level;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\scheduler\Task;

class BlockUpdateTask extends Task {

	/** @var FloatingTextParticle[] */
	private $textParticle;

	/** @var integer */
	private $seconds = 30;
	/** @var integer */
	private $spawnsTime = 0;
	/** @var Level */
	private $level;

	public function __construct(array $list, Level $level){
		$this->textParticle = $list;
		$this->level = $level;
	}

	/**
	 * Actions to execute when run
	 *
	 * @param int $currentTick
	 *
	 * @return void
	 */
	public function onRun(int $currentTick){
		$seconds = $this->textParticle[0];
		$tier = $this->textParticle[2];

		$tier->setTitle("§eTier §a1");
		// This checks the seconds and tiers
		if($this->seconds <= 0){
			$this->seconds = 30;
			$this->spawnsTime++;
			if($this->spawnsTime >= 15){
				$this->seconds = 20;
				$tier->setTitle("§eTier §a2");
			}elseif($this->spawnsTime >= 30){
				$this->seconds = 5;
				$tier->setTitle("§eTier §a3");
			}
		}

		// Set the seconds by time
		$seconds->setTitle("§aSpawns in §e{$this->seconds}§a seconds.");
		$this->seconds--;

		// Then send the packet
		$this->level->addParticle($tier);
		$this->level->addParticle($this->textParticle[1]);
		$this->level->addParticle($seconds);
	}
}