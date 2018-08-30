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

namespace HyPrimeCore\utils\block;


use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\Player;

class ArmorStand extends Entity {

	public const NETWORK_ID = self::ARMOR_STAND;
	public const TAG_HAND_ITEMS = "HandItems";
	public const TAG_ARMOR_ITEMS = "ArmorItems";

	/** @var int[] */
	public const
		HELMET = [
		Item::LEATHER_HELMET,
		Item::CHAIN_HELMET,
		Item::IRON_HELMET,
		Item::GOLD_HELMET,
		Item::DIAMOND_HELMET,
	],
		CHESTPLATE = [
		Item::LEATHER_CHESTPLATE,
		Item::CHAIN_CHESTPLATE,
		Item::IRON_CHESTPLATE,
		Item::GOLD_CHESTPLATE,
		Item::DIAMOND_CHESTPLATE,
		Item::ELYTRA,
	],
		LEGGINGS = [
		Item::LEATHER_LEGGINGS,
		Item::CHAIN_LEGGINGS,
		Item::IRON_LEGGINGS,
		Item::GOLD_LEGGINGS,
		Item::DIAMOND_LEGGINGS,
	],
		BOOTS = [
		Item::LEATHER_BOOTS,
		Item::CHAIN_BOOTS,
		Item::IRON_BOOTS,
		Item::GOLD_BOOTS,
		Item::DIAMOND_BOOTS,
	];

	/** @var string */
	public const
		TYPE_HELMET = "HELMET",
		TYPE_CHESTPLATE = "CHESTPLATE",
		TYPE_LEGGINGS = "LEGGINGS",
		TYPE_BOOTS = "BOOTS",
		TYPE_NULL = "NIL";
	public $height = 1.975;

	// TODO: Poses...
	public $width = 0.5;
	protected $gravity = 0.04;
	/** @var Item */
	protected $itemInHand;
	/** @var Item */
	protected $itemOffHand;
	/** @var Item */
	protected $helmet;
	/** @var Item */
	protected $chestplate;
	/** @var Item */
	protected $leggings;
	/** @var Item */
	protected $boots;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);

		$air = Item::get(Item::AIR)->nbtSerialize();
		if(!$nbt->hasTag(self::TAG_HAND_ITEMS, ListTag::class)){
			$nbt->setTag(new ListTag(self::TAG_HAND_ITEMS, [
				$air, // itemInHand
				$air  // itemOffHand
			], NBT::TAG_Compound));
		}

		if(!$nbt->hasTag(self::TAG_ARMOR_ITEMS, ListTag::class)){
			$nbt->setTag(new ListTag(self::TAG_ARMOR_ITEMS, [
				$air, // boots
				$air, // leggings
				$air, // chestplate
				$air  // helmet
			], NBT::TAG_Compound));
		}

		$handItems = $nbt->getListTag(self::TAG_HAND_ITEMS);
		$armorItems = $nbt->getListTag(self::TAG_ARMOR_ITEMS);

		$this->itemInHand = Item::nbtDeserialize($handItems[0]);
		$this->itemOffHand = Item::nbtDeserialize($handItems[1]);

		$this->helmet = Item::nbtDeserialize($armorItems[3]);
		$this->chestplate = Item::nbtDeserialize($armorItems[2]);
		$this->leggings = Item::nbtDeserialize($armorItems[1]);
		$this->boots = Item::nbtDeserialize($armorItems[0]);

		$this->setHealth(6);
		$this->setMaxHealth(6);
	}

	public static function getType(Item $armor): string{
		if(in_array($armor->getId(), $type = self::HELMET)){
			return self::TYPE_HELMET;
		}
		if(in_array($armor->getId(), self::CHESTPLATE)){
			return self::TYPE_CHESTPLATE;
		}
		if(in_array($armor->getId(), self::LEGGINGS)){
			return self::TYPE_LEGGINGS;
		}
		if(in_array($armor->getId(), self::BOOTS)){
			return self::TYPE_BOOTS;
		}

		return self::TYPE_NULL;
	}

	public function canCollideWith(Entity $entity): bool{
		return false;
	}

	public function kill(): void{
		$this->level->dropItem($this, Item::get(Item::ARMOR_STAND));
		$this->level->dropItem($this, $this->getItemInHand());
		$this->level->dropItem($this, $this->getItemOffHand());
		$this->level->dropItem($this, $this->getHelmet());
		$this->level->dropItem($this, $this->getChestplate());
		$this->level->dropItem($this, $this->getLeggings());
		$this->level->dropItem($this, $this->getBoots());
		parent::kill();
	}

	public function getItemInHand(): Item{
		return $this->itemInHand;
	}

	public function setItemInHand(Item $item){
		$this->itemInHand = $item;
		$this->sendAll();
	}

	public function sendAll(){
		foreach($this->getViewers() as $player){
			$this->sendHandItems($player);
			$this->sendArmorItems($player);
		}
	}

	public function sendHandItems(Player $player){
		$pk = new MobEquipmentPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->inventorySlot = $pk->hotbarSlot = 0;
		$pk->item = $this->getItemInHand();
		$player->dataPacket($pk);

		$pk = new MobEquipmentPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->inventorySlot = $pk->hotbarSlot = 1;
		$pk->item = $this->getItemOffHand();
		$player->dataPacket($pk);
	}

	public function getItemOffHand(): Item{
		return $this->itemOffHand;
	}

	public function setItemOffHand(Item $item){
		$this->itemOffHand = $item;
		$this->sendAll();
	}

	public function sendArmorItems(Player $player){
		$pk = new MobArmorEquipmentPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->slots = [$this->getHelmet(), $this->getChestplate(), $this->getLeggings(), $this->getBoots()];
		$player->dataPacket($pk);
	}

	public function getHelmet(): Item{
		return $this->helmet;
	}

	public function setHelmet(Item $item){
		$this->helmet = $item;
		$this->sendAll();
	}

	public function getChestplate(): Item{
		return $this->chestplate;
	}

	public function setChestplate(Item $item){
		$this->chestplate = $item;
		$this->sendAll();
	}

	public function getLeggings(): Item{
		return $this->leggings;
	}

	public function setLeggings(Item $item){
		$this->leggings = $item;
		$this->sendAll();
	}

	public function getBoots(): Item{
		return $this->boots;
	}

	public function setBoots(Item $item){
		$this->boots = $item;
		$this->sendAll();
	}

	public function spawnTo(Player $player): void{
		parent::spawnTo($player);
		$this->sendArmorItems($player);
		$this->sendHandItems($player);
	}

	public function saveNBT(): void{
		parent::saveNBT();
		$this->namedtag->setTag(new ListTag(self::TAG_ARMOR_ITEMS, [
			$this->boots->nbtSerialize(),
			$this->leggings->nbtSerialize(),
			$this->chestplate->nbtSerialize(),
			$this->helmet->nbtSerialize(),
		], NBT::TAG_Compound));
		$this->namedtag->setTag(new ListTag(self::TAG_HAND_ITEMS, [
			$this->getItemInHand()->nbtSerialize(),
			$this->getItemOffHand()->nbtSerialize(),
		], NBT::TAG_Compound));
	}

	// A E S T H E T I C S  --  from Altay

	public function attack(EntityDamageEvent $source): void{
		$this->setHealth(6);
	}
	// A E S T H E T I C S  --  from Altay
}