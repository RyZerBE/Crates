<?php

namespace matze\chestopening\rarity;

use matze\chestopening\animation\Animation;
use pocketmine\utils\Color;

abstract class Rarity {
    abstract public function getName(): string;
    abstract public function getRewards(Animation $animation): array;
    abstract public function getColor(): Color;
    abstract public function getTextFormat(): string;
    abstract public function getBlockDamage(): int;
    abstract public function getId(): int;
}