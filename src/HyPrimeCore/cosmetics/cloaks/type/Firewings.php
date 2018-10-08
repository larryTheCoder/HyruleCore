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
use HyPrimeCore\utils\Utils;
use pocketmine\level\Location;
use pocketmine\level\particle\FlameParticle;


class Firewings extends ParticleCloak {

	/** @var boolean[][] */
	private $shape = [
		[false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false, false],
		[false, false, false, false, false, true, false, false, false, false, false, false, false, false, true, false, false, false, false, false],
		[false, false, false, false, true, true, true, false, false, false, false, false, false, true, true, true, false, false, false, false],
		[false, false, false, true, true, true, true, true, false, false, false, false, true, true, true, true, true, false, false, false],
		[false, false, false, false, true, true, true, true, true, false, false, true, true, true, true, true, false, false, false, false],
		[false, false, false, false, false, true, true, true, true, false, false, true, true, true, true, false, false, false, false, false],
		[false, false, false, false, false, false, true, true, true, true, true, true, true, true, false, false, false, false, false, false],
		[false, false, false, false, false, false, false, true, true, true, true, true, true, false, false, false, false, false, false, false],
		[false, false, false, false, false, false, false, false, true, true, true, true, false, false, false, false, false, false, false, false],
		[false, false, false, false, false, false, false, true, true, false, false, true, true, false, false, false, false, false, false, false],
		[false, false, false, false, false, false, true, true, true, false, false, true, true, true, false, false, false, false, false, false],
		[false, false, false, false, false, false, true, true, false, false, false, false, true, true, false, false, false, false, false, false],
		[false, false, false, false, false, false, true, false, false, false, false, false, false, true, false, false, false, false, false, false],
	];

	public function __construct($player){
		parent::__construct($player, 2, CloakType::FIREWINGS);
	}

	public function onUpdate(): void{
		$this->active($this->getPlayer()->getLocation());
	}

	private function active(Location $loc){
		$space = 0.2;
		$defX = $x = $loc->getX() - $space * count($this->shape[0]) / 2 + $space / 2;
		$y = $loc->getY() + 2.8;
		$angle = -(($loc->getYaw() + 180) / 60);
		$angle += (($loc->getYaw() < -180) ? 3.25 : 2.985);
		for($i = 0; $i < count($this->shape); ++$i){
			for($j = 0; $j < count($this->shape[$i]); ++$j){
				if($this->shape[$i][$j]){
					$target = clone $loc;
					$target->x = $x;
					$target->y = $y;
					$v2 = Utils::getBackVector($loc);
					$v = Utils::rotateAroundAxisY($target->subtract($loc->add(-0.5, 0, 0.35)), $angle);
					$iT = $i / 18.0;
					$v2->y = 0;
					$newVec = $v->add($v2->multiply(-0.2 - $iT));
					$newLoc = $newVec->add($loc);
					for($k = 0; $k < 3; ++$k){
						$this->addParticle(new FlameParticle($newLoc));
					}
				}
				$x += $space;
			}
			$y -= $space;
			$x = $defX;
		}
	}

	public function getPermissionNode(): string{
		return "core.cloak.firewings";
	}
}