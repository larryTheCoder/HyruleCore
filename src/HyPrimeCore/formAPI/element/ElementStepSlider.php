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