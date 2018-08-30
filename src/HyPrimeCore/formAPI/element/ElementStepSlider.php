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


class ElementStepSlider extends Element {

	/** @var string */
	private $text = "";
	/** @var string[] */
	private $steps = [];
	/** @var int */
	private $defaultStepIndex = 0;

	public function __construct(string $text, array $steps, int $defaultStep){
		$this->text = $text;
		$this->steps = $steps;
		$this->defaultStepIndex = $defaultStep;
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
	 * @return string[]
	 */
	public function getSteps(): array{
		return $this->steps;
	}

	/**
	 * @param string[] $steps
	 */
	public function setSteps(array $steps): void{
		$this->steps = $steps;
	}

	/**
	 * @return int
	 */
	public function getDefaultStepIndex(): int{
		return $this->defaultStepIndex;
	}

	/**
	 * @param int $defaultStepIndex
	 */
	public function setDefaultStepIndex(int $defaultStepIndex): void{
		$this->defaultStepIndex = $defaultStepIndex;
	}
}