<?php


namespace matze\chestopening\command\subcommand;


use matze\chestopening\crate\CrateManager;
use pocketmine\Player;

class CratesSaveCommand extends SubCommand
{
    /**
     * @inheritDoc
     */
    public function execute(Player $sender, array $args): void
    {
        CrateManager::getInstance()->save();
    }
}