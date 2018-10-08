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

namespace HyPrimeCore\cosmetics\cloaks\type;


use HyPrimeCore\cosmetics\cloaks\ParticleCloak;
use pocketmine\level\Location;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\math\Vector3;


class Scanner extends ParticleCloak {

	/** @var float */
	private $radius;
	/** @var int */
	private $particles;
	/** @var int */
	private $step;
	/** @var int */
	private $stepY;
	/** @var int */
	private $locY;

	public function __construct($player){
		parent::__construct($player, 2, CloakType::SCANNER);
		$this->radius = 0.6;
		$this->particles = 25.0;
		$this->step = 0;
		$this->stepY = 0.0;
	}

	public function onUpdate(): void{
		$this->active($this->getPlayer()->getLocation());
	}

	private function active(Location $loc){
		if($this->step > 16){
			$this->locY = 0;
			$this->step = 0;
		}
		for($i2 = 0; $i2 < 9; ++$i2){
			$inc = 0.6283185307179586 / $this->particles;
			$angle = $this->step * $inc + $this->stepY + 3.5 * $i2 - 0.2;
			$v = new Vector3(cos($angle) * $this->radius, 0, sin($angle) * $this->radius);
			$this->addParticle(new CriticalParticle($loc->add($v)->add(0.0, $this->locY, 0.0), 1));
		}
		if($this->stepY < 6.0){
			$this->stepY += 0.045;
		}else{
			$this->stepY = 0.0;
		}
		if($this->step <= 8){
			$this->locY += 0.25;
		}else{
			$this->locY -= 0.25;
		}
		++$this->step;
	}

	public function getPermissionNode(): string{
		return "core.cloak.scanner";
	}
}