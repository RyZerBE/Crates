<?php

namespace matze\chestopening\provider;

class ChestOpeningProvider {

    /**
     * @param string $player
     * @param int $amount
     */
    public static function addKey(string $player, int $amount = 1): void {
        //TODO: add key to player
    }

    /**
     * @param string $player
     * @param int $amount
     */
    public static function removeKey(string $player, int $amount = 1): void {
        //TODO: remove key from player
    }

    /**
     * @param string $player
     * @return int
     */
    public static function getKeys(string $player): int {
        return 100;
    }
}