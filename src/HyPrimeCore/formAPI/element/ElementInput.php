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


class ElementInput extends Element {

	private $text = "";
	private $placeholder = "";
	private $defaultText = "";

	public function __construct(string $text, string $placeholder = "", string $defaultText = ""){
		$this->text = $text;
		$this->placeholder = $placeholder;
		$this->defaultText = $defaultText;
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
	 * @return string
	 */
	public function getPlaceholder(): string{
		return $this->placeholder;
	}

	/**
	 * @param string $placeholder
	 */
	public function setPlaceholder(string $placeholder): void{
		$this->placeholder = $placeholder;
	}

	/**
	 * @return string
	 */
	public function getDefaultText(): string{
		return $this->defaultText;
	}

	/**
	 * @param string $defaultText
	 */
	public function setDefaultText(string $defaultText): void{
		$this->defaultText = $defaultText;
	}


}