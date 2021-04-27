<?php

namespace matze\chestopening\command\subcommand;

use pocketmine\Player;
use function str_repeat;

class HelpSubCommand extends SubCommand {

    /**
     * @param Player $sender
     * @param array $args
     */
    public function execute(Player $sender, array $args): void {
        $sender->sendMessage(str_repeat("-", 15));
        $cmd = $this->getCommand();
        foreach ($cmd->getSubCommands() as $subCommand) $sender->sendMessage($subCommand->getUsage());
        $sender->sendMessage(str_repeat("-", 15));
    }
}