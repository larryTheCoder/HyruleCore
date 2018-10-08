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
