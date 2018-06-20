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

namespace HyPrimeCore\tasks;

use HyPrimeCore\CoreMain;
use HyPrimeCore\utils\Settings;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class BroadcastingSystem extends Task {

    /** @var CoreMain */
    private $plugin;
    /** @var int[] */
    private $currentMessage = [];

    public function __construct(CoreMain $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * Actions to execute when run
     *
     * @param int $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick) {
        $pl = Server::getInstance()->getOnlinePlayers();

        foreach ($pl as $p) {
            // Just joined, cancel the first message
            if (isset($this->plugin->justJoined[strtolower($p->getName())])) {
                unset($this->plugin->justJoined[strtolower($p->getName())]);
                continue;
            }

            if (!isset($this->currentMessage[$p->getName()])) {
                $this->currentMessage[$p->getName()] = 0;
            }

            if ($this->currentMessage[$p->getName()] >= count($this->plugin->getMessage($p, 'broadcast'))) {
                $this->currentMessage[$p->getName()] = 0;
            }

            if (Settings::$messageRandom) {
                $this->currentMessage[$p->getName()] = rand(0, count($this->plugin->getMessage($p, 'broadcast')));
            }

            $array = $this->plugin->getMessage($p, 'broadcast');
            if ($p->getLevel()->getName() === "world") {
                if (Settings::$messagePrefix) {
                    $p->sendMessage(Settings::$prefix . $array[$this->currentMessage[$p->getName()]]);
                } else {
                    $p->sendMessage($array[$this->currentMessage[$p->getName()]]);
                }
            }
            $this->currentMessage[$p->getName()]++;
        }
    }
}