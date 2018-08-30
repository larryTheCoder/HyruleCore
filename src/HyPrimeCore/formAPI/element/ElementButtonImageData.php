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


class ElementButtonImageData {
	const IMAGE_DATA_TYPE_PATH = "path";
	const IMAGE_DATA_TYPE_URL = "url";

	/** @var string */
	private $type;
	/** @var string */
	private $data;

	public function __construct(string $type, string $data){
		if(!$type === ElementButtonImageData::IMAGE_DATA_TYPE_URL && !$type === ElementButtonImageData::IMAGE_DATA_TYPE_PATH) return;
		$this->type = $type;
		$this->data = $data;
	}

	public function getType(): string{
		return $this->type;
	}

	public function setType(String $type){
		$this->type = $type;
	}

	public function getData(): string{
		return $this->data;
	}

	public function setData(String $data){
		$this->data = $data;
	}

}