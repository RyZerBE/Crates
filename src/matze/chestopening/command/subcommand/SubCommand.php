<?php

namespace matze\chestopening\command\subcommand;

use pocketmine\command\Command;
use pocketmine\Player;
use function in_array;
use function is_null;

abstract class SubCommand {

    /** @var string */
    private $name;
    /** @var array */
    private $aliases;
    /** @var Command */
    private $command;
    /** @var string */
    private $usage;

    /**
     * SubCommand constructor.
     * @param string $name
     * @param Command $command
     * @param string|null $usage
     * @param array $aliases
     */
    public function __construct(string $name, Command $command, ?string $usage = null, array $aliases = []) {
        $this->name = $name;
        $this->aliases = $aliases;
        $this->command = $command;
        $this->usage = (is_null($usage) ? $name : $usage);
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUsage(): string {
        return "§r/" . $this->getCommand()->getName() . " " . $this->usage;
    }

    /**
     * @return Command
     */
    public function getCommand(): Command {
        return $this->command;
    }

    /**
     * @return array
     */
    public function getAliases(): array {
        return $this->aliases;
    }

    /**
     * @param string $argument
     * @return bool
     */
    public function isAlias(string $argument): bool {
        return in_array($argument, $this->getAliases());
    }

    /**
     * @param Player $sender
     * @param array $args
     */
    abstract public function execute(Player $sender, array $args): void;
}