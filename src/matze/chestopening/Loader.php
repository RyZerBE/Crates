<?php

namespace matze\chestopening;

use matze\chestopening\animation\types\normal\BoxEntity;
use matze\chestopening\command\CrateCommand;
use matze\chestopening\crate\CrateManager;
use matze\chestopening\entity\FloatingText;
use matze\chestopening\listener\BlockBreakListener;
use matze\chestopening\listener\BlockPlaceListener;
use matze\chestopening\listener\PlayerInteractListener;
use matze\chestopening\rarity\RarityManager;
use matze\chestopening\reward\Reward;
use matze\chestopening\reward\RewardManager;
use matze\chestopening\scheduler\CrateUpdateTask;
use matze\chestopening\session\SessionManager;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase {

    /** @var Loader */
    private static $instance;

    const PREFIX = TextFormat::LIGHT_PURPLE."Crates ".TextFormat::RESET;

    public function onEnable(){
        self::$instance = $this;

        $this->initEntities();
        $this->initListener();

        SessionManager::getInstance();
        CrateManager::getInstance();
        RewardManager::registerRewards();

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
            new PlayerInteractListener(),
            new BlockBreakListener()
        ];
        foreach($listeners as $listener) {
            Server::getInstance()->getPluginManager()->registerEvents($listener, $this);
        }
    }
}