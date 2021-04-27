<?php

namespace matze\chestopening;

use matze\chestopening\animation\types\normal\BoxEntity;
use matze\chestopening\command\CrateCommand;
use matze\chestopening\crate\CrateManager;
use matze\chestopening\entity\FloatingText;
use matze\chestopening\listener\BlockPlaceListener;
use matze\chestopening\listener\PlayerInteractListener;
use matze\chestopening\rarity\RarityManager;
use matze\chestopening\scheduler\CrateUpdateTask;
use matze\chestopening\session\SessionManager;
use matze\city\waypoint\Waypoint;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class Loader extends PluginBase {

    /** @var Loader */
    private static $instance;

    public function onEnable(){
        self::$instance = $this;

        $this->initEntities();
        $this->initListener();

        SessionManager::getInstance();
        CrateManager::getInstance();
        RarityManager::getInstance();

        $this->getScheduler()->scheduleRepeatingTask(new CrateUpdateTask(), 1);
        Server::getInstance()->getCommandMap()->register("chestopening", new CrateCommand());
    }

    public function onDisable(){
        CrateManager::getInstance()->save();
    }

    /**
     * @return Loader
     */
    public static function getInstance(): Loader{
        return self::$instance;
    }

    private function initEntities(): void{
        $entities = [
            BoxEntity::class,
            FloatingText::class
        ];
        foreach($entities as $entity) {
            Entity::registerEntity($entity, true);
        }
    }

    private function initListener(): void {
        $listeners = [
            new BlockPlaceListener(),
            new PlayerInteractListener()
        ];
        foreach($listeners as $listener) {
            Server::getInstance()->getPluginManager()->registerEvents($listener, $this);
        }
    }
}