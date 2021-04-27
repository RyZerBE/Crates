<?php

namespace matze\chestopening\rarity\types;

use matze\chestopening\animation\Animation;
use matze\chestopening\rarity\Rarity;
use matze\chestopening\rarity\RarityIds;
use matze\chestopening\reward\types\BauboStinktReward;
use matze\chestopening\reward\types\MoneyReward;
use pocketmine\utils\Color;
use pocketmine\utils\TextFormat;

class CommonRarity extends Rarity {

    /**
     * @return string
     */
    public function getName(): string{
        return "Common";
    }

    /**
     * @param Animation $animation
     * @return array
     */
    public function getRewards(Animation $animation): array{
        return [
            new BauboStinktReward(),
            (new MoneyReward(1))->setChance(1),
            (new MoneyReward(2))->setChance(1),
        ];
    }

    /**
     * @return Color
     */
    public function getColor(): Color{
        return new Color(45, 175, 20);
    }

    /**
     * @return string
     */
    public function getTextFormat(): string{
        return TextFormat::GREEN;
    }

    /**
     * @return int
     */
    public function getBlockDamage(): int{
        return 13;
    }

    /**
     * @return int
     */
    public function getId(): int{
        return RarityIds::RARITY_COMMON;
    }
}