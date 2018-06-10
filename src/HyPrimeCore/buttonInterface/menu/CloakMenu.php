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

use HyPrimeCore\cloaks\CloakManager;
use HyPrimeCore\cloaks\type\CloakType;
use HyPrimeCore\CoreMain;
use pocketmine\Player;

class CloakMenu extends Menu {

    /** @var int[] */
    private $player = [];
    /** @var string[] */
    private $types = ["Firerings", "Firewings", "Frosty", "Superhero", "Scanner", "Shaman"];

    public function getInteractId(): int {
        return self::INTERACT_CLOAK_MENU;
    }

    /**
     * Get the next menu for player
     *
     * @param Player $p
     * @return string[]
     */
    public function getNextMenu(Player $p): array {
        if (!isset($this->player[$p->getName()])) {
            return [];
        }
        if (count($this->types) > $this->player[$p->getName()]) {
            $this->player[$p->getName()] = 0;
        }
        $id = $this->player[$p->getName()]++;
        if (!$p->hasPermission(CloakType::getCloakById(null, $id)->getPermissionNode())) {
            $msg = CoreMain::get()->getMessage($p, 'error.buy-site');
        } else {
            $msg = "";
        }
        return [$this->types[$id] => $msg];
    }

    /**
     * Get the previous menu for player
     *
     * @param Player $p
     * @return string[]
     */
    public function getPrevMenu(Player $p): array {
        if (!isset($this->player[$p->getName()])) {
            return [];
        }
        if (count($this->types) < $this->player[$p->getName()]) {
            $this->player[$p->getName()] = count($this->types);
        }
        $id = $this->player[$p->getName()]--;
        if (!$p->hasPermission(CloakType::getCloakById(null, $id)->getPermissionNode())) {
            $msg = CoreMain::get()->getMessage($p, 'error.buy-site');
        } else {
            $msg = "";
        }
        return [$this->types[$id] => $msg];
    }

    public function onSelectedMenu(Player $p) {
        $this->player[$p->getName()] = 0;
    }

    public function onReturnMenu(Player $p) {
        unset($this->player[$p->getName()]);
    }

    public function onPlayerSelect(Player $p) {
        if (!isset($this->player[$p->getName()])) {
            return;
        }
        $id = $this->player[$p->getName()];
        if (!$p->hasPermission(CloakType::getCloakById(null, $id)->getPermissionNode())) {
            $p->sendMessage(CoreMain::get()->getPrefix() . CoreMain::get()->getMessage($p, 'error.buy-site'));
            return;
        }
        CloakManager::equipCloak($p, $id);
        $p->sendMessage(CoreMain::get()->getMessage($p, 'panel.cloak-selected', ["{CLOAK}" => $this->types[$id]]));
    }
}