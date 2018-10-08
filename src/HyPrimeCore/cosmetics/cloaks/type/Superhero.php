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
use pocketmine\level\Location;
use pocketmine\level\particle\RedstoneParticle;
use pocketmine\level\particle\SmokeParticle;


class Superhero extends ParticleCloak {

	/** @var bool[][] */
	private $shape = [];

	public function __construct($player){
		parent::__construct($player, 2, CloakType::SUPERHERO);

		$this->shape = [
			[true, true, true, true, true],
			[true, true, true, true, true],
			[true, true, true, true, true],
			[true, true, true, true, true],
			[true, true, true, true, true],
			[true, true, true, true, true],
			[true, true, true, true, true],
		];
	}

	public function onUpdate(): void{
		$this->getPlayer()->getLocation()->add(-0.2, 0, -0.2);
		$this->active($this->getPlayer()->getLocation(), 20);
		$this->addParticle(new SmokeParticle($this->getPlayer()->getLocation(), 10));
	}

	private function active(Location $loc, int $angleDistance){
		$space = 0.2;
		$defX = $x = $loc->getX() - $space * count($this->shape[0]) / 2 + $space;
		$defY = $y = $loc->getY() + 1.3;
		$angle = -(($loc->getYaw() + 180) / 60);
		$angle += (($loc->getYaw() < -180) ? 3.25 : 2.985);
		for($i = 0; $i < count($this->shape); ++$i){
			for($j = 0; $j < count($this->shape[$i]); ++$j){
				if($this->shape[$i][$j]){
					$target = clone $loc;
					$target->x = $x;
					$target->y = $y;
					$v2 = Utils::getBackVector($loc);
					$v = Utils::rotateAroundAxisY($target->subtract($loc->add(-0.1, 0, 0.35)), $angle);
					$iT = $i / $angleDistance;
					$v2->y = 0;
					$newVec = $v->add($v2->multiply(-0.2 - $iT));
					$newLoc = $newVec->add($loc);
					if($this->isMoving()){
						$newLoc->y = $defY;
					}
					for($k = 0; $k < 3; ++$k){
						$this->addParticle(new RedstoneParticle($newLoc));
					}
				}
				$x += $space;
			}
			$y -= $space;
			$x = $defX;
		}
	}

	public function getPermissionNode(): string{
		return "core.cloak.superhero";
	}
}