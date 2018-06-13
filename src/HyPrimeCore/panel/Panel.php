<?php
/**
 * BSD 3-Clause License
 *
 * Copyright (c) 2018, Adam Matthew, Hyrule Minigame Division
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

namespace HyPrimeCore\panel;

use HyPrimeCore\cloaks\CloakManager;
use HyPrimeCore\CoreMain;
use HyPrimeCore\formAPI\event\FormRespondedEvent;
use HyPrimeCore\formAPI\response\FormResponseSimple;
use pocketmine\event\Listener;
use pocketmine\Player;

class Panel implements Listener {

    const CLOAK_CONFIGURATION = 0;

    /** @var int[] */
    private $forms;
    /** @var CoreMain */
    private $plugin;

    public function __construct(CoreMain $plugin) {
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $plugin->getServer()->getLogger()->info($plugin->getPrefix() . "§bIndexed Panel container.");
    }

    public function showCloakConfiguration(Player $p) {
        $form = $this->plugin->getFormAPI()->createSimpleForm();
        $form->setTitle($this->plugin->getMessage($p, 'panel.cloak-title'));
        $form->setContent($this->plugin->getMessage($p, 'panel.cloak-about'));

        $pManager = CoreMain::get()->getPlayerData($p);
        $form->addButton("Firerings");
        $form->addButton("Firewings");
        $form->addButton("Frosty");
        $form->addButton("Superhero");
        $form->addButton("Scanner");
        $form->addButton("Shaman");
        if ($pManager->getCloakData() !== null) {
            $form->addButton($this->plugin->getMessage($p, 'panel.cloak-remove'));
        }
        $form->sendToPlayer($p);
        $this->forms[$form->getId()] = Panel::CLOAK_CONFIGURATION;
    }

    /**
     * @param FormRespondedEvent $event
     * @priority MONITOR
     */
    public function onResponse(FormRespondedEvent $event) {
        $id = $event->getId();
        if (isset($this->forms[$id])) {
            $p = $event->getPlayer();
            $response = $event->getResponse();
            $command = $this->forms[$id];
            unset($this->forms[$id]);
            switch ($command) {
                case self::CLOAK_CONFIGURATION:
                    if ($response->closed) {
                        $p->sendMessage($this->plugin->getMessage($p, 'panel.panel-cancelled'));
                        break;
                    }
                    /** @var FormResponseSimple $sForm */
                    $sForm = $response;
                    CloakManager::equipCloak($p, $sForm->getClickedButtonId());
                    $p->sendMessage($this->plugin->getPrefix() . str_replace("{CLOAK}", $sForm->getClickedButton()->getText(), $this->plugin->getMessage($p, 'panel.cloak-selected')));
                    break;
            }
        }
    }

}