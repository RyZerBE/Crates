<?php

namespace matze\chestopening\reward\types;

use matze\chestopening\reward\Reward;

class BauboStinktReward extends Reward {

    /**
     * @return string
     */
    public function getName(): string{
        return "Baubo Stinkt!";
    }

    public function onReceive(): void{
        // Nö.
    }
}