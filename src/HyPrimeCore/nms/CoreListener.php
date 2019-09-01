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

namespace HyPrimeCore\nms;

use HyPrimeCore\CoreMain;
use HyPrimeCore\utils\Utils;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;

class CoreListener implements Listener {

	/** @var CoreMain */
	private $plugin;
	/** @var string[] */
	private $whitelists;

	public function __construct(CoreMain $plugin){
		$this->plugin = $plugin;
		$this->whitelists = ["EndreEndi", "larryZ00p"];
	}

	public function onPlayerJoin(PlayerJoinEvent $event){
		$p = $event->getPlayer();
		$event->setJoinMessage("");

		$p->sendMessage("§7[----------------------------------------]");
		foreach($this->plugin->getMessage($p, 'login-message') as $msg){
			$msg = Utils::center(str_replace(["{PLAYER}", "{ONLINE}"], [$p->getName(), count(Server::getInstance()->getOnlinePlayers())], $msg));
			$p->sendMessage("- " . $msg);
		}
		$p->sendMessage("§7[----------------------------------------]");
		$p->sendMessage($this->plugin->getPrefix() . $this->plugin->getMessage($p, 'language-select', ['LANGUAGE' => $p->getLocale()]));

		$this->plugin->buffer[strtolower($p->getName())] = time();
		$this->plugin->justJoined[strtolower($p->getName())] = true;
		$this->plugin->idlingTime[strtolower($p->getName())] = microtime(true); // Setup time
		Server::getInstance()->getLogger()->info($this->plugin->getPrefix() . "§7Player §a{$p->getName()} §7logged in with language: §a{$p->getLocale()}");
	}

	public function onPlayerLeave(PlayerQuitEvent $e){
		$p = $e->getPlayer();
		$e->setQuitMessage("");

		if($this->plugin->getPlayerData($p)->getCloakData() !== null){
			$this->plugin->getPlayerData($p)->setCurrentCloak(null);
		}
		unset($this->plugin->idlingTime[strtolower($p->getName())]);
	}

	/**
	 * Handle block break events, disallow those who tries to fuck this server
	 * from breaking and placing weird blocks in this server.
	 *
	 * @priority MONITOR
	 * @param BlockBreakEvent $e
	 */
	public function onPlayerBreak(BlockBreakEvent $e){
		if(isset($this->whitelists[strtolower($e->getPlayer()->getName())])){
			return;
		}
		$e->setCancelled();
	}

	/**
	 * Handle block break events, disallow those who tries to fuck this server
	 * from breaking and placing weird blocks in this server.
	 *
	 * @priority MONITOR
	 * @param BlockPlaceEvent $e
	 */
	public function onPlayerPlace(BlockPlaceEvent $e){
		if(isset($this->whitelists[strtolower($e->getPlayer()->getName())])){
			return;
		}
		$e->setCancelled();
	}

	public function onPlayerMove(PlayerMoveEvent $e){
		$p = $e->getPlayer();

		// TODO: check if the player trying to annoy the server
		$this->plugin->idlingTime[strtolower($p->getName())] = microtime(true);
	}
}