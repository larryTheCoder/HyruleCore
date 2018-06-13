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

use HyPrimeCore\broadcast\BroadcastingSystem;
use HyPrimeCore\buttonInterface\ButtonInterface;
use HyPrimeCore\formAPI\FormAPI;
use HyPrimeCore\kits\KitInjectionModule;
use HyPrimeCore\panel\Panel;
use HyPrimeCore\player\PlayerData;
use HyPrimeCore\tasks\IdleCheckTask;
use HyPrimeCore\utils\Settings;
use HyPrimeCore\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

/**
 * This is the class for the Main-Prime-Core
 * for Hyrule Network. Rev#201-394-675d
 *
 * @package HPrimeCore
 */
class CoreMain extends PluginBase {

    const CONFIG_VERSION = 2;
    const OS = ["unknown", "Android", "iOS", "MacOS", "FireOS", "GearVR", "HoloLens", "Windows10", "Windows", "Dedicated", "Orbis", "NX"];

    /** @var CoreMain */
    private static $instance;
    /** @var int[] */
    public $idlingTime = [];
    /** @var PlayerData[] */
    private $data;
    /** @var FormAPI */
    private $formAPI;
    /** @var Panel */
    private $panel;
    /** @var bool[] */
    public $justJoined = [];
    /** @var Player[] */
    public $fly = [];
    /** @var ButtonInterface */
    private $interface;

    public static function sendVersion(CommandSender $sender) {
        $sender->sendMessage("§e--- HyruleNetwork (C) 2015-2018 ---");
        $sender->sendMessage("§6Owner: §dAlair069");
        $sender->sendMessage("§6Server Manager: §dMrPotato101");
        $sender->sendMessage("§6Head Management: §d@larryTheCoder");
        $sender->sendMessage("§3HyPrimeCore §aREV#201-394-675d §c(PRIVATE)");
        $sender->sendMessage("§3SkyWarsForPE §av1.9.8-Maya §c(PRIVATE)");
    }

    public static function get() {
        return CoreMain::$instance;
    }

    public function onLoad() {
        CoreMain::$instance = $this;
        $this->getServer()->getLogger()->info("§eStarting to load HyruleCore...");
        Utils::ensureDirectory($this);
        $this->saveResource("config.yml", true);
        $this->saveResource("language/en_US.yml", true);
        $this->saveResource("language/pt_BR.yml");
        $cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        if ($cfg->get("config-version") !== CoreMain::CONFIG_VERSION) {
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config.yml.old");
            $this->saveResource("config.yml");
        }
        Settings::load(new Config($this->getDataFolder() . "config.yml", Config::YAML));
        $this->getServer()->getLogger()->info($this->getPrefix() . "§7Loaded languages");
    }

    public function getPrefix() {
        return Settings::$prefix;
    }

    public function onEnable() {
        $this->getServer()->getLogger()->info($this->getPrefix() . "§aStarting booting sequence 0xFFFFFF");
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new BroadcastingSystem($this), Settings::$messageInterval * 20);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new IdleCheckTask($this), 20);

        new KitInjectionModule($this);
        $this->interface = new ButtonInterface($this);
        $this->formAPI = new FormAPI($this);
        $this->panel = new Panel($this);

        $this->getServer()->getPluginManager()->registerEvents(new CoreListener($this), $this);
    }

    public function getFormAPI() {
        return $this->formAPI;
    }

    public function savePlayerData(Player $p, PlayerData $data) {
        if (isset($this->data[$p->getName()])) {
            unset($this->data[$p->getName()]);
        }
        $this->data[$p->getName()] = $data;
    }

    public function getPlayerData(Player $p) {
        if (isset($this->data[$p->getName()])) {
            return $this->data[$p->getName()];
        }
        $data = new PlayerData();
        $this->data[$p->getName()] = $data;
        return $data;
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
        switch (strtolower($cmd->getName())) {
            case "cloak":
                if (!($sender instanceof Player)) {
                    $sender->sendMessage("Please use this command in-game");
                    break;
                }
                $this->getPanel()->showCloakConfiguration($sender);
                break;
            case "fly":
                if (!($sender instanceof Player)) {
                    $sender->sendMessage("Please use this command in-game");
                    break;
                }
                if (!$sender->hasPermission("ability.fly")) {
                    $sender->sendMessage($this->getMessage($sender, "error.no-permission"));
                    break;
                }

                if (isset($this->fly[$sender->getName()])) {
                    $sender->setAllowFlight(false);
                    $sender->sendMessage($this->getPrefix() . $this->getMessage($sender, "general.flight-disabled"));
                    unset($this->fly[$sender->getName()]);
                } else {
                    $sender->setAllowFlight(true);
                    $sender->sendMessage($this->getPrefix() . $this->getMessage($sender, "general.flight-enabled"));
                    $this->fly[$sender->getName()] = [$sender, true];
                }
                break;
            case "setupinterface":
                if (!($sender instanceof Player)) {
                    $sender->sendMessage("Please use this command in-game");
                    break;
                }
                $this->interface->setupInterface($sender);
                break;
        }
        return true;
    }

    public function getPanel() {
        return $this->panel;
    }

    /**
     * Get the translation for the user.
     * Support client localization
     *
     * @param Player $p
     * @param $key
     * @param mixed ...$replacement
     * @return mixed
     */
    public function getMessage(?Player $p, $key, ...$replacement) {
        if ($p === null) {
            if (!$message = (new Config($this->getDataFolder() . "language/en_US.yml", Config::YAML))->getNested($key)) {
                $this->getLogger()->warning("Message $key not found.");
                $message = "";
            }
        } else {
            switch (strtolower($p->getLocale())) {
                case "en_us":
                    $locale = new Config($this->getDataFolder() . "language/en_US.yml", Config::YAML);
                    if (!$message = $locale->getNested($key)) {
                        $this->getLogger()->warning("Message $key not found.");
                        $message = "";
                    }
                    break;
                case "pt_pt":
                case "pc_pt":
                case "pt_br":
                    $locale = new Config($this->getDataFolder() . "language/pt_BR.yml", Config::YAML);
                    if (!$message = $locale->getNested($key)) {
                        $this->getLogger()->warning("Message $key not found.");
                        $message = "";
                    }
                    break;
                default:
                    $locale = new Config($this->getDataFolder() . "language/en_US.yml", Config::YAML);
                    if (!$message = $locale->getNested($key)) {
                        $this->getLogger()->warning("Message $key not found.");
                        $message = "";
                    }
                    break;
            }
        }
        if ($message === "") {
            $locale = new Config($this->getDataFolder() . "language/en_US.yml", Config::YAML);
            if (!$message = $locale->getNested($key)) {
                $this->getLogger()->warning("$key not found in default locale.");
                $message = "";
            }
        }
        foreach ($replacement as $index => $value) {
            $message = str_replace('{' . $index . '}', $value, $message);
        }
        return $message;
    }
}