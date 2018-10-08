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