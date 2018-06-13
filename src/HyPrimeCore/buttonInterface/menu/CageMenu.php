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

namespace HyPrimeCore\buttonInterface\menu;

use HyPrimeCore\CoreMain;
use larryTheCoder\cages\Cage;
use larryTheCoder\SkyWarsPE;
use pocketmine\Player;

class CageMenu extends Menu {

    /** @var Cage[] */
    private $types;
    /** @var null|SkyWarsPE */
    private $plugin;
    /** @var int */
    private $count = 0;
    /** @var array */
    private $menuData = [];

    public function __construct() {
        foreach (SkyWarsPE::getInstance()->cage->getCages() as $cage) {
            $this->types[] = $cage;
        }
    }

    public function getInteractId(): int {
        return Menu::INTERACT_CAGES_MENU;
    }

    /**
     * Get the next menu for player
     *
     * @param Player $p
     * @return string[]
     */
    public function getNextMenu(Player $p): array {
        if (count($this->types) > $this->count) {
            $this->count = 0;
        }
        $id = $this->count++;
        $pd = $this->plugin->getDatabase()->getPlayerData($p->getName());
        $msg = [false, CoreMain::get()->getMessage($p, 'interface.select-cage')];
        if (!in_array(strtolower($this->types[$id]), $pd->cages)) {
            $msg = [true, $this->types[$id]->getPrice()];
        }
        $this->menuData = [$this->types[$id]->getCageName(), $msg];
        return [$this->types[$id]->getCageName(), $msg];
    }

    /**
     * Get the previous menu for player
     *
     * @param Player $p
     * @return string[]
     */
    public function getPrevMenu(Player $p): array {
        if (count($this->types) < $this->count) {
            $this->count = count($this->types);
        }
        $id = $this->count--;
        $pd = $this->plugin->getDatabase()->getPlayerData($p->getName());
        $msg = [false, CoreMain::get()->getMessage($p, 'interface.select-cage')];
        if (!in_array(strtolower($this->types[$id]), $pd->cages)) {
            $msg = [true, $this->types[$id]->getPrice()];
        }
        $this->menuData = [$this->types[$id]->getCageName(), $msg];
        return [$this->types[$id]->getCageName(), $msg];
    }

    /**
     * Executed when a player select the button
     *
     * @param Player $p
     */
    public function onPlayerSelect(Player $p) {
        $this->plugin->cage->setPlayerCage($p, $this->types[$this->count]);
    }

    /**
     * Get the data for a menu
     *
     * @return array
     */
    public function getMenuData(): array {
        return $this->menuData;
    }
}