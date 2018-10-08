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

namespace HyPrimeCore\utils;


use pocketmine\level\Position;

class Cuboid {

	/** @var \pocketmine\level\Level */
	protected $level;
	/** @var int */
	protected $x1;
	/** @var int */
	protected $y1;
	/** @var int */
	protected $z1;
	/** @var int */
	protected $x2;
	/** @var int */
	protected $y2;
	/** @var int */
	protected $z2;

	public function __construct(Position $pos1, Position $pos2){
		if(!$pos1->getLevel() !== $pos2->getLevel()){
			Utils::send("Locations must be on the same world");

			return;
		}

		$this->level = $pos1->getLevel();
		$this->x1 = min($pos1->getFloorX(), $pos2->getFloorX());
		$this->y1 = min($pos1->getFloorY(), $pos2->getFloorY());
		$this->z1 = min($pos1->getFloorZ(), $pos2->getFloorZ());
		$this->x2 = max($pos1->getFloorX(), $pos2->getFloorX());
		$this->y2 = max($pos1->getFloorY(), $pos2->getFloorY());
		$this->z2 = max($pos1->getFloorZ(), $pos2->getFloorZ());
	}

	public function isEmpty(): bool{
		foreach($this->getBlocks() as $block){
			if($block->getId() !== 0){
				return false;
			}
		}

		return true;
	}

	public function getBlocks(): array{
		$blockI = $this->iterator();
		$copy = [];
		while($blockI->valid()){
			$copy[] = $blockI->current();
			$blockI->next();
		}

		return $copy;
	}

	public function iterator(): CuboidIterator{
		return new CuboidIterator($this->level, $this->x1, $this->y1, $this->z1, $this->x2, $this->y2, $this->z2);
	}
}