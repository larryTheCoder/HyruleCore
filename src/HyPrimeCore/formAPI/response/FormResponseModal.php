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

namespace HyPrimeCore\formAPI\response;


use HyPrimeCore\formAPI\form\ModalForm;

class FormResponseModal extends FormResponse {
    /** @var int */
    private $clickedButtonId;
    /** @var string */
    private $clickedButtonText;
    /** @var ModalForm */
    private $form;

    public function __construct(ModalForm $form) {
        $this->form = $form;
    }

    public function getClickedButtonId(): int {
        return $this->clickedButtonId;
    }

    public function getClickedButtonText(): string {
        return $this->clickedButtonText;
    }

    public function setData(string $data) {
        if ($data === "null") {
            $this->closed = true;
            return;
        }

        if ($data === "true") {
            $this->clickedButtonId = 0;
            $this->clickedButtonText = $this->form->data["button1"];
        } else {
            $this->clickedButtonId = 1;
            $this->clickedButtonText = $this->form->data["button2"];
        }
    }
}