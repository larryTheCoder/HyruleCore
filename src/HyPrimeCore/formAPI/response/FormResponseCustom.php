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

use HyPrimeCore\formAPI\element\ElementDropdown;
use HyPrimeCore\formAPI\element\ElementInput;
use HyPrimeCore\formAPI\element\ElementLabel;
use HyPrimeCore\formAPI\element\ElementSlider;
use HyPrimeCore\formAPI\element\ElementStepSlider;
use HyPrimeCore\formAPI\element\ElementToggle;
use HyPrimeCore\formAPI\form\CustomForm;

class FormResponseCustom extends FormResponse {

	/** @var array */
	private $responses = [];
	/** @var FormResponseData[] */
	private $dropdownResponses = [];
	/** @var string[] */
	private $inputResponses = [];
	/** @var integer[] */
	private $sliderResponses = [];
	/** @var FormResponseData[] */
	private $stepSliderResponses = [];
	/** @var boolean[] */
	private $toggleResponses = [];
	/** @var CustomForm */
	private $form;

	public function __construct(CustomForm $form){
		$this->form = $form;
	}

	/**
	 * @return array
	 */
	public function getResponses(): array{
		return $this->responses;
	}

	/**
	 * @param int $id
	 * @return mixed
	 */
	public function getResponse(int $id){
		return $this->responses[$id];
	}

	/**
	 * @param int $id
	 * @return FormResponseData
	 */
	public function getDropdownResponse(int $id): FormResponseData{
		return $this->dropdownResponses[$id];
	}

	/**
	 * @param int $id
	 * @return string
	 */
	public function getInputResponse(int $id): string{
		return $this->inputResponses[$id];
	}

	/**
	 * @param int $id
	 * @return int
	 */
	public function getSliderResponse(int $id): int{
		return $this->sliderResponses[$id];
	}

	/**
	 * @param int $id
	 * @return FormResponseData
	 */
	public function getStepSliderResponse(int $id): FormResponseData{
		return $this->stepSliderResponses[$id];
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public function getToggleResponse(int $id): bool{
		return $this->toggleResponses[$id];
	}

	public function setData(string $data){
		if($data === "null"){
			$this->closed = true;

			return;
		}
		$json = json_decode($data);

		$dropdownResponses = [];
		$inputResponses = [];
		$sliderResponses = [];
		$stepSliderResponses = [];
		$toggleResponses = [];
		$responses = [];

		$i = 0;
		$contents = $this->form->elements;
		foreach($json as $elementData){
			if($i >= count($this->form->elements)){
				break;
			}
			$e = $contents[$i];
			if($e === null) break;
			if($e instanceof ElementLabel){
				$i++;
				continue;
			}
			if($e instanceof ElementDropdown){
				$answer = $e->getOptions()[intval($elementData)];
				$dropdownResponses[$i] = new FormResponseData(intval($elementData), $answer);
				$responses[$i] = $answer;
			}elseif($e instanceof ElementInput){
				$inputResponses[$i] = $elementData;
				$responses[$i] = $elementData;
			}elseif($e instanceof ElementSlider){
				$sliderResponses[$i] = $elementData;
				$responses[$i] = $elementData;
			}elseif($e instanceof ElementStepSlider){
				$answer = $e->getSteps()[intval($elementData)];
				$stepSliderResponses[$i] = new FormResponseData(intval($elementData), $answer);
				$responses[$i] = $answer;
			}elseif($e instanceof ElementToggle){
				$answer = boolval($elementData);
				$toggleResponses[$i] = $answer;
				$responses[$i] = $answer;
			}
			$i++;
		}

		$this->dropdownResponses = $dropdownResponses;
		$this->inputResponses = $inputResponses;
		$this->sliderResponses = $sliderResponses;
		$this->stepSliderResponses = $stepSliderResponses;
		$this->toggleResponses = $toggleResponses;
		$this->responses = $responses;
	}
}