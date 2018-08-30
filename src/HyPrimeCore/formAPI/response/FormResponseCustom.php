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