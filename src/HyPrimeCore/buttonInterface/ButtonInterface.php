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

namespace HyPrimeCore\buttonInterface;

use HyPrimeCore\buttonInterface\item\Button;
use HyPrimeCore\buttonInterface\item\ButtonStone;
use HyPrimeCore\buttonInterface\menu\CageMenu;
use HyPrimeCore\buttonInterface\menu\CloakMenu;
use HyPrimeCore\buttonInterface\menu\KitMenu;
use HyPrimeCore\buttonInterface\menu\Menu;
use HyPrimeCore\CoreMain;
use HyPrimeCore\event\ButtonPushEvent;
use HyPrimeCore\utils\Utils;
use pocketmine\block\BlockFactory;
use pocketmine\block\StoneButton;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\level\Location;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\particle\Particle;
use pocketmine\Player;
use pocketmine\utils\Config;

class ButtonInterface implements Listener {

    /** @var Menu[] */
    private $menuType = []; // Choose a menu
    /** @var int[] */
    private $currentPath = []; // The path of the menu (chosen and not chosen)
    /** @var Menu[] */
    private $menu = [];
    /** @var array */
    private $defaultMessage;
    /** @var Location */
    private $buttonNext, $buttonSelect, $buttonPrev, $buttonBack;
    /** @var Config */
    private $kit;
    /** @var int[] */
    private $setters = [];
    /** @var int[] */
    private $mode;
    /** @var FloatingTextParticle[][] */
    private $textInteract = [];

    public function __construct(CoreMain $plugin) {
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $this->kit = new Config($plugin->getDataFolder() . "kits.yml", Config::YAML);
        BlockFactory::registerBlock(new ButtonStone(), true);
        $this->menu[] = new CageMenu();
        $this->menu[] = new CloakMenu();
        $this->menu[] = new KitMenu();
        $this->buttonNext = Utils::parsePosition($this->kit->getNested('interface.button-next'));
        $this->buttonSelect = Utils::parsePosition($this->kit->getNested('interface.button-choose'));
        $this->buttonPrev = Utils::parsePosition($this->kit->getNested('interface.button-prev'));
        $this->buttonBack = Utils::parsePosition($this->kit->getNested('interface.button-back'));
    }

