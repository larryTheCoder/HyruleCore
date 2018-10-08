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

namespace HyPrimeCore\cosmetics\cloaks\type;

use HyPrimeCore\cosmetics\cloaks\ParticleCloak;
use HyPrimeCore\utils\Utils;
use pocketmine\level\particle\FlameParticle;
use pocketmine\math\Vector3;


class Firerings extends ParticleCloak {

	private $step;

	public function __construct($player){
		parent::__construct($player, 1, CloakType::FIRERINGS);
	}

	public function onUpdate(): void{
		for($i = 0; $i < 2; $i++){
			$inc = 0.07853981633974483;
			$toAdd = 0.0;
			if($i == 1){
				$toAdd = 3.5;
			}
			$angle = $this->step * $inc + $toAdd;
			$v = new Vector3();
			$v->setComponents(cos($angle), 0, sin($angle));
			if($i == 0){
				Utils::rotateAroundAxisZ($v, 10.0);
			}else{
				Utils::rotateAroundAxisZ($v, 100.0);
			}
			$loc = clone $this->getPlayer()->getLocation()->add(0.0, 1.0, 0.0)->add($v);
			$this->addParticle(new FlameParticle($loc));
		}
		$this->step += 3;
	}

	public function getPermissionNode(): string{
		return "core.cloak.firerings";
	}
}