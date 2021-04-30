<?php


namespace matze\chestopening\reward\types\lobby;


use BauboLP\Core\Provider\AsyncExecutor;
use matze\chestopening\reward\Reward;
use pocketmine\Player;
use BauboLP\LobbySystem\LobbySystem;

class CoinBomb extends Reward
{

    public function getName(): string
    {
        return "Coinbomb";
    }

    public function onReceive(Player $player): void
    {
        $lobbyPlayer = LobbySystem::getPlayerCache($player->getName());
        if($lobbyPlayer === null) return;

        $playerName = $player->getName();
        $lobbyPlayer->setCoinBombs($lobbyPlayer->getCoinBombs() + 1);
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli) use ($playerName){
            $mysqli->query("UPDATE CoinBomb SET bombs=bombs+1 WHERE playername='$playerName'");
        });
    }
}