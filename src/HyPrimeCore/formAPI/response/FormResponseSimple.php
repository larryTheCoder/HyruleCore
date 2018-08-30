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