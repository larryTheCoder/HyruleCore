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

namespace HyPrimeCore\kits\types;

use larryTheCoder\kits\KitsAPI;
use pocketmine\item\Item;
use pocketmine\Player;

class NormalKit extends KitsAPI {

    /** @var string */
    private $description;
    /** @var string */
    private $name;
    /** @var int */
    private $price;
    /** @var Item[] */
    private $item;
    /** @var Item[] */
    private $armourContent;

    public function __construct(string $name, int $price, string $description) {
        parent::__construct();

        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
    }

    /**
     * Set the inventory item
     *
     * @param Item[] $item
     */
    public function setInventoryItem(array $item) {
        $this->item =  $item;
    }

    /**
     * Set the armour inventory content
     *
     * @param Item[] $item
     */
    public function setArmourItem(array $item){
        $this->armourContent = $item;
    }

    /**
     * Get the kit name for the Kit
     * @return string
     */
    public function getKitName(): string {
        return $this->name;
    }

    /**
     * The price for the kits, depends on the
     * server if they installed any Economy
     * plugins
     *
     * @return int
     */
    public function getKitPrice(): int {
        return $this->price;
    }

    /**
     * Get the description for the Kit
     * put 'null' if you don't want them
     *
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * What will the provides when the kit was
     * given to player, Use this to handle the
     * item, or events, then use method <code>eventExecution(Event $event)</code>
     *
     * TIP: Add some NBT Tag to ensure that the kit is THE KIT. The code
     * eventExecution will execute everything when player is interacting
     * will any kind of objects.
     *
     * @param Player $p
     */
    public function executeKit(Player $p) {
        $p->getInventory()->setContents($this->item);
        $p->getArmorInventory()->setHelmet($this->armourContent['helmet']);
        $p->getArmorInventory()->setChestplate($this->armourContent['chestplate']);
        $p->getArmorInventory()->setLeggings($this->armourContent['leggings']);
        $p->getArmorInventory()->setBoots($this->armourContent['boots']);
    }
}