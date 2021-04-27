<?php

namespace matze\chestopening\listener;

use matze\chestopening\crate\CrateManager;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use function is_null;

class BlockBreakListener implements Listener {

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event): void {
        $block = $event->getBlock();
        if(is_null(($crate = CrateManager::getInstance()->getCrate($block)))) return;
        CrateManager::getInstance()->removeCrate($crate);
        $event->getPlayer()->sendMessage("Crate was removed.");//todo: message
    }
}