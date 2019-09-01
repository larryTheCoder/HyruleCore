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

namespace HyPrimeCore\buttonInterface;

use HyPrimeCore\buttonInterface\item\Button;
use HyPrimeCore\buttonInterface\item\ButtonStone;
use HyPrimeCore\buttonInterface\item\WoodenButton;
use HyPrimeCore\buttonInterface\menu\Menu;
use HyPrimeCore\CoreMain;
use HyPrimeCore\event\ButtonPushEvent;
use HyPrimeCore\player\FakePlayer;
use HyPrimeCore\utils\Utils;
use onebone\economyapi\EconomyAPI;
use pocketmine\block\BlockFactory;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\level\Location;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;

class ButtonInterface extends Task implements Listener {

	/** @var Menu[] */
	private $menuType = []; // Choose a menu
	/** @var int[] */
	private $currentPath = []; // The path of the menu (chosen and not chosen)
	/** @var array */
	private $defaultMessage;
	/** @var Location */
	private $buttonNext, $buttonSelect, $buttonPrev, $buttonBack, $buttonHome, $puppetLoc;
	/** @var Config */
	private $kit;
	/** @var int[] */
	private $setters = [];
	/** @var int[] */
	private $mode;
	/** @var FloatingTextParticle[][]|FloatingTextParticle[][][] */
	private $textInteract = [];
	/** @var FakePlayer[] */
	private $puppet = [];

	public function __construct(CoreMain $plugin){
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
		$this->kit = new Config($plugin->getDataFolder() . "kits.yml", Config::YAML);
		BlockFactory::registerBlock(new ButtonStone(), true);
		BlockFactory::registerBlock(new WoodenButton(), true);
		$this->buttonNext = Utils::parsePosition($this->kit->getNested('interface.button-next', ""));
		$this->buttonSelect = Utils::parsePosition($this->kit->getNested('interface.button-choose', ""));
		$this->buttonPrev = Utils::parsePosition($this->kit->getNested('interface.button-prev', ""));
		$this->buttonBack = Utils::parsePosition($this->kit->getNested('interface.button-back', ""));
		$this->buttonHome = Utils::parsePosition($this->kit->getNested('interface.button-home', ""));
		$this->puppetLoc = Utils::parseLocation($this->kit->getNested('interface.puppet-pos', ""));
		$plugin->getScheduler()->scheduleRepeatingTask($this, 4);
	}

	public function setupInterface(Player $p){
		$this->setters[$p->getName()] = $this->mode[strtolower($p->getName())] = 0;
		$p->sendMessage("Break another button for previous button. (Previous)");
	}

	/**
	 * @param BlockBreakEvent $event
	 */
	public function onPlayerBreak(BlockBreakEvent $event){
		$p = $event->getPlayer();
		$b = $event->getBlock();
		if(isset($this->setters[$p->getName()])){
			$event->setCancelled();

			switch($this->mode[strtolower($p->getName())]){
				case 0:
					if(!($b instanceof Button)){
						$p->sendMessage("That is not a button.");

						return;
					}
					$this->buttonPrev = $b->asPosition();
					$this->kit->setNested("interface.button-prev", Utils::encodePosition($b));
					$this->mode[strtolower($p->getName())]++;
					$p->sendMessage("Break another button for selection button. (Select)");
					break;
				case 1:
					if(!($b instanceof Button)){
						$p->sendMessage("That is not a button.");

						return;
					}
					$this->buttonSelect = $b->asPosition();
					$this->kit->setNested("interface.button-choose", Utils::encodePosition($b));
					$this->mode[strtolower($p->getName())]++;
					$p->sendMessage("Break another button for next button. (Next)");
					break;
				case 2:
					if(!($b instanceof Button)){
						$p->sendMessage("That is not a button.");

						return;
					}
					$this->buttonNext = $b->asPosition();
					$this->kit->setNested("interface.button-next", Utils::encodePosition($b));
					$this->mode[strtolower($p->getName())]++;
					$p->sendMessage("Break another button for back button. (Back)");
					break;
				case 3:
					if(!($b instanceof Button)){
						$p->sendMessage("That is not a button.");

						return;
					}
					$this->buttonBack = $b->asPosition();
					$this->kit->setNested("interface.button-back", Utils::encodePosition($b));
					$this->mode[strtolower($p->getName())]++;
					$p->sendMessage("Break another button for home button. (Home)");
					break;
				case 4:
					if(!($b instanceof Button)){
						$p->sendMessage("That is not a button.");

						return;
					}
					$this->buttonHome = $b->asPosition();
					$this->kit->setNested("interface.button-home", Utils::encodePosition($b));
					$p->sendMessage("Now stand on the location for the puppet, then break any blocks.");
					$this->mode[strtolower($p->getName())]++;
					break;
				case 5:
					$this->puppetLoc = $p->asLocation();
					$this->kit->setNested("interface.puppet-pos", Utils::encodeLocation($p));
					$p->sendMessage("Successfully setting the button GUI");
					unset($this->mode[strtolower($p->getName())]);
					unset($this->setters[$p->getName()]);
					break;
			}
			$this->kit->save();
		}
	}

