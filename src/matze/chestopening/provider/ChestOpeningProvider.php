<?php

namespace matze\chestopening\provider;

use BauboLP\Core\Provider\AsyncExecutor;
use pocketmine\Server;

class ChestOpeningProvider {
    /** @var array  */
    public static $keys = [];

    /**
     * @param string $player
     * @param int $amount
     */
    public static function addKey(string $player, int $amount = 1): void {
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli) use ($player, $amount){
            $mysqli->query("UPDATE `Crates` SET cratekeys=cratekeys+'$amount' WHERE playername='$player'");
        });
    }

    /**
     * @param string $player
     * @param int $amount
     */
    public static function removeKey(string $player, int $amount = 1): void {
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli) use ($player, $amount){
            $mysqli->query("UPDATE `Crates` SET cratekeys=cratekeys-'$amount' WHERE playername='$player'");
        });
    }

    /**
     * @param string $player
     * @return int
     */
    public static function getKeys(string $player): int {
        $keys = 0;
        if(!isset(self::$keys[$player])) {
            AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli) use ($player){
                $res = $mysqli->query("SELECT * FROM Crates WHERE playername='$player'");
                if($res->num_rows > 0)
                    ChestOpeningProvider::$keys[$player] = $res->fetch_assoc()[0]["cratekeys"];
                else
                    $mysqli->query("INSERT INTO `Crates`(`playername`, `cratekeys`) VALUES ('$player', '0')");
            }, function (Server $server, $result) use (&$keys){
                $keys = $result;
            });
        }else
            $keys = self::$keys[$player];

        return $keys;
    }
}