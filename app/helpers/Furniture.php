<?php

class Furniture {
    function getFurniture() {
        return [
            "BE" => [
                "LE" => "I",
                "DR" => "O",
                "SW" => "O"
            ],
            "ST" => [
                "TR" => "I",
                "DR" => "O",
            ],
            "SC" => [
                "SL" => "I",
                "BS" => "O"
            ],
            "ZU" => [
                "SW" => "O",
                "RO" => "O",
                "ZO" => "I"
            ],
            "MU" => [
                "LD" => "O",
                "LS" => "O",
                "VE" => "I",
            ],
            "KO" => [
                "DS" =>  "O",
                "TE" => "O",
                "KE" => "I"
            ],
            "DE" => [
                "LE" => "I",
                "SE" => "I",
                "SW" => "O"
            ]
        ];
    }

    function getPrettyFurniNames($code) {
        $prettyNames = [
            "BE" => "Bed",
            "LE" => "LED",
            "DR" => "Druksensor",
            "SW" => "Switch",
            "ST" => "Stoel",
            "TR" => "Trilelement",
            "SC" => "Schemerlamp",
            "BS" => "Bewegingssens.",
            "ZU" => "Zuil",
            "RO" => "Rookmelder",
            "ZO" => "Zoemer",
            "MU" => "Muur",
            "LD" => "LDR",
            "VE" => "Venster",
            "DI" => "Dimmer",
            "KO" => "Koelkast",
            "DS" => "Deurschakelaar",
            "KE" => "Koelelement",
            "DE" => "Deur",
            "SE" => "Servo",
            "TO" => "Toggle",
            "AL" => "Alarm",
            "HU" => "Hulp",
            "IN" => "Inbraak"
        ];

        if (array_key_exists($code, $prettyNames)){
          return $prettyNames[$code];
        }
        else{
          return false;
        }
    }

    function getFurniIcon($code) {
        switch ($code) {
            case "BE":
                $icon = '<i class="titleIcon fas fa-bed"></i>';
                break;
            case "ST":
                $icon = '<i class="titleIcon fas fa-chair"></i>';
                break;
            case "SC":
                $icon = '<i class="titleIcon fas fa-moon"></i>';
                break;
            case "ZU":
                $icon = '<i class="titleIcon fas fa-columns"></i>';
                break;
            case "MU":
                $icon = '<i class="titleIcon fas fa-th-large"></i>';
                break;
            case "KO":
                $icon = '<i class="titleIcon fas fa-temperature-low"></i>';
                break;
            case "DE":
                $icon = '<i class="titleIcon fas fa-door-closed"></i>';
                break;
        }

        return $icon;
    }
}
