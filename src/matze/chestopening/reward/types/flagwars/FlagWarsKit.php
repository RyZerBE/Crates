<?php


namespace matze\chestopening\reward\types\flagwars;


use BauboLP\Core\Provider\AsyncExecutor;
use matze\chestopening\reward\Reward;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

abstract class FlagWarsKit extends Reward
{

    abstract public function getKitName(): string;

    public function getName(): string
    {
        return TextFormat::LIGHT_PURPLE.$this->getKitName().TextFormat::DARK_GRAY."(".TextFormat::DARK_AQUA."FlagWars".TextFormat::DARK_GRAY.")";
    }

    public function onReceive(Player $player): void
    {
        $name = $player->getName();
        $kitName = $this->getKitName();
        AsyncExecutor::submitMySQLAsyncTask("FlagWars", function (\mysqli $mysqli) use ($name, $kitName){
            $res = $mysqli->query("SELECT * FROM kits WHERE playername='$name'");
            if($res->num_rows > 0) {
                while ($data = $res->fetch_assoc()) {
                    $kits = explode(";", $data["kits"]);
                    $kits[] = $kitName;
                    $kits = implode(";", $data["kits"]);
                    $mysqli->query("UPDATE `kits` SET kits='$kits' WHERE playername='$name'");
                }
            }
        });
    }
}