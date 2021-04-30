<?php


namespace matze\chestopening\reward\types\lobby\particle;


use BauboLP\Core\Provider\AsyncExecutor;
use BauboLP\Core\Provider\LanguageProvider;
use matze\chestopening\Loader;
use matze\chestopening\provider\ChestOpeningProvider;
use matze\chestopening\reward\Reward;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

abstract class LobbyParticle extends Reward
{

    abstract public function getParticleName(): string;

    public function getName(): string
    {
        return TextFormat::LIGHT_PURPLE.$this->getParticleName().TextFormat::DARK_GRAY."(".TextFormat::YELLOW."Lobby".TextFormat::DARK_GRAY.")";
    }

    public function onReceive(Player $player): void
    {
        $playerName = $player->getName();
        $particleName = $this->getParticleName();
        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysqli) use ($playerName, $particleName){
            $res = $mysqli->query("SELECT particles FROM LobbyPlayer WHERE playername='$playerName'");
            $particles = "";
            if($res->num_rows > 0) {
                while($data = $res->fetch_assoc()) {
                    $particles = $data["particles"];
                    break;
                }
            }

            $particlesArray = explode(":", $particles);
            if(in_array($particleName, $particlesArray))
                return null;

            $particlesArray[] = $particleName;
            $particles = implode(":", $particlesArray);
            $mysqli->query("UPDATE `LobbyPlayer` SET particles='$particles' WHERE playername='$playerName'");

            return true;
        }, function (Server $server, $res) use ($playerName, $particleName) {
            $player = $server->getPlayerExact($playerName);
            if(is_null($player)) return;
            if(is_null($res)) {
                $player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("crate-already-have", $playerName, ["#article" => $particleName]));
                ChestOpeningProvider::addKey($playerName);
                return;
            }
        });
    }
}