	/**
	 * @param PlayerJoinEvent $ev
	 */
	public function onPlayerJoin(PlayerJoinEvent $ev){
		$this->menuType[$ev->getPlayer()->getName()] = null;
		$this->currentPath[$ev->getPlayer()->getName()] = Menu::INTERACT_CLOAK_MENU;
		if($this->puppetLoc === null){
			return;
		}

		// NPC Point
		$puppet = new FakePlayer($this->puppetLoc, $ev->getPlayer(), $ev->getPlayer()->getLevel());
		$ev->getPlayer()->getLevel()->addParticle($puppet, [$ev->getPlayer()]);
		$this->puppet[$ev->getPlayer()->getName()] = $puppet;
		// NPC Point
		$this->updateText($ev->getPlayer());
	}

	private function updateText(Player $p){
		if(!isset($this->defaultMessage[$p->getName()])){
			// These are non update particles.
			$select = CoreMain::get()->getMessage($p, "interface.select");
			$back = CoreMain::get()->getMessage($p, "interface.back");
			$home = CoreMain::get()->getMessage($p, "interface.home");
			$p->getLevel()->addParticle($pk[] = new FloatingTextParticle($this->buttonPrev->add(0.5, 0, 0.5), "", "§a§l<<"), [$p]);
			$p->getLevel()->addParticle($pk[] = new FloatingTextParticle($this->buttonNext->add(0.5, 0, 0.5), "", "§a§l>>"), [$p]);
			$p->getLevel()->addParticle($pk[] = new FloatingTextParticle($this->buttonSelect->add(0.5, 0, 0.5), "", $select), [$p]);
			$p->getLevel()->addParticle($pk[] = new FloatingTextParticle($this->buttonBack->add(0.5, 0, 0.5), "", $back), [$p]);
			$p->getLevel()->addParticle($pk[] = new FloatingTextParticle($this->buttonHome->add(0.5, 0, 0.5), "", $home), [$p]);
			$this->defaultMessage[$p->getName()] = $pk;
		}else{
			foreach($this->defaultMessage[$p->getName()] as $item){
				$p->getLevel()->addParticle($item, [$p]);
			}
		}

		$pos1 = $this->buttonSelect->add(0.5, 1.6, 0.5);
		$pos2 = $this->buttonSelect->add(0.5, 1.2, 0.5);
		$pos3 = $this->buttonSelect->add(0.5, -0.5, 0.5);
		$pos4 = $this->buttonSelect->add(0.5, -0.8, 0.5);
		if($this->menuType[$p->getName()] !== null){
			// Temporarily removes the main menu name
			$particle1 = $this->textInteract[$p->getName()]['object-name'];
			$particle1->setInvisible();
			$p->getLevel()->addParticle($particle1, [$p]);
			$this->textInteract[$p->getName()]['object-name'] = $particle1;

			// Get the menu type and their data
			$menu = $this->menuType[$p->getName()];
			$data = $menu->getMenuData();
			$menu->updateNPC($this->puppet[$p->getName()], false);

			$this->reset($p);
			if(isset($data['kit'])){
				// This is kit menu (Custom array vars)
				$p1 = new FloatingTextParticle($pos1, "", "§a" . $data['name']);
				$p2 = new FloatingTextParticle($pos2, "", CoreMain::get()->getMessage($p, "interface.selection-1"));
				$p->getLevel()->addParticle($p1, [$p]);
				$p->getLevel()->addParticle($p2, [$p]);
				$this->textInteract[$p->getName()]['object-info']['name'] = $p1;
				$this->textInteract[$p->getName()]['object-info']['about'] = $p2;
				// Money / Prices
				$replace1 = ["{VALUE}", "{BALANCE}"];
				$replace2 = [$data['payment'], EconomyAPI::getInstance()->myMoney($p)];
				$msg1 = str_replace($replace1, $replace2, CoreMain::get()->getMessage($p, "interface.price-interface"));
				$msg2 = str_replace($replace1, $replace2, CoreMain::get()->getMessage($p, "interface.balance-interface"));
				$p3 = new FloatingTextParticle($pos3, "", "§a" . $msg1);
				$p4 = new FloatingTextParticle($pos4, "", "§a" . $msg2);
				$p->getLevel()->addParticle($p3, [$p]);
				$p->getLevel()->addParticle($p4, [$p]);
				$this->textInteract[$p->getName()]['object-info']['OBJ-1'] = $p3;
				$this->textInteract[$p->getName()]['object-info']['OBJ-2'] = $p4;
			}elseif(isset($data['cloak'])){
				// Another damn custom array
				$p1 = new FloatingTextParticle($pos1, "", "§a" . $data['name']);
				$p2 = new FloatingTextParticle($pos2, "", CoreMain::get()->getMessage($p, "interface.selection-2"));
				$p->getLevel()->addParticle($p1, [$p]);
				$p->getLevel()->addParticle($p2, [$p]);
				$this->textInteract[$p->getName()]['object-info']['name'] = $p1;
				$this->textInteract[$p->getName()]['object-info']['about'] = $p2;
				if(!$data['available']){
					$p3 = new FloatingTextParticle($pos3, "", CoreMain::get()->getMessage($p, "interface.buy-site"));
				}else{
					$p3 = new FloatingTextParticle($pos3, "", CoreMain::get()->getMessage($p, "interface.available"));
				}
				$p->getLevel()->addParticle($p3, [$p]);
				$this->textInteract[$p->getName()]['object-info']['info'] = $p3;
			}elseif(isset($data['cage'])){
				// This is cage menu (StackOverBytes)
				$p1 = new FloatingTextParticle($pos1, "", "§a" . $data['name']);
				$p2 = new FloatingTextParticle($pos2, "", CoreMain::get()->getMessage($p, "interface.selection-3"));
				$p->getLevel()->addParticle($p1, [$p]);
				$p->getLevel()->addParticle($p2, [$p]);
				$this->textInteract[$p->getName()]['object-info']['name'] = $p1;
				$this->textInteract[$p->getName()]['object-info']['about'] = $p2;
				// Money / Prices
				$replace1 = ["{VALUE}", "{BALANCE}"];
				$replace2 = [$data['payment'], EconomyAPI::getInstance()->myMoney($p)];
				$msg1 = str_replace($replace1, $replace2, CoreMain::get()->getMessage($p, "interface.price-interface"));
				$msg2 = str_replace($replace1, $replace2, CoreMain::get()->getMessage($p, "interface.balance-interface"));
				$p3 = new FloatingTextParticle($pos3, "", "§a" . $msg1);
				$p4 = new FloatingTextParticle($pos4, "", "§a" . $msg2);
				$p->getLevel()->addParticle($p3, [$p]);
				$p->getLevel()->addParticle($p4, [$p]);
				$this->textInteract[$p->getName()]['object-info']['OBJ-1'] = $p3;
				$this->textInteract[$p->getName()]['object-info']['OBJ-2'] = $p4;
			}
		}else{
			$path = $this->currentPath[$p->getName()];
			$menuType = Menu::getMenuName($path);
			$msg = str_replace("{MENU}", $menuType, CoreMain::get()->getMessage($p, "interface.about-menu"));
			if(!isset($this->textInteract[$p->getName()]['object-name'])){
				$particle1 = new FloatingTextParticle($pos1, "", "§a" . $msg);
				$particle1->setInvisible(false);
				$p->getLevel()->addParticle($particle1, [$p]);
				$this->textInteract[$p->getName()]['object-name'] = $particle1;
			}else{
				$particle1 = $this->textInteract[$p->getName()]['object-name'];
				$particle1->setTitle("§a" . $msg);
				$particle1->setInvisible(false);
				$p->getLevel()->addParticle($particle1, [$p]);
				$this->textInteract[$p->getName()]['object-name'] = $particle1;
			}
		}
	}

