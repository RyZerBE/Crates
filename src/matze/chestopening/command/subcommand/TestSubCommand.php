<?php

namespace matze\chestopening\command\subcommand;

use pocketmine\Player;

class TestSubCommand extends SubCommand {

    /**
     * @param Player $sender
     * @param array $args
     */
    public function execute(Player $sender, array $args): void{}
}