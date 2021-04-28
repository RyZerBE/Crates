<?php

namespace matze\chestopening\listener;

use BauboLP\Core\Provider\LanguageProvider;
use matze\chestopening\crate\CrateManager;
use matze\chestopening\form\CrateForm;
use matze\chestopening\Loader;
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
            $player->sendMessage(Loader::PREFIX.LanguageProvider::getMessageContainer("crate-in-use", $player->getName()));
            return;
        }
        CrateForm::open($player, $crate);
    }
}