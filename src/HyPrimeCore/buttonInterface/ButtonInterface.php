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

namespace HyPrimeCore\buttonInterface;

use HyPrimeCore\buttonInterface\menu\CageMenu;
use HyPrimeCore\buttonInterface\menu\CloakMenu;
use HyPrimeCore\buttonInterface\menu\KitMenu;
use HyPrimeCore\buttonInterface\menu\Menu;
use HyPrimeCore\CoreMain;
use HyPrimeCore\event\ButtonPushEvent;
use HyPrimeCore\kits\KitInjectionModule;
use HyPrimeCore\utils\Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\level\Location;
use pocketmine\Player;
use pocketmine\utils\Config;

class ButtonInterface implements Listener {

    /** @var Menu[] */
    private $menuType = []; // Choose a menu
    /** @var int[] */
    private $currentPath = []; // The path of the menu (chosen and not chosen)
    /** @var Menu[] */
    private $menu = [];
    /** @var KitInjectionModule */
    private $module;
    /** @var Location */
    private $buttonNext, $buttonSelect, $buttonPrev, $buttonBack;
    /** @var Config */
    private $kit;

    public function __construct(CoreMain $plugin, KitInjectionModule $module) {
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $this->kit = new Config($plugin->getDataFolder() . "kits.yml", Config::YAML);
        $this->module = $module;
        $this->menu[] = new CageMenu();
        $this->menu[] = new CloakMenu();
        $this->menu[] = new KitMenu();
        $this->buttonNext = Utils::parsePosition($this->kit->getNested('interface.button-next'));
        $this->buttonSelect = Utils::parsePosition($this->kit->getNested('interface.button-choose'));
        $this->buttonPrev = Utils::parsePosition($this->kit->getNested('interface.button-prev'));
        $this->buttonBack = Utils::parsePosition($this->kit->getNested('interface.button-back'));
    }

    /**
     * @param PlayerJoinEvent $ev
     */
    public function onPlayerJoin(PlayerJoinEvent $ev) {
        $this->menuType[$ev->getPlayer()->getName()] = null;
        $this->currentPath[$ev->getPlayer()->getName()] = Menu::INTERACT_CLOAK_MENU;
    }

    public function onButtonPush(ButtonPushEvent $event) {
        $p = $event->getPlayer();
        $menu = $this->menuType[$p->getName()];
        $path = $this->currentPath[$p->getName()];
        if ($menu === null) {
            if ($event->getPos()->equals($this->buttonNext)) {
                if ($path === 3) {
                    $path = 0;
                } else {
                    $path++;
                }
            } else if ($event->getPos()->equals($this->buttonPrev)) {
                if ($path === 0) {
                    $path = 3;
                } else {
                    $path--;
                }
            } else if ($event->getPos()->equals($this->buttonSelect)) {
                $menu = Menu::getMenu($path);
                $menu->onSelectedMenu($p);
                $this->menuType[$p->getName()] = $menu;
            } else {
                // Back button? Its useless buddy, what you gonna to back of?
                return;
            }

            $this->currentPath[$p->getName()] = $path;
            $this->updateText($p);
            return;
        }
        if ($event->getPos()->equals($this->buttonNext)) {
            $menu->getNextMenu($p);
        } else if ($event->getPos()->equals($this->buttonPrev)) {
            $menu->getPrevMenu($p);
        } else if ($event->getPos()->equals($this->buttonSelect)) {
            $menu->onPlayerSelect($p);
        } else {
            $menu->onReturnMenu($p);
            $this->menuType[$p->getName()] = null;
            return;
        }
        $this->menuType[$p->getName()] = $menu;
        $this->updateText($p);
    }

    private function updateText(Player $p) {

    }
}