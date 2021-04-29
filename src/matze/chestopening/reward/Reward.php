<?php

namespace matze\chestopening\reward;

use pocketmine\Player;

abstract class Reward {

    /** @var int  */
    public $chance = 100;

    /**
     * @return int
     */
    public function getChance(): int{
        return $this->chance;
    }

    /**
     * @param int $percent
     * @return Reward
     */
    public function setChance(int $percent): Reward{
        $this->chance = $percent;
        return $this;
    }

    abstract public function getName(): string;
    abstract public function onReceive(Player $player): void;
}