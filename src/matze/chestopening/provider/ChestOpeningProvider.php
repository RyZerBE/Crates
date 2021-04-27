<?php

namespace matze\chestopening\provider;

class ChestOpeningProvider {

    /**
     * @param string $player
     * @param int $rarity
     * @param int $amount
     */
    public static function addKey(string $player, int $rarity, int $amount = 1): void {
        //todo
    }

    /**
     * @param string $player
     * @param int $rarity
     * @param int $amount
     */
    public static function removeKey(string $player, int $rarity, int $amount = 1): void {
        //todo
    }

    /**
     * @param string $player
     * @param int $rarity
     * @return int
     */
    public static function getKeys(string $player, int $rarity): int {
        return 100;
    }
}