<?php

namespace matze\chestopening;

use matze\chestopening\animation\types\normal\BoxEntity;
use matze\chestopening\command\CrateCommand;
use matze\chestopening\crate\CrateManager;
use matze\chestopening\entity\FloatingText;
use matze\chestopening\listener\BlockBreakListener;
use matze\chestopening\listener\BlockPlaceListener;
use matze\chestopening\listener\PlayerInteractListener;
use matze\chestopening\listener\PlayerJoinListener;
use matze\chestopening\reward\RewardManager;
use matze\chestopening\scheduler\CrateUpdateTask;
use matze\chestopening\session\SessionManager;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\util\async\AsyncExecutor;

class Loader extends PluginBase {

    /** @var Loader */
    private static $instance;

    const PREFIX = TextFormat::LIGHT_PURPLE.TextFormat::BOLD."Crates ".TextFormat::RESET;

    public function onEnable(){
        self::$instance = $this;

        $this->initEntities();
        $this->initListener();

        SessionManager::getInstance();
        CrateManager::getInstance();
        RewardManager::registerRewards();

        AsyncExecutor::submitMySQLAsyncTask("Lobby", function (\mysqli $mysql){
            $mysql->query("CREATE TABLE IF NOT EXISTS `Crates`(id INTEGER NOT NULL KEY AUTO_INCREMENT, playername varchar(32) NOT NULL, cratekeys int NOT NULL)");
        });

        $this->getScheduler()->scheduleRepeatingTask(new CrateUpdateTask(), 1);
        Server::getInstance()->getCommandMap()->register("chestopening", new CrateCommand());
    }

    public function onDisable(){
        //CrateManager::getInstance()->save();
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
            new BlockBreakListener(),
            new PlayerJoinListener()
        ];

        new CrateManager();
        foreach($listeners as $listener) {
            Server::getInstance()->getPluginManager()->registerEvents($listener, $this);
        }
    }
}