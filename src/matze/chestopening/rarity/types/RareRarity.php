<?php

namespace matze\chestopening\rarity\types;

use matze\chestopening\animation\Animation;
use matze\chestopening\rarity\Rarity;
use matze\chestopening\rarity\RarityIds;
use matze\chestopening\reward\types\BauboStinktReward;
use pocketmine\utils\Color;
use pocketmine\utils\TextFormat;

class RareRarity extends Rarity {

    /**
     * @return string
     */
    public function getName(): string{
        return "Rare";
    }

    /**
     * @param Animation $animation
     * @return array
     */
    public function getRewards(Animation $animation): array{
        return [
            new BauboStinktReward()
        ];
    }

    /**
     * @return Color
     */
    public function getColor(): Color{
        return new Color(0, 0, 0);//todo
    }

    /**
     * @return string
     */
    public function getTextFormat(): string{
        return TextFormat::GOLD;
    }

    /**
     * @return int
     */
    public function getBlockDamage(): int{
        return 0;//todo
    }

    /**
     * @return int
     */
    public function getId(): int{
        return RarityIds::RARITY_RARE;
    }
}