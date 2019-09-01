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

namespace HyPrimeCore\utils;

use HyPrimeCore\CoreMain;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

/**
 * Public general utils class for SkyWars
 * Copyrights larryTheCoder
 *
 * @package HyPrimeCore\utils
 */
class Utils {

	/** @var integer[] */
	public static $particleTimer = [];
	/** @var integer[][] */
	public static $helixMathMap = [];

	/**
	 * @param Position $pos
	 * @param float $range
	 * @return Living[]
	 */
	public static function getNearbyLivingEntities(Position $pos, float $range): array{
		$livingEntities = [];
		foreach($pos->getLevel()->getEntities() as $entity){
			if($entity instanceof Living && $entity->distance($pos) <= $range){
				$livingEntities[] = $entity;
			}
		}

		return $livingEntities;
	}

	public static function rotateAroundAxisY(Vector3 $v, float $angle): Vector3{
		$cos = cos($angle);
		$sin = sin($angle);
		$x = $v->getX() * $cos + $v->getZ() * $sin;
		$z = $v->getX() * -$sin + $v->getZ() * $cos;

		return $v->setComponents($x, $v->getY(), $z);
	}

	public static function rotateAroundAxisZ(Vector3 $v, float $angle): Vector3{
		$cos = cos($angle);
		$sin = sin($angle);
		$x = $v->getX() * $cos - $v->getY() * $sin;
		$y = $v->getX() * $sin + $v->getY() * $cos;

		return $v->setComponents($x, $y, $v->getZ());
	}

	public static function getBackVector(Location $loc): Vector3{
		$newZ = (float)($loc->getZ() + 0.75 * sin($loc->getYaw() * M_PI / 180));
		$newX = (float)($loc->getX() + 0.75 * cos($loc->getYaw() * M_PI / 180));

		return new Vector3($newX - $loc->getX(), $loc->getY(), $newZ - $loc->getZ());
	}

	public static function getParticleTimer(int $id){
		return Utils::$particleTimer[$id];
	}

	public static function setParticleTimer(int $id, int $i){
		Utils::$particleTimer[$id] = $i;
	}

	public static function getLocation(int $i, int $width, Position $location): Location{
		$x = $width * cos(($i + 1) * 11.25 * M_PI / 180.0) + $location->getX();
		$z = $width * sin(($i + 1) * 11.25 * M_PI / 180.0) + $location->getZ();

		return new Location($x, $location->getY(), $z, 0, 0, $location->getLevel());
	}

	public static function getLocation2(int $i, int $width, Position $location): Location{
		$x = $width * cos(($i + 16 - 31) * 11.25 * M_PI / 180.0) + $location->getX();
		$z = $width * sin(($i + 16 - 31) * 11.25 * M_PI / 180.0) + $location->getZ();

		return new Location($x, $location->getY(), $z, 0, 0, $location->getLevel());
	}

	/**
	 * @param int $id
	 * @return array|null
	 */
	public static function helixMath(int $id){
		try{
			return Utils::$helixMathMap[$id];
		}catch(\Exception $exception){

		}

		return null;
	}

	/**
	 * @param int $id
	 * @param int $rotation
	 * @param int $up
	 */
	public static function setHelixMath(int $id, int $rotation, int $up){
		Utils::$helixMathMap[$id] = [$rotation, $up];
	}

	/**
	 * @param Player $p
	 */
	public static function strikeLightning(Player $p){
		$level = $p->getLevel();

		$light = new AddEntityPacket();
		$light->metadata = [];

		$light->type = 93;
		$light->entityRuntimeId = Entity::$entityCount++;
		$light->entityUniqueId = 0;

		$light->position = $p->getPosition();
		$light->motion = new Vector3();

		$light->yaw = $p->getYaw();
		$light->pitch = $p->getPitch();

		Server::getInstance()->broadcastPacket($level->getPlayers(), $light);
	}

