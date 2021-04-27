<?php

namespace matze\chestopening\reward\types;

use matze\chestopening\reward\Reward;

class MoneyReward extends Reward {

    /** @var int */
    private $amount;

    /**
     * MoneyReward constructor.
     * @param int $amount
     */
    public function __construct(int $amount){
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getName(): string{
        return "§r§a" . $this->amount . " Coin" . ($this->amount === 1 ? "" : "s");
    }

    public function onReceive(): void{
        // Nö.
    }
}