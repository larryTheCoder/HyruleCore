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

namespace HyPrimeCore\cosmetics\cloaks;

use HyPrimeCore\CoreMain;
use HyPrimeCore\player\FakePlayer;
use HyPrimeCore\tasks\CloakTask;
use pocketmine\event\HandlerList;
use pocketmine\level\particle\Particle;
use pocketmine\Player;

abstract class ParticleCloak {

	/** @var bool */
	public $moving = false;
	/** @var null|Player|FakePlayer */
	private $player = null;
	/** @var CloakListener */
	private $listener;
	/** @var int */
	private $data;
	/** @var null|\pocketmine\scheduler\TaskHandler */
	private $task;

	/**
	 * ParticleCloak constructor.
	 * @param null|Player|FakePlayer $player
	 * @param int $delay
	 * @param int $data
	 */
	public function __construct($player, int $delay, int $data){
		$this->player = $player;
		if($this->player instanceof Player){
			if(!$player->hasPermission($this->getPermissionNode())){
				$player->sendMessage(CoreMain::get()->getMessage($player, 'error.buy-site'));

				return;
			}
			$this->moving = false;
			$this->data = $data;
			$this->task = CoreMain::get()->getSchedulerForce()->scheduleRepeatingTask(new CloakTask($this), $delay);
			$this->listener = new CloakListener($this);
			CoreMain::get()->getServer()->getPluginManager()->registerEvents($this->listener, CoreMain::get());
		}elseif($this->player instanceof FakePlayer){
			$this->moving = false;
			$this->data = $data;
			$this->task = CoreMain::get()->getSchedulerForce()->scheduleRepeatingTask(new CloakTask($this), $delay);
			$this->listener = new CloakListener($this);
			CoreMain::get()->getServer()->getPluginManager()->registerEvents($this->listener, CoreMain::get());
		}
	}

	public abstract function getPermissionNode(): string;

	/**
	 * @return int
	 */
	public function getType(): int{
		return $this->data;
	}

	public function clear(){
		if(!is_null($this->task)){
			CoreMain::get()->getSchedulerForce()->cancelTask($this->task->getTaskId());
			HandlerList::unregisterAll($this->listener);
		}

		$this->moving = false;
		$this->player = null;
	}

	/**
	 * @return FakePlayer|null|Player
	 */
	public function getPlayer(){
		return $this->player;
	}

	public abstract function onUpdate(): void;

	public function isMoving(): bool{
		return $this->moving;
	}

	public function addParticle(Particle $particle){
		if($this->player instanceof FakePlayer){
			$this->player->getLevel()->addParticle($particle, [$this->player->getPlayer()]);
		}else{
			$this->player->getLevel()->addParticle($particle);
		}
	}

}