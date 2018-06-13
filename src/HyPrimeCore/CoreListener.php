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

namespace HyPrimeCore;

use HyPrimeCore\cloaks\CloakManager;
use HyPrimeCore\utils\Utils;
use larryTheCoder\events\PlayerJoinArenaEvent;
use larryTheCoder\SkyWarsPE;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\RemoteServerCommandEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\Player;
use pocketmine\Server;

class CoreListener implements Listener {

    const VERSION_COMMANDS = ["version", "ver", "about"];

    /** @var CoreMain */
    private $plugin;

    public function __construct(CoreMain $plugin) {
        $this->plugin = $plugin;
    }

    public function onPlayerJoin(PlayerJoinEvent $event) {
        $p = $event->getPlayer();
        $event->setJoinMessage("");

        $p->sendMessage("§7[----------------------------------------]");
        foreach ($this->plugin->getMessage($p, 'login-message') as $msg) {
            $msg = Utils::center(str_replace(["{PLAYER}", "{ONLINE}"], [$p->getName(), count(Server::getInstance()->getOnlinePlayers())], $msg));
            $p->sendMessage("- " . $msg);
        }
        $p->sendMessage("§7[----------------------------------------]");

        $this->plugin->justJoined[strtolower($p->getName())] = true;
        $this->plugin->idlingTime[strtolower($p->getName())] = microtime(true); // Setup time
        Server::getInstance()->getLogger()->info($this->plugin->getPrefix() . "§7Player §a{$p->getName()} §7logged in with language: §a{$p->getLocale()}");
    }

    public function onPlayerLeave(PlayerQuitEvent $e) {
        $p = $e->getPlayer();
        $e->setQuitMessage("");

        unset($this->plugin->idlingTime[strtolower($p->getName())]);
    }

    public function onPlayerMove(PlayerMoveEvent $e) {
        $p = $e->getPlayer();

        // TODO: check if the player trying to annoy the server
        $this->plugin->idlingTime[strtolower($p->getName())] = microtime(true);
    }

    /**
     * @param PlayerCommandPreprocessEvent $ev
     *
     * @priority LOWEST
     */
    public function onPlayerCommandPreProcess(PlayerCommandPreprocessEvent $ev) {
        if ($ev->isCancelled()) return;
        if (in_array(substr($ev->getMessage(), 1), self::VERSION_COMMANDS) && !$ev->isCancelled()) {
            $ev->setCancelled();
            CoreMain::sendVersion($ev->getPlayer());
        }
    }

    /**
     * @param ServerCommandEvent $ev
     *
     * @priority LOWEST
     */
    public function onServerCommand(ServerCommandEvent $ev) {
        if ($ev->isCancelled()) return;
        if (Utils::in_arrayi($ev->getCommand(), self::VERSION_COMMANDS) && !$ev->isCancelled()) {
            $ev->setCancelled();
            CoreMain::sendVersion($ev->getSender());
        }
    }

    /**
     * @param RemoteServerCommandEvent $ev
     *
     * @priority LOWEST
     */
    public function onRemoteServerCommand(RemoteServerCommandEvent $ev) {
        if ($ev->isCancelled()) return;
        if (Utils::in_arrayi($ev->getCommand(), self::VERSION_COMMANDS) && !$ev->isCancelled()) {
            $ev->setCancelled();
            CoreMain::sendVersion($ev->getSender());
        }
    }


    /**
     * @param PlayerJoinArenaEvent $event
     * @priority LOW
     */
    public function onPlayerJoinArena(PlayerJoinArenaEvent $event) {
        $p = $event->getPlayer();

        $p->setAllowFlight(false);
        CloakManager::unequipCloak($p);
    }

    /**
     * @param EntityDamageEvent $ev
     * @priority MONITOR
     */
    public function onPlayerDamaged(EntityDamageEvent $ev) {
        $p = $ev->getEntity();
        // Here is the place where the player can kill in arena
        $arena = SkyWarsPE::getInstance()->getArenaManager()->getPlayerArena($p);
        if ($p instanceof Player && $arena === null) {
            $ev->setCancelled();
        }
    }

    /**
     * @param PlayerDeathEvent $e
     * @priority MONITOR
     */
    public function onPlayerDeath(PlayerDeathEvent $e) {
        $p = $e->getPlayer();
        // Here is the place where the player can kill in arena
        $arena = SkyWarsPE::getInstance()->getArenaManager()->getPlayerArena($p);
        if ($p instanceof Player && $arena !== null) {
            if ($e->isCancelled()) {
                return;
            }
            if ($arena->getPlayerMode($p) === 0) {
                // TODO: Various silly messages
            }
        } else {
            // Not in arena? Cancel them
            $e->setCancelled();
        }
    }
}