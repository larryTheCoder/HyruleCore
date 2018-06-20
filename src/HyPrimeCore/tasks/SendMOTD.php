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
use pocketmine\scheduler\Task;

/**
 * Message of the day config.
 * This will be used to replace the MOTD plugin to support
 * The new API version of PocketMine-MP.
 *
 * @package HyPrimeCore\tasks
 */
class SendMOTD extends Task {

    /** @var CoreMain */
    private $plugin;
    /** @var int */
    private $line;

    public function __construct(CoreMain $plugin) {
        $this->plugin = $plugin;
        $this->line = -1;
        $this->plugin->getServer()->getLogger()->info($plugin->getPrefix() . "§7Starting motd messages");
    }

    /**
     * Actions to execute when run
     *
     * @param int $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick) {
        $getMOTD = $this->plugin->getConfig()->getNested("motd.message");
        if ($this->plugin->getConfig()->getNested("motd.shuffle") == true) {
            $msg = $getMOTD[mt_rand(0, count($getMOTD) - 1)];
            $this->plugin->getServer()->getNetwork()->setName($msg);
        } else {
            $this->line++;
            $msg = $getMOTD[$this->line];
            $this->plugin->getServer()->getNetwork()->setName($msg);
            if ($this->line === count($getMOTD) - 1) {
                $this->line = -1;
            }
        }
    }
}