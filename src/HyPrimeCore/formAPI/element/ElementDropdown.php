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


class ElementDropdown extends Element {

	/** @var string */
	private $text = "";
	/** @var array|string[] */
	private $options;
	/** @var int */
	private $defaultOptionIndex = 0;

	/**
	 * @param string $text
	 * @param string[] $options
	 * @param int $defaultOption
	 */
	public function __construct(string $text, array $options = [], int $defaultOption = 0){
		$this->text = $text;
		$this->options = $options;
		$this->defaultOptionIndex = $defaultOption;
	}

	public function getDefaultOptionIndex(){
		return $this->defaultOptionIndex;
	}

	public function setDefaultOptionIndex(int $index){
		if($index >= count($this->options)) return;
		$this->defaultOptionIndex = $index;
	}

	public function getOptions(): array{
		return $this->options;
	}

	public function getText(){
		return $this->text;
	}

	public function setText(String $text){
		$this->text = $text;
	}

	public function addOption(String $option, bool $isDefault = false){
		$this->options[] = $option;
		if($isDefault) $this->defaultOptionIndex = count($this->options) - 1;
	}

}