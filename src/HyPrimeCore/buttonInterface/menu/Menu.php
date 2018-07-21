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


use HyPrimeCore\player\FakePlayer;
use pocketmine\Player;

abstract class Menu {

    const INTERACT_CLOAK_MENU = 0;
    const INTERACT_KIT_MENU = 1;
    const INTERACT_CAGES_MENU = 2;

    public abstract function getInteractId(): int;

    public static function getMenuName(int $id): string {
        switch ($id) {
            case self::INTERACT_CLOAK_MENU:
                return "Cloak";
            case self::INTERACT_KIT_MENU:
                return "Kit";
            case self::INTERACT_CAGES_MENU:
                return "Cages";
            default:
                return "Unknown";
        }
    }

    public static function getMenu(Player $p, int $id): ?Menu {
        switch ($id) {
            case self::INTERACT_CLOAK_MENU:
                return new CloakMenu($p);
            case self::INTERACT_KIT_MENU:
                return new KitMenu($p);
            case self::INTERACT_CAGES_MENU:
                return new CageMenu($p);
            default:
                return null;
        }
    }

    /**
     * Get the next menu for player
     */
    public abstract function getNextMenu();

    /**
     * Get the previous menu for player
     */
    public abstract function getPrevMenu();

    /**
     * Executed when a player select the button
     */
    public abstract function onPlayerSelect();

    /**
     * Get the data for a menu
     *
     * @return array
     */
    public abstract function getMenuData(): array;

    /**
     * Get the data for a menu
     *
     * @param FakePlayer $player
     * @param bool $cleanup
     * @return array
     */
    public abstract function updateNPC(FakePlayer $player, bool $cleanup);
}