<?php

namespace matze\chestopening\command\subcommand;

use matze\chestopening\animation\types\normal\NormalAnimation;
use matze\chestopening\crate\Crate;
use matze\chestopening\rarity\RarityIds;
use matze\chestopening\rarity\RarityManager;
use matze\chestopening\session\SessionManager;
use pocketmine\Player;

class TestSubCommand extends SubCommand {

    /**
     * @param Player $sender
     * @param array $args
     */
    public function execute(Player $sender, array $args): void{
        SessionManager::getInstance()->createSession($sender, new NormalAnimation(), RarityManager::getInstance()->getRarity(RarityIds::RARITY_COMMON), new Crate($sender));
    }
}