	/**
	 * Centering a text with a number of the center
	 *
	 * @param string $input The message
	 * @param int $count The input of how much to center
	 * @return string       The input
	 */
	public static function center(string $input, int $count = 44){
		$msgCount = ($count - strlen(TextFormat::clean($input))) / 2;
		$msgTemp = 0;
		while($msgCount >= 0){
			$input = " " . $input;
			$msgCount--;
			$msgTemp++;
		}

		return $input;
	}

	/**
	 * Decode a position form a string
	 *
	 * @param string $decodedPos
	 * @return null|Position
	 */
	public static function parsePosition(string $decodedPos): ?Position{
		$piece = explode(":", $decodedPos);
		if(count($piece) !== 4){
			Utils::send("Attempted to decode a non-decoded-position");

			return null;
		}
		$level = Server::getInstance()->getLevelByName($piece[3]);

		return new Position($piece[0], $piece[1], $piece[2], $level);
	}

	public static function send($string){
		Server::getInstance()->getLogger()->info(CoreMain::get()->getPrefix() . str_replace("&", "§", $string));
	}

	public static function encodePosition(Position $pos): string{
		return "$pos->x:$pos->y:$pos->z:{$pos->getLevel()->getName()}";
	}

	/**
	 * Decode a position form a string
	 *
	 * @param string $decodedPos
	 * @param Level|null $level
	 * @return null|Location
	 */
	public static function parseLocation(string $decodedPos, Level $level = null): ?Location{
		$piece = explode(":", $decodedPos);
		if(($level == null && count($piece) !== 6) || ($level != null && count($piece) !== 5)){
			Utils::send("Attempted to decode a non-decoded-location");

			return null;
		}

		if($level == null) $level = Server::getInstance()->getLevelByName($piece[5]);

		return new Location($piece[0], $piece[1], $piece[2], $piece[3], $piece[4], $level);
	}

	public static function encodeLocation(Location $pos): string{
		return "$pos->x:$pos->y:$pos->z:$pos->yaw:$pos->pitch:{$pos->getLevel()->getName()}";
	}

	public static function loadFirst(string $levelName, bool $load = true){
		Server::getInstance()->generateLevel($levelName);
		if($load){
			Server::getInstance()->loadLevel($levelName);
		}
	}

	public static function checkFile(Config $arena){
		if(!(is_numeric($arena->getNested("signs.join_sign_x")) && is_numeric($arena->getNested("signs.join_sign_y")) && is_numeric($arena->getNested("signs.join_sign_z")) && is_numeric($arena->getNested("arena.max_game_time")) && is_string($arena->getNested("signs.join_sign_world")) && is_string($arena->getNested("signs.status_line_1")) && is_string($arena->getNested("signs.status_line_2")) && is_string($arena->getNested("signs.status_line_3")) && is_string($arena->getNested("signs.status_line_4")) && is_string($arena->getNested("arena.arena_world")) && is_numeric($arena->getNested("chest.refill_rate")) && is_numeric($arena->getNested("arena.spec_spawn_x")) && is_numeric($arena->getNested("arena.spec_spawn_y")) && is_numeric($arena->getNested("arena.spec_spawn_z")) && is_numeric($arena->getNested("arena.max_players")) && is_numeric($arena->getNested("arena.min_players")) && is_numeric($arena->getNested("arena.grace_time")) && is_string($arena->getNested("arena.arena_world")) && is_numeric($arena->getNested("arena.starting_time")) && is_array($arena->getNested("arena.spawn_positions")) && is_string($arena->getNested("arena.finish_msg_levels")) && !is_string($arena->getNested("arena.money_reward")))){
			return false;
		}
		if(!((strtolower($arena->getNested("signs.enable_status")) == true || strtolower($arena->getNested("signs.enable_status")) == false) && (strtolower($arena->getNested("arena.spectator_mode")) == true || strtolower($arena->getNested("arena.spectator_mode")) == false) && (strtolower($arena->getNested("chest.refill")) == true || strtolower($arena->getNested("chest.refill")) == false) && (strtolower($arena->getNested("arena.time")) == true || strtolower($arena->getNested("arena.time")) == "day" || strtolower($arena->getNested("arena.time")) == "night" || is_numeric(strtolower($arena->getNested("arena.time")))) && (strtolower($arena->getNested("arena.start_when_full")) == true || strtolower($arena->getNested("arena.start_when_full")) == false) && (strtolower($arena->get("enabled")) == true || strtolower($arena->get("enabled")) == false))){
			return false;
		}

		return true;
	}

