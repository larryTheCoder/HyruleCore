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
use HyPrimeCore\cloaks\ParticleCloak;
use HyPrimeCore\cloaks\type\CloakType;
use HyPrimeCore\CoreMain;
use HyPrimeCore\player\FakePlayer;
use pocketmine\Player;

class CloakMenu extends Menu {

    /** @var int */
    private $count = 0;
    /** @var Player */
    private $player;
    /** @var ParticleCloak */
    private $cloak;

    public function __construct(Player $p) {
        $this->player = $p;
    }

    public function getInteractId(): int {
        return self::INTERACT_CLOAK_MENU;
    }

    /**
     * Get the next menu for player
     */
    public function getNextMenu() {
        if ($this->count >= count(CloakType::getAll()) - 1) {
            $this->count = 0;
        } else {
            $this->count++;
        }
    }

    /**
     * Get the previous menu for player
     */
    public function getPrevMenu() {
        if ($this->count <= 0) {
            $this->count = count(CloakType::getAll()) - 1;
        } else {
            $this->count--;
        }
    }

    public function onPlayerSelect() {
        $id = $this->count;
        if (!$this->player->hasPermission(CloakType::getCloakPermission($id))) {
            $this->player->sendMessage(CoreMain::get()->getPrefix() . CoreMain::get()->getMessage($this->player, 'error.buy-site'));
            return;
        }
        CloakManager::equipCloak($this->player, $id);
        $msg = str_replace("{CLOAK}", CloakType::getCloakName($id), CoreMain::get()->getMessage($this->player, 'panel.cloak-selected'));
        $this->player->sendMessage(CoreMain::get()->getPrefix() . $msg);
    }

    /**
     * Get the data for a menu
     *
     * @return array
     */
    public function getMenuData(): array {
        $data['cloak'] = true;
        if (!$this->player->hasPermission(CloakType::getCloakPermission($this->count))) {
            $data['available'] = false;
        } else {
            $data['available'] = true;
        }
        $data['name'] = CloakType::getCloakName($this->count);
        return $data;
    }

    /**
     * Update the NPC interface with player
     *
     * @param FakePlayer $player
     * @param bool $cleanup
     * @return void
     */
    public function updateNPC(FakePlayer $player, bool $cleanup) {
        if (!isset($this->cloak)) {
            $this->cloak = CloakType::getCloakById($player, $this->count); // It will start
            return;
        }
        // Clear the cloak first
        $this->cloak->clear();
        unset($this->cloak);
        // Then restart again
        $this->cloak = CloakType::getCloakById($player, $this->count);
    }
}