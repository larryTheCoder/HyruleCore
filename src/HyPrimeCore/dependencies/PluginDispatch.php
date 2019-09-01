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

namespace HyPrimeCore\dependencies;

use HyPrimeCore\CoreMain;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\Server;

abstract class PluginDispatch implements Listener {

	private $__init;

	public function __construct(){
		$this->__init = CoreMain::get();

		Server::getInstance()->getPluginManager()->registerEvents($this, $this->__init);
	}

	/**
	 * Starts the plugin dependency
	 */
	public abstract function startDependency(): void;

	/**
	 * Shutdown this plugin dependency
	 */
	public abstract function shutdownDependency(): void;

	/**
	 * @return Command[]
	 */
	public function getCommands(): array{
		return [];
	}

	/**
	 * A unique data folder for that dependency.
	 *
	 * @return string
	 */
	public function getDataFolder(): string{
		return $this->__init->getDataFolder() . $this->getName() . "/";
	}

	/**
	 * Return the name of this dependencies that is being
	 * registered to the plugin core.
	 *
	 * @return string
	 */
	public abstract function getName(): string;

	/**
	 * @param string $string
	 * @return resource|null
	 */
	public function getResource(string $string){
		return $this->__init->getResource($this->getName() . "/" . $string);
	}

	/**
	 * @return \pocketmine\scheduler\ServerScheduler|\pocketmine\scheduler\TaskScheduler
	 */
	public function getScheduler(){
		return $this->__init->getSchedulerForce();
	}

	/**
	 * @return \AttachableThreadedLogger
	 */
	public function getLogger(){
		return $this->__init->getServer()->getLogger();
	}
}