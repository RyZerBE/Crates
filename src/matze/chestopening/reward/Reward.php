<?php

namespace matze\chestopening\reward;

abstract class Reward {

    /** @var int  */
    private $chance = 100;

    /**
     * @return int
     */
    public function getChance(): int{
        return $this->chance;
    }

    /**
     * @param int $chance
     * @return Reward
     */
    public function setChance(int $chance): Reward{
        $this->chance = $chance;
        return $this;
    }

    abstract public function getName(): string;
    abstract public function onReceive(): void;
}