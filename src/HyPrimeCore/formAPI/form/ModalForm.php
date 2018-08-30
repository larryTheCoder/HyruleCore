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

namespace HyPrimeCore\formAPI\form;

use HyPrimeCore\formAPI\response\FormResponseModal;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;

class ModalForm extends Form {

	/** @var array */
	public $data = [];

	public function __construct($id, ?callable $callable){
		parent::__construct($id, $callable);
		$this->data["type"] = "modal";
		$this->data["title"] = "Modal Form";
		$this->data["content"] = "";
		$this->data["button1"] = "true";
		$this->data["button2"] = "false";
	}

	/**
	 * @return int
	 */
	public function getId(): int{
		return $this->id;
	}

	public function getTitle(){
		return $this->data["title"];
	}

	public function setTitle(string $title){
		$this->data["title"] = $title;
	}

	public function getContent(){
		return $this->data["content"];
	}

	public function setContent(string $content){
		$this->data["content"] = $content;
	}

	public function getButton1(){
		return $this->data["button1"];
	}

	public function setButton1(string $button1){
		$this->data["button1"] = $button1;
	}

	public function getButton2(){
		return $this->data["button2"];
	}

	public function setButton2(string $button2){
		$this->data["button2"] = $button2;
	}

	/**
	 * @param Player $player
	 */
	public function sendToPlayer(Player $player): void{
		$pk = new ModalFormRequestPacket();
		$pk->formId = $this->id;
		$pk->formData = json_encode($this->data);
		$player->dataPacket($pk);
		$this->playerName = $player->getName();
	}

	public function getResponseModal(): FormResponseModal{
		return new FormResponseModal($this);
	}
}