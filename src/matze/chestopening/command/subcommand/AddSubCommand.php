<?php

namespace matze\chestopening\command\subcommand;

use matze\chestopening\utils\ItemUtils;
use pocketmine\item\Item;
use pocketmine\Player;

class AddSubCommand extends SubCommand {

    /**
     * @param Player $sender
     * @param array $args
     */
    public function execute(Player $sender, array $args): void{
        $item = Item::get(Item::END_PORTAL_FRAME)->setCustomName("§r§aCrate [Place]");
        $item = ItemUtils::addItemTag($item, "add", "crate");
        $sender->getInventory()->addItem($item);
    }
}