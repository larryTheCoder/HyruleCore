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

namespace HyPrimeCore\cloaks\type;

use HyPrimeCore\cloaks\ParticleCloak;
use HyPrimeCore\player\FakePlayer;
use pocketmine\Player;

class CloakType {

	const FIRERINGS = 0;
	const FIREWINGS = 1;
	const FROSTY = 2;
	const SUPERHERO = 3;
	const SCANNER = 4;
	const SHAMAN = 5;
	const SUPERWING = 6;
	const BLOODHOUND = 7;
	const WISDOM = 8;

	/**
	 * @param Player|FakePlayer $p
	 * @param int $id
	 * @return ParticleCloak|null
	 */
	public static function getCloakById($p, int $id): ?ParticleCloak{
		switch($id){
			case self::FIREWINGS:
				return new Firewings($p);
			case self::FIRERINGS:
				return new Firerings($p);
			case self::FROSTY:
				return new Frosty($p);
			case self::SUPERHERO:
				return new Superhero($p);
			case self::SCANNER:
				return new Scanner($p);
			case self::SHAMAN:
				return new Shaman($p);
			case self::SUPERWING:
				return new MegaWing($p);
			case self::BLOODHOUND:
				return new BloodHound($p);
			case self::WISDOM:
				return new Wisdom($p);
			default:
				return null;
		}
	}

	/**
	 * Get the cloak permission node
	 *
	 * @param int $id ID of the cloak
	 * @return string|null
	 */
	public static function getCloakPermission(int $id): ?string{
		switch($id){
			case self::FIREWINGS:
				return "core.cloak.firewings";
			case self::FIRERINGS:
				return "core.cloak.firerings";
			case self::FROSTY:
				return "core.cloak.frosty";
			case self::SUPERHERO:
				return "core.cloak.superhero";
			case self::SCANNER:
				return "core.cloak.scanner";
			case self::SHAMAN:
				return "core.cloak.shaman";
			case self::SUPERWING:
				return "core.cloak.superwing";
			case self::BLOODHOUND:
				return "core.cloak.bloodhound";
			case self::WISDOM:
				return "core.cloak.wisdom";
			default:
				return null;
		}
	}

	/**
	 * @param int $id
	 * @return string
	 */
	public static function getCloakName(int $id): string{
		switch($id){
			case self::FIREWINGS:
				return "Firewings";
			case self::FIRERINGS:
				return "Firerings";
			case self::FROSTY:
				return "Frosty";
			case self::SUPERHERO:
				return "Superhero";
			case self::SCANNER:
				return "Scanner";
			case self::SHAMAN:
				return "Shaman";
			case self::SUPERWING:
				return "Superwing";
			case self::BLOODHOUND:
				return "Bloodhound";
			case self::WISDOM:
				return "Wisdom";
			default:
				return "Unknown";
		}
	}

	public static function getAll(): array{
		return ["Firerings", "Firewings", "Frosty", "Superhero", "Scanner", "Shaman", "Superwing", "Bloodhound", "Wisdom"];
	}
}