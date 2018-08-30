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

namespace HyPrimeCore\cloaks\type;

use HyPrimeCore\cloaks\ParticleCloak;
use HyPrimeCore\utils\Utils;
use pocketmine\level\Location;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\level\particle\GenericParticle;
use pocketmine\math\Vector3;

class Frosty extends ParticleCloak {

	/** @var bool[][] */
	private $shape = [
		[true, true, true, true, true],
		[true, true, true, true, true],
		[true, true, true, true, true],
		[false, false, false, false, false],
		[false, false, false, false, false],
		[false, false, false, false, false],
		[false, false, false, false, false],
	];
	/** @var int */
	private $i = 0;

	public function __construct($player){
		parent::__construct($player, 2, CloakType::FROSTY);
	}

	public function onUpdate(): void{
		$this->active($this->getPlayer()->getLocation());
		if($this->i % 6 == 0 || $this->i == 29){
			$this->active2($this->getPlayer()->getLocation(), 20);
		}
	}

	private function active(Location $loc){
		$locs = $this->getCircle($loc->add(0.0, 0.5, 0.0), 1.2, 30);
		$locs2 = $this->getCircleReverse($loc->add(0.0, 0.5, 0.0), 1.2, 30);
		if($this->i <= 6){
			$this->addParticle(new CriticalParticle($locs[$this->i], 1));
			$this->addParticle(new CriticalParticle($locs2[$this->i + 1], 1));
			$this->addParticle(new CriticalParticle($locs[$this->i]->add(0.0, 0.8, 0.0), 1));
			$this->addParticle(new CriticalParticle($locs2[$this->i + 3]->add(0.0, 0.8, 0.0), 1));
		}elseif($this->i >= 7 && $this->i <= 12){
			$this->addParticle(new CriticalParticle($locs[$this->i]->add(0.0, 0.8, 0.0), 1));
			$this->addParticle(new CriticalParticle($locs2[$this->i]->add(0.0, 0.8, 0.0), 1));
			$this->addParticle(new CriticalParticle($locs[$this->i]->add(0.0, 1.3, 0.0), 1));
			$this->addParticle(new CriticalParticle($locs2[$this->i]->add(0.0, 1.3, 0.0), 1));
		}elseif($this->i >= 13 && $this->i <= 18){
			$this->addParticle(new CriticalParticle($locs[$this->i]->add(0.0, 1.0, 0.0), 1));
			$this->addParticle(new CriticalParticle($locs2[$this->i]->add(0.0, 1.0, 0.0), 1));
			$this->addParticle(new CriticalParticle($locs[$this->i]->add(0.0, 0.5, 0.0), 1));
			$this->addParticle(new CriticalParticle($locs2[$this->i]->add(0.0, 0.5, 0.0), 1));
		}elseif($this->i >= 19 && $this->i <= 24){
			$this->addParticle(new CriticalParticle($locs[$this->i]->add(0.0, 0.5, 0.0), 1));
			$this->addParticle(new CriticalParticle($locs2[$this->i]->add(0.0, 0.5, 0.0), 1));
			$this->addParticle(new CriticalParticle($locs[$this->i], 1));
			$this->addParticle(new CriticalParticle($locs2[$this->i], 1));
		}elseif($this->i >= 25 && $this->i < 30){
			$this->addParticle(new CriticalParticle($locs[$this->i]->add(0.0, 1.3, 0.0), 1));
			$this->addParticle(new CriticalParticle($locs2[$this->i]->add(0.0, 1.3, 0.0), 1));
			$this->addParticle(new CriticalParticle($locs[$this->i]->add(0.0, 0.5, 0.0), 1));
			$this->addParticle(new CriticalParticle($locs2[$this->i]->add(0.0, 0.5, 0.0), 1));
		}
		++$this->i;
		if($this->i >= 30){
			$this->i = 0;
		}
	}

	/**
	 * @param Vector3 $vec
	 * @param float $radius
	 * @param int $amount
	 * @return Vector3[]
	 */
	public function getCircle(Vector3 $vec, float $radius, int $amount): array{
		$loc = [];
		$increment = 6.283185307179586 / $amount;
		for($i = 0; $i < $amount; ++$i){
			$angle = $i * $increment;
			$x = $vec->getX() + $radius * cos($angle);
			$z = $vec->getZ() + $radius * sin($angle);
			$loc[] = new Vector3($x, $vec->getY(), $z);
		}

		return $loc;
	}

	/**
	 * @param Vector3 $vec
	 * @param float $radius
	 * @param int $amount
	 * @return Vector3[]
	 */
	public function getCircleReverse(Vector3 $vec, float $radius, int $amount): array{
		$loc = [];
		$increment = 6.283185307179586 / $amount;
		for($i = $amount; $i >= 0; --$i){
			$angle = $i * $increment;
			$x = $vec->getX() + $radius * cos($angle);
			$z = $vec->getZ() + $radius * sin($angle);
			$loc[] = new Vector3($x, $vec->getY(), $z);
		}

		return $loc;
	}

	private function active2(Location $loc, int $angleDistance){
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
						$loc->y = $defY;
					}
					for($k = 0; $k < 3; ++$k){
						$this->addParticle(new GenericParticle($newLoc, 46));
					}
				}
				$x += $space;
			}
			$y -= $space;
			$x = $defX;
		}
	}

	public function getPermissionNode(): string{
		return "core.cloak.frosty";
	}
}