<?php

namespace matze\chestopening\listener;

use matze\chestopening\crate\CrateManager;
use matze\chestopening\Loader;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use function is_null;

class BlockBreakListener implements Listener {

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event): void {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if(is_null(($crate = CrateManager::getInstance()->getCrate($block)))) return;
        if(!$player->hasPermission("crate.remove")) return;

        CrateManager::getInstance()->removeCrate($crate);
        $player->sendMessage(Loader::PREFIX.TextFormat::RED."Crate wurde entfernt.");
    }
}