<?php


namespace matze\chestopening\reward\types\lobby;


use BauboLP\Core\Provider\AsyncExecutor;
use BauboLP\LobbySystem\LobbySystem;
use baubolp\ryzerbe\lobbycore\player\LobbyPlayerCache;
use matze\chestopening\reward\Reward;
use pocketmine\Player;

class CoinBomb extends Reward
{

    public function getName(): string
    {
        return "Coinbomb";
    }

    public function onReceive(Player $player): void
    {
        $lobbyPlayer = LobbyPlayerCache::getLobbyPlayer($player);
        if($lobbyPlayer === null) return;

        $playerName = $player->getName();
        $lobbyPlayer->setCoinBombs($lobbyPlayer->getCoinBombs() + 1);
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli) use ($playerName){
            $mysqli->query("UPDATE Coinbombs SET bombs=bombs+1 WHERE playername='$playerName'");
        });
    }
}