<?php


namespace matze\chestopening\reward\types\lobby\walkingblock;


use BauboLP\Core\Provider\AsyncExecutor;
use BauboLP\Core\Provider\LanguageProvider;
use matze\chestopening\Loader;
use matze\chestopening\provider\ChestOpeningProvider;
use matze\chestopening\reward\Reward;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

abstract class WalkingBlock extends Reward
{

    abstract public function getWalkingBlockName(): string;


    public function getName(): string
    {
        return TextFormat::LIGHT_PURPLE.$this->getWalkingBlockName().TextFormat::DARK_GRAY."(".TextFormat::YELLOW."Lobby".TextFormat::DARK_GRAY.")";
    }

    public function onReceive(Player $player): void
    {
        $playerName = $player->getName();
        $walkingBlockName = $this->getWalkingBlockName();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli) use ($playerName, $walkingBlockName){
            $res = $mysqli->query("SELECT walkingblocks FROM LobbyPlayer WHERE playername='$playerName'");
            $walkingBlocks = "";
            if($res->num_rows > 0) {
                while($data = $res->fetch_assoc()) {
                    $walkingBlocks = $data["walkingblocks"];
                    break;
                }
            }

            $walkingBlocksArray = explode(":", $walkingBlocks);
            if(in_array($walkingBlockName, $walkingBlocksArray))
                return null;

            $walkingBlocksArray[] = $walkingBlockName;
            $walkingBlocks = implode(":", $walkingBlocksArray);
            $mysqli->query("UPDATE `LobbyPlayer` SET walkingblocks='$walkingBlocks' WHERE playername='$playerName'");

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