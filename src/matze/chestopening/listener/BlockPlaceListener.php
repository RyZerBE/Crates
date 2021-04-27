<?php

namespace matze\chestopening\listener;

use matze\chestopening\crate\Crate;
use matze\chestopening\crate\CrateManager;
use matze\chestopening\utils\ItemUtils;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;

class BlockPlaceListener implements Listener {

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event): void {
        $item = $event->getItem();
        $block = $event->getBlock();

        if(!ItemUtils::hasItemTag($item, "crate")) return;
        $crate = new Crate($block);
        CrateManager::getInstance()->addCrate($crate);
    }
}