    public function setupInterface(Player $p) {
        $this->setters[$p->getName()] = $this->mode[strtolower($p->getName())] = 0;
        $p->sendMessage("Break another button for previous button. (Previous)");
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onPlayerBreak(BlockBreakEvent $event) {
        $p = $event->getPlayer();
        $b = $event->getBlock();
        if (isset($this->setters[$p->getName()])) {
            $event->setCancelled();
            if (!($b instanceof Button)) {
                $p->sendMessage("That is not a button.");
                return;
            }

            switch ($this->mode[strtolower($p->getName())]) {
                case 0:
                    $this->buttonPrev = $b->asPosition();
                    $this->kit->setNested("interface.button-prev", Utils::encodePosition($b));
                    $this->mode[strtolower($p->getName())]++;
                    $p->sendMessage("Break another button for selection button. (Select)");
                    break;
                case 1:
                    $this->buttonSelect = $b->asPosition();
                    $this->kit->setNested("interface.button-choose", Utils::encodePosition($b));
                    $this->mode[strtolower($p->getName())]++;
                    $p->sendMessage("Break another button for next button. (Next)");
                    break;
                case 2:
                    $this->buttonNext = $b->asPosition();
                    $this->kit->setNested("interface.button-next", Utils::encodePosition($b));
                    $this->mode[strtolower($p->getName())]++;
                    $p->sendMessage("Break another button for back button. (Back)");
                    break;
                case 3:
                    $this->buttonBack = $b->asPosition();
                    $this->kit->setNested("interface.button-back", Utils::encodePosition($b));
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
    public function onPlayerJoin(PlayerJoinEvent $ev) {
        $this->menuType[$ev->getPlayer()->getName()] = null;
        $this->currentPath[$ev->getPlayer()->getName()] = Menu::INTERACT_CLOAK_MENU;
        $this->updateText($ev->getPlayer());
    }

    public function onButtonPush(ButtonPushEvent $event) {
        echo "EVENT RUN\n";
        $p = $event->getPlayer();
        $menu = $this->menuType[$p->getName()];
        $path = $this->currentPath[$p->getName()];
        if ($menu === null) {
            if ($event->getPos()->equals($this->buttonNext)) {
                if ($path === 3) {
                    $path = 2;
                } else {
                    $path++;
                }
            } else if ($event->getPos()->equals($this->buttonPrev)) {
                if ($path === 0) {
                    $path = 2;
                } else {
                    $path--;
                }
            } else if ($event->getPos()->equals($this->buttonSelect)) {
                $menu = Menu::getMenu($path);
                $this->menuType[$p->getName()] = $menu;
            } else {
                // Back button? Its useless buddy, what you gonna to back of?
                return;
            }

            $this->currentPath[$p->getName()] = $path;
            $this->updateText($p);
            return;
        }
        if ($event->getPos()->equals($this->buttonNext)) {
            $this->currentPath[$p->getName()] = $menu->getNextMenu($p);
        } else if ($event->getPos()->equals($this->buttonPrev)) {
            $this->currentPath[$p->getName()] = $menu->getPrevMenu($p);
        } else if ($event->getPos()->equals($this->buttonSelect)) {
            $menu->onPlayerSelect($p);
        } else {
            $this->menuType[$p->getName()] = null;
            $this->currentPath[$p->getName()] = $menu->getInteractId();
            $this->updateText($p);
            return;
        }
        $this->menuType[$p->getName()] = $menu;
        $this->updateText($p);
    }

    private function updateText(Player $p) {
        if (!isset($this->defaultMessage[$p->getName()])) {
            // These are non update particles.
            $p->getLevel()->addParticle(new FloatingTextParticle($this->buttonPrev, "", "§a§l<<"), [$p]);
            $p->getLevel()->addParticle(new FloatingTextParticle($this->buttonNext, "", "§a§l>>"), [$p]);
            $p->getLevel()->addParticle(new FloatingTextParticle($this->buttonSelect, "", "§eSelect"), [$p]);
            $p->getLevel()->addParticle(new FloatingTextParticle($this->buttonBack, "", "§aBack"), [$p]);
            $this->defaultMessage[$p->getName()] = true;
        }
        if ($this->menuType[$p->getName()] !== null) {
            $menu = $this->menuType[$p->getName()];
            // Data[0] = Item name / Kit name / Object Name
            // Data[1][0] = Bool / Object should have a coins.
            // Data[1][1] = int | string / depends on Bool
            $data = $menu->getMenuData();
            $pos1 = $this->buttonSelect->add(0, 1, 0);
            if (!isset($this->textInteract[$p->getName()]['object-name'])) {
                $particle1 = new FloatingTextParticle($pos1, "", "§a" . $data[0]);
                $this->addPacketTo($p, $particle1);
                $this->textInteract[$p->getName()]['object-name'] = $particle1;
            } else {
                $particle1 = $this->textInteract[$p->getName()]['object-name'];
                $particle1->setTitle("§a" . $data[0]);
                $this->addPacketTo($p, $particle1);
                $this->textInteract[$p->getName()]['object-name'] = $particle1;
            }
        } else {
            $path = $this->currentPath[$p->getName()];
            $menuType = Menu::getMenuName($path);
            $pos1 = $this->buttonSelect->add(0, 1, 0);
            if (!isset($this->textInteract[$p->getName()]['object-name'])) {
                $particle1 = new FloatingTextParticle($pos1, "", "§a" . $menuType);
                $this->addPacketTo($p, $particle1);
                $this->textInteract[$p->getName()]['object-name'] = $particle1;
            } else {
                $particle1 = $this->textInteract[$p->getName()]['object-name'];
                $particle1->setTitle("§a" . $menuType);
                $this->addPacketTo($p, $particle1);
                $this->textInteract[$p->getName()]['object-name'] = $particle1;
            }
        }
    }

    private function addPacketTo(Player $p, ?Particle $particle) {
        echo "DOING FINE\n";
        $p->getLevel()->addParticle($particle, [$p]);
    }
}