	public static function ensureDirectory(PluginBase $plugin, string $directory = ""){
		if(!file_exists($plugin->getDataFolder() . $directory)){
			@mkdir($plugin->getDataFolder() . $directory, 0755);
		}
	}

	public static function copyResourceTo($source, $destination){
		// Check for symlinks
		if(is_link($source)){
			return symlink(readlink($source), $destination);
		}

		// Simple copy for a file
		if(is_file($source)){
			return copy($source, $destination);
		}

		// Make destination directory
		if(!is_dir($destination)){
			mkdir($destination);
		}

		// Loop through the folder
		$dir = dir($source);
		while(false !== $entry = $dir->read()){
			// Skip pointers
			if($entry == '.' || $entry == '..'){
				continue;
			}

			// Deep copy directories
			self::copyResourceTo("$source/$entry", "$destination/$entry");
		}

		// Clean up
		$dir->close();

		return true;
	}

	/**
	 * Convert the string to Block class
	 * This also checks the Block class
	 *
	 * @param string $str
	 * @return Block
	 */
	public static function convertToBlock(string $str){
		$b = explode(":", str_replace([" ", "minecraft:"], ["_", ""], trim($str)));
		if(!isset($b[1])){
			$meta = 0;
		}elseif(is_numeric($b[1])){
			$meta = $b[1] & 0xFFFF;
		}else{
			throw new \InvalidArgumentException("Unable to parse \"" . $b[1] . "\" from \"" . $str . "\" as a valid meta value");
		}

		if(is_numeric($b[0])){
			$item = Block::get(((int)$b[0]) & 0xFFFF, $meta);
		}elseif(defined(BlockIds::class . "::" . strtoupper($b[0]))){
			$item = Block::get(constant(BlockIds::class . "::" . strtoupper($b[0])), $meta);
		}else{
			throw new \InvalidArgumentException("Unable to resolve \"" . $str . "\" to a valid item");
		}

		return $item;
	}

	/**
	 * Convert the item from string to item class
	 * This also checks the Block class
	 *
	 * @param string $str
	 * @return Item
	 */
	public static function convertToItem(string $str): Item{
		$b = explode(":", str_replace([" ", "minecraft:"], ["_", ""], trim($str)));
		if(!isset($b[1])){
			$meta = 0;
		}elseif(is_numeric($b[1])){
			$meta = $b[1] & 0xFFFF;
		}else{
			throw new \InvalidArgumentException("Unable to parse \"" . $b[1] . "\" from \"" . $str . "\" as a valid meta value");
		}

		if(is_numeric($b[0])){
			$item = Item::get(((int)$b[0]) & 0xFFFF, $meta);
		}elseif(defined(ItemIds::class . "::" . strtoupper($b[0]))){
			$item = Item::get(constant(ItemIds::class . "::" . strtoupper($b[0])), $meta);
		}elseif(defined(BlockIds::class . "::" . strtoupper($b[0]))){
			$item = Item::get(constant(BlockIds::class . "::" . strtoupper($b[0])), $meta);
		}else{
			throw new \InvalidArgumentException("Unable to resolve \"" . $str . "\" to a valid item");
		}

		return $item;
	}

	public static function in_arrayi($needle, $haystack){
		return in_array(strtolower($needle), array_map('strtolower', $haystack));
	}
}