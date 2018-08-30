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

namespace HyPrimeCore\utils\block\task;

use HyPrimeCore\utils\block\ArmorStand;
use pocketmine\scheduler\Task;

class ProgressiveScheduler extends Task {

	public $tick = 0;
	public $y = 0;
	/** @var ArmorStand */
	private $armorStand;
	/** @var bool */
	private $reverse;
	/** @var int */
	private $yaw = 0;
	private $rate = 1;
	private $yRate = 0.004;
	private $pause = 0;

	public function __construct(ArmorStand $armorStand){
		$this->armorStand = $armorStand;
	}

	/**
	 * Actions to execute when run
	 *
	 * @param int $currentTick
	 *
	 * @return void
	 */
	public function onRun(int $currentTick){
		$this->tick++;
		$this->y += 0.004;
		// Pause the block from moving, this ensure that
		// The block isn't too _robot_
		if($this->pause > 0){
			$this->pause--;

			return;
		}
		// Vise versa
		if($this->reverse){
			$this->yaw -= $this->rate;
			$this->y += $this->yRate;
			if($this->yaw <= 0){
				$this->reverse = false;
				$this->rate = 1;
				$this->yRate = 0.004;
				$this->pause = 20;
			}elseif($this->yaw <= 20){
				$this->rate -= 0.5;
				$this->yRate -= 0.0002;
			}
		}else{
			$this->yaw++;
			$this->y -= $this->yRate;
			if($this->yaw >= 360){
				$this->reverse = true;
				$this->rate = 1;
				$this->yRate = 0.004;
				$this->pause = 20;
			}elseif($this->yaw >= 340){
				$this->rate -= 0.5;
				$this->yRate -= 0.0002;
			}
		}

		// Then update them
		$this->armorStand->yaw = $this->yaw;
		$this->armorStand->y = $this->y;

	}

}