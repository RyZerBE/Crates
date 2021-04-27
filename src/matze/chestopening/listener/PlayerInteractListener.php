<?php

namespace matze\chestopening\listener;

use matze\chestopening\crate\CrateManager;
use matze\chestopening\form\CrateForm;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use function is_null;
use function time;

class PlayerInteractListener implements Listener {

    /** @var array  */
    private $cooldown = [];

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $name = $player->getName();

        if(is_null(($crate = CrateManager::getInstance()->getCrate($block)))) return;
        if(($this->cooldown[$name] ?? 0) >= time()) return;
        $this->cooldown[$name] = time();
        if($crate->isInUse()) {
            $player->sendMessage("Crate is currently in use. Please wait a moment.");//todo: message
            return;
        }
        CrateForm::open($player, $crate);
    }
}