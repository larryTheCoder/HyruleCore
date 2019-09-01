<?php
/**
 * Adapted from the Wizardry License
 *
 * Copyright (c) 2015-2019 larryTheCoder and contributors
 *
 * Permission is hereby granted to any persons and/or organizations
 * using this software to copy, modify, merge, publish, and distribute it.
 * Said persons and/or organizations are not allowed to use the software or
 * any derivatives of the work for commercial use or any other means to generate
 * income, nor are they allowed to claim this software as their own.
 *
 * The persons and/or organizations are also disallowed from sub-licensing
 * and/or trademarking this software without explicit permission from larryTheCoder.
 *
 * Any persons and/or organizations using this software must disclose their
 * source code and have it publicly available, include this license,
 * provide sufficient credit to the original authors of the project (IE: larryTheCoder),
 * as well as provide a link to the original project.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,FITNESS FOR A PARTICULAR
 * PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
 * USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace HyPrimeCore\dependencies\Bounty;

use HyPrimeCore\CoreMain;
use HyPrimeCore\dependencies\PluginDispatch;
use onebone\economyapi\EconomyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\RemoteConsoleCommandSender;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class BountyHandler extends PluginDispatch {

	/** @var float[] */
	public $buffer;
	/** @var Config */
	private $config;
	/** @var \SQLite3 */
	private $database;
	/** @var Config */
	private $gameTimeCfg;

	/**
	 * Return the name of this dependencies that is being
	 * registered to the plugin core.
	 *
	 * @return string
	 */
	public function getName(): string{
		return "BountyHandler";
	}

	/**
	 * Starts the plugin dependency
	 */
	public function startDependency(): void{
		if(!file_exists($this->getDataFolder() . "config.yml")){
			@mkdir($this->getDataFolder());
			file_put_contents($this->getDataFolder() . "config.yml", $this->getResource("config.yml"));
		}

		$this->gameTimeCfg = new Config($this->getDataFolder() . "gameData.yml");
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		$this->database = new \SQLite3($this->getDataFolder() . "bounty.db");
		$this->database->exec("CREATE TABLE IF NOT EXISTS bounty (player TEXT PRIMARY KEY COLLATE NOCASE, money INT);");
	}

	public function shutdownDependency(): void{
		// TODO: Implement shutdownDependency() method.
	}

	public function onEntityDamage(EntityDamageEvent $event){
		$entity = $event->getEntity();
		if($entity instanceof Player){
			$player = $entity->getPlayer();
			if($this->config->get("bounty_stats") == 1 or $this->config->get("health_stats") == 1){
				$this->renderNametag($player);
			}
		}
	}

	public function renderNameTag(Player $player){
		$username = $player->getName();
		$lower = strtolower($username);
		$bounty = $this->getBountyMoney2($lower);
		if($this->config->get("bounty_stats") == 1 && $this->config->get("health_stats") != 1){
			$player->setNameTag("§a$username\n§eRecompensă pentru crimă: §6$bounty" . "$");
		}
		if($this->config->get("health_stats") == 1 && $this->config->get("bounty_stats") != 1){
			$player->setNameTag("§a$username §c" . $player->getHealth() . "§f/§c" . $player->getMaxHealth());
		}
		if($this->config->get("bounty_stats") == 1 && $this->config->get("health_stats") == 1){
			$player->setNameTag("§a$username §c" . $player->getHealth() . "§f/§c" . $player->getMaxHealth() . "\n§eRecompensă pentru crimă: §6$bounty" . "$");
		}
	}

	public function getBountyMoney2($play){
		if(!$this->bountyExists($play)){
			$i = 0;

			return $i;
		}
		$result = $this->database->query("SELECT * FROM bounty WHERE player = '$play';");
		$resultArr = $result->fetchArray(SQLITE3_ASSOC);

		return (int)$resultArr["money"];
	}

	public function bountyExists($playe){
		$result = $this->database->query("SELECT * FROM bounty WHERE player='$playe';");
		$array = $result->fetchArray(SQLITE3_ASSOC);

		return empty($array) == false;
	}

	public function onEntityRegainHealth(EntityRegainHealthEvent $event){
		$entity = $event->getEntity();
		if($entity instanceof Player){
			$player = $entity->getPlayer();
			if($this->config->get("bounty_stats") == 1 or $this->config->get("health_stats") == 1){
				$this->renderNametag($player);
			}
		}
	}

	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		if($this->config->get("bounty_stats") == 1 or $this->config->get("health_stats") == 1){
			$this->renderNametag($player);
		}
	}

	public function addBounty($player, $mon){
		if($this->bountyExists($player)){
			$stmt = $this->database->prepare("INSERT OR REPLACE INTO bounty (player, money) VALUES (:player, :money);");
			$stmt->bindValue(":player", $player);
			$stmt->bindValue(":money", $this->getBountyMoney($player) + $mon);
			$stmt->execute();
		}
		if(!$this->bountyExists($player)){
			$stmt = $this->database->prepare("INSERT OR REPLACE INTO bounty (player, money) VALUES (:player, :money);");
			$stmt->bindValue(":player", $player);
			$stmt->bindValue(":money", $mon);
			$stmt->execute();
		}
	}

	public function getBountyMoney($play){
		$result = $this->database->query("SELECT * FROM bounty WHERE player = '$play';");
		$resultArr = $result->fetchArray(SQLITE3_ASSOC);

		return (int)$resultArr["money"];
	}

	public function onPlayerLeave(PlayerQuitEvent $e){
		$name = strtolower($e->getPlayer()->getName());
		if(isset($this->buffer[$name])){
			$time = time() - $this->buffer[$name];
			if(!$this->gameTimeCfg->exists($name)) $this->gameTimeCfg->set($name, $time);
			else $this->gameTimeCfg->set($name, $time + $this->gameTimeCfg->get($name));
			$this->gameTimeCfg->save();
			unset($this->buffer[$name]);
		}
	}

	public function onDeath(PlayerDeathEvent $event){
		$cause = $event->getEntity()->getLastDamageCause();
		if($cause instanceof EntityDamageByEntityEvent){
			$player = $event->getEntity();
			$name = $player->getName();
			$nameUncased = strtolower($name);
			$killer = $cause->getDamager();
			if(!($killer instanceof Player)){
				return;
			}
			$name2 = $killer->getName();
			if($player instanceof Player){
				if($this->bountyExists($nameUncased)){
					$money = $this->getBountyMoney($nameUncased);
					$killer->sendMessage("§b[Coamnda]§a>§b Vei primi un premiu în valoare de... §6$money §bper jucator §a$name" . "§b!");
					EconomyAPI::getInstance()->addMoney($killer->getName(), $money);
					if($this->config->get("bounty_broadcast") == 1){
						Server::getInstance()->broadcastMessage("§b§l[Comanda]§a> §r§a$name2 §fTocmai am primit §6$money" . "$ §fpentru uciderea jucatorului: §a$name!");
					}
					if($this->config->get("bounty_fine") == 1){
						$perc = $this->config->get("fine_percentage");
						$fine = ($money * $perc) / 100;
						if(EconomyAPI::getInstance()->myMoney($player->getName()) > $fine){
							EconomyAPI::getInstance()->reduceMoney($player->getName(), $fine);
							$player->sendMessage("§b[BOUNTY]§a>§cAi luat §6$fine" . "$ §cpentru uciderea jucatorului! uciderea= $perc interes trebuie sa dea de victima!");
						}
						if(EconomyAPI::getInstance()->myMoney($player->getName()) <= $fine){
							EconomyAPI::getInstance()->setMoney($player->getName(), 0);
							$player->sendMessage("§b[BOUNTY]§a>§c Ati fost emise §6$fine" . "$ §cpentru faptul ca ai omorat! ucidere = $perc interes pentru tine, bine!");
						}
					}
					$this->deleteBounty($nameUncased);
				}
			}
		}
	}

	public function deleteBounty($pla){
		$this->database->query("DELETE FROM bounty WHERE player = '$pla';");
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
		switch(strtolower($cmd->getName())){
			case "status":
				if(isset($args[2])){
					if(!$sender->hasPermission('gametime.all.other')){
						$sender->sendMessage($this->getMessage($sender, "error.no-permission"));

						return true;
					}

					$name = $args[1];
					if(($p = Server::getInstance()->getPlayer($name)) !== null)
						$name = $p->getName();
					$time = explode(':', $this->getAllTime($name, '%H%:%i%:%s%'));
					if(count($time) != 3){
						$sender->sendMessage(TF::RED . 'No data about this player.');

						return true;
					}
					$sender->sendMessage(TF::GOLD . $name . "'s total time of the game: " . $time[0] . ' hor., ' . $time[1] . ' min., ' . $time[2] . ' sec.');
				}elseif($sender instanceof ConsoleCommandSender || $sender instanceof RemoteConsoleCommandSender){
					$sender->sendMessage(TF::RED . 'You cannot get duration of console sessions. But you can get duration of players sessions.');
				}else{
					if(!$sender->hasPermission('gametime.all.self')){
						$sender->sendMessage($this->getMessage($sender, "error.no-permission"));

						return true;
					}

					$name = $sender->getName();
					$time = explode(':', $this->getAllTime($name, '%H%:%i%:%s%'));

					$sender->sendMessage(TF::GOLD . 'Your total time of the game: ' . $time[0] . ' hor., ' . $time[1] . ' min., ' . $time[2] . ' sec.');
				}
				break;
		}

		return false;
	}

	private function getMessage(CommandSender $sender, string $string){
		return CoreMain::get()->getMessage($sender, $string);
	}

	public function getAllTime($name, $format = false){
		$name = strtolower($name);
		if(!isset($this->buffer[$name])){
			if(!$this->gameTimeCfg->exists($name)) return false;
			else $x = $this->gameTimeCfg->get($name);
		}else{
			if(!$this->gameTimeCfg->exists($name)) $x = time() - $this->buffer[$name];
			else $x = time() - $this->buffer[$name] + $this->gameTimeCfg->get($name);
		}
		if(!$format) return "$x";
		else return $this->getFormattedTime($x, $format);
	}

	private function getFormattedTime($a, $format){
		$d = $H = $i = 0;
		if(strpos($format, 'd') !== false){
			$d = floor($a / 86400);
		}
		if(strpos($format, 'H') !== false){
			$H = floor(($a - $d * 86400) / 3600);
		}
		if(strpos($format, 'i') !== false){
			$i = floor(($a - $d * 86400 - $H * 3600) / 60);
		}
		$s = $a - $d * 86400 - $H * 3600 - $i * 60;

		return str_replace(['%d%', '%H%', '%i%', '%s%'], [strlen($d) == 1 ? '0' . $d : $d, strlen($H) == 1 ? '0' . $H : $H, strlen($i) == 1 ? '0' . $i : $i, strlen($s) == 1 ? '0' . $s : $s], $format);
	}

	public function getCommands(): array{
		return [];
	}

	public function getSessionTime($name, $format = false){
		$name = strtolower($name);
		if(!isset($this->buffer[$name]))
			return false;
		$time = time() - $this->buffer[$name];
		if(!$format) return "$time";
		else return $this->getFormattedTime($time, $format);
	}

}