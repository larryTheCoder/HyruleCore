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

namespace HyPrimeCore\utils;


use pocketmine\level\Level;

class CuboidIterator implements \Iterator {

	/** @var Level */
	private $level;
	/** @var int */
	private $baseX;
	/** @var int */
	private $baseY;
	/** @var int */
	private $baseZ;
	/** @var int */
	private $x;
	/** @var int */
	private $y;
	/** @var int */
	private $z;
	/** @var int */
	private $sizeX;
	/** @var int */
	private $sizeY;
	/** @var int */
	private $sizeZ;

	public function __construct(Level $level, int $x1, int $y1, int $z1, int $x2, int $y2, int $z2){
		$this->level = $level;
		$this->baseX = $x1;
		$this->baseY = $y1;
		$this->baseZ = $z1;
		$this->sizeX = abs($x2 - $x1) + 1;
		$this->sizeY = abs($y2 - $y1) + 1;
		$this->sizeZ = abs($z2 - $z1) + 1;
		$this->z = (false ? 1 : 0);
		$this->y = (false ? 1 : 0);
		$this->x = (false ? 1 : 0);
	}

	/**
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public function current(){
		return $this->level->getBlockAt($this->baseX + $this->x, $this->baseY + $this->y, $this->baseZ + $this->z);
	}

	/**
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next(){
		if(++$this->x >= $this->sizeX){
			$this->x = 0;
			if(++$this->y >= $this->sizeY){
				$this->y = 0;
				++$this->z;
			}
		}
	}

	/**
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key(){
		// TODO: Implement key() method.
	}

	/**
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	public function valid(){
		return $this->x < $this->sizeX && $this->y < $this->sizeY && $this->z < $this->sizeZ;
	}

	/**
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind(){
		// TODO: Implement rewind() method.
	}
}