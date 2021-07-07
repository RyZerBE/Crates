<?php

namespace matze\chestopening\listener;

use matze\chestopening\crate\CrateManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class PlayerJoinListener implements Listener {

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event): void {
        foreach(CrateManager::getInstance()->getCrates() as $crate) {
            if($crate->isInit()) continue;
            $crate->setInit(true);
            $crate->setInUse(false);
        }
    }
}