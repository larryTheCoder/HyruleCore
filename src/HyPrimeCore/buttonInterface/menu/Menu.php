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


use pocketmine\Player;

abstract class Menu {

    const INTERACT_CLOAK_MENU = 0;
    const INTERACT_KIT_MENU = 1;
    const INTERACT_CAGES_MENU = 2;

    public abstract function getInteractId(): int;

    /**
     * Get the next menu for player
     *
     * @param Player $p
     * @return string[]
     */
    public abstract function getNextMenu(Player $p): array;

    /**
     * Get the previous menu for player
     *
     * @param Player $p
     * @return string[]
     */
    public abstract function getPrevMenu(Player $p): array;

    /**
     * Executed when a player clicks on the menu
     *
     * @param Player $p
     */
    public abstract function onSelectedMenu(Player $p);

    /**
     * Player returned to home or main menu
     *
     * @param Player $p
     */
    public abstract function onReturnMenu(Player $p);

    /**
     * Executed when a player select the button
     *
     * @param Player $p
     */
    public abstract function onPlayerSelect(Player $p);

}