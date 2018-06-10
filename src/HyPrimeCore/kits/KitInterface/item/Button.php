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

namespace HyPrimeCore\kits\KitInterface\item;

use HyPrimeCore\event\ButtonPushEvent;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\level\sound\ClickSound;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;

abstract class Button extends Flowable {

    public function isActivated(): bool {
        return (($this->getDamage() & $this->getVariantBitmask()) == $this->getVariantBitmask());
    }

    public function __construct(int $meta = 0) {
        parent::__construct($this->id, $meta, $this->getType() . " Button");
        $this->meta = $meta;
    }

    public abstract function getType(): string;

    public function getVariantBitmask(): int {
        return 0x08;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool {
        if ($blockClicked->isSolid()) {
            $this->meta = $face;
            return $this->level->setBlock($this, $this, true, true);
        }

        return false;
    }

    public function onActivate(Item $item, Player $player = null): bool {
        if (!is_null($player)) {
            $event = new ButtonPushEvent($player);
            Server::getInstance()->getPluginManager()->callEvent($event);
        }
        $this->setDamage($this->getDamage() ^ 0x08);
        $this->level->setBlock($this, $this, true, false);
        $this->level->addSound(new ClickSound($this->add(0.5, 0.5, 0.5)));
        $this->level->scheduleDelayedBlockUpdate($this, 30);
        return true;
    }

    public function onScheduledUpdate(): void {
        $this->setDamage($this->getDamage() ^ 0x08);
        $this->level->setBlock($this, $this, true, false);
        $this->level->addSound(new ClickSound($this->add(0.5, 0.5, 0.5)));
    }

    public function recalculateBoundingBox(): ?AxisAlignedBB {
        $meta = $this->getVariant();
        switch ($meta) {
            case Vector3::SIDE_DOWN:
                if (!$this->isActivated()) {
                    return new AxisAlignedBB(0.3125, 0.875, 0.375, 0.6875, 1.0, 0.625);
                } else {
                    return new AxisAlignedBB(0.3125, 0.9375, 0.375, 0.6875, 1.0, 0.625);
                }
            case Vector3::SIDE_UP:
                if (!$this->isActivated()) {
                    return new AxisAlignedBB(0.3125, 0.0, 0.375, 0.6875, 0.125, 0.625);
                } else {
                    return new AxisAlignedBB(0.3125, 0.0, 0.375, 0.6875, 0.0625, 0.625);
                }
            case Vector3::SIDE_NORTH:
                if (!$this->isActivated()) {
                    return new AxisAlignedBB(0.3125, 0.375, 0.875, 0.6875, 0.625, 1.0);
                } else {
                    return new AxisAlignedBB(0.3125, 0.375, 0.9375, 0.6875, 0.625, 1.0);
                }
            case Vector3::SIDE_SOUTH:
                if (!$this->isActivated()) {
                    return new AxisAlignedBB(0.3125, 0.375, 0.0, 0.6875, 0.625, 0.125);
                } else {
                    return new AxisAlignedBB(0.3125, 0.375, 0.0, 0.6875, 0.625, 0.0625);
                }
            case Vector3::SIDE_WEST:
                if (!$this->isActivated()) {
                    return new AxisAlignedBB(0.875, 0.375, 0.3125, 1.0, 0.625, 0.6875);
                } else {
                    return new AxisAlignedBB(0.9375, 0.375, 0.3125, 1.0, 0.625, 0.6875);
                }
            case Vector3::SIDE_EAST:
                if (!$this->isActivated()) {
                    return new AxisAlignedBB(0.0, 0.375, 0.3125, 0.125, 0.625, 0.6875);
                } else {
                    return new AxisAlignedBB(0.0, 0.375, 0.3125, 0.0625, 0.625, 0.6875);
                }
        }
        return null;
    }
}