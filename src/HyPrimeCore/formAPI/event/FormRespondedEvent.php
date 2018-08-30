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

namespace HyPrimeCore\formAPI\event;

use HyPrimeCore\formAPI\form\Form;
use HyPrimeCore\formAPI\response\FormResponse;
use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;

class FormRespondedEvent extends PlayerEvent {
	public static $handlerList = null;
	/** @var int */
	private $id;
	/** @var Form */
	private $form;
	/** @var FormResponse */
	private $formResponse;
	/** @var bool */
	private $closed;

	public function __construct(Player $player, Form $form, FormResponse $formResponse){
		$this->id = $form->getId();
		$this->form = $form;
		$this->formResponse = $formResponse;
		$this->closed = $formResponse->closed;
		$this->player = $player;
	}

	public function getId(): int{
		return $this->id;
	}

	public function isClosed(){
		return $this->closed;
	}

	public function getForm(): Form{
		return $this->form;
	}

	public function getResponse(): FormResponse{
		return $this->formResponse;
	}

}