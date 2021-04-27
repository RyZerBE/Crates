<?php

namespace matze\chestopening\command;

use matze\chestopening\command\subcommand\AddSubCommand;
use matze\chestopening\command\subcommand\HelpSubCommand;
use matze\chestopening\command\subcommand\SubCommand;
use matze\chestopening\command\subcommand\TestSubCommand;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;

class CrateCommand extends Command {

    /** @var array  */
    private $subCommands = [];

    /**
     * CrateCommand constructor.
     */
    public function __construct(){
        parent::__construct("crate", "Crate Command");
        $this->setPermission("crate.cmd.use");

        $subCommands = [
            new HelpSubCommand("help", $this),
            new TestSubCommand("test", $this),
            new AddSubCommand("add", $this),
        ];
        foreach ($subCommands as $subCommand) {
            $this->subCommands[$subCommand->getName()] = $subCommand;
        }
    }

    /**
     * @return array
     */
    public function getSubCommands(): array {
        return $this->subCommands;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!$sender instanceof Player) return;
        if(!$this->testPermission($sender)) return;
        if(!isset($args[0])) $args[0] = "help";
        if(isset($this->subCommands[$args[0]])) {
            $this->subCommands[$args[0]]->execute($sender, $args);
            return;
        }
        /** @var SubCommand $subCommand */
        foreach ($this->getSubCommands() as $subCommand) {
            if(!$subCommand->isAlias($args[0])) {
                continue;
            }
            $subCommand->execute($sender, $args);
            return;
        }
        Server::getInstance()->dispatchCommand($sender, "crate help");
    }
}