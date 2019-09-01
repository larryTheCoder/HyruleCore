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

namespace HyPrimeCore\dependencies\RegionCore;

use HyPrimeCore\dependencies\PluginDispatch;
use HyPrimeCore\utils\Utils;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class RegionDispatch extends PluginDispatch {

	/** @var Config */
	public $config;
	/** @var Task[] */
	private $notify = [];
	/** @var int[] */
	private $notifyData = [];
	private $combats = [];

	/**
	 * Return the name of this dependencies that is being
	 * registered to the plugin core.
	 *
	 * @return string
	 */
	public function getName(): string{
		return "Regions";
	}

	/**
	 * Starts the plugin dependency
	 */
	public function startDependency(): void{
		$this->config = new Config($this->getDataFolder() . "config.yml");
	}

	public function shutdownDependency(): void{
	}

	public function getCommands(): array{
		return [new RegionCommand()];
	}

	/**
	 * @param EntityDamageEvent $e
	 */
	public function onPlayerDamage(EntityDamageEvent $e){
		$p = $e->getEntity();
		if(!$p instanceof Player){
			return;
		}
		$level = $e->getEntity()->getLevel();

		// The config isn't set. Disable it
		if(!is_array($this->config->getNested("areas.{$level->getName()}"))){
			return;
		}

		$isSafe = $this->inCombatArea($p);
		$underCombat = $this->isInCombat($p);

		// The player is in the safe spawn position.
		// And not under combat mode.
		if($isSafe && !$underCombat){
			$e->setCancelled();
		}

		// Check if the entity that is damaging the entity
		// Is outside the combat area.
		if($e instanceof EntityDamageByEntityEvent){
			$attacker = $e->getDamager();

			if($this->inCombatArea($attacker)){
				$e->setCancelled();
			}
		}
	}

	private function inCombatArea(Entity $p){
		$level = $p->getLevel();

		$minX = $this->config->getNested("areas.{$level->getFolderName()}.area-min-x");
		$minZ = $this->config->getNested("areas.{$level->getFolderName()}.area-min-z");
		$maxX = $this->config->getNested("areas.{$level->getFolderName()}.area-max-x");
		$maxZ = $this->config->getNested("areas.{$level->getFolderName()}.area-max-z");

		// 50 < 150
		if($minX < $maxX){
			$intersectA = $minX < $p->getX() && $p->getX() < $maxX;
		}else{
			$intersectA = $maxX < $p->getX() && $p->getX() < $minX;
		}

		if($minZ < $maxZ){
			$intersectB = $minZ < $p->getZ() && $p->getZ() < $maxZ;
		}else{
			$intersectB = $maxZ < $p->getZ() && $p->getZ() < $minZ;
		}

		return $intersectB && $intersectA;
	}

	/**
	 * Checks if the player is in combat mode within the
	 * seconds set in the config file.
	 *
	 * @param Player $p
	 * @return bool
	 */
	private function isInCombat(Player $p){
		$minTime = $this->config->getNested("min-combat-log", 10);
		if(!isset($this->combats[$p->getName()])){
			$this->combats[$p->getName()] = time();

			return false;
		}

		return time() - $this->combats[$p->getName()] >= $minTime;
	}

	public function onPlayerJoin(PlayerJoinEvent $e){
		$this->notifyData[$e->getPlayer()->getName()] = 1;
	}

	/**
	 * @param PlayerMoveEvent $e
	 */
	public function onPlayerMove(PlayerMoveEvent $e){
		$p = $e->getPlayer();

		// Is about to get killed by void.
		if($p->getY() <= 5 && !$this->isInCombat($p)){
			$p->teleport($this->getSpawnPosition($p->getLevel()));
		}

		// The config isn't set. Disable it
		if(!is_array($this->config->getNested("areas.{$p->getLevel()->getName()}"))){
			return;
		}

		$safeZone = $this->inSafeZoneArea($p);
		$combatMode = $this->inCombatArea($p);
		$isInCombat = $this->isInCombat($p);

		// The player is leaving combat zone.
		// Damage that player >:O
		if(!$combatMode && !isset($this->notify[$p->getName()])){
			$p->sendMessage(TF::RED . "You are leaving combat zone.");
			$p->sendMessage(TF::RED . "Get back into the combat zone or you will get yourself killed.");

			$this->notify[$p->getName()] = new class($p) extends Task {

				/** @var Player */
				private $player;

				public function __construct(Player $p){
					$this->player = $p;
				}

				/**
				 * Actions to execute when run
				 *
				 * @param int $currentTick
				 *
				 * @return void
				 */
				public function onRun(int $currentTick){
					if(!$this->player->isConnected()){
						return;
					}
					$this->player->applyDamageModifiers(new EntityDamageEvent($this->player, EntityDamageEvent::CAUSE_MAGIC, 1));
				}
			};

			// Run a task to 'actually' kill the player.
			$this->getScheduler()->scheduleDelayedRepeatingTask($this->notify[$p->getName()], 40, 40);
		}

		// The player is entering the combat zone again.
		// Check if the damaging event still running and stop em
		if($combatMode && isset($this->notify[$p->getName()])){
			$task = $this->notify[$p->getName()];
			$task->getHandler()->cancel();

			unset($this->notify[$p->getName()]);
		}

		// Player is trying to enter that safe-zone-area but that player
		// Is in combat mode.
		if($isInCombat && $safeZone){
			$p->sendMessage(TF::RED . "You cannot enter safe zone while in combat!");

			$vec = $p->getDirectionPlane();
			$p->knockBack($p, 0, $vec->getX() * -1, $vec->getY() * -1); // Reversely proportional
		}elseif($safeZone && $this->notifyData[$p->getName()] !== 1){
			$p->sendMessage(TF::GREEN . "You are entering safe zone area!");

			$this->notifyData[$p->getName()] = 1;
		}elseif(!$safeZone && $this->notifyData[$p->getName()] !== 0){
			$p->sendMessage(TF::RED . "You are leaving safe zone area!");

			$this->notifyData[$p->getName()] = 0;
		}
	}

	/**
	 * @param Level $level
	 * @return \pocketmine\level\Location|null
	 */
	public function getSpawnPosition(Level $level){
		$location = $this->config->getNested("areas.{$level->getName()}.spawn-location", null);
		if($location == null){
			Utils::send("Default spawn position for {$level->getName()} is missing in the config file!");

			return Location::fromObject($level->getSpawnLocation());
		}

		return Utils::parseLocation($this->config->getNested("areas.{$level->getName()}.spawn-location", "0:0:0:0:0"), $level);
	}

	private function inSafeZoneArea(Entity $p){
		$level = $p->getLevel();

		$minX = $this->config->getNested("areas.{$level->getName()}.protected-min-x");
		$minZ = $this->config->getNested("areas.{$level->getName()}.protected-min-z");
		$maxX = $this->config->getNested("areas.{$level->getName()}.protected-max-x");
		$maxZ = $this->config->getNested("areas.{$level->getName()}.protected-max-z");

		// 50 < 150
		if($minX < $maxX){
			$intersectA = $minX < $p->getX() && $p->getX() < $maxX;
		}else{
			$intersectA = $maxX < $p->getX() && $p->getX() < $minX;
		}

		if($minZ < $maxZ){
			$intersectB = $minZ < $p->getZ() && $p->getZ() < $maxZ;
		}else{
			$intersectB = $maxZ < $p->getZ() && $p->getZ() < $minZ;
		}

		return $intersectB && $intersectA;
	}

	/**
	 * @param PlayerQuitEvent $event
	 */
	public function onPlayerQuit(PlayerQuitEvent $event){
		$p = $event->getPlayer();
		if(isset($this->notify[$p->getName()])){
			$task = $this->notify[$p->getName()];
			$task->getHandler()->cancel();

			unset($this->notify[$p->getName()]);
		}
	}
}