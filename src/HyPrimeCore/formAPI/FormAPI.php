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
declare(strict_types = 1);

namespace HyPrimeCore\formAPI;

use HyPrimeCore\CoreMain;
use HyPrimeCore\formAPI\event\FormRespondedEvent;
use HyPrimeCore\formAPI\form\CustomForm;
use HyPrimeCore\formAPI\form\Form;
use HyPrimeCore\formAPI\form\ModalForm;
use HyPrimeCore\formAPI\form\SimpleForm;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\Server;

class FormAPI implements Listener {

	/** @var int */
	public $formCount = 0;
	/** @var Form[] */
	public $forms = [];

	public function __construct(CoreMain $plugin){
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
		$plugin->getServer()->getLogger()->info($plugin->getPrefix() . "§bRegistered Panel listener.");
	}

	/**
	 * @return CustomForm
	 */
	public function createCustomForm(): CustomForm{
		$this->formCount++;
		$form = new CustomForm($this->formCount, null);
		$this->forms[$this->formCount] = $form;

		return $form;
	}

	public function createModalForm(): ModalForm{
		$this->formCount++;
		$form = new ModalForm($this->formCount, null);
		$this->forms[$this->formCount] = $form;

		return $form;
	}

	public function createSimpleForm(): SimpleForm{
		$this->formCount++;
		$form = new SimpleForm($this->formCount, null);
		$this->forms[$this->formCount] = $form;

		return $form;
	}

	/**
	 * @param DataPacketReceiveEvent $ev
	 * @priority MONITOR
	 */
	public function onPacketReceived(DataPacketReceiveEvent $ev): void{
		$pk = $ev->getPacket();
		if($pk instanceof ModalFormResponsePacket){
			$player = $ev->getPlayer();
			$formId = $pk->formId;
			if(isset($this->forms[$formId])){
				if(!$this->forms[$formId]->isRecipient($player)){
					return;
				}
				$modal = $this->forms[$formId]->getResponseModal();
				$modal->setData(trim($pk->formData));
				$event = new FormRespondedEvent($player, $this->forms[$formId], $modal);
				Server::getInstance()->getPluginManager()->callEvent($event);
				$ev->setCancelled();
			}
		}
	}

	/**
	 * @param PlayerQuitEvent $ev
	 */
	public function onPlayerQuit(PlayerQuitEvent $ev){
		$player = $ev->getPlayer();
		/**
		 * @var int $id
		 * @var Form $form
		 */
		foreach($this->forms as $id => $form){
			if($form->isRecipient($player)){
				unset($this->forms[$id]);
				break;
			}
		}
	}

}
