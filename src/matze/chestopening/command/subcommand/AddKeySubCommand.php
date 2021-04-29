<?php


namespace matze\chestopening\command\subcommand;


use matze\chestopening\Loader;
use matze\chestopening\provider\ChestOpeningProvider;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class AddKeySubCommand extends SubCommand
{

    public function execute(Player $sender, array $args): void
    {
        if(empty($args[2])) {
            $sender->sendMessage(Loader::PREFIX.TextFormat::RED."/crate addkey <player> <keycount>");
            return;
        }

        $playerName = $args[1];
        $keyCount = $args[2];
        ChestOpeningProvider::addKey($playerName, $keyCount);
        $sender->sendMessage(Loader::PREFIX."Du hast dem Spieler ".TextFormat::LIGHT_PURPLE.$playerName." ".$keyCount." Schlüssel ".TextFormat::GREEN."gegeben.");
    }
}