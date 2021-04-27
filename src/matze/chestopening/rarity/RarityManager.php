<?php

namespace matze\chestopening\rarity;

use matze\chestopening\rarity\types\CommonRarity;
use matze\chestopening\rarity\types\EpicRarity;
use matze\chestopening\rarity\types\LegendaryRarity;
use matze\chestopening\rarity\types\RareRarity;
use matze\chestopening\utils\InstantiableTrait;

class RarityManager {
    use InstantiableTrait;

    /** @var array  */
    private $rarities = [];

    /**
     * RarityManager constructor.
     */
    public function __construct(){
        $rarities = [
            new CommonRarity(),
            new RareRarity(),
            new EpicRarity(),
            new LegendaryRarity()
        ];
        foreach($rarities as $rarity) {
            $this->rarities[$rarity->getId()] = $rarity;
        }
    }

    /**
     * @return Rarity[]
     */
    public function getRarities(): array{
        return $this->rarities;
    }

    /**
     * @param int $rarityId
     * @return Rarity|null
     */
    public function getRarity(int $rarityId): ?Rarity {
        return $this->rarities[$rarityId] ?? null;
    }
}