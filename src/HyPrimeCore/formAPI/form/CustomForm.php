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

use HyPrimeCore\formAPI\element\Element;
use HyPrimeCore\formAPI\element\ElementDropdown;
use HyPrimeCore\formAPI\element\ElementInput;
use HyPrimeCore\formAPI\element\ElementLabel;
use HyPrimeCore\formAPI\element\ElementSlider;
use HyPrimeCore\formAPI\element\ElementStepSlider;
use HyPrimeCore\formAPI\element\ElementToggle;
use HyPrimeCore\formAPI\response\FormResponseCustom;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;

class CustomForm extends Form {

    /** @var int */
    public $id;
    /** @var string */
    public $playerName;
    /** @var Element[] */
    public $elements = [];
    /** @var array */
    private $data = [];

    /**
     * @param int $id
     * @param callable $callable
     */
    public function __construct(int $id, ?callable $callable) {
        parent::__construct($id, $callable);
        $this->data["type"] = "custom_form";
        $this->data["title"] = "";
        $this->data["content"] = [];
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
     * @param string $text
     */
    public function addLabel(string $text): void {
        $this->addContent(["type" => "label", "text" => $text]);
        $this->elements[] = new ElementLabel($text);
    }

    /**
     * @param array $content
     */
    private function addContent(array $content): void {
        $this->data["content"][] = $content;
    }

    /**
     * @param string $text
     * @param bool|null $default
     */
    public function addToggle(string $text, bool $default = false): void {
        $content = ["type" => "toggle", "text" => $text];
        if ($default !== null) {
            $content["default"] = $default;
        }
        $this->addContent($content);
        $this->elements[] = new ElementToggle($text, $default);
    }

    /**
     * @param string $text
     * @param int $min
     * @param int $max
     * @param int $step
     * @param int $default
     */
    public function addSlider(string $text, int $min, int $max, int $step = -1, int $default = -1): void {
        $content = ["type" => "slider", "text" => $text, "min" => $min, "max" => $max];
        if ($step !== -1) {
            $content["step"] = $step;
        }
        if ($default !== -1) {
            $content["default"] = $default;
        }
        $this->addContent($content);
        $this->elements[] = new ElementSlider($text, $min, $max, $step, $default);
    }

    /**
     * @param string $text
     * @param array $steps
     * @param int $defaultIndex
     */
    public function addStepSlider(string $text, array $steps, int $defaultIndex = -1): void {
        $content = ["type" => "step_slider", "text" => $text, "steps" => $steps];
        if ($defaultIndex !== -1) {
            $content["default"] = $defaultIndex;
        }
        $this->addContent($content);
        $this->elements[] = new ElementStepSlider($text, $steps, $defaultIndex);
    }

    /**
     * @param string $text
     * @param array $options
     * @param int $default
     */
    public function addDropdown(string $text, array $options, int $default = 0): void {
        $this->addContent(["type" => "dropdown", "text" => $text, "options" => $options, "default" => $default]);
        $this->elements[] = new ElementDropdown($text, $options, $default);
    }

    /**
     * @param string $text
     * @param string $placeholder
     * @param string $default
     */
    public function addInput(string $text, string $placeholder = "", string $default = ""): void {
        $this->addContent(["type" => "input", "text" => $text, "placeholder" => $placeholder, "default" => $default]);
        $this->elements[] = new ElementInput($text, $placeholder, $default);
    }

    public function getResponseModal(): FormResponseCustom {
        return new FormResponseCustom($this);
    }
}