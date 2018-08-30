<?php
/*
 * Copyright (C) 2018 Adam Matthew, Hyrule Minigame Division
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
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