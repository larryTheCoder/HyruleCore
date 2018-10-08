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
use pocketmine\level\Location;
use pocketmine\level\particle\GenericParticle;
use pocketmine\math\Vector3;


class Shaman extends ParticleCloak {

	/** @var float */
	private $i;

	public function __construct($player){
		parent::__construct($player, 2, CloakType::SHAMAN);
		$this->i = 0.0;
	}

	public function onUpdate(): void{
		$this->active($this->getPlayer()->getLocation());
	}

	private function active(Location $loc){
		# Particle Tier 1
		$angle1 = $this->i * 0.07853981633974483;
		$angle2 = $this->i * 0.07853981633974483 + 3.0;
		$v1 = new Vector3(cos($angle1) * 0.6, 0, sin($angle1) * 0.6);
		$v2 = new Vector3(cos($angle2) * 0.6, 0, sin($angle2) * 0.6);
		$this->addParticle(new GenericParticle($loc->add($v1)->add(0, 2), 46));
		$this->addParticle(new GenericParticle($loc->add($v2)->add(0, 2), 46));
		# Particle Tier 2
		$angle3 = $this->i * 0.07853981633974483 + 3.0;
		$angle4 = $this->i * 0.07853981633974483 + 6.0;
		$v3 = new Vector3(cos($angle3) * 0.4, 0, sin($angle3) * 0.4);
		$v4 = new Vector3(cos($angle4) * 0.4, 0, sin($angle4) * 0.4);
		$this->addParticle(new GenericParticle($loc->add($v3)->add(0, 1.25), 46));
		$this->addParticle(new GenericParticle($loc->add($v4)->add(0, 1.25), 46));
		# Particle Tier 3
		$angle5 = $this->i * 0.07853981633974483;
		$angle6 = $this->i * 0.07853981633974483 + 3.0;
		$v5 = new Vector3(cos($angle5) * 0.25, 0, sin($angle5) * 0.25);
		$v6 = new Vector3(cos($angle6) * 0.25, 0, sin($angle6) * 0.25);
		$this->addParticle(new GenericParticle($loc->add($v5)->add(0, 0.65), 46));
		$this->addParticle(new GenericParticle($loc->add($v6)->add(0, 0.65), 46));

		$this->i += 4.0;
	}

	public function getPermissionNode(): string{
		return "core.cloak.shaman";
	}
}