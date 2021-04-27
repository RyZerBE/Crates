<?php

namespace matze\chestopening\reward\types;

use matze\chestopening\reward\Reward;
use pocketmine\Player;

class BauboStinktReward extends Reward {

    /**
     * @return string
     */
    public function getName(): string{
        return "Baubo Stinkt!";
    }

    /**
     * @param Player $player
     */
    public function onReceive(Player $player): void{
        // Nö.
    }
}