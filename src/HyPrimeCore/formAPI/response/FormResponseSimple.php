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

namespace HyPrimeCore\formAPI\response;


use HyPrimeCore\formAPI\element\ElementButton;
use HyPrimeCore\formAPI\form\SimpleForm;

class FormResponseSimple extends FormResponse {

	/** @var SimpleForm */
	private $form;
	/** @var int */
	private $clickedButtonId;
	/** @var ElementButton[] */
	private $buttons;
	/** @var ElementButton */
	private $clickedButton;

	public function __construct(SimpleForm $form, array $buttons){
		$this->form = $form;
		$this->buttons = $buttons;
	}

	/**
	 * @return int
	 */
	public function getClickedButtonId(): int{
		return $this->clickedButtonId;
	}

	/**
	 * @return bool
	 */
	public function isClosed(): bool{
		return $this->closed;
	}

	/**
	 * Get the clicked button
	 *
	 * @return ElementButton
	 */
	public function getClickedButton(): ElementButton{
		return $this->clickedButton;
	}

	/**
	 * @param string $data
	 */
	public function setData(string $data){
		if($data === "null"){
			$this->closed = true;

			return;
		}
		// It quite impossible if we sent a lot of data
		// Or button on this.
		$this->clickedButtonId = (int)$data;
		$this->clickedButton = $this->buttons[$data];
	}
}