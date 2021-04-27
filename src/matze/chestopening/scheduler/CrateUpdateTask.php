<?php

namespace matze\chestopening\scheduler;

use matze\chestopening\session\SessionManager;
use pocketmine\scheduler\Task;

class CrateUpdateTask extends Task {

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick): void{
        foreach(SessionManager::getInstance()->getSessions() as $session) {
            if(!$session->isRunning()) continue;
            $session->onUpdate();
        }
    }
}