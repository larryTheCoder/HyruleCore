<?php
/*
 * Copyright (C) 2018 Adam Matthew, Hyrule Minigame Division
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
declare(strict_types=1);

namespace HyPrimeCore\formAPI\form;

use HyPrimeCore\formAPI\element\ElementButton;
use HyPrimeCore\formAPI\element\ElementButtonImageData;
use HyPrimeCore\formAPI\response\FormResponseSimple;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;

class SimpleForm extends Form {

    const IMAGE_TYPE_PATH = 0;
    const IMAGE_TYPE_URL = 1;

    /** @var int */
    public $id;
    /** @var string */
    public $playerName;
    /** @var array */
    private $data = [];
    /** @var string */
    private $content = "";
    /** @var ElementButton[] */
    private $buttons;

    /**
     * @param int $id
     * @param callable $callable
     */
    public function __construct(int $id, ?callable $callable) {
        parent::__construct($id, $callable);
        $this->data["type"] = "form";
        $this->data["title"] = "";
        $this->data["content"] = $this->content;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param Player $player
     */
    public function sendToPlayer(Player $player): void {
        $pk = new ModalFormRequestPacket();
        $pk->formId = $this->id;
        $pk->formData = json_encode($this->data);
        $player->dataPacket($pk);
        $this->playerName = $player->getName();
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void {
        $this->data["title"] = $title;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->data["title"];
    }

    /**
     * @return string
     */
    public function getContent(): string {
        return $this->data["content"];
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void {
        $this->data["content"] = $content;
    }

    /**
     * @param string $text
     * @param int $imageType
     * @param string $imagePath
     */
    public function addButton(string $text, int $imageType = -1, string $imagePath = ""): void {
        $content = ["text" => $text];
        if ($imageType !== -1) {
            $content["image"]["type"] = $imageType === 0 ? "path" : "url";
            $content["image"]["data"] = $imagePath;
        }
        $this->data["buttons"][] = $content;
        $this->buttons[] = new ElementButton($text, new ElementButtonImageData(strval($imageType), $imagePath));
    }

    public function getResponseModal(): FormResponseSimple {
        return new FormResponseSimple($this, $this->buttons);
    }
}
