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

use HyPrimeCore\utils\Settings;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\utils\TextFormat as TF;

class RegionCommand extends Command {

	public function __construct(){
		parent::__construct("region", "HyCore region settings command", "[arguments]", ["rg"]);
	}

	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param string[] $args
	 *
	 * @throws CommandException
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!isset($args[0])){
			$sender->sendMessage(Settings::$prefix . TF::RED . "Please use /$commandLabel help for more information");

			return;
		}

		switch(strtolower($args[0])){
			case "help":
				$sender->sendMessage("--- HyCore Region Help ---");
				$sender->sendMessage("/rg area1 - Set the combat zone for area 1");
				$sender->sendMessage("/rg area2 - Set the combat zone for area 2");
				$sender->sendMessage("/rg safe1 - Set the safe zone for area 1");
				$sender->sendMessage("/rg safe1 - Set the safe zone for area 2");
				$sender->sendMessage("/rg spawnpoint - Set the world spawn position.");
				break;
		}
	}
}