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

namespace HyPrimeCore\formAPI\element;


class ElementSlider extends Element {

	private $text = "";
	private $min = 0;
	private $max = 100;
	private $step;
	private $defaultValue;

	public function __construct(String $text, float $min, float $max, int $step, float $defaultValue){
		$this->text = $text;
		$this->min = $min < 0 ? 0 : $min;
		$this->max = $max > $this->min ? $max : $this->min;
		if($step != -1 && $step > 0) $this->step = $step;
		if($defaultValue != -1) $this->defaultValue = $defaultValue;
	}

	/**
	 * @return string
	 */
	public function getText(): string{
		return $this->text;
	}

	/**
	 * @param string $text
	 */
	public function setText(string $text): void{
		$this->text = $text;
	}

	/**
	 * @return float|int
	 */
	public function getMin(){
		return $this->min;
	}

	/**
	 * @param float|int $min
	 */
	public function setMin($min): void{
		$this->min = $min;
	}

	/**
	 * @return float|int
	 */
	public function getMax(){
		return $this->max;
	}

	/**
	 * @param float|int $max
	 */
	public function setMax($max): void{
		$this->max = $max;
	}

	/**
	 * @return int
	 */
	public function getStep(): int{
		return $this->step;
	}

	/**
	 * @param int $step
	 */
	public function setStep(int $step): void{
		$this->step = $step;
	}

	/**
	 * @return float
	 */
	public function getDefaultValue(): float{
		return $this->defaultValue;
	}

	/**
	 * @param float $defaultValue
	 */
	public function setDefaultValue(float $defaultValue): void{
		$this->defaultValue = $defaultValue;
	}
}