	private function reset(Player $p){
		if(!isset($this->textInteract[$p->getName()]['object-info'])){
			return;
		}

		foreach($this->textInteract[$p->getName()]['object-info'] as $key => $val){
			$val->setInvisible();
			$p->getLevel()->addParticle($val, [$p]);
			unset($this->textInteract[$p->getName()]['object-info'][$key]);
		}
	}

	/**
	 * @param ButtonPushEvent $event
	 * @priority MONITOR
	 */
	public function onButtonPush(ButtonPushEvent $event){
		$p = $event->getPlayer();
		$menu = $this->menuType[$p->getName()];
		$path = $this->currentPath[$p->getName()];
		if($menu === null){
			if($event->getPos()->equals($this->buttonNext)){
				if($path === 2){
					$path = 0;
				}else{
					$path++;
				}
			}elseif($event->getPos()->equals($this->buttonPrev)){
				if($path === 0){
					$path = 2;
				}else{
					$path--;
				}
			}elseif($event->getPos()->equals($this->buttonSelect)){
				$menu = Menu::getMenu($p, $path);
				$menu->getNextMenu();
				$this->menuType[$p->getName()] = $menu;
			}else{
				// Back button? Its useless buddy, what you gonna to back of?
				return;
			}

			$this->currentPath[$p->getName()] = $path;
			$this->updateText($p);

			return;
		}
		if($event->getPos()->equals($this->buttonNext)){
			$menu->getNextMenu();
		}elseif($event->getPos()->equals($this->buttonPrev)){
			$menu->getPrevMenu();
		}elseif($event->getPos()->equals($this->buttonSelect)){
			$menu->onPlayerSelect();
		}else{
			foreach($this->textInteract[$p->getName()]['object-info'] as $key => $val){
				$val->setInvisible();
				$p->getLevel()->addParticle($val);
			}
			$this->menuType[$p->getName()]->updateNPC($this->puppet[$p->getName()], true);
			$this->menuType[$p->getName()] = null;
			$this->currentPath[$p->getName()] = $menu->getInteractId();
			$this->updateText($p);

			return;
		}
		$this->menuType[$p->getName()] = $menu;
		$this->updateText($p);
	}

	/**
	 * Actions to execute when run
	 *
	 * @param int $currentTick
	 *
	 * @return void
	 */
	public function onRun(int $currentTick){
		foreach($this->puppet as $playerName => $puppet){
			$p = Server::getInstance()->getPlayer($playerName);
			if($p === null || !$p->isOnline()){
				unset($this->puppet[$playerName]);
				continue;
			}
			if($puppet->distance($p) <= 5){
				$puppet->lookAt();
			}
		}
	}
}