<?php


namespace matze\chestopening\reward\types\lobby\special;


use BauboLP\Core\Provider\AsyncExecutor;
use BauboLP\Core\Provider\LanguageProvider;
use matze\chestopening\Loader;
use matze\chestopening\provider\ChestOpeningProvider;
use matze\chestopening\reward\Reward;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

abstract class LobbySpecial extends Reward
{

    abstract public function getSpecialName() : string;

    public function getName(): string
    {
        return TextFormat::LIGHT_PURPLE.$this->getSpecialName().TextFormat::DARK_GRAY."(".TextFormat::YELLOW."Lobby".TextFormat::DARK_GRAY.")";
    }

    public function onReceive(Player $player): void
    {
        $playerName = $player->getName();
        $walkingBlockName = $this->getSpecialName();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli) use ($playerName, $walkingBlockName){
            $res = $mysqli->query("SELECT specials FROM LobbyPlayer WHERE playername='$playerName'");
            $walkingBlocks = "";
            if($res->num_rows > 0) {
                while($data = $res->fetch_assoc()) {
                    $walkingBlocks = $data["specials"];
                    break;
                }
            }

            $walkingBlocksArray = explode(":", $walkingBlocks);
            if(in_array($walkingBlockName, $walkingBlocksArray))
                return null;

            $walkingBlocksArray[] = $walkingBlockName;
            $walkingBlocks = implode(":", $walkingBlocksArray);
            $mysqli->query("UPDATE `LobbyPlayer` SET specials='$walkingBlocks' WHERE playername='$playerName'");

            return true;
        }, function (Server $server, $res) use ($playerName, $walkingBlockName) {
            $player = $server->getPlayerExact($playerName);
            if(is_null($player)) return;
            if(is_null($res)) {
                $player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("crate-already-have", $playerName, ["#article" => $walkingBlockName]));
                ChestOpeningProvider::addKey($playerName);
                return;
            }
        });
    }
}