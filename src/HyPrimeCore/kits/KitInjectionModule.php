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

namespace HyPrimeCore\kits;

use HyPrimeCore\CoreMain;
use HyPrimeCore\kits\types\NormalKit;
use HyPrimeCore\utils\Utils;
use larryTheCoder\SkyWarsPE;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

class KitInjectionModule {

	/** @var CoreMain */
	private $plugin;
	/** @var SkyWarsPE */
	private $injection;

	public function __construct(CoreMain $plugin){
		$this->plugin = $plugin;
		/** @var PluginBase $inj */
		$inj = $plugin->getServer()->getPluginManager()->getPlugin("SkyWarsForPE");

		// Check if injection is available
		if(is_null($inj)){
			$plugin->getServer()->getLogger()->error("Could not inject KitAPI to SkyWarsForPE");

			return;
		}

		$this->injection = $inj;
		$this->load();
	}

	private function load(){
		$this->plugin->saveResource("kits.yml");
		$kit = new Config($this->plugin->getDataFolder() . "kits.yml", Config::YAML);
		$array = $kit->get("kits");
		if(!is_array($array)){
			$this->plugin->getServer()->getLogger()->error("Kits is not in array!");

			return;
		}
		$kit = [];
		foreach(array_keys($array) as $val){
			try{
				$kit[$val]["name"] = $array[$val]["name"];
				$kit[$val]["price"] = $array[$val]["price"];
				$kit[$val]["items"] = $array[$val]["items"];
				if(isset($kit[$val]["armour"])){
					if(isset($kit[$val]["armour"]["hat"])){
						$kit[$val]["armour"]["hat"] = $array[$val]["armour"]["hat"];
					}
					if(isset($kit[$val]["armour"]["chestplate"])){
						$kit[$val]["armour"]["hat"] = $array[$val]["armour"]["chestplate"];
					}
					if(isset($kit[$val]["armour"]["leggings"])){
						$kit[$val]["armour"]["hat"] = $array[$val]["armour"]["leggings"];
					}
					if(isset($kit[$val]["armour"]["boot"])){
						$kit[$val]["armour"]["hat"] = $array[$val]["armour"]["boot"];
					}
				}
			}catch(\Exception $ex){
				Utils::send("&cError on loading kit: &d" . $val);
				continue;
			}
		}

		foreach(array_keys($kit) as $val){
			$kitAPI = new NormalKit($kit[$val]["name"], $kit[$val]["price"], "");
			$items = [];
			foreach($kit[$val]["items"] as $value){
				$split = explode(":", $value);
				if(count($split) === 2){
					$items[] = Item::get($split[0], 0, $split[1]);
				}elseif(count($split) === 3){
					$items[] = Item::get($split[0], $split[2], $split[1]);
				}elseif(count($split) === 5){
					$item = Item::get($split[0], $split[2], $split[1]);
					if(Enchantment::getEnchantment($split[3]) !== null){
						$item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($split[3]), $split[4]));
					}else{
						Server::getInstance()->getLogger()->error(CoreMain::get()->getPrefix() . "§cUnknown EnchantmentID: " . $split[3]);
					}
					$items[] = $item;
				}
			}
			if(isset($kit[$val]["armour"])){
				if(isset($kit[$val]["armour"]['hat'])){
					$armours[] = $kit[$val]["armour"]['hat'];
				}else{
					$armours[] = Item::get(0);
				}
				if(isset($kit[$val]["armour"]['chestplate'])){
					$armours[] = $kit[$val]["armour"]['chestplate'];
				}else{
					$armours[] = Item::get(0);
				}
				if(isset($kit[$val]["armour"]['leggings'])){
					$armours[] = $kit[$val]["armour"]['leggings'];
				}else{
					$armours[] = Item::get(0);
				}
				if(isset($kit[$val]["armour"]['boots'])){
					$armours[] = $kit[$val]["armour"]['boots'];
				}else{
					$armours[] = Item::get(0);
				}
			}else{
				$armours[] = Item::get(0);
				$armours[] = Item::get(0);
				$armours[] = Item::get(0);
				$armours[] = Item::get(0);
			}

			$kitAPI->setInventoryItem($items);
			$kitAPI->setArmourItem($armours);
			$this->injection->kit->registerKit($kitAPI);
		}
	}
}